<?php
//============================================================+
// File name   : cp_edit_reviews.php                           
// Begin       : 2001-11-29                                    
// Last Update : 2008-07-07
//                                                             
// Description : Edit Reviews                                  
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_REVIEWS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);

$thispage_title = $l['t_reviews_editor'];

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
		F_stripslashes_formfields(); // Delete
		$sql = "DELETE FROM ".K_TABLE_REVIEWS." WHERE review_id=".$review_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$review_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_REVIEWS, "review_category='".$review_category."' AND review_product_name='".$review_product_name."'", "review_id", $review_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {$review_image = F_upload_file("userfile", K_PATH_IMAGES_REVIEWS);} //upload
				$review_text = addslashes(serialize($r_text));
				$sql = "UPDATE IGNORE ".K_TABLE_REVIEWS." SET 
				review_category='".$review_category."', 
				review_date='".$review_date."', 
				review_author_name='".$review_author_name."', 
				review_author_email='".$review_author_email."', 
				review_product_name='".$review_product_name."', 
				review_product_link='".$review_product_link."', 
				review_manuf_name='".$review_manuf_name."', 
				review_manuf_link='".$review_manuf_link."', 
				review_rating='".$review_rating."', 
				review_image='".$review_image."', 
				review_text='".$review_text."' 
				WHERE review_id=".$review_id."";
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
			$sql = "SELECT review_id FROM ".K_TABLE_REVIEWS." WHERE review_category='".$review_category."' AND review_product_name='".$review_product_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //check link validity
				//add item
				if($_FILES['userfile']['name']) {$review_image = F_upload_file("userfile", K_PATH_IMAGES_REVIEWS);} //upload
				$review_text = addslashes(serialize($r_text));
				$review_date = gmdate("Y-m-d"); // get the actual date
				$sql = "INSERT IGNORE INTO ".K_TABLE_REVIEWS." (
				review_category, 
				review_date, 
				review_author_name, 
				review_author_email, 
				review_product_name, 
				review_product_link, 
				review_manuf_name, 
				review_manuf_link, 
				review_rating, 
				review_image, 
				review_text
				) VALUES (
				'".$review_category."', 
				'".$review_date."', 
				'".$review_author_name."', 
				'".$review_author_email."', 
				'".$review_product_name."', 
				'".$review_product_link."', 
				'".$review_manuf_name."', 
				'".$review_manuf_link."', 
				'".$review_rating."', 
				'".$review_image."', 
				'".$review_text."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$review_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$review_date = gmdate("Y-m-d"); // get the actual date
		$review_author_name = "";
		$review_author_email = "";
		$review_product_name = "";
		$review_product_link = "";
		$review_manuf_name = "";
		$review_manuf_link = "";
		$review_rating = "";
		$review_image = K_BLANK_IMAGE;
		$r_text = array();
		break;
		}

	default :{ 
		break;
		}

} //end of switch


// Initialize variables
if(!isset($review_category) OR (!$review_category)) {
	$sql = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." ORDER BY revcat_sub_id,revcat_position LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$review_category = $m['revcat_id'];
		}
		else {
			$review_category = false;
		}
	}
	else {
		F_display_db_error();
	}
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if((!isset($review_id) OR (!$review_id)) OR (isset($changecategory) AND $changecategory)) {
			$sql = "SELECT * FROM ".K_TABLE_REVIEWS." WHERE review_category=".$review_category." ORDER BY review_product_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_REVIEWS." WHERE review_id=".$review_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$review_id = $m['review_id'];
				$review_category = $m['review_category'];
				$review_date = $m['review_date'];
				$review_author_name = $m['review_author_name'];
				$review_author_email = $m['review_author_email'];
				$review_product_name = $m['review_product_name'];
				$review_product_link = $m['review_product_link'];
				$review_manuf_name = $m['review_manuf_name'];
				$review_manuf_link = $m['review_manuf_link'];
				$review_rating = $m['review_rating'];
				$review_image = $m['review_image'];
				$review_text = $m['review_text'];
				$r_text = unserialize($review_text);
			}
			else {
				$review_date = gmdate("Y-m-d"); // get the actual date
				$review_author_name = "";
				$review_author_email = "";
				$review_product_name = "";
				$review_product_link = "";
				$review_manuf_name = "";
				$review_manuf_link = "";
				$review_rating = "";
				$review_image = K_BLANK_IMAGE;
				$r_text = array();
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_reviewseditor" id="form_reviewseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="review_product_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_reviewscat_select'); ?></b></td>
<td class="fillOE" colspan="2">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="review_category" id="review_category" size="0" onchange="document.form_reviewseditor.changecategory.value=1; document.form_reviewseditor.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "changecategory=1&amp;";
$noscriptlink .= "review_category=";
F_form_select_tree($review_category, false, K_TABLE_REVIEWS_CATEGORIES, "revcat", $noscriptlink);
?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT links ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_review', 'h_reviewed_select'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="review_id" id="review_id" size="0" onchange="document.form_reviewseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_REVIEWS." WHERE review_category=".$review_category." ORDER BY review_product_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['review_id']."\"";
		if($m['review_id'] == $review_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['review_product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<td class="fillOE" colspan="2"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_product', 'h_reviewed_product'); ?></b></td>
<td class="fillEE"><input type="text" name="review_product_name" id="review_product_name" value="<?php echo htmlentities($review_product_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>


<td class="fillEE" rowspan="10" align="right" valign="top"><img name="imagereview" src="<?php echo K_PATH_IMAGES_REVIEWS; ?><?php echo $review_image; ?>" border="0" alt="" /></td>
</tr>


<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_product_link', 'h_reviewed_link'); ?></b></td>
<td class="fillOE"><input type="text" name="review_product_link" id="review_product_link" value="<?php echo $review_product_link; ?>" size="30" maxlength="255" /></td>
</tr>


<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_image', 'h_reviewed_image'); ?></b></td>
<td class="fillEE"><input type="text" name="review_image" id="review_image" value="<?php echo $review_image; ?>" size="30" maxlength="255" onchange="FJ_show_image2()" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_reviewed_imagedir'); ?></b></td>
<td class="fillOE">
<select name="review_image_dir" id="review_image_dir" size="0" onchange="document.form_reviewseditor.review_image.value=document.form_reviewseditor.review_image_dir.options[document.form_reviewseditor.review_image_dir.selectedIndex].value; FJ_show_image ()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_REVIEWS);
echo "<option value=\"".K_BLANK_IMAGE."\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $review_image) {
				echo " selected=\"selected\"";
			}
			echo ">".$file."</option>\n";
		}
	}
