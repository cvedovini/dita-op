

	function adsensem_form_update(element){
		//element is the calling element, element.id has the identifier
		//detect calling form element and action accordingly
		switch(element.id){
			/* case 'adsensem-product':adsensem_update_product(); break; */
			case 'adsensem-adformat':adsensem_update_custom(); break;
			case 'adsensem-adtype':adsensem_update_formats(); break;
	}
	}
		
		
	function adsensem_update_color(element,id,what){
			target=document.getElementById(id);
			switch(what) {
				case 'bg':	target.style.background='#' + element.value; break;
				case 'title':	target.style.color='#' + element.value; break;
				case 'text':	target.style.color='#' + element.value; break;
				case 'border':	target.style.border='1px solid #' + element.value; break;
				case 'link':	target.style.color='#' + element.value; break;
			}
		}
		
	function adsensem_update_formats(){
		
		list=new Array('adformat','linkformat','referralformat');
		
		if(document.getElementById('adsensem-adtype')){
			switch(document.getElementById('adsensem-adtype').value){
				case 'ad': keep='adformat'; break;
				case 'link': keep='linkformat'; break; 
				case 'ref_image': keep='referralformat'; break;
				case 'ref_text': keep=false; break;
			}

			for(a=0;a<list.length;a++){
				if(list[a]!=keep){	document.getElementById('adsensem-form-'+list[a]).style.display='none'; }
				else {	document.getElementById('adsensem-form-'+list[a]).style.display=''; }
			}
			
		}
	}

		
	function adsensem_update_custom(){
	
		if(document.getElementById('adsensem-adformat') && document.getElementById('adsensem-settings-custom')){
			format=document.getElementById('adsensem-adformat').value;
		
			if(format=='custom'){on='';} else {on='none';}
			document.getElementById('adsensem-settings-custom').style.display=on
		}
	}
	
	
//Initialize everything (call the display/hide functions)
	
	
addLoadEvent(function(){ adsensem_update_custom()});
addLoadEvent(function(){ adsensem_update_formats()});

addLoadEvent(function(){ add_postbox_toggles('adsensem')});


//End Initialise
