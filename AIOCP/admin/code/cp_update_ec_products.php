<?php
//============================================================+
// File name   : cp_update_ec_products.php                     
// Begin       : 2002-10-01                                    
// Last Update : 2008-07-06
//                                                             
// Description : Update products data using a text file        
//               with tab separated values.                    
//               The KEY value is product_manufacturer_code    
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

$pagelevel = K_AUTH_ADMIN_CP_UPDATE_EC_PRODUCTS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);
require_once('../../shared/code/cp_functions_ec_documents.'.CP_EXT);

$thispage_title = $l['t_products_updater'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//$_REQUEST['menu_mode'] = "startlongprocess";
			
			$product_date_added = gmdate("Y-m-d H:i:s");
			
			if($_FILES['userfile']['name']) { //upload file
				$filename = $_FILES['userfile']['tmp_name'];
				
				ini_set("memory_limit", "1024M"); // increase memory limit
				set_time_limit(K_SECONDS_IN_HOUR); //extend the maximum execution time to one day
				
				if ($fd = fopen($filename, "r")) {
					if ($rows_content = fread($fd, filesize($filename))) { //read entire file data
						$rows_content = str_replace("\r", "", $rows_content); //remove CR
						$rows_content = explode("\n", $rows_content); //split rows
						
						while(list($key, $row) = each($rows_content)) { //for each row
							$row = trim($row);
							if(!empty($row)) { // if row is not void
								$i = 1;
								$value = explode("\t", $row); //split values in this row
								
								if (!$up_mode) { //build sql update statement
									$sql = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET ";
									if ($up_product_code) {
										$sql .= "product_code='".trim($value[$i++])."',";
									}
									if ($up_product_barcode) {
										$sql .= "product_barcode='".trim($value[$i++])."',";
									}
									if ($up_product_inventory_code) {
										$sql .= "product_inventory_code='".trim($value[$i++])."',";
									}
									if ($up_product_alternative_codes) {
										$sql .= "product_alternative_codes='".trim($value[$i++])."',";
									}
									if ($up_product_category_id) {
										$sql .= "product_category_id='".trim($value[$i++])."',";
									}
									if ($up_product_manufacturer_id) {
										$sql .= "product_manufacturer_id='".trim($value[$i++])."',";
									}
									if ($up_product_manufacturer_link) {
										$sql .= "product_manufacturer_link='".trim($value[$i++])."',";
									}
									if ($up_product_name) {
										$sql .= "product_name='".htmlentities(trim($value[$i++]))."',";
									}
									if ($up_product_description) {
										$p_description = Array($selected_language => htmlentities(trim($value[$i++])));
										$product_description = addslashes(serialize($p_description));
										$sql .= "product_description='".$product_description."',";
									}
									if ($up_product_warranty) {
										$sql .= "product_warranty='".trim($value[$i++])."',";
									}
									if ($up_product_warranty_id) {
										$sql .= "product_warranty_id='".trim($value[$i++])."',";
									}
									if ($up_product_image) {
										$sql .= "product_image='".trim($value[$i++])."',";
									}
									if ($up_product_transportable) {
										$sql .= "product_transportable='".trim($value[$i++])."',";
									}
									if ($up_product_weight_per_unit) {
										$sql .= "product_weight_per_unit='".trim($value[$i++])."',";
									}
									if ($up_product_length) {
										$sql .= "product_length='".trim($value[$i++])."',";
									}
									if ($up_product_width) {
										$sql .= "product_width='".trim($value[$i++])."',";
									}
									if ($up_product_height) {
										$sql .= "product_height='".trim($value[$i++])."',";
									}
									if ($up_product_unit_of_measure_id) {
										$sql .= "product_unit_of_measure_id='".trim($value[$i++])."',";
									}
									if ($up_product_cost) {
										$sql .= "product_cost='".trim($value[$i++])."',";
									}
									if ($up_product_tax) {
										$sql .= "product_tax='".trim($value[$i++])."',";
									}
									if ($up_product_tax2) {
										$sql .= "product_tax2='".trim($value[$i++])."',";
									}
									if ($up_product_tax3) {
										$sql .= "product_tax3='".trim($value[$i++])."',";
									}
									if ($up_product_q_sold) {
										$sql .= "product_q_sold='".trim($value[$i++])."',";
									}
									if ($up_product_q_available) {
										$sql .= "product_q_available='".trim($value[$i++])."',";
									}
									if ($up_product_q_arriving) {
										$sql .= "product_q_arriving='".trim($value[$i++])."',";
									}
									if ($up_product_arriving_time) {
										$sql .= "product_arriving_time='".trim($value[$i++])."',";
									}
									$sql .= "product_date_added='".$product_date_added."'";
									
									$sql .= "WHERE product_manufacturer_code='".$up_code_prefix.trim($value[0]).$up_code_suffix."'";
								}
								else { //build sql insert statement
									$sql = "INSERT IGNORE INTO ".K_TABLE_EC_PRODUCTS." (";
									$sql .= "product_manufacturer_code,";
									if ($up_product_code) {$sql .= "product_code,";}
									if ($up_product_barcode) {$sql .= "product_barcode,";}
									if ($up_product_inventory_code) {$sql .= "product_inventory_code,";}
									if ($up_product_alternative_codes) {$sql .= "product_alternative_codes,";}
									if ($up_product_category_id) {$sql .= "product_category_id,";}
									if ($up_product_manufacturer_id) {$sql .= "product_manufacturer_id,";}
									if ($up_product_manufacturer_link) {$sql .= "product_manufacturer_link,";}
									if ($up_product_name) {$sql .= "product_name,";}
									if ($up_product_description) {$sql .= "product_description,";}
									if ($up_product_warranty) {$sql .= "product_warranty,";}
									if ($up_product_warranty_id) {$sql .= "product_warranty_id,";}
									if ($up_product_image) {$sql .= "product_image,";}
									if ($up_product_transportable) {$sql .= "product_transportable,";}
									if ($up_product_weight_per_unit) {$sql .= "product_weight_per_unit,";}
									if ($up_product_length) {$sql .= "product_length,";}
									if ($up_product_width) {$sql .= "product_width,";}
									if ($up_product_height) {$sql .= "product_height,";}
									if ($up_product_unit_of_measure_id) {$sql .= "product_unit_of_measure_id,";}
									if ($up_product_cost) {$sql .= "product_cost,";}
									if ($up_product_tax) {$sql .= "product_tax,";}
									if ($up_product_tax2) {$sql .= "product_tax2,";}
									if ($up_product_tax3) {$sql .= "product_tax3,";}
									if ($up_product_q_sold) {$sql .= "product_q_sold,";}
									if ($up_product_q_available) {$sql .= "product_q_available,";}
									if ($up_product_q_arriving) {$sql .= "product_q_arriving,";}
									if ($up_product_arriving_time) {$sql .= "product_arriving_time,";}
									
									$sql .= "product_date_added";
									
									$sql .= ") VALUES (";
									
									$sql .= "'".$up_code_prefix.trim($value[0]).$up_code_suffix."',";
									
									if ($up_product_code) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_barcode) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_inventory_code) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_alternative_codes) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_category_id) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_manufacturer_id) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_manufacturer_link) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_name) {
										$sql .= "'".htmlentities(trim($value[$i++]))."',";
									}
									if ($up_product_description) {
										$p_description = Array($selected_language => htmlentities(trim($value[$i++])));
										$product_description = addslashes(serialize($p_description));
										$sql .= "'".$product_description."',";
									}
									if ($up_product_warranty) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_warranty_id) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_image) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_transportable) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_weight_per_unit) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_length) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_width) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_height) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_unit_of_measure_id) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_cost) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_tax) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_tax2) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_tax3) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_q_sold) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_q_available) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_q_arriving) {$sql .= "'".trim($value[$i++])."',";}
									if ($up_product_arriving_time) {$sql .= "'".trim($value[$i++])."',";}
									
									$sql .= "'".$product_date_added."'";
									$sql .= ")";
								}
								//update database
								if(!$r = F_aiocpdb_query($sql, $db)) {
									F_display_db_error();
								}
							}
							echo "".$value[0]."<br />\n"; flush(); //force flush output to browser
						}
						F_print_error("MESSAGE", $l['m_update_done']);
					}
					else {
						F_print_error("ERROR", "".$filename.": ".$l['m_readfile_not']);
					}
				}
				else {
					F_print_error("ERROR", "".$filename.": ".$l['m_openfile_not']);
				}
				fclose ($fd);
			}
		}
		break;
		}

	default :{ 
		break;
		}

} //end of switch


