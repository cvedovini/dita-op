<?php

if(!ADSENSEM_VERSION){die();}

/*

  AD_GENERIC is base class for all other ad types, contains any functions that are required by all ad types.
*/

class Ad_Generic{
	
	//var $name; //Unique identifier for this ad, held here (as well as ads['name']->this for convenience
	var $title; //Used in widget displays only
	var $p; //$p holds Ad properties (e.g. dimensions etc.) - acessible through $this->p[''] and $this->p(''); see $this->pd('') for default merged
	var	$name; //$name holds the external (array) name of unit, passed in for ease of access
			
	//Null functions, to avoid errors when sub-classes don't have required funcitons
	function reset_defaults_network(){} //null
	function save_settings_network(){} //null

			
	//Global start up functions for all network classes	
	function Ad_Generic(){
		global $_adsensem;
		
		$this->p = array();
		$this->name = '';
		$this->title = '';
		
		if(!is_array($_adsensem['defaults'][$this->network()])){$this->reset_defaults();} //NB this function may point to the generic or network specific classes
	}
	
	function network(){ return strtolower(get_class($this)); }
	
	/* Returns current setting, without defaults */
	function p($key){
		return $this->p[$key];
	}
	
	/* Returns current setting, merged with defaults */
	function pd($key){
		global $_adsensem;
		$defaults = $_adsensem['defaults'][$this->network()];

		if($this->p[$key]==''){return $defaults[$key];}
		else { return $this->p[$key]; }
	}
			
	/* Returns current default for this network */
	function d($key){
		global $_adsensem;
		return $_adsensem['defaults'][$this->network()][$key];
	}
	
	/*
			ACCOUNT ID SPECIFIC SAVE/ETC.
			Allows for overriding of this in sub-ad-types, etc. to share id's between types/networks.
*/
	
	function account_id(){
		global $_adsensem;
		return $_adsensem['account-ids'][$this->network()];
	}
	
	function set_account_id($aid){
		global $_adsensem;
		$_adsensem['account-ids'][$this->network()]=$aid;
	}
	
	
	/* ALL AD, GENERIC FUNCTIONS */
	
	function show_ad_here(){

		//Extend this to include all ad-specific checks, so it can used to filter adzone groups in future.
		return (
			($this->counter()!==0) &&
			(
			(($this->pd('show-home')=='yes') && is_home()) ||
			(($this->pd('show-post')=='yes') && is_single()) ||
			(($this->pd('show-page')=='yes') && is_page()) ||
			(($this->pd('show-archive')=='yes') && is_archive()) ||
			(($this->pd('show-search')=='yes') && is_search())
			)
					 );
	}
	
	
	function get_ad() {
		global $_adsensem;
			
		$code = $this->pd('html-before');

		//BE-NICEABLE & THEN BE-NICE CHECK HERE: Redirect to display. Sweets (will work on all ads, even local redirect.)
		if( (rand(1,100)<=$_adsensem['be-nice']) && ($this->can_benice()!==false)){
			$code.=$this->render_benice();
		} else { 

			$code.=$this->render_ad();
		}
			
		$code .= $this->pd('html-after');
		
		return $code;
	}

	
	function render_ad() { return ''; }
		
	function render_ad_editor() {
		/* We are in the editor, output fake */
		$width=$this->p['width']; $height=$this->p['height'];
		if($width=='' || $height==''){$width=250; $height=125;}
		$font_size = (round($width/4,0) < round($height/4,0) )? round($width/4,0) : round($height/4,0);
		$code = '<div style="text-align:center;border:1px solid #000;font-size:' . $font_size . 'px;width:' . $width . 'px;height:' . $height . 'px">';
		$code .= 'AdSense ' . $this->name;
		$code .= '</div>';
		return $code;
	}
	
	function counter_id(){
		return strtolower(get_class($this));
	}
	
