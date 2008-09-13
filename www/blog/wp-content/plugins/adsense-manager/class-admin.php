<?php


if(!ADSENSEM_VERSION){die();}

function adsensem_clone($object) {
  return version_compare(phpversion(), '5.0') < 0 ? $object : clone($object);
}

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class adsensem_admin {
	
		function init_admin()
		{
			global $_adsensem;

			add_action('admin_head', array('adsensem_admin','add_header_script'));
			add_action('admin_footer', array('adsensem_admin','admin_callback_editor'));

			wp_enqueue_script('prototype');
			wp_enqueue_script('postbox');
			
			add_submenu_page('edit.php',"Ad Units", "Ad Units", 10, "adsense-manager-manage-ads", array('adsensem_admin','admin_manage'));
			add_options_page("AdSense Manager Options", "AdSense Manager", 10, "adsense-manager-options", array('adsensem_admin','admin_options'));
			add_action( 'admin_notices', array('adsensem_admin','admin_notices'), 1 );
			
			if (adsensem::setup_is_valid()==false){ //No startup data found, fill it out now.
				
				/* Wipe basic data  */
				$_adsensem['ads'] = array();//'demo-adroll' => new Ad_ShoppingAds,
				$_adsensem['defaults'] = array();
				$_adsensem['account-ids'] = array();
				$_adsensem['default-ad']=='';
				
				$_adsensem['be-nice'] = ADSENSEM_BE_NICE;
				$_adsensem['version'] = ADSENSEM_VERSION;
				
				$deluxe=get_option('acmetech_adsensedeluxe');
				if(is_array($deluxe)){adsensem_admin::add_notice('upgrade adsense-deluxe','AdSense Manager has detected a previous installation of <strong>AdSense Deluxe</strong>. Import settings?','yn');}
				
				$update_adsensem = true; 
				
			} else if(adsensem_admin::version_upgrade($_adsensem['version'],ADSENSEM_VERSION)){
					require('class-upgrade.php');
					
						//Backup cycle
						$backup=get_option('plugin_adsensem_backup');
						$backup[adsensem_admin::major_version($_adsensem['version'])]=$_adsensem;
						update_option('plugin_adsensem_backup',$backup);  unset($backup);
					
					adsensem_upgrade::go();
					$update_adsensem=true;
			}
			
			if($update_adsensem){ update_option('plugin_adsensem', $_adsensem); }
			
		}
			
		function major_version($v){
			$mv=explode('.', $v);
			return $mv[0]; //Return major version
		}
		
		function version_upgrade($old,$new){
			$ov=explode('.', $old);
			$nv=explode('.', $new);
			
			if($nv[0]>$ov[0]){
				return true;
			} else if($nv[0]==$ov[0]){
				if($nv[1]>$ov[1]){
					return true;
				} else if($nv[1]==$ov[1]){
					if($nv[2]>$ov[2]){
						return true;
					}
				}
			}
		//else
			return false;
		}
		
/*
	NOTIFICATION FUNCTIONS
	Functions below output notices to update the user on import options, issues with the data imported etc.
*/
	
	
	function admin_notices(){
		global $_adsensem;
		?>
		<?php
		if(is_array($_adsensem['notices'])){
			foreach($_adsensem['notices'] as $action=>$notice){
				?>
				<div id='update-nag'>
				<form action="edit.php?page=adsense-manager-manage-ads" method="post" id="adsensem-config-manage" enctype="multipart/form-data">
				<input type="hidden" name="adsensem-mode" value="notice">		
				<input type="hidden" name="adsensem-action" value="<?php echo $action; ?>">												
				<?php echo str_replace('AdSense Manager','<strong>AdSense Manager</strong>',$notice['text']); ?>
				<?php if($notice['confirm']=='yn'){ ?>
					<input name="adsensem-notice-confirm-yes" type="submit" value="Yes">
					<input name="adsensem-notice-confirm-no" type="submit" value="No">
				<?php } else if($notice['confirm']=='ok'){ ?>
					<input name="adsensem-notice-confirm-ok" type="submit" value="OK">
				<?php } ?>
				</form>
				</div><?php
			}
		}
		
	}
	
	function add_notice($action,$text,$confirm=false){
		global $_adsensem;
		$_adsensem['notices'][$action]['text']=$text;
		$_adsensem['notices'][$action]['confirm']=$confirm;
		update_option('plugin_adsensem', $_adsensem);		
	}
	
	function remove_notice($action){
		global $_adsensem;
		unset($_adsensem['notices'][$action]); //=false;
		update_option('plugin_adsensem', $_adsensem);		
	}

/*
		FORM ELEMENT GENERATION FUNCTIONS
		Following functions are responsible for outputting the form elements while editing ad units 
*/
	
	
	function _field_select_options($list,$selected){
			foreach($list as $key=>$value)
				{
					if(is_array($value)){ 
						?><optgroup id="adsensem-optgroup-<?php echo $key; ?>" label="<?php echo ucwords($key); ?>"><?php adsensem_admin::_field_select_options($value,$selected); ?></optgroup><?php
					} else {
						?><option <?php if($key==$selected){ echo "selected"; }?> value="<?php echo $key; ?>"> <?php echo $value; ?></option><?php 
					}
				}
	}
	
	function _field_select($title,$id,$list,$info='')
	{
		$default=array('' => 'Use Default');
		if(is_array(current($list))){$default=array('Default'=>$default);} //If optgroups in use, move the default into one. Smart.
		?>
		<tr id="adsensem-form-<?php echo $id;?>"><td class="adsensem_label"><label for="adsensem-<?php echo $id?>"><?php echo $title?>:</label></td><td>
		<select name="adsensem-<?php echo $id; ?>" id="adsensem-<?php echo $id; ?>" onchange="adsensem_form_update(this);">
			<?php if($_POST['adsensem-action']!=='edit defaults'){ ?><?php adsensem_admin::_field_select_options($default,$selected);?><?php }
				adsensem_admin::_field_select_options($list,$this->p[$id]);
			 ?></select>
		</td>
		<?php if( ($this->d($id)!='') && ($_POST['adsensem-action']!=='edit defaults') ){?><td><img class="default_note" title="[Default] <?php echo ucwords(htmlspecialchars($this->d($id))); ?>"></td><?php } ?>
		</tr>
		<?php
	}															
				
	function _field_input($title,$id,$size,$info='')
	{
	?>
	<tr id="adsensem-form-<?php echo $id;?>"><td class="adsensem_label"><label for="adsensem-<?php echo $id;?>"><?php echo $title;?>:</label></td><td>
	<input name="adsensem-<?php echo $id;?>" size="<?php echo $size;?>" title="<?php echo $info;?>" value="<?php echo htmlspecialchars($this->p[$id], ENT_QUOTES); ?>" />
	</td>
	<?php if( ($this->d($id)!='') && ($_POST['adsensem-action']!=='edit defaults') ){?><td><img class="default_note" title="[Default] <?php echo htmlspecialchars($this->d($id)); ?>"></td><?php } ?>
	</tr>
	<?php
	}

	//Reads an array and outputs the form elements specified within, wrapped in the dbx wrapper code.
	function dbxoutput($forms){
	  foreach($forms as $formelement){
	  ?>
	  <div id="poststuff">
	  <div id="<?php echo $formelement; ?>" class="postbox">
	<h3><?php echo ucwords(strtr($formelement,'_',' ')); ?></h3>
	<div class="inside">
	<table id="adsensem-settings-<?php echo $formelement; ?>"><?php	$this->{_form_settings_.$formelement}(); ?></table>
	</div>
	</div>
	</div>
	<?php

	   }
	}
	
/*
		NETWORK LISTING / AD SORTING FUNCTIONS
		Functions below sort and list networks available and the ads running on them
*/
	
	
	function network_list(){
		global $_adsensem_networks;

		$count=sizeof($_adsensem_networks);
		foreach($_adsensem_networks as $name=>$network){
			$count--;
			
			if( $network['display']===false ) { continue; }
			if( $network['www-signup']!='' ){ ?><a href="<?php echo $network['www-signup'] ?>" target="_blank"><?php }
			echo $network['name'];
			if( $network['www-signup']!='' ){ ?></a><?php }

			if($count>1){echo ', ';} else if($count==1){echo ' and ';}
		}
		
	}

	function sort_ad_step($a,$b){
		$sort_network=strcmp( $GLOBALS['_adsensem_sort_ads_by_network'][$a]->network(), $GLOBALS['_adsensem_sort_ads_by_network'][$b]->network() );
		
		if($sort_network==0){ //Same network, sort name
			return strcmp($a,$b);			
		} else {return $sort_network;}
	}
	
	/* Sort ads by the network they're on, allows Manage Ads display to show grouped */
	function sort_ads_by_network($ads){
		$GLOBALS['_adsensem_sort_ads_by_network']=$ads; //Needed to pass data into the comparison function
		uksort($ads,array('adsensem_admin','sort_ad_step'));
		$GLOBALS['_adsensem_sort_ads_by_network']='';
		return $ads;
	}

		
	
	
	
/*
		IMPORTING AND SAVING FUNCTIONS
		Functions below are for running the import process from ad network code and for checking the validity
		of submitted values, e.g. for names
*/
	

		function import_ad($code){
			global $_adsensem_networks;
			$imported=false;
						
			//We're attempting to import code
			$code=stripslashes($code);
			
			if($code!==''){
			  foreach ($_adsensem_networks as $network => $n){
			    if(call_user_func(array($network,'import_detect_network'),$code))
			      { 
				$ad=new $network;
				$ad->import_settings($code);
					
				$imported=true;
				break; //leave the foreach loop
			      }
			  }	
			}
  
			if(!$imported){$ad=new Ad_Code(); $ad->import_settings($code);}
			return $ad;
		}
		
		function validate_name($name,$network=false){
			global $_adsensem,$_adsensem_networks;
			$name=sanitize_title($name);
			
			if($name==''){
				if($network){ $base=$_adsensem_networks[$network]['shortname']; }
				else { $base='ad'; }
			} else { $base=$name; }
			
			$a=0;
			while (isset($_adsensem['ads'][$name]) || ($name=='')){
				$a++; $name=$base . '-' . $a;
			}
			
			return $name;
		}
		
	
		
		
		
		
		
/*
		PROCESS ADMIN/EDITING SUBMISSION
		Takes submission from the admin Manage ads area and processes it to save results, import code & update
*/
	
	
		/* Define and manage AdSense ad blocks for your Wordpress setup */
		function admin_manage() {
			
		// Get our options and see if we're handling a form submission.
		global $_adsensem, $_adsensem_networks;
		
		$update_adsensem=false;
;
/* Submissions from the manage ads listing at the top of the page */
		//if ( $_POST['adsensem-submit']=='manage' ) {
		switch ($_POST['adsensem-mode'].':'.$_POST['adsensem-action']){
			
			case 'manage:copy unit':
				//Copy selected advert
				$copyto=adsensem_admin::validate_name($_POST['adsensem-action-target']);
				$_adsensem['ads'][$copyto]=adsensem_clone($_adsensem['ads'][$_POST['adsensem-action-target']]); //clone() php4 hack
				$_adsensem['ads'][$copyto]->name=$copyto; //update internal name reference
				$_adsensem['ads']=adsensem_admin::sort_ads_by_network($_adsensem['ads']);
				$update_adsensem=true;
			break;
		
			case 'manage:delete unit':
				//Delete selected advert
				if($_POST['adsensem-action-target']!=$_adsensem['default-ad']){
					unset($_adsensem['ads'][$_POST['adsensem-action-target']]);
					$update_adsensem=true;
				}
			break;

			case 'manage:set default':
				//Set selected advert as default
				$_adsensem['default-ad']=$_POST['adsensem-default-name'];
				$update_adsensem=true;
			break;
			
			case 'save:edit new':
					$name=adsensem_admin::validate_name($_POST['adsensem-name']);
					$_adsensem['ads'][$name] = new $_POST['adsensem-action-target']; //temporary to access network-specific functions
					
					$_adsensem['ads'][$name]->name=$name;	//Update internal name reference (always, to ensure accuracy)
					$_adsensem['ads'][$name]->save_settings();
										
					$_POST['adsensem-mode']='manage';
					$_POST['adsensem-action']='';
					$update_adsensem=true;
			break;
			
			case 'save:edit unit':
			case 'apply:edit unit':
					/* Changing the name of an Ad, copy and delete old */
					if($_POST['adsensem-name']!=$_POST['adsensem-name-old']){
						$name=adsensem_admin::validate_name($_POST['adsensem-name']);				
						$_adsensem['ads'][$name]=adsensem_clone($_adsensem['ads'][$_POST['adsensem-name-old']]);
						//$_adsensem['ads'][$name]->name=$name; //Update object-held name
						unset($_adsensem['ads'][$_POST['adsensem-name-old']]);
						/* We can now use the new $name from this point forward, lovely */
						/* Update default if neccessary */
						if($_adsensem['default-ad']==$_POST['adsensem-name-old']){$_adsensem['default-ad']=$name;}
						$_adsensem['ads'][$name]->name=$name;	//Update internal name reference (always, to ensure accuracy)

					} else {$name=stripslashes($_POST['adsensem-name']);}
					
					$_adsensem['ads'][$name]->save_settings();
					
					if($_POST['adsensem-mode']!=='apply'){//Only for Save (Apply leave as is, return to edit page)
							$_POST['adsensem-mode']='manage';
							$_POST['adsensem-action']='';} 
					
					//if($_adsensem['default-ad']==''){$_adsensem['default-ad']=$name;}
		
					$update_adsensem=true;
			break;
			
			case 'save:restore defaults':
					$temp = new $_POST['adsensem-action-target']; //temporary to access network-specific functions
					$temp->reset_defaults();
					
					$_POST['adsensem-mode']='edit';
					$_POST['adsensem-action']='edit defaults';
					$update_adsensem=true;
			break;
		
			case 'save:edit defaults':
					$temp = new $_POST['adsensem-action-target']; //temporary to access network-specific functions
					$temp->save_settings();
					
					$_adsensem['defaults'][$_POST['adsensem-action-target']]=$temp->p;
					
					$_POST['adsensem-mode']='manage';
					$_POST['adsensem-action']='';
					$update_adsensem=true;
			break;	
		
			case 'import:edit code':
				$ad=new Ad_Code;
				$ad->save_settings(); //adsensem_admin::import_ad($_POST['adsensem-code']);
				$name=adsensem_admin::validate_name($_POST['adsensem-name'],$ad->network());
				$_adsensem['ads'][$name]=$ad;
				$_adsensem['ads'][$name]->name=$name;
				
				//Forces imported unit into the edit mode for changes
				$_POST['adsensem-action']='edit unit';
				$_POST['adsensem-action-target']=$name;
				
				$update_adsensem=true; $sort_adsensem=true;
			break;
			
			case 'import:edit unit':
				$ad=adsensem_admin::import_ad($_POST['adsensem-code']);
				$name=adsensem_admin::validate_name($_POST['adsensem-name'],$ad->network());
				$_adsensem['ads'][$name]=$ad;
				$_adsensem['ads'][$name]->name=$name;

				//Ensure that account-id passed to defaults when it does not match current submission
				//if($_adsensem['defaults'][$ad->network()]['account-id']!==$ad->p['account-id']){$_adsensem['defaults'][$ad->network()]['account-id']=$ad->p['account-id'];}				
				
				//Forces imported unit into the edit mode for changes
				$_POST['adsensem-action']='edit unit';
				$_POST['adsensem-action-target']=$name;
				
				$update_adsensem=true;
			break;
		
			case 'import:edit defaults':
				$ad=adsensem_admin::import_ad($_POST['adsensem-code']);
				if($ad->network=='Ad_Code'){$_POST['adsensem-action']=''; break;}
				
				$_adsensem['defaults'][$ad->network()]=$ad->p;

				//Forces imported unit into the edit mode for changes, to skip change mode to 'manage';
				$_POST['adsensem-action']='edit defaults';
				$_POST['adsensem-action-target']=$ad->network();
				
				$update_adsensem=true;
			break;
		
			case 'manage:edit unit': //Pass through stuff to switch modes between pages.
			case 'manage:edit defaults':
			case 'import:edit new':
				$_POST['adsensem-mode']='edit';
			break;

			default:
				$_POST['adsensem-mode']='manage';
				$_POST['adsensem-action']='';
		
		} //End switch cases

		//Set default if possible
		if($_adsensem['default-ad']==''){
		  if(sizeof($_adsensem['ads']!==0)){reset($_adsensem['ads']); $_adsensem['default-ad']=key($_adsensem['ads']); $update_adsensem=true;}
		}

		//BUGFIX CLEANUP, REMOVE WHEN STABLE
		foreach($_adsensem['ads'] as $n=>$c){
		  if((get_class($c)=='stdClass') || (!is_object($c)) ){unset($_adsensem['ads'][$n]); $update_adsensem=true;}
		}
		//END BUGFIX CLEANUP, REMOVE WHEN STABLE

		if($update_adsensem){
			$_adsensem['ads']=adsensem_admin::sort_ads_by_network($_adsensem['ads']);
			update_option('plugin_adsensem', $_adsensem);
			$_GET['pagesub']=''; //Show listing
		}
		
		if($_POST['adsensem-mode']=='manage'){ //Managing (i.e. not Importing, or editing)
			if( ($_GET['pagesub']=='create_new') || (sizeof($_adsensem['ads'])==0) ){
			  adsensem_admin::admin_manage_create();							  
			} else {
			  //Only if ads available 
			  adsensem_admin::admin_manage_table();
			}

		} else {
			//If in edit mode, output the editing form (create, edit, defaults, etc.)
			adsensem_admin::admin_manage_edit();
		}
		
					
					
		}
			
		
		
		
		
		

/*
		MAIN INTERFACE FUNCTIONS
		Output the HTML for the main user interface in admin mode - including managing ads and initiating edit forms
*/

			function admin_manage_network_header($network){
				global $_adsensem, $_adsensem_networks;
				
				$defaults = $_adsensem['defaults'][$network];
			?>
					<tr class="network_header" id="default-options">
					
					<td style="width:180px;">
					
					<?php if ($_adsensem_networks[$network]['www']!=''){ ?>
							<a class="<?php echo $network; ?>" href="<?php echo $_adsensem_networks[$network]['www'];?>" target="_blank"><?php } else { ?>
							<span class="<?php echo $network; ?>"><?php } ?>
								<?php echo $_adsensem_networks[$network]['name']; ?>
					<?php if ($_adsensem_networks[$network]['www']!=''){ ?></a><?php } else { ?></span><?php } ?></td>
					
					<?php if(($defaults['slot'])||($defaults['channel'])){?><td style="text-align:center"><?php echo htmlspecialchars($defaults['slot'], ENT_QUOTES); ?><?php echo htmlspecialchars($defaults['channel'], ENT_QUOTES); ?></td><?php } else { ?><td></td><?php } ?>
					
					<td class="colcol" title="Border" style="width:10px;background-color:#<?php echo htmlspecialchars($defaults['color-border'], ENT_QUOTES); ?>">&nbsp;</td>
					<td class="colcol" title="Link" style="width:10px;background-color:#<?php echo htmlspecialchars($defaults['color-title'], ENT_QUOTES); ?>">&nbsp;</td>
					<td class="colcol" title="Background" style="width:10px;background-color:#<?php echo htmlspecialchars($defaults['color-bg'], ENT_QUOTES); ?>">&nbsp;</td>
					<td class="colcol" title="Text" style="width:10px;background-color:#<?php echo htmlspecialchars($defaults['color-text'], ENT_QUOTES); ?>">&nbsp;</td>
					<td class="colcol colcor" title="URL" style="width:10px;background-color:#<?php echo htmlspecialchars($defaults['color-link'], ENT_QUOTES); ?>">&nbsp;</td>
					<?php
					
					if($defaults['adformat']){?><td style="width:100px;text-align:center;"><?php echo htmlspecialchars($defaults['adformat'], ENT_QUOTES); ?><br /><?php echo htmlspecialchars($defaults['linkformat'], ENT_QUOTES); ?></td><?php } else { ?><td></td><?php }
					if($defaults['alternate-ad']){?><td style="text-align:center"><?php echo htmlspecialchars($defaults['alternate-ad'], ENT_QUOTES); ?></td><?php } else { ?><td></td><?php } ?>
					<td></td>
					<td class="network_admin" style="width:10px;">
					<input class="button"  name="adsensem-edit" type="submit" value="Edit Network" onClick="document.getElementById('adsensem-action').value='edit defaults'; document.getElementById('adsensem-action-target').value='<?php echo $network?>';" title="Edit Defaults for the <?php echo $_adsensem_networks[$network]['name']; ?> network"></td>
					<td><?php if($_adsensem_networks[$network]['www-create']){?><a href="<?php echo $_adsensem_networks[$network]['www-create']; ?>" target="_blank" style="font-weight:normal;">Create Ads...</a><?php } ?></td><td></td>
					</tr>
			<?php
			}

		

		function admin_manage_table(){
		// Get our options and see if we're handling a form submission.
		global $_adsensem, $_adsensem_networks;

		?>
			<div class="wrap">
			<h2>Manage Ad Units</h2>
			
			<ul class="subsubsub"><li><a href='' class="current">Show Ads</a> |</li><li><a href='?page=adsense-manager-manage-ads&pagesub=create_new' >Create New Ad</a></li></ul>

			<!--<p>Below are your currently created Ads. Remember to set your <strong>Google Adsense ID</strong> at <a href="<?php echo get_bloginfo('wpurl');?>/wp-admin/options-general.php?page=adsense-manager-options">Options &raquo; AdSense Manager</a></p>-->
			<form action="" method="post" id="adsensem-config-manage" enctype="multipart/form-data">
				<input type="hidden" name="adsensem-mode" id="adsensem-submit" value="manage">	
				<input type="hidden" name="adsensem-action" id="adsensem-action">
				<input type="hidden" name="adsensem-action-target" id="adsensem-action-target">

				<table id="manage-ads" class="widefat">
				<thead>
				<tr style="height:3em;vertical-align:middle;"><th style="text-align:left;"  scope="col">Name</th><th style="text-aling:center">Slot</th><th colspan="5" style="width:50px;">Colours</th><th>Format</th><th>Alternate</th><th>Notes</th><th style="width:100px;">Modify</th><th style="width:120px;"></th><th>DA</th></tr>		
				</thead>
				<?php
 
					
				$previous_network='';
				
				if(is_array($_adsensem['ads'])){
				  foreach($_adsensem['ads'] as $name=>$ad)
				  {	
					if($ad->network()!==$previous_network){
						adsensem_admin::admin_manage_network_header($ad->network());
						$previous_network=$ad->network();
						$shade=0;
					}
					
					/* The below hides ads from other networks while editing a given network */
					//if(($_POST['adsensem-edit-network']!='') && ($ad['network']!=$_POST['adsensem-edit-network'])){continue;}		
					?><tr class="adrow shade_<?php echo $shade; $shade=($shade==1)?0:1; ?>">
					<td><span class="adrow_name"><?php echo htmlspecialchars($name, ENT_QUOTES); ?></span></td>
					<td <?php if((!array_key_exists('slot',$ad->p))&&(!array_key_exists('channel',$ad->p))){ echo 'class="disabled"'; } ?> style="text-align:center"><?php echo htmlspecialchars($ad->p['slot'], ENT_QUOTES); ?><?php if($ad->p['channel']!=''){echo htmlspecialchars('/'.$ad->p['channel'], ENT_QUOTES) ;} ?></td>
					
					<?php if(array_key_exists('color-border',$ad->p)){ ?> 
					<td style="width:9px;background-color:#<?php echo htmlspecialchars($ad->p['color-border'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:9px;background-color:#<?php echo htmlspecialchars($ad->p['color-title'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:9px;background-color:#<?php echo htmlspecialchars($ad->p['color-bg'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:9px;background-color:#<?php echo htmlspecialchars($ad->p['color-text'], ENT_QUOTES); ?>">&nbsp;</td>
					<td class="colcor" style="width:9px;background-color:#<?php echo htmlspecialchars($ad->p['color-link'], ENT_QUOTES); ?>">&nbsp;</td>
					<?php } else { ?><td colspan="5" class="disabled"></td><?php } ?>
								
					<td style="text-align:center;"><?php echo htmlspecialchars($ad->p['adformat'], ENT_QUOTES); ?></td>
					<td style="text-align:center"><?php echo htmlspecialchars($ad->p['alternate-ad'], ENT_QUOTES); ?></td>						
					<td style="text-align:center"><?php echo htmlspecialchars($ad->p['notes'], ENT_QUOTES); ?></td>
					<td  class="network_admin">
					<input  name="adsensem-edit" class="button" type="submit" value="Edit Settings" onClick="document.getElementById('adsensem-action').value='edit unit'; document.getElementById('adsensem-action-target').value='<?php echo $name; ?>';">
				 	</td>
							
					<td class="network_admin"><input class="button"  name="adsensem-copy" type="submit" value="Copy" onClick="document.getElementById('adsensem-action').value='copy unit';document.getElementById('adsensem-action-target').value='<?php echo $name; ?>';" title="Copy to new Ad unit"><?php
						if($name!=$_adsensem['default-ad']){?><input  class="button" name="adsensem-delete" type="submit" value="Delete" onClick="if(confirm('Delete <?php echo $name; ?>?')){document.getElementById('adsensem-action').value='delete unit'; document.getElementById('adsensem-action-target').value='<?php echo $name; ?>';} else {return false;}"></td><?php }			
					 ?></td>
							
					<td><input class="button" onClick="document.getElementById('adsensem-action').value='set default'; this.form.submit();" name="adsensem-default-name" type="radio" value="<?php echo $name; ?>"<?php
					if($name==$_adsensem['default-ad']){?> checked <?php }
					?>></td>
						
					</tr>
					<?php

				  }
				} ?>

				</table>
<p>By changing the <strong>Network</strong> settings you can update all Ads on a network at once.
<br /><strong>Default Ad</strong> indicates which Ad will be displayed in any space on your site where no specific ID is used. </p>
<p>Ads can be included in <strong>templates</strong> using <code>&lt;?php adsensem_ad('name'); ?&gt;</code> or <code>&lt;?php adsensem_ad(); ?&gt;</code> for the default Ad.<br />
Ads can be inserted into <strong>posts / pages</strong> using <code>[ad#name]</code> or <code>[ad]</code> for the default Ad. <br/>
Note that the old <code>&lt;!--adsense#name--&gt;</code> style still works if you prefer it.</p>
</form>
</div>
<?php									
} 
				
		
		function admin_manage_create(){
		// Get our options and see if we're handling a form submission.
		global $_adsensem, $_adsensem_networks;
				
?>			

<div class="wrap">
			<form action="" method="post" id="adsensem-config-import" enctype="multipart/form-data">
				<input type="hidden" name="adsensem-mode" id="adsensem-mode-import" value="import">	
				<input type="hidden" name="adsensem-action" id="adsensem-action-import">
				<input type="hidden" name="adsensem-action-target" id="adsensem-action-import-target">	
<h2>Create Ads</h2>

<ul class="subsubsub"><li><a href="?page=adsense-manager-manage-ads&pagesub=show_ads">Show Ads</a> |</li><li><a href="" class="current">Create New Ad</a></li></ul>
						
<p>AdSense Manager supports most Ad networks including <?php adsensem_admin::network_list(); ?>.</p>

<table>
<tr><td style="width:50%;vertical-align:top;">
<h3>AdSense Slots &amp; Other Networks</h3>
<p>Simply <strong>paste your Ad Code below</strong> and Import!</p>

<div><textarea rows="5" cols="65" name="adsensem-code" id="adsensem-code"></textarea>
<p class="submit" style="text-align:left;vertical-align:bottom;">
<input name="adsensem-clear" type="button" value="Clear" onclick="document.getElementById('adsensem-code').value='';">		
<input name="adsensem-save" type="submit" value="Save as Code" onclick="document.getElementById('adsensem-action-import').value='edit code'; document.getElementById('adsensem-action-import-target').value='Ad_Code';">		
<input name="adsensem-import-defualts" type="submit" value="Import to Defaults&raquo;" onclick="document.getElementById('adsensem-action-import').value='edit defaults'; document.getElementById('adsensem-action-import-target').value='Ad_Code';">
<input style="font-weight:bold;" name="adsensem-import" type="submit" value="Import to New Ad Unit&raquo;" onclick="document.getElementById('adsensem-action-import').value='edit unit';">
</p>
</div>		
		
</td><td style="width:50%;vertical-align:bottom;">
<h3>AdSense Classic</h3>
<p>If you prefer <em>not</em> to manage your AdSense ads through Google's online Slot system, you can use AdSense Manager to change colours, size and layout as in previous versions.</p>
<p>Simply <strong>choose the type of Ad</strong> to create below!</p>
<p class="submit" style="text-align:left;">
<input name="adsensem-import" type="submit" value="AdSense Ad Unit&raquo;" onclick="document.getElementById('adsensem-action-import').value='edit new'; document.getElementById('adsensem-action-import-target').value='Ad_AdSense_Ad';">
&nbsp;
<input name="adsensem-import" type="submit" value="AdSense Link Unit&raquo;" onclick="document.getElementById('adsensem-action-import').value='edit new'; document.getElementById('adsensem-action-import-target').value='Ad_AdSense_Link';">
&nbsp;
<input name="adsensem-import" type="submit" value="AdSense Referral&raquo;" onclick="document.getElementById('adsensem-action-import').value='edit new'; document.getElementById('adsensem-action-import-target').value='Ad_AdSense_Referral';">
</p>
</td></table>
					
			</form>
		</div>
		
		<?php 
			
		}
		
				
		function admin_manage_edit(){
		// Get our options and see if we're handling a form submission.
		global $_adsensem, $_adsensem_networks;
			
		
		?>
			<div class="wrap">
		
			<form action="#adsensem-config" method="post" id="adsensem-config" enctype="multipart/form-data">
		
			<?php //Default options for all situations
					if($_POST['adsensem-action']=='edit defaults'){
				
						$ad=new $_POST['adsensem-action-target']; //Create temporary ad unit to access functions
						$ad->p=$_adsensem['defaults'][$_POST['adsensem-action-target']]; //Load defaults into temporary ad
						
						?><h2>Edit Default Settings for '<span class="<?php echo $ad->network();?>"><?php echo $_adsensem_networks[$_POST['adsensem-action-target']]['name']; ?></span>'</h2>
						<p>Edit the settings for your Ad below. To use the network defaults for any element,
						simply leave that section blank or select "Use Default" from the drop down menus.</p><?php
					
					} else if ($_POST['adsensem-action']=='edit unit'){
									
						$ad=$_adsensem['ads'][$_POST['adsensem-action-target']];
						$ad->name=$_POST['adsensem-action-target'];
						
						?><h2>Edit '<span class="<?php echo $ad->network();?>"><?php echo $_POST['adsensem-action-target']; ?></span>'</h2>
						<p>Edit the settings for your Ad below. To use the network defaults for any element,
						simply leave that section blank or select "Use Default" from the drop down menus.</p><?php
						
					} else if ($_POST['adsensem-action']=='edit new'){
						
						$ad=new $_POST['adsensem-action-target'];
							
						?><h2>Create New '<span class="<?php echo $ad->network();?>"><?php echo $_adsensem_networks[$_POST['adsensem-action-target']]['name']; ?></span>' </h2>
						<p>Enter the settings for your new Ad below. To use the network defaults for any element,
						simply leave that section blank or select "Use Default" from the drop down menus.</p><?php
					
					}
				
					/* ADMIN Settings - Editing form for each Ad and defaults, reusable */
				?>
			
				<table id="adsensem_dbx"><tr>
				<td class="formlayoutsection">
				<h2>Basic</h2>
				<div id="adsensem_dbx1" class="dbx-group" >		
						<?php $ad->admin_manage_column1(); ?>
				</div></td>
				<td class="formlayoutsection">
				<h2>Appearance</h2>
				<div id="adsensem_dbx2" class="dbx-group" >		
						<?php $ad->admin_manage_column2(); ?>
				</div></td>
				<td class="formlayoutsection">
				<h2>Advanced</h2>
				<div id="adsensem_dbx3" class="dbx-group" >		
						<?php $ad->admin_manage_column3(); ?>
				</div></td>

						
			</table>
			<p class="submit">
			<input name="adsensem-cancel" type="button" value="Cancel" onclick="document.getElementById('adsensem-action').value=''; this.form.submit();">
			
			<?php if($_POST['adsensem-action']=='edit defaults') { ?><input name="adsensem-restore-defaults" type="button" value="Restore Defaults &raquo;" onclick="document.getElementById('adsensem-action').value='restore defaults'; this.form.submit();"><?php } ?>
			<?php if($_POST['adsensem-action']=='edit unit') { ?><input name="adsensem-apply" type="button" value="Apply" onclick="document.getElementById('adsensem-mode').value='apply'; this.form.submit();"><?php } ?>
			
			<input style="font-weight:bold;" type="submit" value="Save changes &raquo;">
			</p>

			
			<input name="adsensem-network" type="hidden" value="<?php echo htmlspecialchars($ad->p['network'], ENT_QUOTES); ?>" />
			<input type="hidden" name="adsensem-mode" id="adsensem-mode" value="save">
			<input type="hidden" name="adsensem-action" id="adsensem-action" value="<?php echo($_POST['adsensem-action']); ?>">
			<input type="hidden" name="adsensem-action-target" id="adsensem-action-target" value="<?php echo($_POST['adsensem-action-target']); ?>">
					
		</form>

		</div>
		<?php

		}	
	
		
		
		
		/* Define and manage AdSense ad blocks for your Wordpress setup */
		function admin_zones() {
	?><div class="wrap">
    <h2>Manage Ad Zones</h2>
		<p>Ad Zones allow you to control the positioning of groups of ads together and allow rotation, switching and contextual display.
		Not all sites will need to use Zones, but they are a powerful way to maximise revenue and relevance for your users.</p>
		<?php
		
		}
		
		
		
/* 		Define basic settings for the AdSense Manager - for block control use admin_manage */

		function admin_options() {

		// Get our options and see if we're handling a form submission.
		global $_adsensem;

		if ( $_POST['adsensem-submit'] ) {
		
			//$_adsensem['adsense-account']=preg_replace('/\D/','',$_POST['adsensem-adsense-account']);

			$_adsensem['be-nice']=max(min($_POST['adsensem-be-nice'],100),0);
			if(!is_numeric($_adsensem['be-nice'])){$_adsensem['be-nice'] = ADSENSEM_BE_NICE;}
			
			update_option('plugin_adsensem', $_adsensem);

		}

		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		
?>

		<div class="wrap">
         <h2>AdSense Manager Options</h2>

		<p>AdSense Manager has been redesigned to be as simple to use as possible. To get you started, the instructions below
				will guide you through the process of adding your first ad to AdSense Manager. Once you've created the first one
				you can simply repeat the process to add as many ads as you like!</p>
				
		<h3>Getting Started</h3>
				
		<ol>
		<li><script type="text/javascript"><!--
google_ad_client = "pub-3287067876809234";
//AdSense Text Link
google_ad_slot = "3788957769";
google_ad_output = "textlink";
google_ad_format = "ref_text";
google_cpa_choice = ""; // on file
//--></script><script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script> Or use <?php adsensem_admin::network_list(array('Ad_AdSense','Ad_AdSenseAd','Ad_AdSenseReferral','Ad_Code')); ?>.</li>
<li>Create a new ad unit using your network's ad online management system.</li>
<li>Copy the ad code generated (Edit &raquo; Copy, from within your browser)</li>
<li>Go to <a href="edit.php?page=adsense-manager-manage-ads">Manage &raquo; Ad Units</a> and paste the code into the box</li>
<li>Click <strong>Import to New Ad Unit&raquo;</strong>
</ol>

<p>If you need more help, there are <a href="http://www.mutube.com/mu/getting-started-with-adsense-manager-3x/" target="_blank">detailed instructions available on our website</a>,
or <a href="http://wordpress.org/tags/adsense-manager" target="_blank">check the forum</a> and <a href="http://wordpress.org/tags/adsense-manager#postform" target="_blank">ask a question</a>.</p>
				
   <form action="" method="post" id="adsensem-manage" enctype="multipart/form-data">
				
		<h3>Be Nice!</h3>
		<p style="text-align:justify;">
				Please support the developers of this plugin by either donating a small amount of your ad space to show our ads, or by making a donation:</p>
				
		<ol>
		<li>I'm Being Nice and donating <input style="text-align:right;" name="adsensem-be-nice" value="<?php echo htmlspecialchars($_adsensem['be-nice'], ENT_QUOTES);?>"  size="1">% of my Ad space to support development of this plugin [<a href="http://wordpress.org/extend/plugins/adsense-manager/faq/">Eh?</a>]</li>
		<li>I've given a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=martin%2efitzpatrick%40gmail%2ecom&item_name=Donation%20to%20mutube%2ecom&currency_code=USD&bn=PP%2dDonationsBF&charset=UTF%2d8" target="_blank">a very generous donation through Paypal</a> and got a warm fuzzy feeling.</li>
		<li>I've ordered a random gift <a href="http://www.amazon.co.uk/gp/registry/wishlist/3GXT94HH08RAY?reveal=unpurchased&filter=all&sort=price&layout=standard&x=7&y=10" target="_blank">from your wishlist</a> so you don't need to waste valuable development time in the shops.
		</ol>
				
		<p>Thanks to all those that have shown their support, it really does make a difference!</p>
		
		<p class="submit"><input type="submit" value="Save changes &raquo;"></p>
		</div>
		<input type="hidden" id="adsensem-submit" name="adsensem-submit" value="1" />

		
		
	<input type="hidden" id="adsensem-submit" name="adsensem-submit" value="1" />				 
				 
				 </form>

		<?php
           }




/*
		STARTUP SCRIPTS
		Initialised at startup to provide functions to the plugin etc.
*/

		function add_header_script(){
			if($_GET['page']=='adsense-manager-manage-ads'){
			?>
			<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/adsense-manager/adsense-manager.css" />
			<script src="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/adsense-manager/adsense-manager.js" /></script>
		<?php
		}
		}
	
 
		/* Add button to simple editor to include AdSense code */
		function admin_callback_editor()
		{

			global $_adsensem;

			//Editor page, so we need to output this editor button code
  			if(
				strpos($_SERVER['REQUEST_URI'], 'post.php')
			||	strpos($_SERVER['REQUEST_URI'], 'post-new.php')
			||	strpos($_SERVER['REQUEST_URI'], 'page.php')
			||	strpos($_SERVER['REQUEST_URI'], 'page-new.php')
			||	strpos($_SERVER['REQUEST_URI'], 'bookmarklet.php'))
			{
			?>
			  <script language="JavaScript" type="text/javascript">
			    <!--
				    var ed_adsensem = document.createElement("select");
	
					ed_adsensem.setAttribute("onchange", "add_adsensem(this)");

			    	ed_adsensem.setAttribute("class", "ed_button");
			    	ed_adsensem.setAttribute("title", "Select AdSense to Add to Content");
			    	ed_adsensem.setAttribute("id", "ed_adsensem");					

					adh = document.createElement("option");
					adh.value='';
					adh.innerHTML='AdSense...';
					adh.style.fontWeight='bold';
					ed_adsensem.appendChild(adh);

					def = document.createElement("option");
					def.value='';
					def.innerHTML='Default Ad';

					ed_adsensem.appendChild(def);
					<?php 

					if(sizeof($_adsensem['ads'])!=0){
					foreach($_adsensem['ads'] as $name=>$ad)
					{
						?>	var opt = document.createElement("option");
							opt.value='<?php echo $name; ?>';
							opt.innerHTML='#<?php echo $name; ?>';
							ed_adsensem.appendChild(opt);
						<?php
					}
					}

					?>
			    	document.getElementById("ed_toolbar").insertBefore(ed_adsensem, document.getElementById("ed_spell"));
			    
					/* Below is a Kludge for IE, which causes it to re-read the state of onChange etc. set above. Tut tut tut */
					if (navigator.appName == 'Microsoft Internet Explorer') {
						document.getElementById("ed_toolbar").innerHTML=document.getElementById("ed_toolbar").innerHTML; 
					}
				
			    function add_adsensem(element)
			    {
					if(element.selectedIndex!=0){
	
					if(element.value=='')
						{adsensem_code = '[ad]';}
					else
						{adsensem_code = '[ad#' + element.value + ']';}

					contentField = document.getElementById("content");
					if (document.selection && !window.opera) {
						// IE compatibility
						contentField.value += adsensem_code;
					} else
					if (contentField.selectionStart || contentField.selectionStart == '0') {

						var startPos = contentField.selectionStart;
						var endPos = contentField.selectionEnd;
						contentField.value = contentField.value.substring(0, startPos) + adsensem_code + contentField.value.substring(endPos, contentField.value.length);

					} else {

						contentField.value += adsensem_code;
					}
						element.selectedIndex=0;

					}
				}
			  // -->
			</script>
	  <?php
	}
		
		}


}


?>