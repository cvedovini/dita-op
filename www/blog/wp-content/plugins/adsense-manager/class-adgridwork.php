<?php

if(!ADSENSEM_VERSION){die();}

$_adsensem_networks['ad_adgridwork'] = array(
		'name'	=>	'AdGridWork',
		'shortname' => 'adgrid',
		'www'		=>	'http://www.adgridwork.com/',
		'www-create' => 'http://www.adgridwork.com/u.php?page=submitsite',
		'www-signup'	=>	'http://www.adgridwork.com/?r=18501',														 
		 );

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_AdGridWork extends Ad_Generic {
	
	function Ad_AdGridWork(){
		$this->Ad_Generic();
	}
		
	function render_ad(){
		
		$code ='<a href="http://www.adgridwork.com/?r=' . $this->account_id() . '" style="color: #' . $this->pd('color-link') .  '; font-size: 14px" target="_blank">Free Advertising</a>';
		$code.='<script type="text/javascript">' . "\n";
		$code.="var sid = '"  . $this->pd('slot') . "';\n";
		$code.="var title_color = '" . $this->pd('color-title') . "';\n";
		$code.="var description_color = '" . $this->pd('color-text') . "';\n";
		$code.="var link_color = '" . $this->pd('color-url') . "';\n";
		$code.="var background_color = '" . $this->pd('color-bg') . "';\n";
		$code.="var border_color = '" . $this->pd('color-border') . "';\n";
		$code.='</script><script type="text/javascript" src="http://www.mediagridwork.com/mx.js"></script>';
		
		return $code;
	}
	
/*	BENICE WILL REQUIRED MULTIPLE ALTERNATIVES AND OUTPUT DIFFERENT SID DEPENDING ON DIMENSIONS
	function render_benice(){
		$this->p['slot']==''; //TEMPORARILY override the slot id
		return $this->render_ad();
	}
	
	function can_benice(){return true;}
	*/	

		function save_settings_network() {
			
			$this->p['slot']=strip_tags(stripslashes($_POST['adsensem-slot']));
			$this->p['code']=stripslashes($_POST['adsensem-code']);
			
			$this->p['color-border']=strip_tags(stripslashes($_POST['adsensem-color-border']));
			$this->p['color-title']=strip_tags(stripslashes($_POST['adsensem-color-title']));
			$this->p['color-bg']=strip_tags(stripslashes($_POST['adsensem-color-bg']));
			$this->p['color-text']=strip_tags(stripslashes($_POST['adsensem-color-text']));
			$this->p['color-url']=strip_tags(stripslashes($_POST['adsensem-color-url']));			

		}
		
		
	function reset_defaults_network() {
		global $_adsensem;
		$_adsensem['defaults'][$this->network()]+= array (
				'color-border'=> '646360',
				'color-title'	=> '000000',
				'color-bg' 	=> 'FFFFFF',
				'color-text'	=> '646360',
				'color-url'	=> '7FBE00',
								);
	}

		function import_detect_network($code){
			
			return (	(strpos($code,'www.adgridwork.com')!==false) ||
			 					(strpos($code,'www.mediagridwork.com/mx.js')!==false)
						 );

		}
		
		function import_settings($code){
			
			if(preg_match('/var sid = \'(\w*)\'/', $code, $matches)!=0){ $_POST['adsensem-slot'] = $matches[1]; }
			
			if(preg_match("/var title_color = '(\w*)'/", $code, $matches)!=0){ $_POST['adsensem-color-title'] = $matches[1]; }
			if(preg_match("/var description_color = '(\w*)'/", $code, $matches)!=0){ $_POST['adsensem-color-text'] = $matches[1]; }
			if(preg_match("/var link_color = '(\w*)'/", $code, $matches)!=0){ $_POST['adsensem-color-url'] = $matches[1]; }
			if(preg_match("/var background_color = '(\w*)'/", $code, $matches)!=0){ $_POST['adsensem-color-bg'] = $matches[1]; }
			if(preg_match("/var border_color = '(\w*)'/", $code, $matches)!=0){ $_POST['adsensem-color-border'] = $matches[1]; }
			
			$this->save_settings();
		}

		function _var_ad_formats_available(){
			$formats['horizontal']=array('800x90' => '800 x 90 Large Leaderboard', '728x90' => '728 x 90 Leaderboard', '600x90' => '600 x 90 Small Leaderboard ', '468x60' => '468 x 60 Banner', '400x90' => '400 x 90 Tall Banner', '234x60' => '234 x 60 Half Banner', '200x90' => '200 x 90 Tall Half Banner');
			$formats['vertical']=array('120x600' => '120 x 600 Skyscraper', '160x600' => '160 x 600 Wide Skyscraper', '200x360' => '200 x 360 Wide Half Banner', '200x270' => '200 x 270 Wide Short Banner');
			$formats['square']=array('336x280' => '336 x 280 Large Rectangle', '300x250' => '300 x 250 Medium Rectangle', '250x250' => '250 x 250 Square', '200x200' => '200 x 180 Small Rectangle', '180x150' => '180 x 150 Small Rectangle');
			return $formats;
	}
		
	function _form_settings_help(){
	?><tr><td><p>Further configuration and control over channel and slot setup can be achieved through <a href="http://www.adgridwork.com/u.php" target="_blank">AdGridWorks's online system</a>:</p>
	<ul>
	<li><a href="http://www.adgridwork.com/u.php?page=metrics&sid=<?php echo $this->p['slot']; ?>" target="_blank">Campaign Metrics</a><br />
			View hits, clicks, and other stats information.</li>
	<li><a href="http://www.adgridwork.com/u.php?page=submitsite&sid=<?php echo $this->p['slot']; ?>" target="_blank">Edit Campaign</a><br />
			Change keywords, ad format and layout.</li>
	</ul></td></tr>
	<?php	
	}
	
	
	function _form_settings_network(){
	?><td><td><p>No network settings.</p></td></tr>
	<?php
	}
	
	
//Middle
function _var_forms_column2(){ return array('ad_format','colors'); }	
	
	
}

?>