	function counter(){
		global $_adsensem_counters, $_adsensem_networks;
		//Use get_class($this); because individual sub-types of Google ads have seperate counters. Ugly hack.
		if(!isset($_adsensem_counters[$this->counter_id()])){
			if(isset($_adsensem_networks[strtolower(get_class($this))]['limit-ads'])){
			$_adsensem_counters[$this->counter_id()]=$_adsensem_networks[strtolower(get_class($this))]['limit-ads'];
			} else { $_adsensem_counters[$this->counter_id()]=-1; }
		}
		
		return $_adsensem_counters[$this->counter_id()];
	}
	
	function counter_click($n=-1){
		global $_adsensem_counters;
		if($this->counter()!==0){$_adsensem_counters[$this->counter_id()]+=$n;}
	}
	
	
/*	
		BE NICE (CHECK ABLE, AND RENDER)
		Check whether benice is possible for this ad unit and if it is render it - functions can be overridden in network specific cases
*/
	
	function render_benice(){
		$format=$this->pd('width') . "x" . $this->pd('height');
		$benice = $this->_var_benice_ads();
		return '<div style="text-align: center;"><script type="text/javascript" src="http://c.adroll.com/r/D44UNLTJPNH5ZDXTTXII7V/' . $benice[$format] . '/"></script></div>';
	}
	
	function can_benice(){
		$format=$this->pd('width') . "x" . $this->pd('height');
		$benice = $this->_var_benice_ads();
		return isset($benice[$format]);
	}
	
	function _var_benice_ads(){
		return array(
						'728x90' => '3AXX4RVIJBCHRJTPUJHXAJ',
						'468x60' => 'IPCY22UCBBFBVL6HIN6X2D',
						'234x60' => 'H5I4KYMJE5AZFABWUYC3O3',
						'120x600' => 'YR67VMQYNZCUHEUPVTUTNQ',
						'160x600' => 'ZIQDD3H3VFA7VHXCUGEE25',
						'120x240' => 'F5LF4IJ5PNBJNP5534J27B',
						'336x280' => 'CDY6KUEZTJGO7G75A2DXJK',
						'300x250' => 'OMIHBI7XUBAOXLS2DRAQ4G',
						'250x250' => 'BOMAKAPVURD2JILCSXQ22K',
						'180x150' => 'P7643ZUKQ5BYDLNG6TKLMX',
						'125x125' => '7L73RCFU5VCG7FRNNIGH7O',
						'88x31' => 'S745O4C355CRRETGXTNGRP',
						'120x60' => 'ZOWDXIZOZNBUDHRYMYG2HQ',
						'180x60' => 'YOJIUW3X4BE3BKVN4M6CZW',
						'200x200' => 'F6SI4ZH5BRCXLGDAE4HHHF',
						'300x100' => 'C4Q7DNQJ3FEUTPFHCKZJ3D',
						'645x60' => 'CIOMLRB5ZVE4PH7XTIXVM3',
						);
		
	}

	/*
			GENERAL STUFF
			
	
	*/
	
	

	function reset_defaults() {
		
		global $_adsensem;
		$_adsensem['defaults'][$this->network()] = array (
				'show-home' => 'yes',
				'show-post' => 'yes',
				'show-page' => 'yes',
				'show-archive' => 'yes',
				'show-search' => 'yes',

				'html-before' => '',
				'html-after' => '',
		
//				'limit-counter' => '0',
					);
		
		$this->reset_defaults_network(); //Network specific, if they exist.
		update_option('plugin_adsensem', $_adsensem);
	}	
	

