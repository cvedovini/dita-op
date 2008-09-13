<?php

if(!ADSENSEM_VERSION){die();}

$_adsensem_networks['ad_adsense_ad'] = array(
		'name'	=>	'AdSense Ad Unit',
		'shortname' => 'ada',
		'display' => false,
		'limit-ads' => 3
	);

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_AdSense_Ad extends Ad_AdSense_Classic {

	function Ad_AdSense_Ad(){
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
					
			$code.= 'google_ad_format = "' . $this->pd('adformat') . '_as"' . ";\n";
			$code.= 'google_ad_type = "' . $this->pd('adtype') . '"' . ";\n";

			$code.=$this->_render_alternate_ad_code();
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
			if(preg_match('/google_ad_type = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-adtype']=$matches[1];}
			if(preg_match('/google_alternate_ad_url = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-alternate-url'] = $matches[1]; $_POST['adsensem-alternate-ad']='url';}
			if(preg_match('/google_alternate_color = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-alternate-color'] = $matches[1]; $_POST['adsensem-alternate-ad']='color'; }
		}

		function save_settings_network_subtype() {
			
			$this->p['alternate-ad']=stripslashes($_POST['adsensem-alternate-ad']);
			$this->p['alternate-url']=stripslashes($_POST['adsensem-alternate-url']);
			$this->p['alternate-color']=stripslashes($_POST['adsensem-alternate-color']);
			
			$this->p['slot']='Ad';
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

				'linkformat' => '120x90',
				'linktype' => '_0ads_al_s',

				'html-before' => '',
				'html-after' => '',
		
				'alternate-ad'=>'benice',

								);
	}

		
		//Processes the alternate ad options and returns correct code(complicated and needed across the adsense ads).
		function _render_alternate_ad_code(){
		$code='';
			switch ($this->pd('alternate-ad')){
				case 'benice'	: $code.= 'google_alternate_ad_url = "?adsensem-benice=' . $this->pd('adformat') . '";' . "\n"; break;
				case 'url'		: $code.= 'google_alternate_ad_url = "' . $this->pd('alternate-url') . '";' . "\n"; break;
				case 'color'	: $code.= 'google_alternate_ad_color = "' . $this->pd('alternate-color') . '";' . "\n"; break;
				case ''				: break;
				default				: $code.= 'google_alternate_ad_url = "' . get_bloginfo('wpurl') . '/?adsensem-show-ad=' . $this->p('alternate-ad') . '";'  . "\n";
			}
		return $code;
		}

	function import_detect_network($code){
			
			return (	(strpos($code,'google_ad_client')!==false) &&
								(strpos($code,'_as')!==false) &&
								(strpos($code,'google_cpa_choice')===false) && //i.e. not a referral thing
			 					(strpos($code,'google_ad_slot')===false) //i.e. not using the new slot system
						 );
	}
	
	
//				$yesno=array("yes" => 'Yes', "no" => 'No');
			
	
	/*Adsense*/

	function _var_ad_formats_available(){
			$formats['horizontal']=array('728x90' => '728 x 90 Leaderboard', '468x60' => '468 x 60 Banner', '234x60' => '234 x 60 Half Banner');
			$formats['vertical']=array('120x600' => '120 x 600 Skyscraper', '160x600' => '160 x 600 Wide Skyscraper', '120x240' => '120 x 240 Vertical Banner');
			$formats['square']=array('336x280' => '336 x 280 Large Rectangle', '300x250' => '300 x 250 Medium Rectangle', '250x250' => '250 x 250 Square', '200x200' => '200 x 200 Small Square', '180x150' => '180 x 150 Small Rectangle', '125x125' => '125 x 125 Button');
			return $formats;
	}

	function _var_ad_types_available(){
		return array('text_image' => 'Text &amp; Image', 'image' => 'Image Only', 'text' => 'Text Only');
	}
	
	function _form_settings_alternate_ads(){
			$default=array('' => 'Use Default');
			$alternates['Basic']=array('benice'=>'Be Nice!','url'=>'URL (Enter)','color'=>'Color (Enter)','none'=>'None');
			$alternates['Defined Ads']=$this->get_alternate_ads();
			adsensem_admin::_field_select('Alternates','alternate-ad',$alternates);
			adsensem_admin::_field_input('URL','alternate-url',20,'Enter URL to alternate Ad for display when Google Ad unavailable.');
			adsensem_admin::_field_input('Color','alternate-color',20,'Enter #RRGGBB color to display when Google Ad unavailable.');
	}
	

	
function _var_forms_column2(){ return array('ad_format','colors','styles'); }
function _var_forms_column3(){ return array('help','wrap_html_code','alternate_ads','notes'); }

	

}

?>
