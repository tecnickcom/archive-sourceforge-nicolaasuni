<?php
//============================================================+
// File name   : cp_functions_aiocpcode.php
// Begin       : 2002-01-09
// Last Update : 2008-04-22
// 
// Description : Translate AIOCP proprietary code into HTML
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Tecnick.com LTD
//               Manor Coach House, Church Hill
//               Aldershot, Hants, GU12 4RQ
//               UK
//               www.tecnick.com
//               info@tecnick.com
//
// License: GNU GENERAL PUBLIC LICENSE v.2
//          http://www.gnu.org/copyleft/gpl.html
//============================================================+

// ------------------------------------------------------------
// Return HTML code from text with AIOCP code Tags
// ------------------------------------------------------------
function F_decode_aiocp_code($text_to_decode) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	global $l, $db;
	
	// Patterns and replacements for URL and email tags..
	$pattern = array();
	$replacement = array();
	$i=0;
	
	//$newtext = htmlentities(stripslashes($text_to_decode), ENT_NOQUOTES, $l['a_meta_charset']); //"disable" all HTML tags
	$newtext = htmlentities($text_to_decode, ENT_NOQUOTES, $l['a_meta_charset']); //"disable" all HTML tags
	
	// [url]http://www.domain.com[/url]
	$pattern[$i] = "#\[url\]([a-z]+?://){1}(.*?)\[/url\]#si";
	$replacement[$i] = '<a class="aiocpcode" href="\1\2" target="_blank">\1\2</a>';
	
	// [url]www.domain.com[/url]
	$pattern[++$i] = "#\[url\](.*?)\[/url\]#si";
	$replacement[++$i] = '<a class="aiocpcode" href="http://\1" target="_blank" rel="nofollow">\1</a>';
	
	// [url=http://www.domain.com]linkname[/url]
	$pattern[++$i] = "#\[url=([a-z]+?://){1}(.*?)\](.*?)\[/url\]#si";
	$replacement[++$i] = '<a class="aiocpcode" href="\1\2" target="_blank" rel="nofollow">\3</a>';
	
	// [url=www.domain.com]linkname[/url]
	// just put a dot before link to obtain a local address
	$pattern[++$i] = "#\[url=(.*?)\](.*?)\[/url\]#si";
	$replacement[++$i] = '<a class="aiocpcode" href="http://\1" target="_blank" rel="nofollow">\2</a>';
	
	// [urll]path_to_local[/urll] (local addresses)
	$pattern[++$i] = "#\[urll\](.*?)\[/urll\]#si";
	$replacement[++$i] = '<a class="aiocpcode" href="\1">\1</a>';
	
	// [urll=path_to_local]linkname[/urll] (local addresses)
	$pattern[++$i] = "#\[urll=(.*?)\](.*?)\[/urll\]#si";
	$replacement[++$i] = '<a class="aiocpcode" href="\1">\2</a>';
	
	// [email]user@domain.com[/email]
	$pattern[++$i] = "#\[email\](.*?)\[/email\]#si";
	$replacement[++$i] = '<a class="aiocpcode" href="mailto:\1">\1</a>';
	
	// [email=user@domain.com]email name[/email]
	$pattern[++$i] = "#\[email=(.*?)\](.*?)\[/email\]#si";
	$replacement[++$i] = '<a class="aiocpcode" href="mailto:\1">\2</a>';
	
	// [img]image_url_here[/img]
	$pattern[++$i] = "#\[img\](.*?)\[/img\]#si";
	$replacement[++$i] = '<img src="\1" border="0" />';
	
	// [code] and [/code] display text as source code
	$pattern[++$i] = "#\[code\](.*?)\[/code\]#si";
	$replacement[++$i] = '<p class="aiocpcode">\1</p>';
	
	// [quote] and [/quote] for quote a text
	$pattern[++$i] = "#\[quote\](.*?)\[/quote\]#si";
	$replacement[++$i] = '<blockquote class="aiocpcode"><b>'.$l['w_quote'].':</b><br />\1</blockquote></p>';
	
	// [small] and [/small] for small text
	$pattern[++$i] = "#\[small\](.*?)\[/small\]#si";
	$replacement[++$i] = '<small class="aiocpcode">\1</small>';
	
	// [b] and [/b] for bolding text.
	$pattern[++$i] = "#\[b\](.*?)\[/b\]#si";
	$replacement[++$i] = '<b class="aiocpcode">\1</b>';
	
	// [i] and [/i] for italicizing text.
	$pattern[++$i] = "#\[i\](.*?)\[/i\]#si";
	$replacement[++$i] = '<i class="aiocpcode">\1</i>';
	
	// [sub] and [/sub] for subscript text.
	$pattern[++$i] = "#\[sub\](.*?)\[/sub\]#si";
	$replacement[++$i] = '<sub class="aiocpcode">\1</sub>';
	
	// [sup] and [/sup] for superscript text.
	$pattern[++$i] = "#\[sup\](.*?)\[/sup\]#si";
	$replacement[++$i] = '<sup class="aiocpcode">\1</sup>';
	
	// [ulist] and [/ulist] unordered list.
	$pattern[++$i] = "#\[ulist\](.*?)\[/ulist\]#si";
	$replacement[++$i] = '<ul class="aiocpcode">\1</ul>';
	
	// [olist] and [/olist] ordered list.
	$pattern[++$i] = "#\[olist\](.*?)\[/olist\]#si";
	$replacement[++$i] = '<ol class="aiocpcode">\1</ol>';
	
	// [olist=a] and [/olist] ordered list with parameter.
	$pattern[++$i] = "#\[olist=([a1])\](.*?)\[/olist\]#si";
	$replacement[++$i] = '<ol class="aiocpcode" type=\1>\2</ol>';
	
	// [li] list items [/li]
	$pattern[++$i] = "#\[li\](.*?)\[/li\]#si";
	$replacement[++$i] = '<li class="aiocpcode">\1</li>';
	
	// fix some spaces
	$pattern[++$i] = "#\[smile=(.*?)[\s]+/\]#si";
	$replacement[++$i] = '[smile=\1/]';
	
	$pattern[++$i] = "#\[avatar=(.*?)[\s]+/\]#si";
	$replacement[++$i] = '[avatar=\1/]';
	
	$pattern[++$i] = "#\[flag=(.*?)[\s]+/\]#si";
	$replacement[++$i] = '[flag=\1/]';
	
	$newtext = preg_replace($pattern, $replacement, $newtext);
	
	// line breaks
	$newtext = ereg_replace("(\r\n|\n|\r)", "<br />", $newtext);
	$newtext = str_replace("<br /><li", "<li", $newtext);
	
	//search and replace each smile code with image equivalent
	$sql = "SELECT * FROM ".K_TABLE_EMOTICONS."";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$newtext = str_replace("[smile=".$m['smile_id']."/]", "<img src=\"".K_PATH_IMAGES_EMOTICONS.$m['smile_link']."\" width=\"".$m['smile_width']."\" height=\"".$m['smile_height']."\" border=\"0\" alt=\"".htmlentities($m['smile_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" />",$newtext);
		}
	}
	else {
		F_display_db_error();
	}
	
	/*
	//search and replace each avatar code with image equivalent
	$sql = "SELECT * FROM ".K_TABLE_AVATARS."";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$newtext = str_replace("[avatar=".$m['avatar_id']."/]", "<img src=\"".K_PATH_IMAGES_AVATARS.$m['avatar_link']."\" width=\"".$m['avatar_width']."\" height=\"".$m['avatar_height']."\" border=\"0\" alt=\"".htmlentities($m['avatar_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" />",$newtext);
		}
	}
	else {
		F_display_db_error();
	}
	*/
	
	//search and replace each flag code with image equivalent
	$sql = "SELECT * FROM ".K_TABLE_COUNTRIES."";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$newtext = str_replace("[flag=".$m['country_id']."/]", "<img src=\"".K_PATH_IMAGES_FLAGS.$m['country_flag']."\" width=\"".$m['country_width']."\" height=\"".$m['country_height']."\" border=\"0\" alt=\"".htmlentities($m['country_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" />",$newtext);
		}
	}
	else {
		F_display_db_error();
	}
	
	// bad words censor (based on bad words table)
	if(K_BAD_WORD_CENSOR) {
		$sql = "SELECT * FROM ".K_TABLE_BADWORDS."";
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				$newword = substr_replace($m['badword_name'], str_repeat("*", strlen($m['badword_name'])-2), 1, strlen($m['badword_name'])-2);
				$newtext = str_replace(htmlentities($m['badword_name'], ENT_NOQUOTES, $l['a_meta_charset']), htmlentities($newword, ENT_NOQUOTES, $l['a_meta_charset']), $newtext);
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	return ($newtext);
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