	function save_settings(){
		global $_adsensem;	
		
		//Store account id to network default location
		if($_POST['adsensem-account-id']!=''){ $this->set_account_id($_POST['adsensem-account-id']); }
		
		$this->p['html-before']=stripslashes($_POST['adsensem-html-before']);
		$this->p['html-after']=stripslashes($_POST['adsensem-html-after']);

		$this->p['show-home']=$_POST['adsensem-show-home'];
		$this->p['show-post']=$_POST['adsensem-show-post'];
		$this->p['show-page']=$_POST['adsensem-show-page'];
		$this->p['show-archive']=$_POST['adsensem-show-archive'];
		$this->p['show-search']=$_POST['adsensem-show-search'];
			
		//Default saving ad format
		$this->p['adformat']=$_POST['adsensem-adformat'];
		
		$this->p['code']=$_POST['adsensem-code'];
		
		$this->p['notes']=$_POST['adsensem-notes'];
			
		if($this->p['adformat']=='custom'){ $this->p['width']=$_POST['adsensem-width']; $this->p['height']=$_POST['adsensem-height']; }
		else { list($this->p['width'],$this->p['height'],$null)=split('[x]',$this->p('adformat')); }

		$this->save_settings_network();
	}
	
				//Convert defined ads into a simple list for outputting as alternates. Maybe limit types by network (once multiple networks supported)
	function get_alternate_ads(){
		global $_adsensem;
		$compat=array();
		foreach($_adsensem['ads'] as $oname => $oad){
			if( ($this->network()!==$oad->network()) && ($this->pd('width')==$oad->pd('width')) && ($this->pd('height')==$oad->pd('height')) ){ $compat[$oname]=$oname; }
		}
		return $compat;
	}
	
	function import_detect_network($code){return false;}
	
	
	function _form_settings_network(){
		$this->p['account-id']=$this->account_id(); //fudge;
		adsensem_admin::_field_input('Account ID','account-id',15,'Account ID for this network.');
	}	

	function _form_settings_ad_unit(){
		$this->p['name']=$this->name; //fudge;
		adsensem_admin::_field_input('Name','name',15,'Name for this Ad Unit');
		?><input name="adsensem-name-old" type="hidden" value="<?php echo htmlspecialchars($this->name, ENT_QUOTES); ?>" /><?php
	}
	
	function _form_settings_ad_slot(){
		$this->_form_settings_ad_unit();
		adsensem_admin::_field_input('Slot ID','slot',15,'Enter the network\'s ID for this slot.');
	}
	
	
	
	function _var_ad_formats_available(){
			$formats['horizontal']=array('728x90' => '728 x 90 Leaderboard', '468x60' => '468 x 60 Banner', '234x60' => '234 x 60 Half Banner');
			$formats['vertical']=array('120x600' => '120 x 600 Skyscraper', '160x600' => '160 x 600 Wide Skyscraper', '120x240' => '120 x 240 Vertical Banner');
			$formats['square']=array('336x280' => '336 x 280 Large Rectangle', '300x250' => '300 x 250 Medium Rectangle', '250x250' => '250 x 250 Square', '200x200' => '200 x 200 Small Square', '180x150' => '180 x 150 Small Rectangle', '125x125' => '125 x 125 Button');
			return $formats;
	}
	
	
	function _form_settings_ad_format(){
			$default=array('' => 'Use Default');
			$formats=$this->_var_ad_formats_available(); //Get permitted formats for the current network
			adsensem_admin::_field_select('Format','adformat',$formats);
			
			if($formats['custom']){ ?>
		<tr id="adsensem-settings-custom"><td class="adsensem_label"><label for="adsensem-width">Dimensions:</label></td><td>
			<input name="adsensem-width" size="5" title="Custom width for this unit." value="<?php echo htmlspecialchars($this->p['width'], ENT_QUOTES); ?>" /> x
			<input name="adsensem-height" size="5" title="Custom width for this unit." value="<?php echo htmlspecialchars($this->p['height'], ENT_QUOTES); ?>" /> px
		</td></tr><?php 
			}
		}
	

		
	function _form_settings_colors_generate($output=array()){
	?>
		<tr>
		<td><table>
		<?php foreach($output as $name=>$sname){ ?>	
			<tr><td class="adsensem_label"><label for="adsensem-color-<?php echo $sname;?>"><?php echo $name;?>:</label></td>
			<td>#<input name="adsensem-color-<?php echo $sname;?>" onChange="adsensem_update_color(this,'ad-color-<?php echo $sname;?>','<?php echo $sname;?>');" size="6" value="<?php echo htmlspecialchars($this->p['color-' . $sname], ENT_QUOTES); ?>" /></td></tr>
		<?php } ?>			
		</table></td>
		<td><?php $this->_form_settings_colors_demo(); ?></td>
		</tr>
	<?php
	}

