<?php
//============================================================+
// File name   : cp_functions_icons.php                        
// Begin       : 2001-09-07                                    
// Last Update : 2008-07-06
//                                                             
// Description : Functions for Icons                           
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
// Show a grid of icons for selection
// (pass icon_id to a calling form)
//
// $fieldtype: 0=select form field; 1=input form field
// $formname name of the calling form
// $idfield name of the field that contain icon_id or icon_link
// $fsubmit 1=submit calling form after selection
// ------------------------------------------------------------
function F_select_icons($icontable, $iconpath, $formname, $idfield, $fieldtype, $fsubmit) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
		
	if(!F_count_rows($icontable)) { //if the table is void
		echo "<h2>".$l['m_databasempty']."</h2>";
	}
	else {
		echo "<p><b>";
		echo $l['m_select'];
		echo "</b></p>";
		$selectindex=0;
		$sql = "SELECT * FROM ".$icontable." ORDER BY icon_name";
		
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				if(!$fieldtype) { //select form field
					echo "<a href=\"javascript:window.opener.document.".$formname.".".$idfield.".selectedIndex=".$selectindex++.";";
				}
				else { //input form field
					echo "<a href=\"javascript:window.opener.document.".$formname.".".$idfield.".value=".$m['icon_id'].";";
				}
				if($fsubmit) {
					echo "window.opener.document.".$formname.".submit();";
				}
				else { //give foucus to $idfield to enable onfocus Event
					echo "window.opener.document.".$formname.".".$idfield.".focus();";
				}
				echo "window.close();\">";
				
				echo "<img name=\"imageicon_".$m['icon_id']."\" src=\"";
				if(F_is_relative_link($m['icon_link'])) {
					echo $iconpath;
				}
				echo "".$m['icon_link']."\" border=\"1\" alt=\"".$m['icon_name']."\" width=\"".$m['icon_width']."\" height=\"".$m['icon_height']."\" />";
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
