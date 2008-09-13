<?php

if(!ADSENSEM_VERSION){die();}


$_adsensem_networks['ad_adpinion'] = array(
		'name'	=>	'Adpinion',
		'shortname' => 'adpinion',
		'www'		=>	'http://www.adpinion.com/',
		'www-create'	=>	'http://www.adpinion.com/',
		'www-signup'		=>	'http://www.adpinion.com/',
																				);

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_Adpinion extends Ad_Generic {
	
	function Ad_Adpinion(){
		$this->Ad_Generic();
	}

				
	function render_ad(){
		
/* 46860"
 */	
		if($this->pd('width')>$this->pd('height')){$xwidth=18;$xheight=17;} else {$xwidth=0;$xheight=35;}
		$code ='';
	 	$code .= '<iframe src="http://www.adpinion.com/app/adpinion_frame?website=' . $this->account_id() . '&amp;width=' . $this->pd('width') . '&amp;height=' . $this->pd('height') . '" ';
		$code .= 'id="adframe" style="width:' . ($this->pd('width')+$xwidth) . 'px;height:' . ($this->pd('height')+$xheight) . 'px;" scrolling="no" frameborder="0">.</iframe>';
	
		return $code;
		
	}

	
		function import_detect_network($code){
			return (	preg_match('/src="http:\/\/www.adpinion.com\/app\//', $code, $matches) !==0);
		}
		
		function import_settings($code){
			//<iframe src="http://www.adpinion.com/app/adpinion_frame?website=133599&amp;width=468&amp;height=60" id="adframe" style="width:486px;height:60px;" scrolling="no" frameborder="0">
					
			if(preg_match("/website=(\w*)/", $code, $matches)!=0){ $_POST['adsensem-account-id'] = $matches[1]; }
			if(preg_match("/width=(\w*)/", $code, $matches)!=0){ $width = $matches[1]; }
			if(preg_match("/height=(\w*)/", $code, $matches)!=0){ $height = $matches[1]; }
			$_POST['adsensem-adformat'] = $width . "x" . $height;
			
			$this->save_settings();
		}


	function render_benice(){
		$this->set_account_id('135667'); //TEMPORARILY override the account id
		return $this->render_ad();
	}
	
	function can_benice(){return true;}
		
		
		
		
		
	function _var_ad_formats_available(){
			$formats['ads']['horizontal']=array('728x90' => '728 x 90 Leaderboard', '468x60' => '468 x 60 Banner');
			$formats['ads']['vertical']=array('120x600' => '120 x 600 Skyscraper', '160x600' => '160 x 600 Wide Skyscraper');
			$formats['ads']['square']=array('300x250' => '300 x 250 Medium Rectangle');
			return $formats;
	}

		
	function _form_settings_help(){
	?><tr><td>
			
			
	</td></tr><?php
	}
		
	
//Middle
function _var_forms_column2(){ return array('ad_format'); }	

		
}

?>
