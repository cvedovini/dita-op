<?php

if(!ADSENSEM_VERSION){die();}

$_adsensem_networks['ad_crispads']	= array(
		'name' => 'CrispAds',
		'shortname' => 'crisp',
		'www' => 'http://www.crispads.com/',
		'www-create' => 'http://www.crispads.com/spinner/www/admin/zone-edit.php',
		'www-signup' => 'http://www.crispads.com/'
		);

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_CrispAds extends Ad_Generic {
	
	function Ad_CrispAds(){
		$this->Ad_Generic();
	}

				
	function render_ad(){
		
		if ($this->pd('codemethod')=='javascript'){
			$code='<script type="text/javascript"><!--//<![CDATA[' . "\n";
			$code.="var m3_u = (location.protocol=='https:'?'https://www.crispads.com/spinner/www/delivery/ajs.php':'http://www.crispads.com/spinner/www/delivery/ajs.php');\n";
			$code.="var m3_r = Math.floor(Math.random()*99999999999);\n";
   		$code.="if (!document.MAX_used) document.MAX_used = ',';\n";
   		$code.="document.write (\"<scr\"+\"ipt type='text/javascript' src='\"+m3_u);\n";
   		$code.='document.write ("?zoneid=' . $this->pd('slot') . '");' . "\n";
   		$code.="document.write ('&amp;cb=' + m3_r);\n";
			$code.="if (document.MAX_used != ',') document.write (\"&amp;exclude=\" + document.MAX_used);\n";
   		$code.='document.write ("&amp;loc=" + escape(window.location));' . "\n";
			$code.='if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));' . "\n";
   		$code.='if (document.context) document.write ("&context=" + escape(document.context));' . "\n";
   		$code.='if (document.mmm_fo) document.write ("&amp;mmm_fo=1");' . "\n";
   		$code.='document.write ("\'><\/scr"+"ipt>");' . "\n";
			$code.='//]]>--></script><noscript><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=' . $this->pd('identifier') . '&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=' . $this->pd('slot') . '&amp;n=' . $this->pd('identifier') . '" border="0" alt="" /></a></noscript>';
		} else { //Iframe
		$code='<iframe id="' . $this->pd('identifier') . '" name="' . $this->pd('identifier') . '" src="http://www.crispads.com/spinner/www/delivery/afr.php?n=' . $this->pd('identifier') . '&amp;zoneid=' . $this->pd('slot') . '" framespacing="0" frameborder="no" scrolling="no" width="' . $this->pd('width') . '" height="' . $this->pd('height') . '"><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=' . $this->pd('identifier') . '&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=' . $this->pd('slot') . '&amp;n=' . $this->pd('identifier') . '" border="0" alt="" /></a></iframe>';
		$code.='<script type="text/javascript" src="http://www.crispads.com/spinner/www/delivery/ag.php"></script>';
		}
		
		return $code;
	}
	
		function save_settings_network() {
			
			$this->p['slot']=strip_tags(stripslashes($_POST['adsensem-slot']));
			$this->p['identifier']=strip_tags(stripslashes($_POST['adsensem-identifier']));
			$this->p['codemethod']=strip_tags(stripslashes($_POST['adsensem-codemethod'])); //Javascript or IFRAME
		}

	
		function import_detect_network($code){

			return (	preg_match('/http:\/\/www.crispads.com\/spinner\//', $code, $matches) !==0);
			
		}
		
		function import_settings($code){
			
			if(preg_match("/zoneid=(\w*)/", $code, $matches)!=0){$_POST['adsensem-slot'] = $matches[1]; }
			if(preg_match("/n=(\w*)/", $code, $matches)!=0){$_POST['adsensem-identifier'] = $matches[1]; }
			
			if(preg_match("/iframe/", $code, $matches)!=0){$_POST['adsensem-codemethod'] = 'iframe'; } else {$_POST['adsensem-codemethod'] = 'javascript';}
			
			//Only available on IFRAME ads
			if(preg_match('/width="(\w*)"/', $code, $matches)!=0){
				$width=$matches[1]; 
				if(preg_match('/height="(\w*)"/', $code, $matches)!=0){
					$height=$matches[1]; 
					$_POST['adsensem-adformat'] = $width . "x" . $height; //Only set if both width and height present
				}
			}
			
			
			$this->save_settings();
		}

		
	function _form_settings_help(){
	?><tr><td>Configuration is available through the <a href="http://www.crispads.com/" target="_blank">CrispAds site</a>.<br /> 
	Ad unit specific links below:
	<ul>
	<li><a href="http://www.crispads.com/spinner/www/admin/zone-edit.php?zoneid=<?php echo $this->p['slot']; ?>" target="_blank">Edit this ad unit</a><br />
			Change colours, dimensions and keywords.</li>
	</ul>
	</td></tr><?php
	}
				
		
		
		

		
		
	function can_benice(){return false;}
		
	function _form_settings_ad_slot(){
		$this->_form_settings_ad_unit();
		adsensem_admin::_field_input('Slot ID','slot',15,'Enter the network\'s ID for this slot.');
		adsensem_admin::_field_input('Identifier','slot',15,'Random identifier for this unit.');
		?><input name="adsensem-name-old" type="hidden" value="<?php echo htmlspecialchars($this->name, ENT_QUOTES); ?>" /><?php
	}
	
	function _form_settings_code_method(){
			$codemethods=array('javascript' => "Javascript",'iframe' => "IFRAME");
			adsensem_admin::_field_select('Output Code', 'codemethod',$codemethods);
	}

	
//Middle
function _var_forms_unit(){ return array('ad_slot');}
function _var_forms_column2(){ return array('ad_format','code_method'); }	
			
		
		
}

?>
