/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('adsense_deluxe', '');

function TinyMCE_adsense_deluxe_initInstance(inst) {
	tinyMCE.importCSS(inst.getDoc(), tinyMCE.baseURL + "/plugins/adsense_deluxe/adsense_deluxe.css");
}

/**
 * Information about the plugin.
 */
function TinyMCE_g2image_getInfo() {
	return {
		longname  : 'Adsense Deluxe plugin',
		author    : 'Wayne K Walrath',
		authorurl : 'http://www.acmetech.com/blog/',
		infourl   : 'http://www.acmetech.com/blog/',
		version   : "0.7"
	};
};

function TinyMCE_adsense_deluxe_getControlHTML(control_name) {
//class="mce_ads_dlx_SelectList"
	switch (control_name) {
		case "adsense_deluxe":
			var html = '<select id="{$editor_id}_adsense_select" name="{$editor_id}_adsense_select" onchange="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mce_adsense_select\',false,this.options[this.selectedIndex].value);this.selectedIndex=0;" class="mceSelectList" style="font-size:8pt;">';
			html += '<option value="">--AdSense--</option>';

			// Build format select
			html += '<option value="adsense">' + "adsense" + '</option>';
			if( __ADSENSE_DELUXE_ADS != null ){
				for(var i=0; i<__ADSENSE_DELUXE_ADS.length; i++){
					html += '<option value="adsense#' + __ADSENSE_DELUXE_ADS[i] + '">' + "&nbsp;&nbsp;&nbsp;adsense#" + __ADSENSE_DELUXE_ADS[i]  + '</option>';
				}

			}

			html += '</select>';
			//ad_select

			return html;

			var title_adsense = tinyMCE.getLang('lang_adsense_deluxe_button');
			var buttons = '<a href="javascript:tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mce_ads_deluxe\')" target="_self" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mce_ads_deluxe\');return false;"><img id="{$editor_id}_adsdlx" src="{$pluginurl}/images/adsense-deluxe.gif" title="'+title_adsense+'" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>';

		return buttons;
	}

	return '';
}

function TinyMCE_adsense_deluxe_parseAttributes(attribute_string) {
	var attributeName = "";
	var attributeValue = "";
	var withInName;
	var withInValue;
	var attributes = new Array();
	var whiteSpaceRegExp = new RegExp('^[ \n\r\t]+', 'g');
	var titleText = tinyMCE.getLang('lang_wordpress_more');
	var titleTextPage = tinyMCE.getLang('lang_wordpress_page');

	if (attribute_string == null || attribute_string.length < 2)
		return null;

	withInName = withInValue = false;

	for (var i=0; i<attribute_string.length; i++) {
		var chr = attribute_string.charAt(i);

		if ((chr == '"' || chr == "'") && !withInValue)
			withInValue = true;
		else if ((chr == '"' || chr == "'") && withInValue) {
			withInValue = false;

			var pos = attributeName.lastIndexOf(' ');
			if (pos != -1)
				attributeName = attributeName.substring(pos+1);

			attributes[attributeName.toLowerCase()] = attributeValue.substring(1).toLowerCase();

			attributeName = "";
			attributeValue = "";
		} else if (!whiteSpaceRegExp.test(chr) && !withInName && !withInValue)
			withInName = true;

		if (chr == '=' && withInName)
			withInName = false;

		if (withInName)
			attributeName += chr;

		if (withInValue)
			attributeValue += chr;
	}

	return attributes;
}

function TinyMCE_adsense_deluxe_execCommand(editor_id, element, command, user_interface, value) {
	var inst = tinyMCE.getInstanceById(editor_id);
	var focusElm = inst.getFocusElement();
	var doc = inst.getDoc();

	function getAttrib(elm, name) {
		return elm.getAttribute(name) ? elm.getAttribute(name) : "";
	}

	// Handle commands
	switch (command) {
			case "mce_adsense_select":
				//var rc = alert("Value = " + value);
				//element.selectedIndex = 0; // reset menu
				//return true;

			case "mce_ads_deluxe":
				var flag = "";
				var template = new Array();
				var altMore = tinyMCE.getLang('lang_adsense_deluxe_alt');

				// Is selection a image
				if (focusElm != null && focusElm.nodeName.toLowerCase() == "img") {
					flag = getAttrib(focusElm, 'class');
	
					if (flag != 'mce_plugin_adsense_deluxe') // Not a wordpress
						return true;
	
					action = "update";
				}
	
				html = TinyMCE_adsense_deluxe_make_imgtag(value);
				tinyMCE.execCommand("mceInsertContent",true,html);
				tinyMCE.selectedInstance.repaint();
				return true;

	}

	// Pass to next handler in chain
	return false;
}