closedir($handle);
?>
</select>
</td>
</tr>

<!-- Upload file ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_reviewed_imageup'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_manufacturer', 'h_reviewed_manufacturer'); ?></b></td>
<td class="fillOE"><input type="text" name="review_manuf_name" id="review_manuf_name" value="<?php echo htmlentities($review_manuf_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_manufacturer_link', 'h_reviewed_manufacturer_link'); ?></b></td>
<td class="fillEE"><input type="text" name="review_manuf_link" id="review_manuf_link" value="<?php echo $review_manuf_link; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_author', 'h_reviewed_author'); ?></b></td>
<td class="fillOE"><input type="text" name="review_author_name" id="review_author_name" value="<?php echo htmlentities($review_author_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_author_email', 'h_reviewed_author_email'); ?></b></td>
<td class="fillEE"><input type="text" name="review_author_email" id="review_author_email" value="<?php echo $review_author_email; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_rating', 'h_reviewed_rating'); ?></b></td>
<td class="fillOE">
<select name="review_rating" id="review_rating" size="0">
<?php
for ($rvalue=0;$rvalue<=100;$rvalue++) {
	echo "<option value=\"".$rvalue."\"";
	if($rvalue == $review_rating) {echo " selected=\"selected\"";}
	echo ">".$rvalue."</option>\n";
}
?>
</select>
</td>
</tr>

<!-- iterate for each language ==================== -->
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\"><hr /></td>";
			echo "<td class=\"fillEE\" colspan=\"2\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_text', 'h_reviewed_text')."</b><br />";
			
			$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
			if ($doc_charset) {
				$doc_charset_url = "&amp;charset=".$doc_charset;
			}
			else {
				$doc_charset_url = "";
			}
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_reviewseditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
if (isset($r_text[$m['language_code']])) {
	$current_ta_code = $r_text[$m['language_code']];
} else {
	$current_ta_code = "";
}
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillOE\" colspan=\"2\"><textarea cols=\"50\" rows=\"5\" name=\"r_text[".$m['language_code']."]\" id=\"r_text_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END iterate for each language ==================== -->

<?php
if (isset($review_category) AND ($review_category > 0)) {
?>
<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE" colspan="2"><a href="cp_edit_review_categories.<?php echo CP_EXT; ?>?revcat_id=<?php echo $review_category; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_reviews_categories_editor']; ?></b></a></td>
</tr>
<?php
}
?>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="review_date" id="review_date" value="<?php echo $review_date; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($review_id) AND ($review_id > 0)) {
	F_submit_button("form_reviewseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_reviewseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_reviewseditor","menu_mode",$l['w_add']); 
F_submit_button("form_reviewseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_reviewseditor.review_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_reviewseditor.elements.length;i++) {
		if(what == document.form_reviewseditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}

function FJ_show_image (){
	document.images.imagereview.src= "<?php echo K_PATH_IMAGES_REVIEWS; ?>"+document.form_reviewseditor.review_image_dir.options[document.form_reviewseditor.review_image_dir.selectedIndex].value;
}

function FJ_show_image2 (){
	document.images.imagereview.src= "<?php echo K_PATH_IMAGES_REVIEWS; ?>"+document.form_reviewseditor.review_image.value;
}
//]]>
</script>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
