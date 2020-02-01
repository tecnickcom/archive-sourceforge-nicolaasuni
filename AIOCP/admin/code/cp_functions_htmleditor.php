<?php
//============================================================+
// File name   : cp_functions_htmleditor.php                   
// Begin       : 2001-10-26                                    
// Last Update : 2012-11-27                                  
//                                                             
// Description : HTML Editor                                   
//                                                             
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Tecnick.com s.r.l.
//               Via Della Pace n. 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//
// License: GNU GENERAL PUBLIC LICENSE v.2
//          http://www.gnu.org/copyleft/gpl.html
//============================================================+

// ------------------------------------------------------------
// Display HTML EDITOR
// $callingform = name of the calling form
// $callingfield = name of the calling form field (where text code will be sent)
// $templates = set of templates to use on current session
// $doc_charset = charset to use for XHTML validation
// ------------------------------------------------------------
function F_html_editor($callingform, $callingfield, $templates, $doc_charset) {
	global $l, $db;
	global $indent;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_xhtml_validator.'.CP_EXT);
?>

<!-- ====================================================== -->
<script language="JavaScript" src="<?php echo K_PATH_SHARED_JSCRIPTS; ?>inserttag.js" type="text/javascript"></script>

<script language="JavaScript" type="text/javascript">
//<![CDATA[

//Open FCKeditor
function F_open_fckeditor() {
	<?php echo "xhtmleditor=window.open('cp_edit_html_fckeditor.".CP_EXT."?templates=".$templates."&callingform=form_htmleditor&callingfield=htmltext&charset=".$doc_charset."','xhtmleditor','dependent,height=500,width=660,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');";?>
}
//]]>
</script>
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_htmleditor" id="form_htmleditor">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />

<table class="edge" border="0" cellspacing="1" cellpadding="2" >

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fill" align="left" valign="top">
<!-- TEXTAREA ==================== -->
<td class="fill" align="left" valign="top">
<?php echo $l['w_xhtml_code']; ?><br />

<?php
if (isset($_REQUEST['htmltext'])) {
	$tmp_text = F_validate_xhtml(stripslashes($_REQUEST['htmltext']), $doc_charset, $indent);
	$tmp_text = htmlentities($tmp_text, ENT_NOQUOTES, $doc_charset);
}
else {
	$tmp_text = "";
}
?>

<textarea cols="60" rows="20" name="htmltext" id="htmltext" onselect="FJ_store_caret(this);" onclick="FJ_store_caret(this);" onkeyup="FJ_store_caret(this);"><?php echo $tmp_text; ?></textarea><br />
<?php

// FCKeditor button
F_generic_button("ckeditor","CKeditor","F_open_fckeditor()");

// Image Wizard
F_generic_button("imageeditor1","IMG","imageWindow=window.open('cp_edit_html_image.".CP_EXT."?templates=".$templates."&amp;callingform=form_htmleditor&amp;callingfield=tagoptions','imageWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");

// Image Upload Wizard
F_generic_button("imageeditor2","IMG UP","imageUploadWindow=window.open('cp_upload_image.".CP_EXT."?templates=".$templates."','imageUploadWindow','dependent,height=110,width=350,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");

F_generic_button("autobrbutton","auto BR","FJ_auto_br(document.form_htmleditor.htmltext)");
F_generic_button("compactbutton",$l['w_compact_v'],"FJ_remove_indentation(document.form_htmleditor.htmltext)");

// XHTML validator
F_submit_button("form_htmleditor","menu_mode",$l['w_validate_xhtml']); 

echo "<input type=\"checkbox\" name=\"indent\" id=\"indent\" value=\"1\"";
if ($indent) { echo "checked";}
echo" />".$l['w_indent'];
?> 

</td>

<!-- TAGS LIST ==================== -->
<td class="fill" align="left" valign="top">
<?php echo $l['w_tags']; ?><br />

<?php 
if(($templates=="newsletter") OR ($templates=="all")) { ?>
<!-- NEWSLETTER ==================== -->
<select name="tags_newsletter" id="tags_newsletter" size="0">
	<option value="">- <?php echo $l['w_newsletter']; ?> -</option>
	<option value="#CATEGORYNAME#"><?php echo $l['w_newsletter_name']; ?></option>
	<option value="#CATEGORYDESCRIPTION#"><?php echo $l['w_newsletter_description']; ?></option>
	<option value="#USERNAME#"><?php echo $l['w_username']; ?></option>
	<option value="#USERFIRSTNAME#"><?php echo $l['w_firstname']; ?></option>
	<option value="#USERLASTNAME#"><?php echo $l['w_lastname']; ?></option>
	<option value="#EMAIL#"><?php echo $l['w_user_email']; ?></option>
	<option value="#USERIP#"><?php echo $l['w_userip']; ?></option>
	<option value="#UNSUBSCRIBEURL#"><?php echo $l['w_unsubscribe_url']; ?></option>
	<option value="#SUBSCRIBEURL#"><?php echo $l['w_subscribe_url']; ?></option>
	<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
</select>
<?php F_generic_button("addnewslettertag",$l['w_add'],"FJ_insert_text(document.form_htmleditor.htmltext,document.form_htmleditor.tags_newsletter.options[document.form_htmleditor.tags_newsletter.selectedIndex].value)"); ?>
<br />
<?php 
} 

if(($templates=="forum") OR ($templates=="all")) { ?>
<!-- NEWSLETTER ==================== -->
<select name="tags_forum" id="tags_forum" size="0">
	<option value="">- <?php echo $l['w_forum']; ?> -</option>
	<option value="#CATEGORY#"><?php echo $l['w_category']; ?></option>
	<option value="#FORUM#"><?php echo $l['w_forum']; ?></option>
	<option value="#TOPIC#"><?php echo $l['w_topic']; ?></option>
	<option value="#USERNAME#"><?php echo $l['w_username']; ?></option>
	<option value="#EMAIL#"><?php echo $l['w_user_email']; ?></option>
	<option value="#USERFIRSTNAME#"><?php echo $l['w_firstname']; ?></option>
	<option value="#USERLASTNAME#"><?php echo $l['w_lastname']; ?></option>
	<option value="#USERIP#"><?php echo $l['w_userip']; ?></option>
	<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
</select>
<?php F_generic_button("addforumtag",$l['w_add'],"FJ_insert_text(document.form_htmleditor.htmltext,document.form_htmleditor.tags_forum.options[document.form_htmleditor.tags_forum.selectedIndex].value)"); ?>
<br />
<?php 
} 

if(($templates=="user") OR ($templates=="all")) { ?>
<!-- USER ==================== -->
<select name="tags_user" id="tags_user" size="0">
	<option value="">- <?php echo $l['w_user']; ?> -</option>
	<option value="#USERNAME#"><?php echo $l['w_username']; ?></option>
	<option value="#PASSWORD#"><?php echo $l['w_password']; ?></option>
	<option value="#EMAIL#"><?php echo $l['w_user_email']; ?></option>
	<option value="#USERFIRSTNAME#"><?php echo $l['w_firstname']; ?></option>
	<option value="#USERLASTNAME#"><?php echo $l['w_lastname']; ?></option>
	<option value="#USERIP#"><?php echo $l['w_userip']; ?></option>
	<option value="#SUBSCRIBEURL#"><?php echo $l['w_subscribe_url']; ?></option>
	<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
</select>
<?php F_generic_button("addusertag",$l['w_add'],"FJ_insert_text(document.form_htmleditor.htmltext,document.form_htmleditor.tags_user.options[document.form_htmleditor.tags_user.selectedIndex].value)"); ?>
<br />
<?php
} 

if(($templates=="page") OR  
	($templates=="user") OR 
	($templates=="forum") OR 
	($templates=="all")) {
?>
<!-- PAGE TEMPLATES ==================== -->
<select name="tags_page" id="tags_page" size="0">
	<option value="">- <?php echo $l['w_page']; ?> -</option>
	<option value="#LEVEL#"><?php echo $l['w_level']; ?></option>	
	<option value="#LANGUAGE#"><?php echo $l['w_language']; ?></option>
	<option value="#LANGDIR#"><?php echo $l['w_language_dir']; ?></option>
	<option value="#CHARSET#"><?php echo $l['w_charset']; ?></option>
	<option value="#AUTHOR#"><?php echo $l['w_author']; ?></option>
	<option value="#REPLYTO#"><?php echo $l['w_replyto']; ?></option>
	<option value="#STYLE#"><?php echo $l['w_style']; ?></option>
	<option value="#TITLE#"><?php echo $l['w_title']; ?></option>
	<option value="#DESCRIPTION#"><?php echo $l['w_description']; ?></option>
	<option value="#KEYWORDS#"><?php echo $l['w_keywords']; ?></option>	
<?php
	$sql = "SELECT * FROM ".K_TABLE_PAGE_MODULES." ORDER BY pagemod_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$template_symbol = "#".strtoupper($m['pagemod_name'])."";
			if($m['pagemod_params']) {
				$template_symbol .= "=";
				for ($i=1; $i<=$m['pagemod_params']; $i++) {
					$template_symbol .= "0,";
				}
				$template_symbol = substr($template_symbol, 0, -1); //remove trailing comma
			}
			$template_symbol .= "#";
			
			echo "<option value=\"".$template_symbol."\">".$m['pagemod_name']."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
	<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
</select>
<?php F_generic_button("addpagestag",$l['w_add'],"FJ_insert_text(document.form_htmleditor.htmltext,document.form_htmleditor.tags_page.options[document.form_htmleditor.tags_page.selectedIndex].value)"); ?>
<br />

<!-- CLIENT MENU ==================== -->
<select name="tags_menu" id="tags_menu" size="0">
	<option value="#MENU#">- <?php echo $l['w_menu']; ?> -</option>
	<?php
	$sql = "SELECT * FROM ".K_TABLE_MENU_LIST." ORDER BY menulst_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"#MENU=".$m['menulst_name']."#\">".$m['menulst_name']."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
	?>
	<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
</select>
<?php F_generic_button("addmenutag",$l['w_add'],"FJ_insert_text(document.form_htmleditor.htmltext,document.form_htmleditor.tags_menu.options[document.form_htmleditor.tags_menu.selectedIndex].value)"); ?>
<br />

<!-- BANNER ==================== -->
<select name="tags_banner" id="tags_banner" size="0">
	<option value="#BANNER#">- <?php echo $l['w_banner']; ?> -</option>
	<?php
	$sql = "SELECT * FROM ".K_TABLE_BANNERS_ZONES." ORDER BY banzone_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"#BANNER=".$m['banzone_name']."#\">".$m['banzone_name']."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
	?>
	<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
</select>
<?php F_generic_button("addmenupagestag",$l['w_add'],"FJ_insert_text(document.form_htmleditor.htmltext,document.form_htmleditor.tags_banner.options[document.form_htmleditor.tags_banner.selectedIndex].value)"); ?>
<br />
<?php
} ?>

<!-- EXTENDED CHARS ==================== -->
<select name="extended_chars" id="extended_chars" size="0">
	<option value="">- <?php echo $l['w_extended_characters']; ?> -</option>
	<option value="&amp;nbsp;">&nbsp; (&amp;nbsp;)</option>
	<option value="&amp;iexcl;">&iexcl; (&amp;iexcl;)</option>
	<option value="&amp;cent;">&cent; (&amp;cent;)</option>
	<option value="&amp;pound;">&pound; (&amp;pound;)</option>
	<option value="&amp;curren;">&curren; (&amp;curren;)</option>
	<option value="&amp;yen;">&yen; (&amp;yen;)</option>
	<option value="&amp;brvbar;">&brvbar; (&amp;brvbar;)</option>
	<option value="&amp;sect;">&sect; (&amp;sect;)</option>
	<option value="&amp;uml;">&uml; (&amp;uml;)</option>
	<option value="&amp;copy;">&copy; (&amp;copy;)</option>
	<option value="&amp;ordf;">&ordf; (&amp;ordf;)</option>
	<option value="&amp;laquo;">&laquo; (&amp;laquo;)</option>
	<option value="&amp;not;">&not; (&amp;not;)</option>
	<option value="&amp;shy;">&shy; (&amp;shy;)</option>
	<option value="&amp;reg;">&reg; (&amp;reg;)</option>
	<option value="&amp;macr;">&macr; (&amp;macr;)</option>
	<option value="&amp;deg;">&deg; (&amp;deg;)</option>
	<option value="&amp;plusmn;">&plusmn; (&amp;plusmn;)</option>
	<option value="&amp;sup2;">&sup2; (&amp;sup2;)</option>
	<option value="&amp;sup3;">&sup3; (&amp;sup3;)</option>
	<option value="&amp;acute;">&acute; (&amp;acute;)</option>
	<option value="&amp;micro;">&micro; (&amp;micro;)</option>
	<option value="&amp;para;">&para; (&amp;para;)</option>
	<option value="&amp;middot;">&middot; (&amp;middot;)</option>
	<option value="&amp;cedil;">&cedil; (&amp;cedil;)</option>
	<option value="&amp;sup1;">&sup1; (&amp;sup1;)</option>
	<option value="&amp;ordm;">&ordm; (&amp;ordm;)</option>
	<option value="&amp;raquo;">&raquo; (&amp;raquo;)</option>
	<option value="&amp;frac14;">&frac14; (&amp;frac14;)</option>
	<option value="&amp;frac12;">&frac12; (&amp;frac12;)</option>
	<option value="&amp;frac34;">&frac34; (&amp;frac34;)</option>
	<option value="&amp;iquest;">&iquest; (&amp;iquest;)</option>
	<option value="&amp;Agrave;">&Agrave; (&amp;Agrave;)</option>
	<option value="&amp;Aacute;">&Aacute; (&amp;Aacute;)</option>
	<option value="&amp;Acirc;">&Acirc; (&amp;Acirc;)</option>
	<option value="&amp;Atilde;">&Atilde; (&amp;Atilde;)</option>
	<option value="&amp;Auml;">&Auml; (&amp;Auml;)</option>
	<option value="&amp;Aring;">&Aring; (&amp;Aring;)</option>
	<option value="&amp;AElig;">&AElig; (&amp;AElig;)</option>
	<option value="&amp;Ccedil;">&Ccedil; (&amp;Ccedil;)</option>
	<option value="&amp;Egrave;">&Egrave; (&amp;Egrave;)</option>
	<option value="&amp;Eacute;">&Eacute; (&amp;Eacute;)</option>
	<option value="&amp;Ecirc;">&Ecirc; (&amp;Ecirc;)</option>
	<option value="&amp;Euml;">&Euml; (&amp;Euml;)</option>
	<option value="&amp;Igrave;">&Igrave; (&amp;Igrave;)</option>
	<option value="&amp;Iacute;">&Iacute; (&amp;Iacute;)</option>
	<option value="&amp;Icirc;">&Icirc; (&amp;Icirc;)</option>
	<option value="&amp;Iuml;">&Iuml; (&amp;Iuml;)</option>
	<option value="&amp;ETH;">&ETH; (&amp;ETH;)</option>
	<option value="&amp;Ntilde;">&Ntilde; (&amp;Ntilde;)</option>
	<option value="&amp;Ograve;">&Ograve; (&amp;Ograve;)</option>
	<option value="&amp;Oacute;">&Oacute; (&amp;Oacute;)</option>
	<option value="&amp;Ocirc;">&Ocirc; (&amp;Ocirc;)</option>
	<option value="&amp;Otilde;">&Otilde; (&amp;Otilde;)</option>
	<option value="&amp;Ouml;">&Ouml; (&amp;Ouml;)</option>
	<option value="&amp;times;">&times; (&amp;times;)</option>
	<option value="&amp;Oslash;">&Oslash; (&amp;Oslash;)</option>
	<option value="&amp;Ugrave;">&Ugrave; (&amp;Ugrave;)</option>
	<option value="&amp;Uacute;">&Uacute; (&amp;Uacute;)</option>
	<option value="&amp;Ucirc;">&Ucirc; (&amp;Ucirc;)</option>
	<option value="&amp;Uuml;">&Uuml; (&amp;Uuml;)</option>
	<option value="&amp;Yacute;">&Yacute; (&amp;Yacute;)</option>
	<option value="&amp;THORN;">&THORN; (&amp;THORN;)</option>
	<option value="&amp;szlig;">&szlig; (&amp;szlig;)</option>
	<option value="&amp;agrave;">&agrave; (&amp;agrave;)</option>
	<option value="&amp;aacute;">&aacute; (&amp;aacute;)</option>
	<option value="&amp;acirc;">&acirc; (&amp;acirc;)</option>
	<option value="&amp;atilde;">&atilde; (&amp;atilde;)</option>
	<option value="&amp;auml;">&auml; (&amp;auml;)</option>
	<option value="&amp;aring;">&aring; (&amp;aring;)</option>
	<option value="&amp;aelig;">&aelig; (&amp;aelig;)</option>
	<option value="&amp;ccedil;">&ccedil; (&amp;ccedil;)</option>
	<option value="&amp;egrave;">&egrave; (&amp;egrave;)</option>
	<option value="&amp;eacute;">&eacute; (&amp;eacute;)</option>
	<option value="&amp;ecirc;">&ecirc; (&amp;ecirc;)</option>
	<option value="&amp;euml;">&euml; (&amp;euml;)</option>
	<option value="&amp;igrave;">&igrave; (&amp;igrave;)</option>
	<option value="&amp;iacute;">&iacute; (&amp;iacute;)</option>
	<option value="&amp;icirc;">&icirc; (&amp;icirc;)</option>
	<option value="&amp;iuml;">&iuml; (&amp;iuml;)</option>
	<option value="&amp;eth;">&eth; (&amp;eth;)</option>
	<option value="&amp;ntilde;">&ntilde; (&amp;ntilde;)</option>
	<option value="&amp;ograve;">&ograve; (&amp;ograve;)</option>
	<option value="&amp;oacute;">&oacute; (&amp;oacute;)</option>
	<option value="&amp;ocirc;">&ocirc; (&amp;ocirc;)</option>
	<option value="&amp;otilde;">&otilde; (&amp;otilde;)</option>
	<option value="&amp;ouml;">&ouml; (&amp;ouml;)</option>
	<option value="&amp;divide;">&divide; (&amp;divide;)</option>
	<option value="&amp;oslash;">&oslash; (&amp;oslash;)</option>
	<option value="&amp;ugrave;">&ugrave; (&amp;ugrave;)</option>
	<option value="&amp;uacute;">&uacute; (&amp;uacute;)</option>
	<option value="&amp;ucirc;">&ucirc; (&amp;ucirc;)</option>
	<option value="&amp;uuml;">&uuml; (&amp;uuml;)</option>
	<option value="&amp;yacute;">&yacute; (&amp;yacute;)</option>
	<option value="&amp;thorn;">&thorn; (&amp;thorn;)</option>
	<option value="&amp;yuml;">&yuml; (&amp;yuml;)</option>
	<option value="&amp;fnof;">&fnof; (&amp;fnof;)</option>
	<option value="&amp;Alpha;">&Alpha; (&amp;Alpha;)</option>
	<option value="&amp;Beta;">&Beta; (&amp;Beta;)</option>
	<option value="&amp;Gamma;">&Gamma; (&amp;Gamma;)</option>
	<option value="&amp;Delta;">&Delta; (&amp;Delta;)</option>
	<option value="&amp;Epsilon;">&Epsilon; (&amp;Epsilon;)</option>
	<option value="&amp;Zeta;">&Zeta; (&amp;Zeta;)</option>
	<option value="&amp;Eta;">&Eta; (&amp;Eta;)</option>
	<option value="&amp;Theta;">&Theta; (&amp;Theta;)</option>
	<option value="&amp;Iota;">&Iota; (&amp;Iota;)</option>
	<option value="&amp;Kappa;">&Kappa; (&amp;Kappa;)</option>
	<option value="&amp;Lambda;">&Lambda; (&amp;Lambda;)</option>
	<option value="&amp;Mu;">&Mu; (&amp;Mu;)</option>
	<option value="&amp;Nu;">&Nu; (&amp;Nu;)</option>
	<option value="&amp;Xi;">&Xi; (&amp;Xi;)</option>
	<option value="&amp;Omicron;">&Omicron; (&amp;Omicron;)</option>
	<option value="&amp;Pi;">&Pi; (&amp;Pi;)</option>
	<option value="&amp;Rho;">&Rho; (&amp;Rho;)</option>
	<option value="&amp;Sigma;">&Sigma; (&amp;Sigma;)</option>
	<option value="&amp;Tau;">&Tau; (&amp;Tau;)</option>
	<option value="&amp;Upsilon;">&Upsilon; (&amp;Upsilon;)</option>
	<option value="&amp;Phi;">&Phi; (&amp;Phi;)</option>
	<option value="&amp;Chi;">&Chi; (&amp;Chi;)</option>
	<option value="&amp;Psi;">&Psi; (&amp;Psi;)</option>
	<option value="&amp;Omega;">&Omega; (&amp;Omega;)</option>
	<option value="&amp;alpha;">&alpha; (&amp;alpha;)</option>
	<option value="&amp;beta;">&beta; (&amp;beta;)</option>
	<option value="&amp;gamma;">&gamma; (&amp;gamma;)</option>
	<option value="&amp;delta;">&delta; (&amp;delta;)</option>
	<option value="&amp;epsilon;">&epsilon; (&amp;epsilon;)</option>
	<option value="&amp;zeta;">&zeta; (&amp;zeta;)</option>
	<option value="&amp;eta;">&eta; (&amp;eta;)</option>
	<option value="&amp;theta;">&theta; (&amp;theta;)</option>
	<option value="&amp;iota;">&iota; (&amp;iota;)</option>
	<option value="&amp;kappa;">&kappa; (&amp;kappa;)</option>
	<option value="&amp;lambda;">&lambda; (&amp;lambda;)</option>
	<option value="&amp;mu;">&mu; (&amp;mu;)</option>
	<option value="&amp;nu;">&nu; (&amp;nu;)</option>
	<option value="&amp;xi;">&xi; (&amp;xi;)</option>
	<option value="&amp;omicron;">&omicron; (&amp;omicron;)</option>
	<option value="&amp;pi;">&pi; (&amp;pi;)</option>
	<option value="&amp;rho;">&rho; (&amp;rho;)</option>
	<option value="&amp;sigmaf;">&sigmaf; (&amp;sigmaf;)</option>
	<option value="&amp;sigma;">&sigma; (&amp;sigma;)</option>
	<option value="&amp;tau;">&tau; (&amp;tau;)</option>
	<option value="&amp;upsilon;">&upsilon; (&amp;upsilon;)</option>
	<option value="&amp;phi;">&phi; (&amp;phi;)</option>
	<option value="&amp;chi;">&chi; (&amp;chi;)</option>
	<option value="&amp;psi;">&psi; (&amp;psi;)</option>
	<option value="&amp;omega;">&omega; (&amp;omega;)</option>
	<option value="&amp;thetasym;">&thetasym; (&amp;thetasym;)</option>
	<option value="&amp;upsih;">&upsih; (&amp;upsih;)</option>
	<option value="&amp;piv;">&piv; (&amp;piv;)</option>
	<option value="&amp;bull;">&bull; (&amp;bull;)</option>
	<option value="&amp;hellip;">&hellip; (&amp;hellip;)</option>
	<option value="&amp;prime;">&prime; (&amp;prime;)</option>
	<option value="&amp;Prime;">&Prime; (&amp;Prime;)</option>
	<option value="&amp;oline;">&oline; (&amp;oline;)</option>
	<option value="&amp;frasl;">&frasl; (&amp;frasl;)</option>
	<option value="&amp;weierp;">&weierp; (&amp;weierp;)</option>
	<option value="&amp;image;">&image; (&amp;image;)</option>
	<option value="&amp;real;">&real; (&amp;real;)</option>
	<option value="&amp;trade;">&trade; (&amp;trade;)</option>
	<option value="&amp;alefsym;">&alefsym; (&amp;alefsym;)</option>
	<option value="&amp;larr;">&larr; (&amp;larr;)</option>
	<option value="&amp;uarr;">&uarr; (&amp;uarr;)</option>
	<option value="&amp;rarr;">&rarr; (&amp;rarr;)</option>
	<option value="&amp;darr;">&darr; (&amp;darr;)</option>
	<option value="&amp;harr;">&harr; (&amp;harr;)</option>
	<option value="&amp;crarr;">&crarr; (&amp;crarr;)</option>
	<option value="&amp;lArr;">&lArr; (&amp;lArr;)</option>
	<option value="&amp;uArr;">&uArr; (&amp;uArr;)</option>
	<option value="&amp;rArr;">&rArr; (&amp;rArr;)</option>
	<option value="&amp;dArr;">&dArr; (&amp;dArr;)</option>
	<option value="&amp;hArr;">&hArr; (&amp;hArr;)</option>
	<option value="&amp;forall;">&forall; (&amp;forall;)</option>
	<option value="&amp;part;">&part; (&amp;part;)</option>
	<option value="&amp;exist;">&exist; (&amp;exist;)</option>
	<option value="&amp;empty;">&empty; (&amp;empty;)</option>
	<option value="&amp;nabla;">&nabla; (&amp;nabla;)</option>
	<option value="&amp;isin;">&isin; (&amp;isin;)</option>
	<option value="&amp;notin;">&notin; (&amp;notin;)</option>
	<option value="&amp;ni;">&ni; (&amp;ni;)</option>
	<option value="&amp;prod;">&prod; (&amp;prod;)</option>
	<option value="&amp;sum;">&sum; (&amp;sum;)</option>
	<option value="&amp;minus;">&minus; (&amp;minus;)</option>
	<option value="&amp;lowast;">&lowast; (&amp;lowast;)</option>
	<option value="&amp;radic;">&radic; (&amp;radic;)</option>
	<option value="&amp;prop;">&prop; (&amp;prop;)</option>
	<option value="&amp;infin;">&infin; (&amp;infin;)</option>
	<option value="&amp;ang;">&ang; (&amp;ang;)</option>
	<option value="&amp;and;">&and; (&amp;and;)</option>
	<option value="&amp;or;">&or; (&amp;or;)</option>
	<option value="&amp;cap;">&cap; (&amp;cap;)</option>
	<option value="&amp;cup;">&cup; (&amp;cup;)</option>
	<option value="&amp;int;">&int; (&amp;int;)</option>
	<option value="&amp;there4;">&there4; (&amp;there4;)</option>
	<option value="&amp;sim;">&sim; (&amp;sim;)</option>
	<option value="&amp;cong;">&cong; (&amp;cong;)</option>
	<option value="&amp;asymp;">&asymp; (&amp;asymp;)</option>
	<option value="&amp;ne;">&ne; (&amp;ne;)</option>
	<option value="&amp;equiv;">&equiv; (&amp;equiv;)</option>
	<option value="&amp;le;">&le; (&amp;le;)</option>
	<option value="&amp;ge;">&ge; (&amp;ge;)</option>
	<option value="&amp;sub;">&sub; (&amp;sub;)</option>
	<option value="&amp;sup;">&sup; (&amp;sup;)</option>
	<option value="&amp;nsub;">&nsub; (&amp;nsub;)</option>
	<option value="&amp;sube;">&sube; (&amp;sube;)</option>
	<option value="&amp;supe;">&supe; (&amp;supe;)</option>
	<option value="&amp;oplus;">&oplus; (&amp;oplus;)</option>
	<option value="&amp;otimes;">&otimes; (&amp;otimes;)</option>
	<option value="&amp;perp;">&perp; (&amp;perp;)</option>
	<option value="&amp;sdot;">&sdot; (&amp;sdot;)</option>
	<option value="&amp;lceil;">&lceil; (&amp;lceil;)</option>
	<option value="&amp;rceil;">&rceil; (&amp;rceil;)</option>
	<option value="&amp;lfloor;">&lfloor; (&amp;lfloor;)</option>
	<option value="&amp;rfloor;">&rfloor; (&amp;rfloor;)</option>
	<option value="&amp;lang;">&lang; (&amp;lang;)</option>
	<option value="&amp;rang;">&rang; (&amp;rang;)</option>
	<option value="&amp;loz;">&loz; (&amp;loz;)</option>
	<option value="&amp;spades;">&spades; (&amp;spades;)</option>
	<option value="&amp;clubs;">&clubs; (&amp;clubs;)</option>
	<option value="&amp;hearts;">&hearts; (&amp;hearts;)</option>
	<option value="&amp;diams;">&diams; (&amp;diams;)</option>
	<option value="&amp;quot;">&quot; (&amp;quot;)</option>
	<option value="&amp;amp;">&amp; (&amp;amp;)</option>
	<option value="&amp;lt;">&lt; (&amp;lt;)</option>
	<option value="&amp;gt;">&gt; (&amp;gt;)</option>
	<option value="&amp;OElig;">&OElig; (&amp;OElig;)</option>
	<option value="&amp;oelig;">&oelig; (&amp;oelig;)</option>
	<option value="&amp;Scaron;">&Scaron; (&amp;Scaron;)</option>
	<option value="&amp;scaron;">&scaron; (&amp;scaron;)</option>
	<option value="&amp;Yuml;">&Yuml; (&amp;Yuml;)</option>
	<option value="&amp;circ;">&circ; (&amp;circ;)</option>
	<option value="&amp;tilde;">&tilde; (&amp;tilde;)</option>
	<option value="&amp;ensp;">&ensp; (&amp;ensp;)</option>
	<option value="&amp;emsp;">&emsp; (&amp;emsp;)</option>
	<option value="&amp;thinsp;">&thinsp; (&amp;thinsp;)</option>
	<option value="&amp;zwnj;">&zwnj; (&amp;zwnj;)</option>
	<option value="&amp;zwj;">&zwj; (&amp;zwj;)</option>
	<option value="&amp;lrm;">&lrm; (&amp;lrm;)</option>
	<option value="&amp;rlm;">&rlm; (&amp;rlm;)</option>
	<option value="&amp;ndash;">&ndash; (&amp;ndash;)</option>
	<option value="&amp;mdash;">&mdash; (&amp;mdash;)</option>
	<option value="&amp;lsquo;">&lsquo; (&amp;lsquo;)</option>
	<option value="&amp;rsquo;">&rsquo; (&amp;rsquo;)</option>
	<option value="&amp;sbquo;">&sbquo; (&amp;sbquo;)</option>
	<option value="&amp;ldquo;">&ldquo; (&amp;ldquo;)</option>
	<option value="&amp;rdquo;">&rdquo; (&amp;rdquo;)</option>
	<option value="&amp;bdquo;">&bdquo; (&amp;bdquo;)</option>
	<option value="&amp;dagger;">&dagger; (&amp;dagger;)</option>
	<option value="&amp;Dagger;">&Dagger; (&amp;Dagger;)</option>
	<option value="&amp;permil;">&permil; (&amp;permil;)</option>
	<option value="&amp;lsaquo;">&lsaquo; (&amp;lsaquo;)</option>
	<option value="&amp;rsaquo;">&rsaquo; (&amp;rsaquo;)</option>
	<option value="&amp;euro;">&euro; (&amp;euro;)</option>
	<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
</select>
<?php F_generic_button("addextendedchars",$l['w_add'],"FJ_insert_text(document.form_htmleditor.htmltext,document.form_htmleditor.extended_chars.options[document.form_htmleditor.extended_chars.selectedIndex].value)"); ?>
<br />

<!-- COLOR ==================== -->
<?php echo $l['w_color']; ?> <input type="text" name="hexcolor" id="hexcolor" value="" size="10" maxlength="7" />

<?php F_generic_button("pickcolor",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_htmleditor&amp;callingfield=hexcolor','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')");
F_generic_button("addhexcolor",$l['w_add'],"FJ_insert_text(document.form_htmleditor.htmltext,document.form_htmleditor.hexcolor.value)"); ?>
<br /><br />

<!-- HTML TAGS ==================== -->
<select name="tag_name" id="tag_name" size="0" onchange="document.form_htmleditor.tagoptions.value=document.form_htmleditor.tag_name.options[document.form_htmleditor.tag_name.selectedIndex].value">
	<option value="">--- XHTML ---</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_XHTML_TAGS." ORDER BY tag_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"&lt;".$m['tag_name']."";
			if (!$m['tag_endtag']) {echo " /";}
			echo "&gt;\">".$m['tag_name']."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>

<!-- TAG ATTRIBUTES ==================== -->
<?php F_generic_button("edittag",$l['w_edit'],"tagoptWindow=window.open('cp_edit_html_options.".CP_EXT."?tag_name='+document.form_htmleditor.tag_name.options[document.form_htmleditor.tag_name.selectedIndex].value,'tagoptWindow','dependent,height=400,width=500,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");?>

<?php
 F_generic_button("addtag",$l['w_add'],"FJ_insert_tag(document.form_htmleditor.htmltext,document.form_htmleditor.tagoptions.value)"); ?>
<br />
<textarea cols="30" rows="5" name="tagoptions" id="tagoptions"></textarea><br />
</td>

</tr>
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<?php F_generic_button("cancel",$l['w_cancel'],"window.close()"); ?>
<?php F_generic_button("pastecode",$l['w_submit'],"window.opener.document.".$callingform.".".$callingfield.".value=document.form_htmleditor.htmltext.value;window.close()"); ?>
</td>
</tr>

</table>

<input type="hidden" name="callingform" id="callingform" value="<?php echo $callingform; ?>" />
<input type="hidden" name="callingfield" id="callingfield" value="<?php echo $callingfield; ?>" />
<input type="hidden" name="templates" id="templates" value="<?php echo $templates; ?>" />
</form>

<form action="cp_html_preview.<?php echo CP_EXT; ?>" method="post" enctype="multipart/form-data" name="form_htmlpreview" id="form_htmlpreview" target="_blank">
<input type="hidden" name="htmlcode" id="fhp_htmlcode" value="<?php echo $htmltext; ?>" />
<input type="hidden" name="menu_mode" id="fhp_menu_mode" value="" />
<?php F_generic_submit_button("form_htmlpreview", "menu_mode", $l['w_preview'], "document.form_htmlpreview.htmlcode.value=document.form_htmleditor.htmltext.value"); ?>
</form>

<?php 
	if (!isset($_REQUEST['menu_mode'])) { //get text from calling form
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
		echo "//<![CDATA[\n";
		echo "document.form_htmleditor.htmltext.value=window.opener.document.".$callingform.".".$callingfield.".value;";
		echo "//]]>\n";
		echo "</script>";
	}
return;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
