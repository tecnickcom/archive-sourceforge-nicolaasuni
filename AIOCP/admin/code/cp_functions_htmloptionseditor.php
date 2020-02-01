<?php
//============================================================+
// File name   : cp_functions_htmloptionseditor.php            
// Begin       : 2001-11-02                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : HTML (XHTML) Options Editor functions         
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

// ------------------------------------------------------------
// Display HTML EDITOR
// ------------------------------------------------------------
function F_html_options_editor($tag_name) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	
	if(!$tag_name) {
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
		echo "//<![CDATA[\n";
		echo "window.close();";//close window
		echo "//]]>\n";
		echo "</script>";
		return FALSE;
	}
	
	//clean tag (extrapolate tag name)
	ereg("<([^[:space:]/>]*)", $tag_name, $regs);
    $tag_name = $regs[1];
	
	// Read tag attributes list
	$sql = "SELECT * FROM ".K_TABLE_XHTML_TAGS." WHERE tag_name='".$tag_name."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$tag_id = $m['tag_id'];
			$tag_name = $m['tag_name'];
			$tag_description = $m['tag_description'];
			$tag_categoryid = $m['tag_categoryid'];
			$tag_statusid = $m['tag_statusid'];
			$tag_endtag = $m['tag_endtag'];
			$tag_attributes = $m['tag_attributes'];
			$attributes = explode("|",$tag_attributes);
			sort($attributes); //sort attributes by id
			$tag_dtd = $m['tag_dtd'];
		}
	}
	else {
		F_display_db_error();
	}
?>


<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_htmloptionseditor" id="form_htmloptionseditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<b><?php echo $tag_name; ?></b> - <i><?php echo $tag_description; ?></i>
</th></tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php

// Print TAG information
echo "<tr class=\"fillO\">";
echo "<td  class=\"fillOO\" colspan=\"2\" valign=\"top\">";
echo "<b>".$l['w_category']."</b>: ";
$sql = "SELECT * FROM ".K_TABLE_XHTML_TAGS_CATEGORIES." WHERE tagcat_id=".$tag_categoryid." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				echo "".$l[$m['tagcat_name']]."";
				echo " (<i><small>".$l[$m['tagcat_description']]."</small></i>)";
			}
		}
		else {
			F_display_db_error();
		}
echo "<br /><b>".$l['w_status']."</b>: ";
$sql = "SELECT * FROM ".K_TABLE_XHTML_TAGS_STATUS." WHERE tagstat_id=".$tag_statusid." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				echo "".$l[$m['tagstat_name']]."";
				echo " (<i><small>".$l[$m['tagstat_description']]."</small></i>)";
			}
		}
		else {
			F_display_db_error();
		}
echo "<br />&nbsp;</td></tr>";

	for ($i=0; $attributes[$i]; $i++) { //print an input field for each attribute
		$sql = "SELECT * FROM ".K_TABLE_XHTML_ATTRIBUTES." WHERE htmattrib_id=".$attributes[$i]." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				
				//change style for each row
				if (isset($rowodd) AND ($rowodd)) {
					$rowclass = "O";
					$rowodd=0;
				} else {
					$rowclass = "E";
					$rowodd=1;
				}
				
				echo "<tr class=\"fill".$rowclass."\">";
				echo "<td  class=\"fill".$rowclass."O\" align=\"right\" valign=\"top\">";
				echo "<b>".$m['htmattrib_name']."</b><br />".$m['htmattrib_description']."";
				echo "</td>";
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\">";
				switch($m['htmattrib_type']) { //choose type of field to display
					case 0: { //input text
						echo "<input type=\"text\" name=\"F".$m['htmattrib_id']."\" id=\"F".$m['htmattrib_id']."\" value=\"".$m['htmattrib_default']."\" size=\"30\" maxlength=\"255\" />";
						break;
					}
					case 1: { // check
						echo "<input type=\"checkbox\" name=\"F".$m['htmattrib_id']."\" id=\"F".$m['htmattrib_id']."\" value=\"1\" />";
						break;
					}
					case 2: { // text area
						echo "<textarea cols=\"30\" rows=\"4\" name=\"F".$m['htmattrib_id']."\" id=\"F".$m['htmattrib_id']."\">".$m['htmattrib_default']."</textarea>";
						break;
					}
					case 3: { // select
						echo "<select name=\"F".$m['htmattrib_id']."\" id=\"F".$m['htmattrib_id']."\" size=\"0\">";
						$options = explode("|",$m['htmattrib_values']);
						for ($j=0; $options[$j]; $j++) {
							echo "<option value=\"".$options[$j]."\"";
							if($options[$j] == $m['htmattrib_default']) {
								echo " selected=\"selected\"";
							}
							echo ">".$options[$j]."</option>\n";
						}
						echo "<option value=\"\"> </option>\n"; //add null value option
						echo "</select>";
						break;
					}
				}
				echo "</td></tr>";
			}
		}
		else {
			F_display_db_error();
		}
	}
?>


</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="fulltag" id="fulltag" value="" />
<?php F_generic_button("cancel",$l['w_cancel'],"window.close()"); ?>
<?php F_generic_button("submitOpt",$l['w_submit'],"submitAttributes()"); ?>
</td>
</tr>

</table>
</form>

<script language="JavaScript" type="text/javascript">
//<![CDATA[
//paste attributes with value to full tag field
function submitAttributes() {
<?php
	echo "document.form_htmloptionseditor.fulltag.value = \"";
	echo "<".$tag_name."\";\n"; //write tag
	
	//write attributes
	for ($i=0; $attributes[$i]; $i++) {
		$sql = "SELECT * FROM ".K_TABLE_XHTML_ATTRIBUTES." WHERE htmattrib_id=".$attributes[$i]." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				echo "if(document.form_htmloptionseditor.F".$m['htmattrib_id'].".value) {";
				echo "document.form_htmloptionseditor.fulltag.value += ";
				if($m['htmattrib_type']==1) { //check type option
					echo "' ".$m['htmattrib_name']."';";
				}
				else {
					echo "' ".$m['htmattrib_name']."=\"'+document.form_htmloptionseditor.F".$m['htmattrib_id'].".value+'\"';";
				}
				echo "}\n";
			}
		}
	}
	echo "document.form_htmloptionseditor.fulltag.value += \"";
	if(!$tag_endtag) {echo " /";} //close tag in xhtml style
	echo ">\";";
?>
window.opener.document.form_htmleditor.tagoptions.value=document.form_htmloptionseditor.fulltag.value; window.close();
}
//]]>
</script>

<!-- ====================================================== -->
<?php 
return;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
