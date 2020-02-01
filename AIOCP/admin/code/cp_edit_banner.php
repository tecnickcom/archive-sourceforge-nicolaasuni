<?php
//============================================================+
// File name   : cp_edit_banner.php                            
// Begin       : 2002-04-29                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit banners                                  
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_BANNER;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$thispage_title = $l['t_banner_editor'];

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
		<input type="hidden" name="banner_id" id="banner_id" value="<?php echo $banner_id; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_BANNERS." WHERE banner_id=".$banner_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			//delete banner stats
			$sql = "DELETE FROM ".K_TABLE_BANNERS_STATS." WHERE banstat_banner_id=".$banner_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$banner_id=FALSE;
		}
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_BANNERS, "banner_name='".$banner_name."'", "banner_id", $banner_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_BANNERS." SET 
					banner_name='".$banner_name."',
					banner_customer_id='".$banner_customer_id."',
					banner_language='".$banner_language."',
					banner_enabled='".$banner_enabled."',
					banner_code='".$banner_code."',
					banner_link='".$banner_link."',
					banner_zone='".$banner_zone."',
					banner_start_date='".$banner_start_date."',
					banner_end_date='".$banner_end_date."',
					banner_max_views='".$banner_max_views."',
					banner_weight='".$banner_weight."',
					banner_cpm='".$banner_cpm."',
					banner_cpc='".$banner_cpc."',
					banner_views_stats='".$banner_views_stats."',
					banner_clicks_stats='".$banner_clicks_stats."'
					WHERE banner_id=".$banner_id."";
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
			//check if links is unique
			$sql = "SELECT banner_id FROM ".K_TABLE_BANNERS." WHERE banner_name='".$banner_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_BANNERS." (
					banner_name,
					banner_customer_id,
					banner_language,
					banner_enabled,
					banner_code,
					banner_link,
					banner_zone,
					banner_start_date,
					banner_end_date,
					banner_max_views,
					banner_weight,
					banner_cpm,
					banner_cpc,
					banner_views_stats,
					banner_clicks_stats
				) VALUES (
					'".$banner_name."',
					'".$banner_customer_id."',
					'".$banner_language."',
					'".$banner_enabled."',
					'".$banner_code."',
					'".$banner_link."',
					'".$banner_zone."',
					'".$banner_start_date."',
					'".$banner_end_date."',
					'".$banner_max_views."',
					'".$banner_weight."',
					'".$banner_cpm."',
					'".$banner_cpc."',
					'".$banner_views_stats."',
					'".$banner_clicks_stats."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$banner_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$banner_name = "";
		$banner_language = $default_language;
		$banner_enabled = 0;
		$banner_code = "";
		$banner_link = "";
		$banner_zone = 1;
		$banner_start_date = gmdate("Y-m-d");
		$banner_end_date = gmdate("Y-m-d", time() + K_SECONDS_IN_MONTH);
		$banner_max_views = "";
		$banner_weight = "";
		$banner_cpm = "";
		$banner_cpc = "";
		$banner_views_stats = 0;
		$banner_clicks_stats = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch


// Initialize variables
if(!isset($banner_customer_id) OR (!$banner_customer_id)) {
	$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY." ORDER BY company_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$banner_customer_id = $m['company_id'];
		} else {
			$banner_customer_id = 0;
		}
	}
	else {
		F_display_db_error();
	}
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($banner_id) OR (!$banner_id) OR (isset($changecustomer) AND $changecustomer)) {
			$sql = "SELECT * FROM ".K_TABLE_BANNERS." WHERE banner_customer_id='".$banner_customer_id."' ORDER BY banner_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_BANNERS." WHERE banner_id=".$banner_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$banner_id = $m['banner_id'];
				$banner_name = $m['banner_name'];
				$banner_customer_id = $m['banner_customer_id'];
				$banner_language = $m['banner_language'];
				$banner_enabled = $m['banner_enabled'];
				$banner_code = $m['banner_code'];
				$banner_link = $m['banner_link'];
				$banner_zone = $m['banner_zone'];
				$banner_start_date = $m['banner_start_date'];
				$banner_end_date = $m['banner_end_date'];
				$banner_max_views = $m['banner_max_views'];
				$banner_weight = $m['banner_weight'];
				$banner_cpm = $m['banner_cpm'];
				$banner_cpc = $m['banner_cpc'];
				$banner_views_stats = $m['banner_views_stats'];
				$banner_clicks_stats = $m['banner_clicks_stats'];
			}
			else {
				$banner_name = "";
				$banner_language = $selected_language;
				$banner_enabled = 0;
				$banner_code = "";
				$banner_link = "";
				$banner_zone = 1;
				$banner_start_date = gmdate("Y-m-d");
				$banner_end_date = gmdate("Y-m-d", time() + K_SECONDS_IN_MONTH);
				$banner_max_views = "";
				$banner_weight = "";
				$banner_cpm = "";
				$banner_cpc = "";
				$banner_views_stats = 0;
				$banner_clicks_stats = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_bannereditor" id="form_bannereditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="banner_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT CUSTOMER ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_company', 'h_bannered_company'); ?></b></td>
<td class="fillOE">
<select name="banner_customer_id" id="banner_customer_id" size="0" onchange="document.form_bannereditor.changecustomer.value=1; document.form_bannereditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY." ORDER BY company_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['company_id']."\"";
		if($m['company_id'] == $banner_customer_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT CUSTOMER ==================== -->

<!-- SELECT links ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_banner', 'h_bannered_banner'); ?></b></td>
<td class="fillEE">
<select name="banner_id" id="banner_id" size="0" onchange="document.form_bannereditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_BANNERS." WHERE banner_customer_id='".$banner_customer_id."' ORDER BY banner_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['banner_id']."\"";
		if($m['banner_id'] == $banner_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['banner_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT links ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_bannered_name'); ?></b></td>
<td class="fillEE"><input type="text" name="banner_name" id="banner_name" value="<?php echo htmlentities($banner_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_bannered_language'); ?></b></td>
<td class="fillOE">
<select name="banner_language" id="banner_language" size="0">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $banner_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_enabled', 'h_bannered_enabled'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"banner_enabled\" value=\"1\"";
if($banner_enabled) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"banner_enabled\" value=\"0\"";
if(!$banner_enabled) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_code', 'h_bannered_code'); ?></b>
<br />
<?php 
$doc_charset = $l['a_meta_charset'];
F_html_button("page","form_bannereditor","banner_code", $doc_charset); 

$current_ta_code = $banner_code;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE"><textarea cols="30" rows="5" name="banner_code" id="banner_code"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_bannered_link'); ?></b></td>
<td class="fillEE"><input type="text" name="banner_link" id="banner_link" value="<?php echo $banner_link; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_zone', 'h_bannered_zone'); ?></b></td>
<td class="fillOE">
<select name="banner_zone" id="banner_zone" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_BANNERS_ZONES." ORDER BY banzone_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['banzone_id']."\"";
		if($m['banzone_id'] == $banner_zone) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['banzone_name']."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date_start', 'h_bannered_date_start'); ?></b></td>
<td class="fillEE"><input type="text" name="banner_start_date" id="banner_start_date" value="<?php echo $banner_start_date; ?>" size="30" maxlength="10" />
<input type="hidden" name="x_banner_start_date" id="x_banner_start_date" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_banner_start_date" id="xl_banner_start_date" value="<?php echo $l['w_date_start']; ?>" /></td>
</tr>


<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_date_end', 'h_bannered_date_end'); ?></b></td>
<td class="fillOE"><input type="text" name="banner_end_date" id="banner_end_date" value="<?php echo $banner_end_date; ?>" size="30" maxlength="10" />
<input type="hidden" name="x_banner_end_date" id="x_banner_end_date" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_banner_end_date" id="xl_banner_end_date" value="<?php echo $l['w_date_end']; ?>" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_max_views', 'h_bannered_max_views'); ?></b></td>
<td class="fillEE"><input type="text" name="banner_max_views" id="banner_max_views" value="<?php echo $banner_max_views; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_weight', 'h_bannered_weight'); ?></b></td>
<td class="fillOE"><input type="text" name="banner_weight" id="banner_weight" value="<?php echo $banner_weight; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_view_cost', 'h_banner_cpm'); ?></b></td>
<td class="fillEE"><input type="text" name="banner_cpm" id="banner_cpm" value="<?php echo $banner_cpm; ?>" size="30" maxlength="255" /> [<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>]</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_click_cost', 'h_banner_cpc'); ?></b></td>
<td class="fillOE"><input type="text" name="banner_cpc" id="banner_cpc" value="<?php echo $banner_cpc; ?>" size="30" maxlength="255" /> [<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>]</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_views', 'h_bannered_views'); ?></b></td>
<td class="fillEE"><input type="text" name="banner_views_stats" id="banner_views_stats" value="<?php echo $banner_views_stats; ?>" size="30" maxlength="255" readonly="readonly" disabled="disabled" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_clicks', 'h_bannered_clicks'); ?></b></td>
<td class="fillOE"><input type="text" name="banner_clicks_stats" id="banner_clicks_stats" value="<?php echo $banner_clicks_stats; ?>" size="30" maxlength="255" readonly="readonly" disabled="disabled" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="changecustomer" id="changecustomer" value="0" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($banner_id) AND ($banner_id > 0)) {
	F_submit_button("form_bannereditor","menu_mode",$l['w_update']); 
	F_submit_button("form_bannereditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_bannereditor","menu_mode",$l['w_add']); 
F_submit_button("form_bannereditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_bannereditor.banner_id.focus();
//]]>
</script>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
