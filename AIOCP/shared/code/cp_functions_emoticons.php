<?php
//============================================================+
// File name   : cp_functions_emoticons.php
// Begin       : 2001-11-01
// Last Update : 2007-02-08
// 
// Description : Functions for Smiles
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
// Show a grid of smiles for selection
// (pass smile_id to a calling form)
//
// $fieldtype: 1=select form field; 0=input form field
// $formname name of the calling form
// $idfield name of the field that contain smile_id
// $fsubmit 1=submit calling form after selection
// ------------------------------------------------------------
function F_select_smiles($formname, $idfield, $fieldtype, $fsubmit) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if(!F_count_rows(K_TABLE_EMOTICONS)) { //if the table is void
		echo "<h2>".$l['m_databasempty']."</h2>";
	}
	else {
		echo "<p><b>";
		echo $l['m_select'];
		echo "</b></p>";
		$selectindex=0;
		$sql = "SELECT * FROM ".K_TABLE_EMOTICONS." ORDER BY smile_name";
		
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				if(!$fieldtype) { //select form field
					echo "<a href=\"javascript:window.opener.document.".$formname.".".$idfield.".selectedIndex=".$selectindex++.";";
				}
				elseif($fieldtype==1) { //input form field
					echo "<a href=\"javascript:window.opener.document.".$formname.".".$idfield.".value=".$m['smile_id'].";";
				}
				else { //aiocp code insert
					echo "<a href=\"javascript:window.opener.document.".$formname.".".$idfield.".value=window.opener.document.".$formname.".".$idfield.".value+'[smile=".$m['smile_id']."/]';";
				}
				
				if($fsubmit) {
					echo "window.opener.document.".$formname.".submit();";
				}
				else { //give foucus to $idfield to enable onfocus Event
					echo "window.opener.document.".$formname.".".$idfield.".focus();";
				}
				echo "window.close();\">";
				echo "<img name=\"imagesmile_".$m['smile_id']."\" src=\"";
				if(F_is_relative_link($m['smile_link'])) {echo K_PATH_IMAGES_EMOTICONS;}
				echo "".$m['smile_link']."\" border=\"1\" alt=\"".htmlentities($m['smile_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" width=\"".$m['smile_width']."\" height=\"".$m['smile_height']."\" />";
				echo "</a>\n";
			}
		}
		else {
			F_display_db_error();
		}
	}
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
