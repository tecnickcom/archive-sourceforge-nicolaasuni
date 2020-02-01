<?php
//============================================================+
// File name   : cp_functions_countries.php
// Begin       : 2001-11-01
// Last Update : 2007-02-08
// 
// Description : Functions for countries
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
// Show a grid of flags for selection
// (pass selected country flag to a calling form)
//
// $fieldtype: 1=select form field; 0=input form field
// $formname name of the calling form
// $idfield name of the field that contain country_id
// $fsubmit 1=submit calling form after selection
// ------------------------------------------------------------
function F_select_country($formname, $idfield, $fieldtype, $fsubmit) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	
	if(!F_count_rows(K_TABLE_COUNTRIES)) { //if the table is void
		echo "<h2>".$l['m_databasempty']."</h2>";
	}
	else {
		echo "<p><b>";
		echo $l['m_select'];
		echo "</b></p>";
		$selectindex=0;
		$sql = "SELECT * FROM ".K_TABLE_COUNTRIES." ORDER BY country_name";
		
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				if(!$fieldtype) { //select form field
					echo "<a href=\"javascript:window.opener.document.".$formname.".".$idfield.".selectedIndex=".$selectindex++.";";
				}
				elseif($fieldtype==1) { //input form field
					echo "<a href=\"javascript:window.opener.document.".$formname.".".$idfield.".value=".$m['country_id'].";";
				}
				else { //aiocp code insert
					echo "<a href=\"javascript:window.opener.document.".$formname.".".$idfield.".value=window.opener.document.".$formname.".".$idfield.".value+'[flag=".$m['country_id']."/]';";
				}
				
				if($fsubmit) {
					echo "window.opener.document.".$formname.".submit();";
				}
				else { //give foucus to $idfield to enable onfocus Event
					echo "window.opener.document.".$formname.".".$idfield.".focus();";
				}
				echo "window.close();\">";
				echo "<img name=\"imagecountry_".$m['country_id']."\" src=\"";
				if(F_is_relative_link($m['country_flag'])) {echo K_PATH_IMAGES_FLAGS;}
				echo "".$m['country_flag']."\" border=\"1\" alt=\"".htmlentities($m['country_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" width=\"".$m['country_width']."\" height=\"".$m['country_height']."\" />";
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
