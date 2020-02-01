<?php
//============================================================+
// File name   : cp_edit_html_link.php                         
// Begin       : 2002-04-10                                    
// Last Update : 2003-10-26                                    
//                                                             
// Description : HTML <A> tag (Link) Editor                    
//                                                             
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_HTML_LINK;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$thispage_title = "IMAGE";

require_once('../code/cp_page_header_popup.'.CP_EXT);

switch($menu_mode) {
	case unhtmlentities($l['w_submit']):
	case $l['w_submit']: {
		$linktag = "<a";
		if ($lhref) {$linktag .= " href=\"".htmlentities(urldecode($lhref))."\"";}
		if ($ltarget) {$linktag .= " target=\"".$ltarget."\"";}
		if ($ltitle) {$linktag .= " title=\"".$ltitle."\"";}
		if ($laccesskey) {$linktag .= " accesskey=\"".$laccesskey."\"";}
		if ($lcharset) {$linktag .= " charset=\"".$lcharset."\"";}
		if ($ldir) {$linktag .= " dir=\"".$ldir."\"";}
		if ($lclass) {$linktag .= " class=\"".$lclass."\"";}
		if ($lstyle) {$linktag .= " style=\"".$lstyle."\"";}
		if ($llang) {$linktag .= " lang=\"".$llang."\"";}
		if ($lhreflang) {$linktag .= " hreflang=\"".$lhreflang."\"";}
		if ($lname) {$linktag .= " name=\"".$lname."\"";}
		if ($lid) {$linktag .= " id=\"".$lid."\"";}
		if ($lrel) {$linktag .= " rel=\"".$lrel."\"";}
		if ($lrev) {$linktag .= " rev=\"".$lrev."\"";}
		if ($lshape) {$linktag .= " shape=\"".$lshape."\"";}
		if ($lcoords) {$linktag .= " coords=\"".$lcoords."\"";}
		if ($ltabindex) {$linktag .= " tabindex=\"".$ltabindex."\"";}
		if ($ltype) {$linktag .= " type=\"".$ltype."\"";}
		$linktag .= ">";
		?>
		<script language="JavaScript" type="text/javascript">
		//<![CDATA[
		if (window.opener.document.all.tbContentElement.DOM.selection.type == "Control") {
			window.opener.document.all.tbContentElement.DOM.selection.clear();
		}
		sel = window.opener.document.all.tbContentElement.DOM.selection.createRange();
		sel.collapse;
		sel.pasteHTML('<?php echo $linktag; ?>' + sel.htmlText + '</a>');
		window.close();
		//]]>
		</script>
		<?php
		break;
	}
} //end of switch
?>


<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_linkeditor" id="form_linkeditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>href</b><br /><small>URI for linked resource</small></td>
<td class="fillOE" valign="top"><input type="text" name="lhref" id="lhref" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>target</b><br /><small>render in this frame</small></td>
<td class="fillOE" valign="top">
<select name="ltarget" id="ltarget" size="0">
<option value="">&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_FRAME_TARGETS." ORDER BY target_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['target_name']."\">".$m['target_name']."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>title</b><br /><small>advisory title</small></td>
<td class="fillEE" valign="top"><input type="text" name="ltitle" id="ltitle" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>accesskey</b><br /><small>accessibility key character</small></td>
<td class="fillEE" valign="top"><input type="text" name="laccesskey" id="laccesskey" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>charset</b><br /><small>char encoding of linked resource</small></td>
<td class="fillOE" valign="top"><input type="text" name="lcharset" id="lcharset" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>dir</b><br /><small>direction for weak/neutral text</small></td>
<td class="fillEE" valign="top">
<select name="ldir" id="ldir" size="0">
<option value="" selected=\"selected\">&nbsp;</option>
<option value="ltr">ltr</option>
<option value="rtl">rtl</option>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>class</b><br /><small>space-separated list of classes</small></td>
<td class="fillEE" valign="top"><input type="text" name="lclass" id="lclass" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>style</b><br /><small>associated style info</small></td>
<td class="fillOE" valign="top"><textarea cols="20" rows="2" name="lstyle" id="lstyle"></textarea></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>lang</b><br /><small>language code</small></td>
<td class="fillEE" valign="top"><input type="text" name="llang" id="llang" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>hreflang</b><br /><small>language code</small></td>
<td class="fillEE" valign="top"><input type="text" name="lhreflang" id="lhreflang" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>name</b><br /><small>named link end</small></td>
<td class="fillOE" valign="top"><input type="text" name="lname" id="lname" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>id</b><br /><small>document-wide unique id</small></td>
<td class="fillOE" valign="top"><input type="text" name="lid" id="lid" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>rel</b><br /><small>forward link types</small></td>
<td class="fillEE" valign="top"><input type="text" name="lrel" id="lrel" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>rev</b><br /><small>reverse link types</small></td>
<td class="fillOE" valign="top"><input type="text" name="lrev" id="lrev" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>shape</b><br /><small>for use with client-side image maps</small></td>
<td class="fillEE" valign="top"><input type="text" name="lshape" id="lshape" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>coords</b><br /><small>for use with client-side image maps</small></td>
<td class="fillOE" valign="top"><input type="text" name="lcoords" id="lcoords" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>tabindex</b><br /><small>position in tabbing order</small></td>
<td class="fillEE" valign="top"><input type="text" name="ltabindex" id="ltabindex" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>type</b><br /><small>advisory content type</small></td>
<td class="fillOE" valign="top"><input type="text" name="ltype" id="ltype" value="" size="20" maxlength="255" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php F_generic_button("cancel", $l['w_cancel'], "window.close()"); ?>
<?php F_submit_button("form_linkeditor", "menu_mode", $l['w_submit']); ?> 
</td>
</tr>
</table>
</form>

<br />

<!-- ====================================================== -->
<?php 
require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>