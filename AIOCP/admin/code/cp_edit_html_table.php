<?php
//============================================================+
// File name   : cp_edit_html_table.php                        
// Begin       : 2002-04-08                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : edit HTML tables properties                   
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_HTML_TABLE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$thispage_title = "TABLE";

require_once('../code/cp_page_header_popup.'.CP_EXT);
?>

<!-- ====================================================== -->
<script language="JavaScript" FOR="window" EVENT="onload">
//<![CDATA[
  for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    case "NumRows":
      document.form_tableeditor.trows.value = window.dialogArguments["NumRows"];
      break;
    case "NumCols":
      document.form_tableeditor.tcolumns.value = window.dialogArguments["NumCols"];
      break;
    case "TableAttrs":
      //TableAttrs.value = window.dialogArguments["TableAttrs"];
      break;
    case "CellAttrs":
      //CellAttrs.value = window.dialogArguments["CellAttrs"];
      break;
    case "Caption":
      //Caption.value = window.dialogArguments["Caption"];
      break;
    }
  }
//]]>
</script>


<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_table() {
	var arr = new Array();
	
	if (document.form_tableeditor.trows.value) {arr["NumRows"] = document.form_tableeditor.trows.value;}
	else {arr["NumRows"] = 2;}
	if (document.form_tableeditor.tcolumns.value) {arr["NumCols"] = document.form_tableeditor.tcolumns.value;}
	else {arr["NumCols"] = 2;}
	
	// compose table attributes
	arr["TableAttrs"] = "";
	if (document.form_tableeditor.tborder.value) {arr["TableAttrs"] += ' border="'+document.form_tableeditor.tborder.value+'"';}
	if (document.form_tableeditor.tcellpadding.value) {arr["TableAttrs"] += ' cellpadding="'+document.form_tableeditor.tcellpadding.value+'"';}
	if (document.form_tableeditor.tcellspacing.value) {arr["TableAttrs"] += ' cellspacing="'+document.form_tableeditor.tcellspacing.value+'"';}
	if (document.form_tableeditor.twidth.value) {arr["TableAttrs"] += ' width="'+document.form_tableeditor.twidth.value+'"';}
	if (document.form_tableeditor.talign.value) {arr["TableAttrs"] += ' align="'+document.form_tableeditor.talign.value+'"';}
	if (document.form_tableeditor.tbgcolor.value) {arr["TableAttrs"] += ' bgcolor="'+document.form_tableeditor.tbgcolor.value+'"';}
	if (document.form_tableeditor.tclass.value) {arr["TableAttrs"] += ' class="'+document.form_tableeditor.tclass.value+'"';}
	if (document.form_tableeditor.tstyle.value) {arr["TableAttrs"] += ' style="'+document.form_tableeditor.tstyle.value+'"';}
	if (document.form_tableeditor.tdir.value) {arr["TableAttrs"] += ' dir="'+document.form_tableeditor.tdir.value+'"';}
	if (document.form_tableeditor.tframe.value) {arr["TableAttrs"] += ' frame="'+document.form_tableeditor.tframe.value+'"';}
	if (document.form_tableeditor.tid.value) {arr["TableAttrs"] += ' id="'+document.form_tableeditor.tid.value+'"';}
	if (document.form_tableeditor.tlang.value) {arr["TableAttrs"] += ' lang="'+document.form_tableeditor.tlang.value+'"';}
	if (document.form_tableeditor.ttitle.value) {arr["TableAttrs"] += ' title="'+document.form_tableeditor.ttitle.value+'"';}
	if (document.form_tableeditor.tsummary.value) {arr["TableAttrs"] += ' summary="'+document.form_tableeditor.tsummary.value+'"';}
	if (document.form_tableeditor.trules.value) {arr["TableAttrs"] += ' rules="'+document.form_tableeditor.trules.value+'"';}

	arr["CellAttrs"] = "";
	arr["Caption"] = "";
	window.returnValue = arr;
	window.close();
}
//]]>
</script>

<form action="" name="form_tableeditor" id="form_tableeditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOE" align="right"><b>rows</b><br /><small>number of rows</small></td>
<td class="fillOE" colspan="2"><input type="text" name="trows" id="trows" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b>columns</b><br /><small>number of columns</small></td>
<td class="fillEE" colspan="2"><input type="text" name="tcolumns" id="tcolumns" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>border</b><br /><small>controls frame width around table</small></td>
<td class="fillOE" valign="top"><input type="text" name="tborder" id="tborder" value="1" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>cellpadding</b><br /><small>spacing within cells</small></td>
<td class="fillEE" valign="top"><input type="text" name="tcellpadding" id="tcellpadding" value="2" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>cellspacing</b><br /><small>spacing between cells</small></td>
<td class="fillOE" valign="top"><input type="text" name="tcellspacing" id="tcellspacing" value="2" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>width</b><br /><small>table width</small></td>
<td class="fillEE" valign="top"><input type="text" name="twidth" id="twidth" value="" size="20" maxlength="255" /></td></tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>align</b><br /><small>table position relative to window</small></td>
<td class="fillOE" valign="top">
<select name="talign" id="talign" size="0">
<option value="" selected=\"selected\">&nbsp;</option>
<option value="left">left</option>
<option value="center">center</option>
<option value="right">right</option>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>bgcolor</b><br /><small>background color for cells</small></td>
<td class="fillEE" valign="top"><input type="text" name="tbgcolor" id="tbgcolor" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>class</b><br /><small>space-separated list of classes</small></td>
<td class="fillOE" valign="top"><input type="text" name="tclass" id="tclass" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>style</b><br /><small>associated style info</small></td>
<td class="fillEE" valign="top"><textarea cols="20" rows="3" name="tstyle" id="tstyle"></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>dir</b><br /><small>direction for weak/neutral text</small></td>
<td class="fillOE" valign="top">
<select name="tdir" id="tdir" size="0">
<option value="" selected=\"selected\">&nbsp;</option>
<option value="ltr">ltr</option>
<option value="rtl">rtl</option>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>frame</b><br /><small>which parts of frame to render</small></td>
<td class="fillEE" valign="top"><input type="text" name="tframe" id="tframe" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>id</b><br /><small>document-wide unique id</small></td>
<td class="fillOE" valign="top"><input type="text" name="tid" id="tid" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>lang</b><br /><small>language code</small></td>
<td class="fillEE" valign="top"><input type="text" name="tlang" id="tlang" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>title</b><br /><small>advisory title</small></td>
<td class="fillOE" valign="top"><input type="text" name="ttitle" id="ttitle" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>summary</b><br /><small>purpose/structure for speech output</small></td>
<td class="fillEE" valign="top"><textarea cols="20" rows="3" name="tsummary" id="tsummary"></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>rules</b><br /><small>rulings between rows and cols</small></td>
<td class="fillOE" valign="top"><input type="text" name="trules" id="trules" value="" size="20" maxlength="255" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<?php F_generic_button("cancel",$l['w_cancel'],"window.close()"); ?>
<?php F_generic_button("submit", $l['w_submit'], "FJ_submit_table();"); ?>
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
