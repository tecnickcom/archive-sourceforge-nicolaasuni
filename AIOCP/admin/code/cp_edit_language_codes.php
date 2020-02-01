<?php
//============================================================+
// File name   : cp_edit_language_codes.php
// Begin       : 2001-09-30
// Last Update : 2008-07-07
// 
// Description : Enable/Disable languages in
//				 K_TABLE_LANGUAGE_CODES table
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
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_LANGUAGE_CODES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_language_codes_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {
	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update data
		$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." ORDER BY language_name";
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				if (($opt[$m['language_code']]) AND (!$m['language_enabled']) ) {
					// create table column
					$nsql = "ALTER TABLE ".K_TABLE_LANGUAGE_DATA." ADD ".$m['language_code']." LONGTEXT NULL";
					F_aiocpdb_query($nsql, $db);
					$nsql = "ALTER TABLE ".K_TABLE_LANGUAGE_HELP." ADD ".$m['language_code']." LONGTEXT NULL";
					F_aiocpdb_query($nsql, $db);
					$nsql = "ALTER TABLE ".K_TABLE_LANGUAGE_PAGES." ADD ".$m['language_code']." LONGTEXT NULL";
					F_aiocpdb_query($nsql, $db);
				} elseif ((!$opt[$m['language_code']]) AND ($_REQUEST['deletedisabled'])) {
					// drop table column
					$nsql = "ALTER TABLE ".K_TABLE_LANGUAGE_DATA." DROP ".$m['language_code']."";
					F_aiocpdb_query($nsql, $db);
					$nsql = "ALTER TABLE ".K_TABLE_LANGUAGE_HELP." DROP ".$m['language_code']."";
					F_aiocpdb_query($nsql, $db);
					$nsql = "ALTER TABLE ".K_TABLE_LANGUAGE_PAGES." DROP ".$m['language_code']."";
					F_aiocpdb_query($nsql, $db);
				}
				$sqlu = "UPDATE IGNORE ".K_TABLE_LANGUAGE_CODES." SET language_enabled='".$opt[$m['language_code']]."' WHERE language_code='".$m['language_code']."'";
				if(!$ru = F_aiocpdb_query($sqlu, $db)) {
					F_display_db_error();
				}
			}
			
			//reload all frames from index (if javascript enable)
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			echo "//<![CDATA[\n";
			echo "var mainpage = escape(parent.frames['".K_MAIN_FRAME_NAME."'].location.href);\n";
			echo "top.location.replace(\"../code/index.".CP_EXT."?load_page=\" + mainpage);\n";
			echo "//]]>\n";
			echo "</script>\n";
		}
		else {
			F_display_db_error();
		}
		break;
	}
	
	default :{ 
		break;
	}
} //end of switch
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_languagecodeeditor" id="form_languagecodeeditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge" colspan="3"><?php echo $l['w_language']; ?></th>
</tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php
$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." ORDER BY language_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		
		//change style for each row
		if (isset($rowodd) AND ($rowodd)) {
			$rowclass = "O";
			$rowodd=0;
		} else {
			$rowclass = "E";
			$rowodd=1;
		}
		
		echo "<tr class=\"fill".$rowclass."\">";
		echo "<td class=\"fill".$rowclass."E\" align=\"center\">";
		echo "<input type=\"checkbox\" name=\"opt[".$m['language_code']."]\" id=\"opt_".$m['language_code']."\" value=\"1\" ";
		if($m['language_enabled']==1) {
			echo "checked=\"checked\" /></td>";
			echo "<td class=\"fill".$rowclass."E\"><b>".$m['language_code']."</b></td>";
			echo "<td class=\"fill".$rowclass."E\"><b>".$m['language_name']."</b></td></tr>";
		}
		else {
			echo " /></td>";
			echo "<td class=\"fill".$rowclass."E\">".$m['language_code']."</td><td>".$m['language_name']."</td></tr>";
		}
	}
}
else {
	F_display_db_error();
}

if (isset($rowodd) AND ($rowodd)) {
	$rowclass = "O";
	$rowodd=0;
} else {
	$rowclass = "E";
	$rowodd=1;
}
		
echo "<tr class=\"fill".$rowclass."\">";
echo "<td class=\"fill".$rowclass."E\" align=\"right\" colspan=\"3\">";
echo "<hr /><strong>".F_display_field_name('w_delete_disabled', 'h_delete_disabled')."</strong>";
echo "<input type=\"checkbox\" name=\"deletedisabled\" id=\"deletedisabled\" value=\"1\" />";
echo "</td></tr>";
?>
<!-- END SELECT WORD ID ==================== -->

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php 
F_submit_button("form_languagecodeeditor","menu_mode",$l['w_update']); 
?>

</td>
</tr>
</table>
</form>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
