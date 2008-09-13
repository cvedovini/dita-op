<?php

if(!ADSENSEM_VERSION){die();}

$_adsensem_networks['ad_adsense_link'] = array(
		'name'	=>	'AdSense Link Unit',
		'shortname' => 'adl',
		'display' => false,
		'limit-ads' => 3
	);

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_AdSense_Link extends Ad_AdSense_Classic {

	function Ad_AdSense_Link(){
		$this->Ad_AdSense_Classic();
	}
	

	function render_ad() {
		
		global $_adsensem;
				
		$code='';

		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "pub-' . $this->account_id() . '";' . "\n";
					
		if($this->pd('channel')!==''){ $code.= 'google_ad_channel = "' . $this->pd('channel') . '";' . "\n"; }
		if($this->pd('uistyle')!==''){ $code.= 'google_ui_features = "rc:' . $this->pd('uistyle') . '";' . "\n"; }
					
		$code.= 'google_ad_width = ' . $this->pd('width') . ";\n";
		$code.= 'google_ad_height = ' . $this->pd('height') . ";\n";
					
		$code.= 'google_ad_format = "' . $this->pd('adformat') . $this->pd('adtype') . '"' . ";\n"; 

		//$code.=$this->_render_alternate_ad_code();
		$code.=$this->_render_color_code();
			
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="' . GOOGLE_ADSENSE_SCRIPTADS_URL . '"></script>' . "\n";

		return $code;
	}
				
	/* ADMIN Settings - Editing form for each Ad and defaults, reusable */
	function admin_manage_form_network($name=false)
	{

			if($this->p['product']==''){$this->p['product']='ad';}
	 if($name!==false){ ?>
		<script>adsensem_update_options('<?php echo $this->p['product']; ?>');</script>
		<?php } 
	}


		   
		function import_settings_network_subtype($code) {
			
			if(preg_match('/google_ad_format = "(.*)"/', $code, $matches)!=0){
				$adformat=$matches[1];
				if(strstr($adformat,'_0adsl_al_s')){ $_POST['adtype']="_0adsl_al_s"; } else { $_POST['adtype']="_0adsl_al"; }
			}
		
		}

		function save_settings_network_subtype() {
			
			$this->p['alternate-ad']=stripslashes($_POST['adsensem-alternate-ad']);
			$this->p['alternate-url']=stripslashes($_POST['adsensem-alternate-url']);
			$this->p['alternate-color']=stripslashes($_POST['adsensem-alternate-color']);

			$this->p['slot']='Link';
			
			//$this->p['notes']='Link Unit';
		}

	function reset_defaults_network() {
		global $_adsensem;
		$_adsensem['defaults'][$this->network()]+= array (
				'color-border'=> 'FFFFFF',
				'color-title'	=> '0000FF',
				'color-bg' 	=> 'FFFFFF',
				'color-text'	=> '000000',
				'color-link'	=> '008000',
				
				'channel' => '',
				'uistyle' => '',
				'slot' => '',

				'adformat' => '250x250',
				'adtype' => 'text_image',

				'adformat' => '120x90',
				'linktype' => '_0ads_al_s',

				'html-before' => '',
				'html-after' => '',

								);
	}


	function import_detect_network($code){
			
			return (	(strpos($code,'google_ad_client')!==false) &&
								(strpos($code,'_0ads_al')!==false) &&
					
								(strpos($code,'google_cpa_choice')===false) && //i.e. not a referral thing
			 					(strpos($code,'google_ad_slot')===false) //i.e. not using the new slot system
						 );
			
	}
	
	function _var_ad_formats_available(){
			$formats['horizontal']=array('728x15' => '728 x 15',  '468x15' => '468 x 15');
			$formats['square']=array('200x90' => '200 x 90',  '180x90' => '180 x 90',  '160x90' => '160 x 90',  '120x90' => '120 x 90');
			return $formats;
	}
	
	function _var_ad_types_available(){
		return array('_0ads_al' => '4 Ads Per Unit', '_0ads_al_s' => '5 Ads Per Unit');
	}

	function can_benice(){return false;}
	
	
	
//Middle
function admin_manage_column2(){ return array('ad_format', 'colors','styles'); }

	
	
	
	

}

?>