//set default values
if (($menu_mode != $l['w_update']) AND ($menu_mode != unhtmlentities($l['w_update']))) {
	$up_mode = 0;
	$up_code_prefix = "";
	$up_code_suffix = "";
	$up_product_code = 0;
	$up_product_barcode = 0;
	$up_product_inventory_code = 0;
	$up_product_alternative_codes = 0;
	$up_product_category_id = 0;
	$up_product_manufacturer_id = 0;
	$up_product_manufacturer_link = 0;
	$up_product_name = 0;
	$up_product_description = 0;
	$up_product_warranty = 0;
	$up_product_warranty_id = 0;
	$up_product_image = 0;
	$up_product_transportable = 0;
	$up_product_weight_per_unit = 0;
	$up_product_length = 0;
	$up_product_width = 0;
	$up_product_height = 0;
	$up_product_unit_of_measure_id = 0;
	$up_product_cost = 1;
	$up_product_tax = 0;
	$up_product_tax2 = 0;
	$up_product_tax3 = 0;
	$up_product_q_sold = 0;
	$up_product_q_available = 0;
	$up_product_q_arriving = 0;
	$up_product_arriving_time = 0;
}
?>
   
<p><?php echo $l['d_products_updater']; ?></p>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_productupdater" id="form_productupdater">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- Upload file ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_up_products_upload'); ?></b></td>
<td class="fillOE" colspan="2">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_mode', ''); ?></b></td>
<td class="fillEE" colspan="2">
<?php
echo "<input type=\"radio\" name=\"up_mode\" value=\"0\"";
if(!$up_mode) {echo " checked=\"checked\"";}
echo " />".F_display_field_name('w_update', 'h_up_products_update')."&nbsp;";

