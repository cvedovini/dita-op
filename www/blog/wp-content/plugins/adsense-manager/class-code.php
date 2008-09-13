<?php

if(!ADSENSEM_VERSION){die();}

$_adsensem_networks['ad_code']  = array(
		'name'	=>	'HTML Code',
		'shortname' => 'co',
		'www'		=>	'',
		'www-signup'		=>	'',
		 );

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class Ad_Code extends Ad_Generic {

	function Ad_Code(){
		$this->Ad_Generic();
	}
				
  function render_ad() {

		global $_adsensem;

		$code=$this->p['code']; 

			return $code;
    }

	function save_settings_network() {
		
		/* Maybe reprocesses the dimensions *import* code here?
		 Possible to extract dimensions from most blocks of code, e.g. width="xxx" ? */
		
		$this->p['code']=stripslashes($_POST['adsensem-code']);
	}
			
	function can_benice(){return false;}
	
	
	function import_settings($code){
	  //Attempt to find html width/height strings
	  if(preg_match('/width="(\w*)"/', $code, $matches)!=0){ $width=$matches[1]; }
	  if(preg_match('/height="(\w*)"/', $code, $matches)!=0){ $height=$matches[1]; }
	  $_POST['adsensem-adformat'] = $width . "x" . $height;
	  $_POST['adsensem-code']=$code;
      
	  $this->save_settings();
	}


	function _form_settings_help(){
	?>
			<p>AdSense Manager supports most Ad networks including <?php adsensem_admin::network_list(array('Ad_AdSenseAd','Ad_AdSenseReferral','Ad_Code')); ?>.</p>
			<p>Any networks not supported directly will be can be managed as HTML Code units. You can re-attempt import of code units at any time using the Import Options.</p>
	<?php
	}

	function _form_settings_html_code(){
		?><tr><td><textarea rows="20" cols="50" name="adsensem-code"><?php echo htmlspecialchars($this->p['code'], ENT_QUOTES); ?></textarea></tr></tr><?php
	}

	function _var_ad_formats_available(){
			$formats['horizontal']=array('728x90' => '728 x 90 Leaderboard', '468x60' => '468 x 60 Banner', '234x60' => '234 x 60 Half Banner');
			$formats['vertical']=array('120x600' => '120 x 600 Skyscraper', '160x600' => '160 x 600 Wide Skyscraper', '120x240' => '120 x 240 Vertical Banner');
			$formats['square']=array('336x280' => '336 x 280 Large Rectangle', '300x250' => '300 x 250 Medium Rectangle', '250x250' => '250 x 250 Square', '200x200' => '200 x 200 Small Square', '180x150' => '180 x 150 Small Rectangle', '125x125' => '125 x 125 Button');
			$formats['custom']=array('custom' => 'Custom');
			return $formats;
	}

//Middle
function _var_forms_column2(){ return array('ad_format','html_code' /*,'import'*/ );}	

}

?>
