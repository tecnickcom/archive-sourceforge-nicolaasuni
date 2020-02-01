<?php
//============================================================+
// File name   : cp_edit_ec_vat.php                            
// Begin       : 2003-02-13                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit VAT percentages by country and user type 
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
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_VAT;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_vat_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="vat_id" id="vat_id" value="<?php echo $vat_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		<?php
		break;
	}

	case 'forcedelete':{
		F_stripslashes_formfields(); // Delete
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed
			$sql = "DELETE FROM ".K_TABLE_EC_VAT." WHERE vat_id=".$vat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$vat_id=FALSE;
		}
		break;	
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_VAT, "vat_name='".$vat_name."'", "vat_id", $vat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$vat_consumer = serialize($usr);
				$vat_company = serialize($cmp);
				$vat_cmptype = serialize($cmptype);
				$sql = "UPDATE IGNORE ".K_TABLE_EC_VAT." SET 
				vat_name='".$vat_name."',
				vat_consumer='".$vat_consumer."',
				vat_company='".$vat_company."',
				vat_cmptype='".$vat_cmptype."'
				WHERE vat_id=".$vat_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			$sql = "SELECT vat_id FROM ".K_TABLE_EC_VAT." WHERE vat_name='".$vat_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$vat_consumer = serialize($usr);
				$vat_company = serialize($cmp);
				$vat_cmptype = serialize($cmptype);
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_VAT." (
				vat_name,
				vat_consumer,
				vat_company,
				vat_cmptype
				) VALUES (
				'".$vat_name."',
				'".$vat_consumer."',
				'".$vat_company."',
				'".$vat_cmptype."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$vat_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$vat_name = "";
		$vat_consumer = "";
		$vat_company = "";
		$vat_cmptype = "";
		$usr = array();
		$cmp = array();
		$cmptype = array();
	break;
		}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($vat_id) OR (!$vat_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_VAT." ORDER BY vat_name LIMIT 1";
		}	else {
			$sql = "SELECT * FROM ".K_TABLE_EC_VAT." WHERE vat_id=".$vat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$vat_id = $m['vat_id'];
				$vat_name = $m['vat_name'];
				$vat_consumer = $m['vat_consumer'];
				$vat_company = $m['vat_company'];
				$vat_cmptype = $m['vat_cmptype'];
				$usr = unserialize($vat_consumer);
				$cmp = unserialize($vat_company);
				$cmptype = unserialize($vat_cmptype);
			}
			else {
				$vat_name = "";
				$vat_consumer = "";
				$vat_company = "";
				$vat_cmptype = "";
				$usr = array();
				$cmp = array();
				$cmptype = array();
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_vateditor" id="form_vateditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="vat_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_ec_tax', 'h_vat_select'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="vat_id" id="vat_id" size="0" onchange="document.form_vateditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_VAT." ORDER BY vat_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['vat_id']."\"";
		if($m['vat_id'] == $vat_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['vat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_vat_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="vat_name" id="vat_name" value="<?php echo $vat_name; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO"><b><?php echo $l['w_country']; ?></b></td>
<td class="fillEE" align="center"><b><?php echo $l['w_user']."<br />".$l['w_ec_tax']." [%]"; ?></b></td>
<td class="fillEO" align="center"><b><?php echo $l['w_company']."<br />".$l['w_ec_tax']." [%]"; ?></b></td>
<?php
//company type VAT
$sqlct = "SELECT * FROM ".K_TABLE_USERS_COMPANY_TYPES." ORDER BY comptype_name";
if($rct = F_aiocpdb_query($sqlct, $db)) {
	while($mct = F_aiocpdb_fetch_array($rct)) {
		//change style for each column
		if($colodd) {$colclass = "O"; $colodd=0;}
		else {$colclass = "E"; $colodd=1;}
		echo "<td class=\"fillE".$colclass."\" align=\"center\"><b>".F_decode_field($mct['comptype_name'])."<br />".$l['w_ec_tax']." [%]</b></td>";
	}
}
else {
	F_display_db_error();
}
?>		

</tr>

<?php
$sql = "SELECT * FROM ".K_TABLE_COUNTRIES." ORDER BY country_name";
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
		
		//country name
		echo "<td class=\"fill".$rowclass."O\"><b>".htmlentities($m['country_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</b></td>";
		
		//user VAT
		if ( (!isset($usr[$m['country_id']][0])) OR (!$usr[$m['country_id']][0])) {
			$usr[$m['country_id']][0]=0;
		}
		echo "<td class=\"fill".$rowclass."E\" align=\"center\">";
		echo "<input type=\"text\" name=\"usr[".$m['country_id']."][0]\" id=\"usr_".$m['country_id']."_0\" value=\"".$usr[$m['country_id']][0]."\" size=\"5\" maxlength=\"10\" />";
		echo "</td>";
		
		//company VAT
		if ( (!isset($cmp[$m['country_id']][0])) OR (!$cmp[$m['country_id']][0])) {
			$cmp[$m['country_id']][0]=0;
		}
		echo "<td class=\"fill".$rowclass."O\" align=\"center\">";
		echo "<input type=\"text\" name=\"cmp[".$m['country_id']."][0]\" id=\"cmp_".$m['country_id']."_0\" value=\"".$cmp[$m['country_id']][0]."\" size=\"5\" maxlength=\"10\" />";
		echo "</td>";
			
		//company type VAT
		$sqlct = "SELECT * FROM ".K_TABLE_USERS_COMPANY_TYPES." ORDER BY comptype_name";
		if($rct = F_aiocpdb_query($sqlct, $db)) {
			while($mct = F_aiocpdb_fetch_array($rct)) {
				//change style for each column
				if($colodd) {$colclass = "O"; $colodd=0;}
				else {$colclass = "E"; $colodd=1;}
				if ( (!isset($cmptype[$m['country_id']][0][$mct['comptype_id']])) OR (!$cmptype[$m['country_id']][0][$mct['comptype_id']])) {
					$cmptype[$m['country_id']][0][$mct['comptype_id']]=0;
				}
				echo "<td class=\"fill".$rowclass.$colclass."\" align=\"center\">";
				echo "<input type=\"text\" name=\"cmptype[".$m['country_id']."][0][".$mct['comptype_id']."]\" id=\"cmptype_".$m['country_id']."_0_".$mct['comptype_id']."\" value=\"".$cmptype[$m['country_id']][0][$mct['comptype_id']]."\" size=\"5\" maxlength=\"10\" />";
				echo "</td>";
			}
		}
		else {
			F_display_db_error();
		}
		
		echo "</tr>";
		
		//list country states inputs
		$sqlcs = "SELECT * FROM ".K_TABLE_COUNTRIES_STATES." WHERE state_country_id='".$m['country_id']."' ORDER BY state_name";
		if($rcs = F_aiocpdb_query($sqlcs, $db)) {
			while($mcs = F_aiocpdb_fetch_array($rcs)) {

				echo "<tr class=\"fill".$rowclass."\">";
			
				//country name
				echo "<td class=\"fill".$rowclass."O\"><b>".htmlentities($m['country_name'], ENT_NOQUOTES, $l['a_meta_charset'])." :: ".htmlentities($mcs['state_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</b></td>";
			
				//user VAT
				if ((!isset($usr[$m['country_id']][$mcs['state_id']])) OR (!$usr[$m['country_id']][$mcs['state_id']])) {
					$usr[$m['country_id']][$mcs['state_id']]=0;
				}
				echo "<td class=\"fill".$rowclass."E\" align=\"center\">";
				echo "<input type=\"text\" name=\"usr[".$m['country_id']."][".$mcs['state_id']."]\" id=\"usr_".$m['country_id']."_".$mcs['state_id']."\" value=\"".$usr[$m['country_id']][$mcs['state_id']]."\" size=\"5\" maxlength=\"10\" />";
				echo "</td>";
			
				//company VAT
				if ((!isset($cmp[$m['country_id']][$mcs['state_id']])) OR (!$cmp[$m['country_id']][$mcs['state_id']])) {
					$cmp[$m['country_id']][$mcs['state_id']]=0;
				}
				echo "<td class=\"fill".$rowclass."O\" align=\"center\">";
				echo "<input type=\"text\" name=\"cmp[".$m['country_id']."][".$mcs['state_id']."]\" id=\"cmp_".$m['country_id']."_".$mcs['state_id']."\" value=\"".$cmp[$m['country_id']][$mcs['state_id']]."\" size=\"5\" maxlength=\"10\" />";
				echo "</td>";
			
				//company type VAT
				$sqlct = "SELECT * FROM ".K_TABLE_USERS_COMPANY_TYPES." ORDER BY comptype_name";
				if($rct = F_aiocpdb_query($sqlct, $db)) {
					while($mct = F_aiocpdb_fetch_array($rct)) {
						//change style for each column
						if($colodd) {$colclass = "O"; $colodd=0;}
						else {$colclass = "E"; $colodd=1;}
						if ((!isset($cmptype[$m['country_id']][$mcs['state_id']][$mct['comptype_id']])) OR (!$cmptype[$m['country_id']][$mcs['state_id']][$mct['comptype_id']])) {
							$cmptype[$m['country_id']][$mcs['state_id']][$mct['comptype_id']]=0;
						}
						echo "<td class=\"fill".$rowclass.$colclass."\" align=\"center\">";
						echo "<input type=\"text\" name=\"cmptype[".$m['country_id']."][".$mcs['state_id']."][".$mct['comptype_id']."]\" id=\"cmptype_".$m['country_id']."_".$mcs['state_id']."_".$mct['comptype_id']."\" value=\"".$cmptype[$m['country_id']][$mcs['state_id']][$mct['comptype_id']]."\" size=\"5\" maxlength=\"10\" />";
						echo "</td>";
					}
				}
				else {
					F_display_db_error();
				}
			
				echo "</tr>";
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
?>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($vat_id) {
	F_submit_button("form_vateditor","menu_mode",$l['w_update']); 
	F_submit_button("form_vateditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_vateditor","menu_mode",$l['w_add']); 
F_submit_button("form_vateditor","menu_mode",$l['w_clear']); 
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
