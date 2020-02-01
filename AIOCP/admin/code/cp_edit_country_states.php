<?php
//============================================================+
// File name   : cp_edit_state_states.php                      
// Begin       : 2003-11-05                                    
// Last Update : 2008-07-06
//                                                             
// Description : Add/remove/update states or provices for      
//               selected country                              
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

require_once('../code/cp_functions_upload.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_COUNTRY;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_country_states_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
require_once('../../shared/code/cp_functions_form.'.CP_EXT);
if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete country
		$sql = "DELETE FROM ".K_TABLE_COUNTRIES_STATES." WHERE state_id=".$state_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$state_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update country
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_COUNTRIES_STATES, "state_country_id='".$state_country_id."' AND state_name='".$state_name."'", "state_id", $state_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_COUNTRIES_STATES." SET 
				state_country_id='".$state_country_id."', 
				state_name='".strtoupper($state_name)."'
				WHERE state_id=".$state_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add country
		if($formstatus = F_check_form_fields()) {
			//check if state_name is unique
			$sql = "SELECT state_name FROM ".K_TABLE_COUNTRIES_STATES." WHERE state_country_id='".$state_country_id."' AND state_name='".$state_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_COUNTRIES_STATES." (
				state_country_id,
				state_name
				) VALUES (
				'".$state_country_id."',
				'".strtoupper($state_name)."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$state_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$state_name = "";
		break;
		}

	default :{ 
		break;
		}

} //end of switch


$country_name = "";

if(!$state_country_id) {
	$sql = "SELECT * FROM ".K_TABLE_COUNTRIES." ORDER BY country_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$state_country_id = $m['country_id'];
			$country_name = $m['country_name'];
			$country_flag = $m['country_flag'];
			$country_width = $m['country_width'];
			$country_height = $m['country_height'];
		}
		else {
			$state_country_id = false;
		}
	}
	else {
		F_display_db_error();
	}
}
else { //get country name
	$sql = "SELECT * FROM ".K_TABLE_COUNTRIES." WHERE country_id='".$state_country_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$state_country_id = $m['country_id'];
			$country_name = $m['country_name'];
			$country_flag = $m['country_flag'];
			$country_width = $m['country_width'];
			$country_height = $m['country_height'];
		}
	}
	else {
		F_display_db_error();
	}
}

// Initialize variables
if($formstatus) {
	if ($state_country_id) {
		if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
			if(!isset($state_id) OR (!$state_id)) {
				$sql = "SELECT * FROM ".K_TABLE_COUNTRIES_STATES." WHERE state_country_id='".$state_country_id."' ORDER BY state_name LIMIT 1";
			} else {
				$sql = "SELECT * FROM ".K_TABLE_COUNTRIES_STATES." WHERE state_id=".$state_id." LIMIT 1";
			}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$state_id = $m['state_id'];
					$state_country_id = $m['state_country_id'];
					$state_name = $m['state_name'];
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
	else {
		$state_name = "";
	}
}
if (!isset($state_name)) {
	$state_name = "";
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_stateseditor" id="form_stateseditor">

<input type="hidden" name="state_country_id" id="state_country_id" value="<?php echo $state_country_id; ?>" />

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="state_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />



<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<img name="imagecountry" src="<?php echo K_PATH_IMAGES_FLAGS; ?><?php echo $country_flag; ?>" border="0" alt="" width="<?php echo $country_width; ?>" height="<?php echo $country_height; ?>" /> <?php echo $country_name; ?>
</th>
</tr>

<tr class="edge">
<td class="edge">
<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT country ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_state', 'h_state_select'); ?></b></td>
<td class="fillOE">
<select name="state_id" id="state_id" size="0" onchange="document.form_stateseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_COUNTRIES_STATES." WHERE state_country_id='".$state_country_id."' ORDER BY state_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['state_id']."\"";
		if($m['state_id'] == $state_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['state_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT country ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_state_name'); ?></b></td>
<td class="fillOE"><input type="text" name="state_name" id="state_name" value="<?php echo htmlentities($state_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_country.<?php echo CP_EXT; ?>?country_id=<?php echo $state_country_id; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_country_editor']; ?></b></a></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($state_id) AND $state_id) {
	F_submit_button("form_stateseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_stateseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_stateseditor","menu_mode",$l['w_add']); 
F_submit_button("form_stateseditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->


<!-- Show selected flag image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_flag() {
	document.images.imagecountry.src= "<?php echo K_PATH_IMAGES_FLAGS; ?>"+document.form_stateseditor.state_flag_dir.options[document.form_stateseditor.state_flag_dir.selectedIndex].value;
}

function FJ_show_flag2() {
	document.images.imagecountry.src= "<?php echo K_PATH_IMAGES_FLAGS; ?>"+document.form_stateseditor.state_flag.value;
}

document.form_stateseditor.state_id.focus();
//]]>
</script>
<!-- END Cange focus to state_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