function TinyMCE_adsense_deluxe_make_imgtag(ad_name){
	var html = ''
			+ '<img src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" '
			+ ' width="80" height="16" '
			+ 'alt="'+ad_name+'" title="'+ ad_name +'" class="mce_plugin_adsense_deluxe" name="mce_plugin_adsense_deluxe" />';
	return html;
}
function TinyMCE_adsense_deluxe_cleanup(type, content) {
//	return content;
	
	
	switch (type) {
	
		case "insert_to_editor":
			//--
			//-- This doesn't handle the situation where you have AdsDlx tags
			//-- with ad names which were deleted from AdsDlx options after
			//-- the post was originally created. E.g., you inserted <!--adsense#foo-->
			//-- published that post, then later you're editing the post but the "foo"
			//-- ads no longer existin in the plugin options.
			//-- [suggested "ToDo" for that: 
			var startPos = 0;
			var altMore = tinyMCE.getLang('lang_adsense_deluxe_alt');
			var ad_names = new Array('adsense');
			if( __ADSENSE_DELUXE_ADS != null ){
				for(var i=0; i<__ADSENSE_DELUXE_ADS.length; i++){
					ad_names.push('adsense#' + __ADSENSE_DELUXE_ADS[i]);
				}
			}

			// Parse all <!--adsense--> tags and replace them with images
			for(var i=0; i<ad_names.length; i++){
				startPos = 0;
				//alert('<!--'+ad_names[i]+'-->');
				while ((startPos = content.indexOf('<!--'+ad_names[i]+'-->', startPos)) != -1) {
					// Insert image
					var contentAfter = content.substring(startPos + ad_names[i].length+7);
					content = content.substring(0, startPos);
					content += TinyMCE_adsense_deluxe_make_imgtag(ad_names[i]);
					content += contentAfter;
	
					startPos++;
				}
				// go to next ad placeholder
			}

			// If any blocks weren't replaced, do it with this statement 
			content = content.replace(new RegExp('<!--adsense#([^-]+)-->', 'g'), '<strong style="color:red">[Undefined Adsense Block ($1)]</strong>');

			break;

		case "get_from_editor":
			// Parse all img tags and replace them with <!--adsense-->
			var startPos = -1;
			while ((startPos = content.indexOf('<img', startPos+1)) != -1) {
				var endPos = content.indexOf('/>', startPos);
				var attribs = TinyMCE_adsense_deluxe_parseAttributes(content.substring(startPos + 4, endPos));

				if (attribs['class'] == "mce_plugin_adsense_deluxe") {
					endPos += 2;
					
					var embedHTML = '<!--adsense-->';
					if( attribs['title'] != null && attribs['title'] != '' )
						embedHTML = '<!--' + attribs['title'] + '-->';

					// Insert embed/object chunk
					chunkBefore = content.substring(0, startPos);
					chunkAfter = content.substring(endPos);
					content = chunkBefore + embedHTML + chunkAfter;
				}
			}

			// If it says & in the WYSIWYG editor, it should say &amp; in the html.
//			content = content.replace(new RegExp('&', 'g'), '&amp;');
//			content = content.replace(new RegExp('&amp;nbsp;', 'g'), '&nbsp;');

			
			break;
	}

	// Pass through to next handler in chain
	return content;
}

function TinyMCE_adsense_deluxe_handleNodeChange(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {

	return false;
	function getAttrib(elm, name) {
		return elm.getAttribute(name) ? elm.getAttribute(name) : "";
	}

	tinyMCE.switchClassSticky(editor_id + '_wordpress_more', 'mceButtonNormal');

	if (node == null)
		return;

	do {
		if (node.nodeName.toLowerCase() == "img" && getAttrib(node, 'class').indexOf('mce_plugin_wordpress_more') == 0)
			tinyMCE.switchClassSticky(editor_id + '_wordpress_more', 'mceButtonSelected');
		if (node.nodeName.toLowerCase() == "img" && getAttrib(node, 'class').indexOf('mce_plugin_wordpress_page') == 0)
			tinyMCE.switchClassSticky(editor_id + '_wordpress_page', 'mceButtonSelected');
	} while ((node = node.parentNode));

	return true;
}
