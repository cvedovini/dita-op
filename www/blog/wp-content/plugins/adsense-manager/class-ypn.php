<?php

if(!ADSENSEM_VERSION){die();}

$_adsensem_networks['ad_ypn'] = array(
		'name'	=>	'Yahoo! PN',
		'shortname' => 'ypn',
		'www'		=>	'http://ypn.yahoo.com/',
		//'www-create' => 'http://www.adbrite.com/zones/commerce/purchase.php?product_id_array=22',
		'www-signup'	=>	'http://ypn.yahoo.com/',														 
		 );

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_YPN extends Ad_Generic {
	
	function Ad_YPN(){
		$this->Ad_Generic();
	}
		
	function render_ad(){

		$code = '<script language="JavaScript">';
		$code .= '<!--';
		$code .= 'ctxt_ad_partner = "' . $this->account_id() . '";' . "\n";
		$code .= 'ctxt_ad_section = "' . $this->pd('channel') . '";' . "\n";
		$code .= 'ctxt_ad_bg = "";' . "\n";
		$code .= 'ctxt_ad_width = "' . $this->pd('width') . '";' . "\n";
		$code .= 'ctxt_ad_height = "' . $this->pd('height') . '";' . "\n";
		
		$code .= 'ctxt_ad_bc = "' . $this->pd('color-bg') . '";' . "\n";
		$code .= 'ctxt_ad_cc = "' . $this->pd('color-border') . '";' . "\n";
		$code .= 'ctxt_ad_lc = "' . $this->pd('color-title') . '";' . "\n";
		$code .= 'ctxt_ad_tc = "' . $this->pd('color-text') . '";' . "\n";
		$code .= 'ctxt_ad_uc = "' . $this->pd('color-url') . '";' . "\n";
		
		$code .= '// -->';
		$code .= '</script>';
		$code .= '<script language="JavaScript" src="http://ypn-js.overture.com/partner/js/ypn.js">';
		$code .= '</script>';
		
		return $code;
	}
	

		function save_settings_network() {
			
			$this->p['channel']=strip_tags(stripslashes($_POST['adsensem-channel']));
			
			$this->p['color-border']=strip_tags(stripslashes($_POST['adsensem-color-border']));
			$this->p['color-title']=strip_tags(stripslashes($_POST['adsensem-color-title']));
			$this->p['color-bg']=strip_tags(stripslashes($_POST['adsensem-color-bg']));
			$this->p['color-text']=strip_tags(stripslashes($_POST['adsensem-color-text']));
			$this->p['color-url']=strip_tags(stripslashes($_POST['adsensem-color-url']));

		}
		
		
	function reset_defaults_network() {
		global $_adsensem;
		$_adsensem['defaults'][$this->network()]+= array (
				'color-border'=> 'FFFFFF',
				'color-title'	=> '0000FF',
				'color-bg' 	=> 'FFFFFF',
				'color-text'	=> '000000',
				'color-url'	=> '0000FF',
				
				'url' => '',

				'adformat' => '250x250',
								);
	}

	
		function import_detect_network($code){
			
			return (	(strpos($code,'ypn-js.overture.com')!==false) );

		}
		
		function import_settings($code){

			if(preg_match('/ctxt_ad_section = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-channel'] = $matches[1];  }

			if(preg_match('/ctxt_ad_bc = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-color-bg'] = $matches[1]; }
			if(preg_match('/ctxt_ad_cc = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-color-border'] = $matches[1]; }
			if(preg_match('/ctxt_ad_lc = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-color-title'] = $matches[1]; }
			if(preg_match('/ctxt_ad_tc = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-color-text'] = $matches[1]; }
			if(preg_match('/ctxt_ad_uc = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-color-url'] = $matches[1]; }
			
			if(preg_match('/ctxt_ad_width = (\w*)/', $code, $matches)!=0){ $width = $matches[1]; }
			if(preg_match('/ctxt_ad_height = (\w*)/', $code, $matches)!=0){ $height = $matches[1]; }
			$_POST['adsensem-adformat'] = $width . "x" . $height;
			
			if(preg_match('/ctxt_ad_partner = "(\w*)"/', $code, $matches)!=0){$_POST['adsensem-account-id'] = $matches[1]; }
			
			
			$this->save_settings();
		}


	function _form_settings_colors(){
		$this->_form_settings_colors_generate(array('Border'=>'border','Title'=>'title','Background'=>'bg','Text'=>'text','URL'=>'url'));
	}
		
	function _form_settings_ad_format(){
			$default=array('' => 'Use Default');
			$formats=$this->_var_ad_formats_available(); //Get permitted formats for the current network
			adsensem_admin::_field_select('Format','adformat',$formats);
			adsensem_admin::_field_input('Channel','channel',20,'Enter multiple Channels separated by + signs.');
		}

		
}

?>
