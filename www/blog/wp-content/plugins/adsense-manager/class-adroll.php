<?php

if(!ADSENSEM_VERSION){die();}


$_adsensem_networks['ad_adroll'] = array(
		'name'	=>	'AdRoll',
		'shortname' => 'adroll',
		'www'		=>	'http://www.adroll.com/',
		'www-create'	=>	'http://www.adroll.com/home',
		'www-signup'		=>	'http://www.adroll.com/tag/wordpress?r=ZPERWFQF25BGNG5EDWYBUV',
																				);

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_AdRoll extends Ad_Generic {
	
	function Ad_AdRoll(){
		$this->Ad_Generic();
	}

				
	function render_ad(){
		
/* <!-- Start: Ads -->
<script type="text/javascript" src="http://re.adroll.com/a/D44UNLTJPNH5ZDXTTXII7V/IPCY22UCBBFBVL6HIN6X2D/">
</script>
<!-- Start: Your Profile Link -->
<script type="text/javascript" src="http://re.adroll.com/a/D44UNLTJPNH5ZDXTTXII7V/IPCY22UCBBFBVL6HIN6X2D/link">
</script> */	

		//http://re.adroll.com/a/
		//http://c.adroll.com/r/
		
		$code ='';
		$code .= '<!-- Start: Adroll Ads -->';
	 	$code .= '<script type="text/javascript" src="http://c.adroll.com/r/' . $this->account_id() . '/' . $this->pd('slot') . '/">';
		$code .= '</script>';
		$code .= '<!-- Start: Adroll Profile Link -->';
	 	$code .= '<script type="text/javascript" src="http://c.adroll.com/r/' . $this->account_id() . '/' . $this->pd('slot') . '/link">';
		$code .= '</script>';
	
		return $code;
		
	}


		function save_settings_network() {
			
			$this->p['slot']=strip_tags(stripslashes($_POST['adsensem-slot']));
			$this->p['code']=stripslashes($_POST['adsensem-code']);

		}

	
		function import_detect_network($code){
			
			return (	preg_match('/src="http:\/\/(\w*).adroll.com\/(\w*)\//', $code, $matches) !==0);
			
		}
		
		function import_settings($code){
			
/* <!-- Start: Ads -->
<script type="text/javascript" src="http://re.adroll.com/a/D44UNLTJPNH5ZDXTTXII7V/IPCY22UCBBFBVL6HIN6X2D/">
</script>
<!-- Start: Your Profile Link -->
<script type="text/javascript" src="http://re.adroll.com/a/D44UNLTJPNH5ZDXTTXII7V/IPCY22UCBBFBVL6HIN6X2D/link">
</script> */	
		
			if(preg_match("/http:\/\/(\w*).adroll.com\/(\w*)\/(\w*)\/(\w*)/", $code, $matches)!=0){ 
				//ACCOUNT ID / SLOT ID
				$_POST['adsensem-account-id'] = $matches[3]; 
				$_POST['adsensem-slot'] = $matches[4]; 
			}
			
			$this->save_settings();
		}

		
		
		
	function _form_settings_help(){
	?><tr><td><p>Configuration is available through <a href="http://www.adroll.com/" target="_blank">Adroll's site</a>. Specific links to configure
			this ad unit are below:</p>
	<ul>
	<li><a href="http://www.adroll.com/private/publishers/adsensemanagernetwork/adspace/manage/IPCY22UCBBFBVL6HIN6X2D" target="_blank">Manage Ad</a><br />
			Configure ad rotation and display settings.</li>
	<li><a href="http://www.adroll.com/private/publishers/adsensemanagernetwork/adspace/edit/IPCY22UCBBFBVL6HIN6X2D" target="_blank">Edit Ad</a><br />
			Change dimensions, positioning and tags.</li>
	<li><a href="http://www.adroll.com/private/publishers/adsensemanagernetwork/adspace/adcode/IPCY22UCBBFBVL6HIN6X2D" target="_blank">Get Ad Code</a><br />
			Get current ad code for this unit.</li>
	</ul></td></tr><?php
	}
		
	
function _var_forms_unit(){ return array('ad_slot');}
function _var_forms_column2(){ return array('ad_format'); }	
	
	
	
	
		
}

?>
