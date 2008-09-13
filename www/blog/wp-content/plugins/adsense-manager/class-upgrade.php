<?php

class adsensem_upgrade {
	
	function go(){
		global $_adsensem;
	
		$upgraded=false;
		/* List of possible upgrade paths here: Ensure that versions automagically stack on top of one another
				e.g. v1.x to v3.x should be possilbe v1.x > v2.x > v3.x */
		
		if(adsensem_admin::version_upgrade($_adsensem['version'],"3.0")){adsensem_upgrade::v2_x_to_3_0(); $upgraded=true;}
	
		//Previous version bugfix
		if(!is_numeric($_adsensem['be-nice'])){ $_adsensem['be-nice'] = ADSENSEM_BE_NICE; }
			
		//Write notice, ONLY IF UPGRADE HAS OCCURRED
		if($upgraded){adsensem_admin::add_notice('upgrade adsense-manager','AdSense Manager has detected a previous installation and automatically upgraded your settings','ok');}
		
		$_adsensem['version']=ADSENSEM_VERSION;
	}

	
	
	function v2_x_to_3_0(){
		global $_adsensem;
		
		$old=$_adsensem;
		
				/*  VERSION 3.x  */
				$_adsensem['ads'] = array();
				
				$_adsensem['be-nice'] = $old['benice'];
				$_adsensem['default-ad'] = $old['defaults']['ad'];
				
				$_adsensem['defaults']=array();
				$_adsensem['defaults']['ad_adsense_classic']=adsensem_upgrade::_process_v2_x_to_3_0($old['defaults']);
				$_adsensem['defaults']['ad_adsense']=$_adsensem['defaults']['ad_adsense_classic'];
				
				/* Copy AdSense account-id to both class/new settings */
				$_adsensem['account-ids']['ad_adsense']=$old['adsense-account'];
				
				/* Now all that remains is to convert the ads. In 2.x ads were stored as simply arrays containing the options.
					To upgrade create new objects using product/slot/etc. info, or for code units run an import cycle. */
				
				if(is_array($old['ads'])){
				foreach($old['ads'] as $oname=>$oad){
					
					if($oad['slot']!=''){$type='slot';}
					else {$type=$oad['product'];}
					
					$name=adsensem_admin::validate_name($oname);
					
					switch($type){
						
						/* HTML Code Ads */
						case 'code':
							$ad=adsensem_admin::import_ad($oad['code']);
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
						break;
						
						/* AdSense Slot Ads */
						case 'slot':
							$ad=new Ad_AdSense();
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
							$_adsensem['ads'][$name]->p=adsensem_upgrade::_process_v2_x_to_3_0($oad);
						break;
						/* AdSense Ad */
						case 'ad':
							$ad=new Ad_AdSense_Ad();
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
							$_adsensem['ads'][$name]->p=adsensem_upgrade::_process_v2_x_to_3_0($oad);
						break;
						case 'link':
							$ad=new Ad_AdSense_Link();
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
							$_adsensem['ads'][$name]->p=adsensem_upgrade::_process_v2_x_to_3_0($oad);
						break;
							
						case 'referral':
						case 'referral-image':
						case 'referral-text':
							$ad=new Ad_AdSense_Referral();
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
							$_adsensem['ads'][$name]->p=adsensem_upgrade::_process_v2_x_to_3_0($oad);
						break;	
					}
					
				} 
				}
			
			$_adsensem['ads']=adsensem_admin::sort_ads_by_network($_adsensem['ads']);
		}
		
	
	function _process_v2_x_to_3_0($old){
		$new=$old;
				
		/* Additional conversaion required for rearrangement of colors system */
		$new['color-border']=$old['colors']['border'];								
		$new['color-title']=$old['colors']['link'];								
		$new['color-bg']=$old['colors']['bg'];								
		$new['color-text']=$old['colors']['text'];
		$new['color-url']=$old['colors']['url'];	
		/* End color rearrangement */
		$new['show-page']=$old['show-post'];
		
		/* Adformat codes etc. need to be moved */
		switch($old['product']){
		case 'ad':
			if($old['alternate-url']){ $new['alternate-ad']='url'; } else if($old['alternate-color']) { $new['alternate-ad']='color'; } else { $new['alternate-ad']='benice'; }
		break;
		case 'link':
			$new['adformat']=$old['linkformat'];
			$new['adtype']=$old['linktype'];
		break;
		case 'referral':
		case 'referral-text':
			$new['adformat']=$old['referralformat'];
		break;
		}
		
		list($new['width'],$new['height'],$null)=split('[x]',$new['adformat']);  //Split to fill width/height information
		
		return $new;
	}
	
	
	function adsense_deluxe_to_3_0(){
		global $_adsensem;
		$deluxe=get_option('acmetech_adsensedeluxe');
		
			foreach($deluxe['ads'] as $key => $vals){
				$ad=adsensem_admin::import_ad($vals['code']);
				$name=adsensem_admin::validate_name($vals['name']);
				
				$_adsensem['ads'][$name]=$ad;
				$_adsensem['ads'][$name]->name=$name;
				
				$_adsensem['ads'][$name]->p['show-home']=($deluxe['enabled_for']['home']==1)?'yes':'no';
				$_adsensem['ads'][$name]->p['show-post']=($deluxe['enabled_for']['posts']==1)?'yes':'no';
				$_adsensem['ads'][$name]->p['show-archive']=($deluxe['enabled_for']['archives']==1)?'yes':'no';
				$_adsensem['ads'][$name]->p['show-page']=($deluxe['enabled_for']['page']==1)?'yes':'no';
				
				if($vals['make_default']==1){$_adsensem['default-ad']=$name;}
			}
		
		$_adsensem['ads']=adsensem_admin::sort_ads_by_network($_adsensem['ads']);
	}
	
	
}

?>