echo "<input type=\"radio\" name=\"up_mode\" value=\"1\"";
if($up_mode) {echo " checked=\"checked\"";}
echo " />".F_display_field_name('w_insert', 'h_up_products_insert')."&nbsp;";
?>
</td>
</tr>


<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_code_prefix', 'h_code_prefix'); ?></b></td>
<td class="fillOE" colspan="2">
<input type="text" name="up_code_prefix" id="up_code_prefix" value="<?php echo $up_code_prefix; ?>" size="10" maxlength="255" />
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_code_suffix', 'h_code_suffix'); ?></b></td>
<td class="fillEE" colspan="2">
<input type="text" name="up_code_suffix" id="up_code_suffix" value="<?php echo $up_code_suffix; ?>" size="10" maxlength="255" />
</td>
</tr>

<tr class="fillO">
<td class="fillOO" colspan="2"><hr /></td>
<td class="fillOE" ><b><?php echo F_display_field_name('w_fields_to_update', 'h_up_products_fields_to_update'); ?></b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_code', 'h_productsed_code'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_code\" id=\"up_product_code\" value=\"1\"";
if($up_product_code) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_code</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_barcode', 'h_productsed_barcode'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_barcode\" id=\"up_product_barcode\" value=\"1\"";
if($up_product_barcode) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_barcode</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_inventory_code', 'h_productsed_inventory_code'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_inventory_code\" id=\"up_product_inventory_code\" value=\"1\"";
if($up_product_inventory_code) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_inventory_code</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_alternative_codes', 'h_productsed_alternative_codes'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_alternative_codes\" id=\"up_product_alternative_codes\" value=\"1\"";
if($up_product_alternative_codes) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_alternative_codes</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_productscat_select'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_category_id\" id=\"up_product_category_id\" value=\"1\"";
if($up_product_category_id) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_category_id</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_manufacturer', 'h_productsed_manufacturer'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_manufacturer_id\" id=\"up_product_manufacturer_id\" value=\"1\"";
if($up_product_manufacturer_id) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_manufacturer_id</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_link', 'h_productsed_manufacturer_link'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_manufacturer_link\" id=\"up_product_manufacturer_link\" value=\"1\"";
if($up_product_manufacturer_link) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_manufacturer_link</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_productsed_product'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_name\" id=\"up_product_name\" value=\"1\"";
if($up_product_name) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_name</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_description', 'h_productsed_description'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_description\" id=\"up_product_description\" value=\"1\"";
if($up_product_description) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_description</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_warranty_time', 'h_productsed_warranty_time'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_warranty\" id=\"up_product_warranty\" value=\"1\"";
if($up_product_warranty) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_warranty</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_warranty', 'h_productsed_warranty'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_warranty_id\" id=\"up_product_warranty_id\" value=\"1\"";
if($up_product_warranty_id) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_warranty_id</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_image', 'h_productsed_image'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_image\" id=\"up_product_image\" value=\"1\"";
if($up_product_image) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_image</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_transportable', 'h_productsed_transportable'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_transportable\" id=\"up_product_transportable\" value=\"1\"";
if($up_product_transportable) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_transportable</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_packaged_weight', 'h_productsed_weight'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_weight_per_unit\" id=\"up_product_weight_per_unit\" value=\"1\"";
if($up_product_weight_per_unit) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_weight_per_unit</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_length', 'h_productsed_length'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_length\" id=\"up_product_length\" value=\"1\"";
if($up_product_length) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_length</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_width', 'h_productsed_width'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_width\" id=\"up_product_width\" value=\"1\"";
if($up_product_width) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_width</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_height', 'h_productsed_height'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_height\" id=\"up_product_height\" value=\"1\"";
if($up_product_height) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_height</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_unit', 'h_productsed_unit'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_unit_of_measure_id\" id=\"up_product_unit_of_measure_id\" value=\"1\"";
if($up_product_unit_of_measure_id) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_unit_of_measure_id</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_cost_per_unit', 'h_productsed_cost'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_cost\" id=\"up_product_cost\" value=\"1\"";
if($up_product_cost) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_cost</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_ec_tax', 'h_productsed_tax'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_tax\" id=\"up_product_tax\" value=\"1\"";
if($up_product_tax) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_tax</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_ec_tax2', 'h_productsed_tax'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_tax2\" id=\"up_product_tax2\" value=\"0\"";
if($up_product_tax2) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_tax2</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_ec_tax3', 'h_productsed_tax'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_tax3\" id=\"up_product_tax3\" value=\"0\"";
if($up_product_tax3) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_tax3</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_sold_quantity', 'h_productsed_sold_quantity'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_q_sold\" id=\"up_product_q_sold\" value=\"1\"";
if($up_product_q_sold) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">product_q_sold</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_quantity', 'h_productsed_quantity'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_q_available\" id=\"up_product_q_available\" value=\"1\"";
if($up_product_q_available) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">product_q_available</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_arriving_quantity', 'h_productsed_arriving_quantity'); ?></b></td>
<td class="fillOE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_q_arriving\" id=\"up_product_q_arriving\" value=\"1\"";
if($up_product_q_arriving) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillOO">up_product_q_arriving</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_arriving_time', 'h_productsed_arriving_time'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"up_product_arriving_time\" id=\"up_product_arriving_time\" value=\"1\"";
if($up_product_arriving_time) {echo " checked=\"checked\"";}
echo " />";?>
</td>
<td class="fillEO">arriving_time</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
F_submit_button("form_productupdater","menu_mode",$l['w_update']); 
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
