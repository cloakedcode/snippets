<?php

// --------------------------------------------------------------
// Strips out all HTML elements, converts $document to plan text
// --------------------------------------------------------------
function html2txt($document)
{
	$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript 
		       '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags 
		       '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly 
		       '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA 
	); 

	return preg_replace($search, '', $document); 
} 

// --------------------------------------------------------------
// Strips out dangerous tags, allowing markup tags
// --------------------------------------------------------------
function stackoverflow_strip_tags($text)
{
	$tags = array('<a>', '<b>', '<blockquote>', '<code>', '<del>', '<dd>', '<dl>', '<dt>', '<em>', '<h1>', '<h2>', '<h3>', '<i>', '<img>', '<kbd>', '<li>', '<ol>', '<p>', '<pre>', '<s>', '<sup>', '<sub>', '<strong>', '<strike>', '<ul>', '<br/>', '<hr/>');

	return strip_javascript_attributes(strip_html_tags($text, $tags));
}

// --------------------------------------------------------------
// Strips out dangerous tags, allowing $tags
// --------------------------------------------------------------
function strip_html_tags($text, $tags = array())
{
	$text = html_entity_decode($text);
	$text = preg_replace(
			array(
				// Remove invisible content
				'@<head[^>]*?>.*?</head>@siu',
				'@<style[^>]*?>.*?</style>@siu',
				'@<script[^>]*?.*?</script>@siu',
				'@<object[^>]*?.*?</object>@siu',
				'@<embed[^>]*?.*?</embed>@siu',
				'@<applet[^>]*?.*?</applet>@siu',
				'@<noframes[^>]*?.*?</noframes>@siu',
				'@<noscript[^>]*?.*?</noscript>@siu',
				'@<noembed[^>]*?.*?</noembed>@siu',
				// Add line breaks before and after blocks
				'@</?((address)|(blockquote)|(center)|(del))@iu',
				'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
				'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
				'@</?((table)|(th)|(td)|(caption))@iu',
				'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
				'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
				'@</?((frameset)|(frame)|(iframe))@iu',
			     ),
		array(
				' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
				"\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
				"\n\$0", "\n\$0",
		     ),
		$text );
	return strip_tags($text, implode('', $tags));
}

// --------------------------------------------------------------
// Strips out javascript attributes (e.g. onclick, onblur)
// --------------------------------------------------------------
function strip_javascript_attributes($text)
{
	$aDisabledAttributes = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmoveout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

	return preg_replace('/<(.*?)>/ie', "'<' . preg_replace(array('/javascript:[^\"\']*/i', '/(" . implode('|', $aDisabledAttributes) . ")[ \\t\\n]*=[ \\t\\n]*[\"\'][^\"\']*[\"\']/i', '/\s+/'), array('', '', ' '), stripslashes('\\1')) . '>'", $text);
}

?>