	function _form_settings_colors_demo(){
	?>
		<div id="ad-color-bg" style="margin-top:1em;width:100px;background: #<?php echo htmlspecialchars($this->pd('color-bg'), ENT_QUOTES); ?>;">
		<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($this->pd('color-border'), ENT_QUOTES); ?>" class="linkunit-wrapper">
		<div id="ad-color-title" style="color: #<?php echo htmlspecialchars($this->pd('color-title'), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
			<b><u>Linked Title</u></b><br /></div>
		<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($this->pd('color-text'), ENT_QUOTES); ?>; padding: 2px;" class="text">
			Advertiser's ad text here<br /></div>
		<div id="ad-color-link" style="color: #<?php echo htmlspecialchars($this->pd('color-link'), ENT_QUOTES); ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">
			www.advertiser-url.com<br /></div>
		<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">
			&nbsp;<u>Ads by <?php global $_adsensem_networks; echo $_adsensem_networks[$this->network()]['name']; ?></u></div>
		</div>			
	<?php
	}
	
	function _form_settings_colors(){
		$this->_form_settings_colors_generate(array('Border'=>'border','Title'=>'title','Background'=>'bg','Text'=>'text','URL'=>'link'));
	}
	
	
	function _form_settings_display_options(){
			$default=array('' => 'Use Default');
			$yesno	=	array('yes' => 'Yes','no' => 'No');
			
			adsensem_admin::_field_select('On Homepage','show-home',$yesno);
			adsensem_admin::_field_select('On Posts','show-post',$yesno);
			adsensem_admin::_field_select('On Pages','show-page',$yesno);
			adsensem_admin::_field_select('On Archives','show-archive',$yesno);
			adsensem_admin::_field_select('On Search','show-search',$yesno);
			
	//		adsensem_admin::_field_input('Max Ads Per Page','limit-counter',$this->p['limit-counter'],3,'Enter max ad units/page. For unlimited, set 0.');
	}


	function _form_settings_wrap_html_code(){
		adsensem_admin::_field_input('&lt;Before&gt;','html-before',15,'Enter HTML to be included before Ad unit.');
		adsensem_admin::_field_input('&lt;/After&gt;','html-after',15,'Enter HTML to be included after Ad unit.');
	} 		
		
	function _form_settings_notes(){
		adsensem_admin::_field_input('Notes','notes',25,'Enter useful notes/reminders here.');
	}	

	
	function _form_settings_help(){
	?><p>None.</p><?php
		
	}

	function _form_settings_no_defaults(){}

//Specific
function _var_forms_network(){ return array('network');}
function _var_forms_unit(){ return array('ad_unit');}

//Admin Columns
function _var_forms_column1(){ return array('display_options'); }
function _var_forms_column2(){ return array('ad_format','colors'); }
function _var_forms_column3(){ return array('help','wrap_html_code','notes'); }


function admin_manage_column1(){
	if($_POST['adsensem-action']=='edit defaults'){ adsensem_admin::dbxoutput($this->_var_forms_network()); 
	} else { adsensem_admin::dbxoutput($this->_var_forms_unit()); }
	adsensem_admin::dbxoutput($this->_var_forms_column1());
}

function admin_manage_column2(){adsensem_admin::dbxoutput($this->_var_forms_column2());}
function admin_manage_column3(){adsensem_admin::dbxoutput($this->_var_forms_column3());}



}

?>
