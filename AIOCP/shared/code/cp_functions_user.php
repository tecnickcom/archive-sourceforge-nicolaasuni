<?php
//============================================================+
// File name   : cp_functions_user.php
// Begin       : 2001-09-28
// Last Update : 2007-10-03
// 
// Description : Functions for User
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

//------------------------------------------------------------
// get user data from ID 
//------------------------------------------------------------
function F_get_user_data($userid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$userid = intval($userid);
	
	$sql = "SELECT * FROM ".K_TABLE_USERS." WHERE user_id='".$userid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$userdata = new stdClass();
			$userdata->id = $m['user_id'];
			$userdata->regdate = $m['user_regdate'];
			$userdata->ip = $m['user_ip'];
			$userdata->name = $m['user_name'];
			$userdata->email = $m['user_email'];
			$userdata->password = $m['user_password'];
			$userdata->language = $m['user_language'];
			$userdata->firstname = $m['user_firstname'];
			$userdata->lastname = $m['user_lastname'];
			$userdata->birthdate = $m['user_birthdate'];
			$userdata->birthplace = $m['user_birthplace'];
			$userdata->fiscalcode = $m['user_fiscalcode'];
			$userdata->level = $m['user_level'];
			$userdata->photo = $m['user_photo'];
			$userdata->signature = $m['user_signature'];
			$userdata->notes = $m['user_notes'];
			$userdata->publicopt = $m['user_publicopt'];
			return $userdata;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

//------------------------------------------------------------
// get user data from ID 
//------------------------------------------------------------
function F_get_user_verification_data($userid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_USERS_VERIFICATION." WHERE user_id='".$userid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$userdata = new stdClass();
			$userdata->id = $m['user_id'];
			$userdata->regdate = $m['user_regdate'];
			$userdata->ip = $m['user_ip'];
			$userdata->name = $m['user_name'];
			$userdata->email = $m['user_email'];
			$userdata->password = $m['user_password'];
			$userdata->language = $m['user_language'];
			$userdata->firstname = $m['user_firstname'];
			$userdata->lastname = $m['user_lastname'];
			$userdata->birthdate = $m['user_birthdate'];
			$userdata->birthplace = $m['user_birthplace'];
			$userdata->fiscalcode = $m['user_fiscalcode'];
			$userdata->level = $m['user_level'];
			$userdata->photo = $m['user_photo'];
			$userdata->signature = $m['user_signature'];
			$userdata->notes = $m['user_notes'];
			$userdata->publicopt = $m['user_publicopt'];
			$userdata->verifycode = $m['user_verifycode'];
			return $userdata;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ----------------------------------------------------------
// get shipping_zone details
// ----------------------------------------------------------
function F_get_user_shipping_address($user_id) {
	global $db, $l, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	$address = false;
	
	if ($user_id > 1) { //if user is not anonymous
		//search if user is a company
		$sql_company = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$user_id." LIMIT 1";
		if($r_company = F_aiocpdb_query($sql_company, $db)) {
			if($customer = F_aiocpdb_fetch_array($r_company)) { //user is a company
				
				//billing address
				$sql_address = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_id=".$customer['company_billing_address_id']." LIMIT 1";
				if($r_address = F_aiocpdb_query($sql_address, $db)) {
					$address = F_aiocpdb_fetch_array($r_address);
				}
				else {
					F_display_db_error();
				}
			}
			else { //user is not a company
				$sql_address = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid=".$user_id." AND address_default=1 LIMIT 1";
				if($r_address = F_aiocpdb_query($sql_address, $db)) {
					$address = F_aiocpdb_fetch_array($r_address);
				}
				else {
					F_display_db_error();
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
	return $address;
}

// ----------------------------------------------------------
// get user document data 
// (name, fiscalcode, shipping address, legal address)
// ----------------------------------------------------------
function F_get_user_document_data($user_id) {
	global $db, $l, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	$userdocdata = new stdClass();
	
	$sqlu = "SELECT user_email FROM ".K_TABLE_USERS." WHERE user_id=".$user_id." LIMIT 1";
	if($ru = F_aiocpdb_query($sqlu, $db)) {
		if($usr = F_aiocpdb_fetch_array($ru)) {
			$userdocdata->email = $usr['user_email'];
		}
	}
	else {
		F_display_db_error();
	}
	
	//search if user is a company
	$sql_company = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$user_id." LIMIT 1";
	if($r_company = F_aiocpdb_query($sql_company, $db)) {
		if($customer = F_aiocpdb_fetch_array($r_company)) { //user is a company
			
			$userdocdata->company = true;
			
			$userdocdata->company_type_id = $customer['company_type_id'];
			if ($userdocdata->company_type_id) { 
				//get company type name and discount
				$sqlct = "SELECT * FROM ".K_TABLE_USERS_COMPANY_TYPES." WHERE comptype_id=".$userdocdata->company_type_id." LIMIT 1";
				if($rct = F_aiocpdb_query($sqlct, $db)) {
					if($mct = F_aiocpdb_fetch_array($rct)) {
						$comptype_name = unserialize($mct['comptype_name']);
						$userdocdata->comptype_name = $comptype_name[$selected_language];
						$userdocdata->comptype_discount = $mct['comptype_discount'];
					}
					else {
						$userdocdata->comptype_name = "";
						$userdocdata->comptype_discount = 0;
					}
				}
				else {
					F_display_db_error();
				}
			}
			
			//legal address
			$sql_address = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_id=".$customer['company_legal_address_id']." LIMIT 1";
			if($r_address = F_aiocpdb_query($sql_address, $db)) {
				if($address = F_aiocpdb_fetch_array($r_address)) {
					$userdocdata->name = $customer['company_name'];
					$userdocdata->l_address = $address['address_address'];
					$userdocdata->l_postcode = $address['address_postcode'];
					$userdocdata->l_city = $address['address_city'];
					$userdocdata->l_state = $address['address_state'];
					$userdocdata->l_country = F_GetCountryName($address['address_countryid']);
					$userdocdata->fiscalcode = $customer['company_fiscalcode'];
					$userdocdata->l_country_id = $address['address_countryid'];
					$userdocdata->l_state_id = 0;
					//get state ID
					$sqlcs = "SELECT * FROM ".K_TABLE_COUNTRIES_STATES." WHERE state_country_id='".$userdocdata->l_country_id."' AND state_name='".strtoupper(trim($userdocdata->l_state))."' LIMIT 1";
					if($rcs = F_aiocpdb_query($sqlcs, $db)) {
						if($mcs = F_aiocpdb_fetch_array($rcs)) {
							$userdocdata->l_state_id = $mcs['state_id'];
							$userdocdata->l_state_name = $mcs['state_name'];
						}
						else {
							$userdocdata->l_state_id = 0;
							$userdocdata->l_state_name = 0;
						}
					}
					else {
						F_display_db_error();
					}
				}
			}
			else {
				F_display_db_error();
			}
			
			//billing address
			$sql_address = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_id=".$customer['company_billing_address_id']." LIMIT 1";
			if($r_address = F_aiocpdb_query($sql_address, $db)) {
				if($address = F_aiocpdb_fetch_array($r_address)) {
					$userdocdata->address = $address['address_address'];
					$userdocdata->postcode = $address['address_postcode'];
					$userdocdata->city = $address['address_city'];
					$userdocdata->state = $address['address_state'];
					$userdocdata->country_id = $address['address_countryid'];
					$userdocdata->country = F_GetCountryName($address['address_countryid']);
				}
			}
			else {
				F_display_db_error();
			}
		}
		else { //user is not a company 
			$userdocdata->company = false;
			
			$sql_user = "SELECT * FROM ".K_TABLE_USERS." WHERE user_id=".$user_id." LIMIT 1";
			if($r_user = F_aiocpdb_query($sql_user, $db)) {
				if($customer = F_aiocpdb_fetch_array($r_user)) {
					
					$sql_address = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid=".$user_id." AND address_default=1 LIMIT 1";
					if($r_address = F_aiocpdb_query($sql_address, $db)) {
						if($address = F_aiocpdb_fetch_array($r_address)) {
							$userdocdata->name = $customer['user_firstname']." ".$customer['user_lastname'];
							$userdocdata->address = $address['address_address'];
							$userdocdata->postcode = $address['address_postcode'];
							$userdocdata->city = $address['address_city'];
							$userdocdata->state = $address['address_state'];
							$userdocdata->country_id = $address['address_countryid'];
							$userdocdata->country = F_GetCountryName($address['address_countryid']);
							$userdocdata->fiscalcode = $customer['user_fiscalcode'];
							
							//get state ID
							$sqlcs = "SELECT * FROM ".K_TABLE_COUNTRIES_STATES." WHERE state_country_id='".$userdocdata->country_id."' AND state_name='".strtoupper(trim($userdocdata->state))."' LIMIT 1";
							if($rcs = F_aiocpdb_query($sqlcs, $db)) {
								if($mcs = F_aiocpdb_fetch_array($rcs)) {
									$userdocdata->state_id = $mcs['state_id'];
									$userdocdata->state_name = $mcs['state_name'];
								}
								else {
									$userdocdata->state_id = 0;
									$userdocdata->state_name = 0;
								}
							}
							else {
								F_display_db_error();
							}	
						}
					}
					else {
						F_display_db_error();
					}
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
	else {
		F_display_db_error();
	}
	
	if (isset($userdocdata)) {
		return $userdocdata;
	}
	return false;
}

//------------------------------------------------------------
// get user user discount (if any) 
//get also the discount associate with company type (if any)
//------------------------------------------------------------
function F_get_user_discount($userid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	$discount = 0;
	
	if (!isset($userid) OR (isset($userid) AND ($userid <= 1))) {
		return 0;
	}
	
	//get company type discount
	$userdata = F_get_user_document_data($userid);
	if (isset($userdata->comptype_discount) AND $userdata->comptype_discount) {
		$discount += $userdata->comptype_discount;
	}
		
	//get user discount
	$sql = "SELECT * FROM ".K_TABLE_EC_USER_DISCOUNT." WHERE discount_userid=".$userid." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$discount = (1 + ($discount/100)) * (1 + ($m['discount_value']/100));
			$discount = ($discount - 1) * 100; //restore discount in percentage
		}
	}
	else {
		F_display_db_error();
	}
	
	return $discount;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
