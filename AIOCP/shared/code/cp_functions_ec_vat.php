<?php
//============================================================+
// File name   : cp_functions_ec_vat.php
// Begin       : 2003-02-13
// Last Update : 2007-10-03
// 
// Description : Functions to handle VAT taxes
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

// ----------------------------------------------------------
// display select options for user documents
// ----------------------------------------------------------
function F_get_vat_value($vat_id, $user_id) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	$tax = 0;
	
	$sql = "SELECT * FROM ".K_TABLE_EC_VAT." WHERE vat_id='".$vat_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			
			$userdata = F_get_user_document_data($user_id);
			
			if (isset($userdata->company_type_id) AND $userdata->company_type_id) {	
				$cmptype = unserialize($m['vat_cmptype']);
				if (isset($cmptype[$userdata->l_country_id][$userdata->l_state_id][$userdata->company_type_id])) {
					$tax = $cmptype[$userdata->l_country_id][$userdata->l_state_id][$userdata->company_type_id];
				} else {
					// DEFAULT VAT TAX
					$tax = $cmptype[1][0][0];
				}
			}
			elseif (isset($userdata->company) AND $userdata->company) {	
				$cmp = unserialize($m['vat_company']);
				if (isset($cmp[$userdata->l_country_id][$userdata->l_state_id])) {
					$tax = $cmp[$userdata->l_country_id][$userdata->l_state_id];
				} else {
					// DEFAULT VAT TAX
					$tax = $cmp[1][0];
				}
			}
			else {
				$usr = unserialize($m['vat_consumer']);
				if (isset($usr[$userdata->country_id][$userdata->state_id])) {
					$tax = $usr[$userdata->country_id][$userdata->state_id];
				} else {
					// DEFAULT VAT TAX
					$tax = $usr[1][0];
				}
			}
		}
	}
	else {
		F_display_db_error();
	}
	
	return $tax;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
