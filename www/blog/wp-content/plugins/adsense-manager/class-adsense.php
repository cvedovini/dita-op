<?php

if(!ADSENSEM_VERSION){die();}

$_adsensem_networks['ad_adsense'] = array(
		'name'	=>	'AdSense',
		'shortname' => 'ad',
		'ico'		=>	'http://www.google.com/favicon.ico',
		'www'		=>	'http://www.google.com/adsense/',
		'www-create' => 'https://www.google.com/adsense/adsense-products',
		'www-signup'		=>	'https://www.google.com/adsense/',
		'display' => false,
		'limit-ads' => 9
		);


@define("GOOGLE_ADSENSE_SCRIPTADS_URL", "http://pagead2.googlesyndication.com/pagead/show_ads.js");

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_AdSense extends Ad_Generic {

	function Ad_AdSense(){
		$this->Ad_Generic();
	}
		
	function render_ad() {
		global $_adsensem;

		$code='';
			
			$code .= '<script type="text/javascript"><!--' . "\n";
			$code.= 'google_ad_client = "pub-' . $this->account_id() . '";' . "\n";
			$code.= 'google_ad_slot = "' . str_pad($this->pd('slot'),10,'0',STR_PAD_LEFT) . '"' . ";\n"; //String padding to max 10 char slot ID
			
			if($this->pd('adtype')=='ref_text'){
				$code.= 'google_ad_output = "textlink"' . ";\n";
				$code.= 'google_ad_format = "ref_text"' . ";\n";
				$code.= 'google_cpa_choice = ""' . ";\n";
			} else if($this->pd('adtype')=='ref_image'){
				$code.= 'google_ad_width = ' . $this->pd('width') . ";\n";
				$code.= 'google_ad_height = ' . $this->pd('height') . ";\n";
				$code.= 'google_cpa_choice = ""' . ";\n";
			} else {
				$code.= 'google_ad_width = ' . $this->pd('width') . ";\n";
				$code.= 'google_ad_height = ' . $this->pd('height') . ";\n";
			}
			
			$code.= '//--></script>' . "\n";

			$code.= '<script type="text/javascript" src="' . GOOGLE_ADSENSE_SCRIPTADS_URL . '"></script>' . "\n";

			return $code;
    }
	
	
		function import_settings($code) {
		
			if(preg_match('/google_ad_client = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-account-id'] = $matches[1]; }
			
			if(preg_match('/google_ad_width = (\d*);/', $code, $matches)!=0){
				$_POST['adsensem-width'] = $matches[1]; 
				if(preg_match('/google_ad_height = (\d*);/', $code, $matches)!=0){
					$_POST['adsensem-height'] = $matches[1]; 
					$_POST['adsensem-adformat']=$_POST['adsensem-width'] . "x" . $_POST['adsensem-height'];
				}
			}
			$this->import_settings_network($code); //passes to subtypes too in classic types
			$this->save_settings();
		}
		
		function import_settings_network($code) {

			//Copy in the name from /* comment */
			// /* 728x90, created 2/25/08 */
			if(preg_match('/\/\* (.*) \*\//', $code, $matches)!=0){ $_POST['adsensem-notes'] = $matches[1]; }
			if(preg_match('/google_ad_slot = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-slot'] = $matches[1]; }
			
			if(preg_match('/google_cpa_choice = ""/', $code, $matches)!=0){
			//Referral unit
				if(preg_match('/google_ad_output = "textlink";/', $code, $matches)!=0){$_POST['adsensem-adtype']='ref_text';}
				else {
					$_POST['adsensem-adtype']='ref_image';
					$_POST['adsensem-referralformat']=$_POST['adsensem-adformat']; //passthru
				}
			} else {
			//Non-referral unit
					$formats=$this->_var_ad_formats_available();

				if(	isset($formats['links']['horizontal'][$_POST['adsensem-adformat']]) || 
								isset($formats['links']['square'][$_POST['adsensem-adformat']]) ){
						$_POST['adsensem-adtype']='link';
						$_POST['adsensem-linkformat']=$_POST['adsensem-adformat']; //passthru
				} else {
						$_POST['adsensem-adtype']='ad';
				}
				
			}
		}
		

		

		function save_settings_network() {
			
			//Note the account-id will not be saved from a non-default page because there is no form element for it >> blank.
			if($_POST['adsensem-account-id']!=''){ $this->set_account_id(preg_replace('/\D/','',$_POST['adsensem-account-id'])); }
			
			$this->p['slot']=strip_tags(stripslashes($_POST['adsensem-slot']));
			$this->p['adtype']=strip_tags(stripslashes($_POST['adsensem-adtype']));
			
			//Override adformat saving already
			switch($this->p['adtype']){
				case 'ad': 		$this->p['adformat']=$_POST['adsensem-adformat']; break;
				case 'link': 	$this->p['adformat']=$_POST['adsensem-linkformat'];	break;
				case 'ref_image': $this->p['adformat']=$_POST['adsensem-referralformat']; break;
				default: $this->p['adformat']='';
			 }

			 list($this->p['width'],$this->p['height'],$null)=split('[x]',$this->p('adformat')); 
		}

	function reset_defaults_network() {
		global $_adsensem;
		$_adsensem['defaults'][$this->network()]+= array (

				'slot' => '',
//				'limit-counter' => '3', //AdSense network restrictions limits combined units to 
								);
	}


	function import_detect_network($code){
			
			return (	(strpos($code,'google_ad_client')!==false) &&
//								(strpos($code,'google_cpa_choice')===false) //i.e. not a referral thing
			 					(strpos($code,'google_ad_slot')!==false) //i.e. not using the new slot system
						 );
	}


	
	function counter_id(){ //Redirect to sub-type counters (maintains compatibility between old/new adsense types. Sweet)
		switch($this->p['adtype']){
			case 'ad': 				return 'ad_adsense_ad'; break;
			case 'link': 			return 'ad_adsense_link'; break;
			case 'ref_image': return 'ad_adsense_referral'; break;
		 }
	}

	
	
	/*
			ACCOUNT ID SPECIFIC SAVE/ETC.
			Allows for overriding of this in sub-ad-types, etc. to share id's between types/networks.
	*/
	
	function account_id(){
		global $_adsensem;
		return $_adsensem['account-ids']['ad_adsense'];
	}
	
	function set_account_id($aid){
		global $_adsensem;
		$_adsensem['account-ids']['ad_adsense']=$aid;
	}
	
	function can_benice(){
		return ($this->p['adtype']=='ad');
	}
	
	function _form_settings_help(){
	?><tr><td><p>Further configuration and control over channel and slot setup can be achieved through <a href="http://www.google.com/adsense/" target="_blank">Google's online system</a>:</p>
	<ul>
	<li><a href="https://www.google.com/adsense/adslots" target="_blank">Manage Ad</a><br />
			Configure ad rotation and display settings.</li>
	<li><a href="https://www.google.com/adsense/channels" target="_blank">Edit Channels</a><br />
			Get current ad code for this unit.</li>
	<li><a href="https://www.google.com/adsense/styles" target="_blank">Edit Styles</a><br />
			Change dimensions, positioning and tags.</li>
	</ul></td></tr>
	<?php	
	}
	
	function _form_settings_stats(){
	?><tr><td><p><a href="https://www.google.com/adsense/report/overview">Statistics and earnings</a></p></td></tr><?php
	}
	
	function _form_settings_ad_format(){
			//Google AdSense data
			$default=array('' => 'Use Default');
			$adtypes=$this->_var_ad_types_available();
			$formats=$this->_var_ad_formats_available(); //Get permitted formats for the current network

			//UGLY HACK, TRY REMOVE THIS SOMEHOW
			  $this->p['linkformat']=$this->p['adformat'];
			  $this->p['referralformat']=$this->p['adformat'];

			adsensem_admin::_field_select('Ad Type','adtype',$adtypes);
			adsensem_admin::_field_select('<a href="https://www.google.com/adsense/adformats" target="_new">Format</a>','adformat',$formats['ads']);
			adsensem_admin::_field_select('<a href="https://www.google.com/adsense/adformats" target="_new">Format</a>','linkformat',$formats['links']);
			adsensem_admin::_field_select('<a href="https://www.google.com/adsense/adformats" target="_new">Format</a>','referralformat',$formats['referrals']);
	}

	function _var_ad_formats_available(){
			$formats['ads']['horizontal']=array('728x90' => '728 x 90 Leaderboard', '468x60' => '468 x 60 Banner', '234x60' => '234 x 60 Half Banner');
			$formats['ads']['vertical']=array('120x600' => '120 x 600 Skyscraper', '160x600' => '160 x 600 Wide Skyscraper', '120x240' => '120 x 240 Vertical Banner');
			$formats['ads']['square']=array('336x280' => '336 x 280 Large Rectangle', '300x250' => '300 x 250 Medium Rectangle', '250x250' => '250 x 250 Square', '200x200' => '200 x 200 Small Square', '180x150' => '180 x 150 Small Rectangle', '125x125' => '125 x 125 Button');
			$formats['links']['horizontal']=array('728x15' => '728 x 15',  '468x15' => '468 x 15');
			$formats['links']['square']=array('200x90' => '200 x 90',  '180x90' => '180 x 90',  '160x90' => '160 x 90',  '120x90' => '120 x 90');
			$formats['referrals']['horizontal']=array('110x32' => '110 x 32',  '120x60' => '120 x 60',  '180x60' => '180 x 60',  '468x60' => '468 x 60');
			$formats['referrals']['square']=array('125x125' => '125 x 125');
			$formats['referrals']['vertical']=array('120x240' => '120 x 240');
			return $formats;
	}
	
	function _var_ad_types_available(){
		return array('ad' => 'Ad Unit', 'link' => 'Link Unit','ref_text' => 'Text Referral', 'ref_image' => 'Image Referral');
	}
	
	
//Admin Columns
function _var_forms_unit(){ return array('ad_slot');}
function _var_forms_column2(){ return array('ad_format'); }
function _var_forms_column3(){ return array('help','wrap_html_code','notes'); }

	
	
	
	
	
	
	

}

?>
