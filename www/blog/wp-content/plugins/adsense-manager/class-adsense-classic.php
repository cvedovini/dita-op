<?php

if(!ADSENSEM_VERSION){die();}

$_adsensem_networks['ad_adsense_classic'] = array(
		'name'	=>	'AdSense (Classic)',
		'shortname' => 'adc',
		'www'		=>	'http://www.google.com/adsense/',
		'display' => false,
	);

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_AdSense_Classic extends Ad_AdSense {

	function Ad_AdSense_Classic(){
		$this->Ad_AdSense();
	}

	function network(){ return 'ad_adsense_classic'; }
	
	
			function save_settings_network() {
				
			if($_POST['adsensem-account-id']!=''){ $this->set_account_id(preg_replace('/\D/','',$_POST['adsensem-account-id'])); }
			
			$this->p['channel']=strip_tags(stripslashes($_POST['adsensem-channel']));
			$this->p['adtype']=strip_tags(stripslashes($_POST['adsensem-adtype']));
			
			$this->p['color-border']=strip_tags(stripslashes($_POST['adsensem-color-border']));
			$this->p['color-title']=strip_tags(stripslashes($_POST['adsensem-color-title']));
			$this->p['color-bg']=strip_tags(stripslashes($_POST['adsensem-color-bg']));
			$this->p['color-text']=strip_tags(stripslashes($_POST['adsensem-color-text']));
			$this->p['color-link']=strip_tags(stripslashes($_POST['adsensem-color-link']));
			$this->p['uistyle']=strip_tags(stripslashes($_POST['adsensem-uistyle']));

			
			if($_POST['adsensem-action']!='edit defaults'){
				//Specific stuff for the ad/referral subtypes (cleaner)
				$this->save_settings_network_subtype();
			}
	

		}


			//Processes the alternate ad options and returns correct code(complicated and needed across the adsense ads).
		function _render_color_code(){
		$code='';
			$code.= 'google_color_border = "' . $this->pd('color-border') . '"' . ";\n";
			$code.= 'google_color_bg = "' . $this->pd('color-bg') . '"' . ";\n";
			$code.= 'google_color_link = "' . $this->pd('color-title') . '"' . ";\n";
			$code.= 'google_color_text = "' . $this->pd('color-text') . '"' . ";\n";
			$code.= 'google_color_url = "' . $this->pd('color-link') . '"' . ";\n";		return $code;
		}
	
	
		
		function import_settings_network($code) {

			if(preg_match('/google_ad_channel = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-channel'] = $matches[1]; }
			
			if(preg_match('/google_color_border = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-color-border']=$matches[1];}
			if(preg_match('/google_color_bg = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-color-title']=$matches[1];}
			if(preg_match('/google_color_link = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-color-bg']=$matches[1];}
			if(preg_match('/google_color_text = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-color-text']=$matches[1];}
			if(preg_match('/google_color_url = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-color-link']=$matches[1];}
			
			$this->import_settings_network_subtype($code);
			//$this->save_settings(); now done in the main Ad_AdSense class
		}
	
	function import_detect_network($code){return false;}
	
	
		function _form_settings_styles(){
			$default=array('' => 'Use Default');
			$uistyle=array('0' => 'Square corners', '6' => 'Slightly rounded corners', '10' => 'Very rounded corners');
			adsensem_admin::_field_select('Corner Style','uistyle',$uistyle);
	}
	
		function _form_settings_ad_format(){
			//Google AdSense data
			$default=array('' => 'Use Default');
			$adtypes=$this->_var_ad_types_available();
			$formats=$this->_var_ad_formats_available(); //Get permitted formats for the current network
			adsensem_admin::_field_select('Ad Type','adtype',$adtypes);
			adsensem_admin::_field_select('<a href="https://www.google.com/adsense/adformats" target="_new">Format</a>','adformat',$formats);
			adsensem_admin::_field_input('Channel','channel',20,'Enter multiple Channels separated by + signs.');
	}
	
	
//Middle
function _var_forms_unit(){ return array('ad_unit');}
function _var_forms_column2(){ return array('colors','styles'); }

//Show network-id on AdSense Classic
function admin_manage_column1(){
  adsensem_admin::dbxoutput($this->_var_forms_network());
  adsensem_admin::dbxoutput($this->_var_forms_unit());
  adsensem_admin::dbxoutput($this->_var_forms_column1());
}


}



require_once('class-adsense-ad.php'); //AdSense Ad units pre-slot style
require_once('class-adsense-link.php'); //AdSense Link units pre-slot style
require_once('class-adsense-referral.php'); //AdSense Referral units pre-slot style


?>
