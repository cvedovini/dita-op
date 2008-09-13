<?php

if(!ADSENSEM_VERSION){die();}

$_adsensem_networks['ad_adsense_referral'] = array(
		'name'	=>	'AdSense Referral',
		'shortname' => 'adr',
		'display' => false,
		'limit-ads' => 3
		);

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_AdSense_Referral extends Ad_AdSense_Classic {

	function Ad_AdSense_Referral(){
		$this->Ad_AdSense_Classic();
	}
		

	function render_ad() {
		
		global $_adsensem;

		//if($ad===false){$ad=$_adsensem['ads'][$_adsensem['default_ad']];}
		//$ad=adsensem::merge_defaults($ad); //Apply defaults
		if($this->pd('product')=='referral-image') {
					$format = $this->pd('adformat') . '_as_rimg';
				} else if($this->pd('product')=='referral-text') {
					$format = 'ref_text';
				}				
			$code='';

	
					$code .= '<script type="text/javascript"><!--' . "\n";
					$code.= 'google_ad_client = "pub-' . $this->account_id() . '";' . "\n";
					
					if($this->pd('channel')!==''){ $code.= 'google_ad_channel = "' . $this->pd('channel') . '";' . "\n"; }
					
					if($this->pd('product')=='referral-image'){
						$code.= 'google_ad_width = ' . $this->pd('width') . ";\n";
						$code.= 'google_ad_height = ' . $this->pd('height') . ";\n";
					}
					
					if($this->pd('product')=='referral-text'){$code.='google_ad_output = "textlink"' . ";\n";}
					$code.='google_cpa_choice = "' . $this->pd('referral') . '"' . ";\n";
					
					$code.= "\n" . '//--></script>' . "\n";

					$code.= '<script type="text/javascript" src="' . GOOGLE_ADSENSE_SCRIPTADS_URL . '"></script>' . "\n";

			return $code;
    }
				
		function can_benice(){return false;}
		
		function save_settings_network_subtype() {
			$this->p['product']=$_POST['adsensem-product'];
			$this->p['referral']=strip_tags(stripslashes($_POST['adsensem-referral']));
			
			$this->p['slot']='Referral';
		
		}

		

		   
		function import_settings_network_subtype($code) {
			
			//as_rimg - Referral (Image)
			//ref_text - Referral (Text)
			//_0ads_al - Link Unit
			//_as - Ad Unit
			
			if(preg_match('/google_ad_format = "(.*)"/', $code, $matches)!=0){
				$adformat=$matches[1];
				if(strstr($adformat,'_as_rimg')!==false){ $_POST['adsensem-product']='referral-image'; }
				else { $_POST['adsensem-product']='referral-text';	}
				preg_match('/google_cpa_choice = "(.*)"/', $code, $matches);
				$_POST['adsensem-referral']=$matches[1];
			}

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

								);
	}


	function import_detect_network($code){
			
			return (	(strpos($code,'google_ad_client')!==false) &&
								(strpos($code,'google_cpa_choice')!==false) && //a referral thing
			 					(strpos($code,'google_ad_slot')===false) //i.e. not using the new slot system
						 );
						
			//as_rimg - Referral (Image)
			//ref_text - Referral (Text)
			//_0ads_al - Link Unit
			//_as - Ad Unit
	}
	
	
	function _var_ad_formats_available(){
			$formats['horizontal']=array('110x32' => '110 x 32',  '120x60' => '120 x 60',  '180x60' => '180 x 60',  '468x60' => '468 x 60');
			$formats['square']=array('125x125' => '125 x 125');
			$formats['vertical']=array('120x240' => '120 x 240');
			return $formats;
	}
	
	function _var_ad_types_available(){
			return array('referral-text' => "Referral (Text)",'referral-image' => "Referral (Image)");	
	}
		
	
	
	function _form_settings_product(){
			$default=array('' => 'Use Default');
			$products=array('referral-text' => "Referral (Text)",'referral-image' => "Referral (Image)");	
			
			adsensem_admin::_field_select('Product', 'product',$products);
		?>
		<tr><td class="adsensem_label"><label for="adsensem-channel">Channel:</label></td><td>
		<input name="adsensem-channel" size="15" title="Enter multiple Channels seperated by + signs" value="<?php echo htmlspecialchars($this->p['channel'], ENT_QUOTES); ?>" /></td></tr>
	<?php
	}
	
	/*Adsense*/
	function _form_settings_ad_format(){
		$formats=$this->_var_ad_formats_available(); //Get permitted formats for the current network
		adsensem_admin::_field_select('<a href="https://www.google.com/adsense/adformats" target="_blank">Format</a>','adformat',$formats);
		adsensem_admin::_field_input('Referral Code (CPA)','alternate-referral',25,'Enter referral code from Google AdSense site.');
	}

	

//Middle
function _var_forms_column2(){ return array('product', 'ad_format'); }
function _var_forms_column3(){ return array('notes','wrap_html_code');}
	
	

}

?>
