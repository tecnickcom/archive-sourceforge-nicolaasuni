<?php
//============================================================+
// File name   : cp_functions_ec_products.php
// Begin       : 2002-07-09
// Last Update : 2008-01-01
// 
// Description : Functions for products (ecommerce)
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Tecnick.com s.r.l.
//               Via Della Pace n. 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//
// License: GNU GENERAL PUBLIC LICENSE v.2
//          http://www.gnu.org/copyleft/gpl.html
//============================================================+

// ------------------------------------------------------------
// Display products
// $viewmode: 0=compact(headers only); 1=full 
// $selectedproduct = product to display in full mode while in compact mode
// ------------------------------------------------------------
function F_show_products($product_category_id, $product_manufacturer_id, $viewmode, $selectedproduct, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	global $only_preferred, $term, $submitted, $productssearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_ec_vat.'.CP_EXT);
	
	//initialize variables
	$userlevel = $_SESSION['session_user_level'];
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "product_category_id,product_name";}
	else {$order_field = preg_replace('/[^a-z\_\,]+/i', '', $order_field);}
	
	$valid_order_field = array ('product_category_id,product_name',
	'product_category_id, product_code', 'product_name', 'product_code', 'product_barcode', 'product_category_id', 'product_description', 'product_date_added DESC', 'product_q_sold DESC');
	
	if (!in_array($order_field, $valid_order_field)) {
		$order_field = 'product_category_id,product_name';
	}

	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_EC_PRODUCTS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	// --- ------------------------------------------------------
	if($product_manufacturer_id) {
		$product_manufacturer_id = intval($product_manufacturer_id);
		$sqlm = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." WHERE manuf_id='".$product_manufacturer_id."' LIMIT 1";
		if($rm = F_aiocpdb_query($sqlm, $db)) {
			if($mm = F_aiocpdb_fetch_array($rm)) {
				
				echo "<tr class=\"edge\">";
				echo "<th class=\"edge\" align=\"center\">";
				
				if ($mm['manuf_url']) {
					echo "<a href=\"".$mm['manuf_url']."\" target=\"_blank\">";
				}
				if ($mm['manuf_logo']) {
					echo "<img src=\"".K_PATH_IMAGES_MANUFACTURERS.$mm['manuf_logo']."\" alt=\"".htmlentities($mm['manuf_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" border=\"0\" />";
					echo "<br />";
				}
				echo htmlentities($mm['manuf_name'], ENT_NOQUOTES, $l['a_meta_charset']);
				if ($mm['manuf_url']) {
					echo "</a>";
				}
				echo "</th></tr>";
			}
		}
		else {
			F_display_db_error();
		}
		
		if (!$wherequery) {$wherequery = "WHERE (product_manufacturer_id='".$product_manufacturer_id."')";}
		else {$wherequery .= " AND (product_manufacturer_id='".$product_manufacturer_id."')";}
	}
	
	if($product_category_id) {
		$product_category_id = intval($product_category_id);
		$sqlc = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." WHERE prodcat_id=".$product_category_id." LIMIT 1";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['prodcat_level']>$userlevel) {
					F_print_error("WARNING", $l['m_authorization_deny']);
					F_logout_form();
					return;
				}
				$thisname = F_decode_field($mc['prodcat_name']);
				$thisdesc = F_decode_field($mc['prodcat_description']);
				
				echo "<tr class=\"edge\">";
				echo "<th class=\"edge\" align=\"left\">";
				echo "<img src=\"".K_PATH_IMAGES_PRODUCTS_CATEGORIES."".$mc['prodcat_image']."\" alt=\"".$thisname."\" border=\"0\" align=\"left\" />";
				echo "".htmlentities($thisname, ENT_NOQUOTES, $l['a_meta_charset']).":<br /><i>".$thisdesc."</i>";
				echo "</th></tr>";
				
				/*
				$subcatquery = "";
				// search sub categories
				$sqlsc = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." WHERE prodcat_sub_id=".$product_category_id."";
				if($rsc = F_aiocpdb_query($sqlsc, $db)) {
					while($msc = F_aiocpdb_fetch_array($rsc)) {
						$subcatquery .= " OR product_category_id='".$msc['prodcat_id']."'";
					}
				}
				else {
					F_display_db_error();
				}
				*/
				
				$subcatquery = "";
				$subcatquery = F_select_subcategories($product_category_id);
				
				
				if (!$wherequery) {$wherequery = "WHERE (product_category_id='".$product_category_id."'".$subcatquery.")";}
				else {$wherequery .= " AND (product_category_id='".$product_category_id."'".$subcatquery.")";}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	
	if($r = F_aiocpdb_query($sql, $db)) {
		echo "<tr class=\"edge\">";
		echo "<td class=\"edge\">";
		echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
		
		if ( !is_integer(strpos($wherequery, "product_id")) ) {
			echo "<tr class=\"fillO\">";
			echo "<th class=\"fillO\">".$l['w_code']."</th>";
			echo "<th class=\"fillE\">".$l['w_name']."</th>";
			echo "<th class=\"fillO\">".$l['w_cost']."</th>";
			echo "<th class=\"fillE\">".$l['w_quantity']."</th>";
			echo "<th class=\"fillO\">&nbsp;</th>";
			echo "</tr>";
		}
		
		while($m = F_aiocpdb_fetch_array($r)) {
			
			$unit_name = F_get_unit_name($m['product_unit_of_measure_id']);
			
			//get category data
			if (!$product_category_id) {$catdata = F_get_product_category_data($m['product_category_id']);}
			//check authorization rights
			if (($product_category_id) OR ($userlevel >= $catdata->level)) {
				
				//change style for each row
				if (isset($rowodd) AND ($rowodd)) {
					$rowclass = "O";
					$rowodd = 0;
				} else {
					$rowclass = "E";
					$rowodd = 1;
				}
				
				if(($viewmode)OR($m['product_id'] == $selectedproduct)) { //full mode
					
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\" colspan=\"5\">";
					
					echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" style=\"width:100%\">";
					echo "<tr class=\"edge\">";
					echo "<th class=\"edge\" align=\"left\" colspan=\"2\">";
					
					if ($viewmode == 2) {
						echo "<a class=\"edge\" href=\"javascript:FJ_submit_products_form('".$firstrow."','".urlencode($order_field)."','0',".$m['product_category_id'].",".$m['product_id'].");\">".htmlentities($m['product_code'], ENT_NOQUOTES, $l['a_meta_charset'])." - ".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					
					// noscript alternative for search engines
					echo "<noscript><a href=\"cp_show_ec_products.".CP_EXT."?pid=".$m['product_id']."\">".htmlentities($m['product_code'], ENT_NOQUOTES, $l['a_meta_charset'])." - ".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></noscript>";
					
					}
					else {
						echo "<a class=\"edge\" href=\"cp_show_ec_shopping_cart.".CP_EXT."?npid=".$m['product_id']."\">".htmlentities($m['product_code'], ENT_NOQUOTES, $l['a_meta_charset'])." - ".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					}
					
					if ($userlevel >= K_AUTH_EDIT_PRODUCTS_LEVEL) {
						echo "&nbsp;&nbsp;";
						F_generic_button("edit".$m['product_id'], $l['w_edit'], "location.replace('../../admin/code/cp_edit_ec_products.".CP_EXT."?product_category_id=".$m['product_category_id']."&amp;product_id=".$m['product_id']."')", "../../admin/code/cp_edit_ec_products.".CP_EXT."?product_category_id=".$m['product_category_id']."&amp;product_id=".$m['product_id']."");
					}
					
					echo "&nbsp;&nbsp;";
					//generate a verification code to avoid unauthorized calls to PDF viewer
					$verifycode = F_generate_verification_code($m['product_id'], 4);
					F_generic_button("pdfreport".$m['product_id'], $l['w_report'], "PDFREP=window.open('cp_show_ec_pdf_product_report.".CP_EXT."?product_id=".$m['product_id']."&amp;user_id=".$_SESSION['session_user_id']."&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."','PDFREP','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
					echo "&nbsp;&nbsp;";
					
					//display buy button
					F_generic_button("buy".$m['product_id'], $l['w_buy'], "location.replace('cp_show_ec_shopping_cart.".CP_EXT."?npid=".$m['product_id']."')", "cp_show_ec_shopping_cart.".CP_EXT."?npid=".$m['product_id']."");
					
					echo "</th></tr>";
					echo "<tr class=\"edge\" style=\"width:100%\">";
					
					echo "<td class=\"edge\" valign=\"top\" align=\"center\" width=\"".K_PRODUCT_IMAGE_WIDTH."\">";
					echo "<a href=\"".htmlentities(urldecode(K_PATH_IMAGES_PRODUCTS."".$m['product_image']))."\" target=\"_blank\"><img src=\"".K_PATH_IMAGES_PRODUCTS."s_".$m['product_image']."\" alt=\"".$m['product_name']."\" border=\"0\" width=\"".K_PRODUCT_IMAGE_WIDTH."\" height=\"".K_PRODUCT_IMAGE_HEIGHT."\" /></a>";
					echo "</td>";
					
					echo "<td class=\"edge\">";
					echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">";
					
					if (isset($m['product_manufacturer_id']) AND $m['product_manufacturer_id']) {
						echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_manufacturer']."</b></td>";
						echo "<td class=\"fillOE\">";
						$sqlmf = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." WHERE manuf_id='".$m['product_manufacturer_id']."' LIMIT 1";
						if($rmf = F_aiocpdb_query($sqlmf, $db)) {
							if($mmf = F_aiocpdb_fetch_array($rmf)) {
								
								if ($mmf['manuf_url']) {
									echo "<a href=\"".htmlentities(urldecode($mmf['manuf_url']))."\" target=\"_blank\">".$mmf['manuf_name']."</a>";
								}
								else {
									echo $mmf['manuf_name'];
								}
							}
						}
						else {
							F_display_db_error();
						}
						echo "</td></tr>";
					}
					
					if (isset($m['product_warranty']) AND ($m['product_warranty']!=0)) {
						echo "<tr class=\"fillE\"><td class=\"fillEO\">";
						if (isset($m['product_warranty_id']) AND $m['product_warranty_id']) {
							echo "<b><a href=\"cp_show_ec_warranty.".CP_EXT."?warranty_id=".$m['product_warranty_id']."\" target=\"_blank\">".$l['w_warranty']."</a></b>";
						}
						else {
							echo "<b>".$l['w_warranty']."</b>";
						}
						echo "</td><td class=\"fillEE\">";
						if ($m['product_warranty']>0) {
							echo "".$m['product_warranty']."&nbsp;".$l['w_months'].""; 
						}
						elseif ($m['product_warranty']<0) {
							echo $l['w_lifetime'];
						}
						else {
							echo $l['w_no'];
						}
						echo "&nbsp;</td></tr>";
					}
					
					echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_cost']."</b></td><td class=\"fillOE\">".K_MONEY_CURRENCY_UNICODE_SYMBOL." ".F_FormatCurrency($m['product_cost'])."&nbsp;</td></tr>";
					
					//print tax (if any)
					
					if (($m['product_tax']>1) AND ($_SESSION['session_user_id'] > 1)) {
						$vat = F_get_vat_value($m['product_tax'], $_SESSION['session_user_id']);
						$tax_amount = $m['product_cost'] * ($vat / 100);
						echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_ec_tax']."</b></td><td class=\"fillEE\">".$vat." %</td></tr>";
					}
					elseif ($_SESSION['session_user_id'] > 1) {
						echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_ec_tax']."</b></td><td class=\"fillEE\">0 %</td></tr>";
						$vat = 0;
						$tax_amount = 0;
					}
					else {
						echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_ec_tax']."</b></td><td class=\"fillEE\"><a href=\"cp_login.".CP_EXT."\">???</a></td></tr>";
						$vat = 0;
						$tax_amount = 0;
					}

					//print tax 2 (if any)
					if (K_EC_DISPLAY_TAX_2) {
						if (($m['product_tax2']>1) AND ($_SESSION['session_user_id'] > 1)) {
							$vat2 = F_get_vat_value($m['product_tax2'], $_SESSION['session_user_id']);
							$tax_amount2 = $m['product_cost'] * ($vat2 / 100);
							echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_ec_tax2']."</b></td><td class=\"fillOE\">".$vat2." %</td></tr>";
						}
						elseif ($_SESSION['session_user_id'] > 1) {
							echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_ec_tax2']."</b></td><td class=\"fillOE\">0 %</td></tr>";
							$vat = 0;
							$tax_amount = 0;
						}
						else {
							echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_ec_tax2']."</b></td><td class=\"fillOE\"><a href=\"cp_login.".CP_EXT."\">???</a></td></tr>";
							$vat2 = 0;
							$tax_amount2 = 0;
						}
					}
					
					//print tax 3 (if any)
					if (K_EC_DISPLAY_TAX_3) {
						if (($m['product_tax3']>1) AND ($_SESSION['session_user_id'] > 1)) {
							$vat3 = F_get_vat_value($m['product_tax3'], $_SESSION['session_user_id']);
							$tax_amount3 = ($m['product_cost'] + $tax_amount + $tax_amount2) * ($vat3 / 100);
							echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_ec_tax3']."</b></td><td class=\"fillEE\">".$vat3." %</td></tr>";
						}
						elseif ($_SESSION['session_user_id'] > 1) {
							echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_ec_tax3']."</b></td><td class=\"fillEE\">0 %</td></tr>";
							$vat = 0;
							$tax_amount = 0;
						}
						else {
							echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_ec_tax3']."</b></td><td class=\"fillEE\"><a href=\"cp_login.".CP_EXT."\">???</a></td></tr>";
							$vat3 = 0;
							$tax_amount3 = 0;
						}
					}
					
					//print total (net + taxes)
					if ((($m['product_tax']>1) OR ($m['product_tax2']>1) OR ($m['product_tax3']>1)) AND ($_SESSION['session_user_id'] > 1)) {
						echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_total']."</b></td><td class=\"fillOE\">".K_MONEY_CURRENCY_UNICODE_SYMBOL." ".F_FormatCurrency($m['product_cost'] + $tax_amount + $tax_amount2 + $tax_amount3)."&nbsp;</td></tr>";
					}
					
					if (!$m['product_q_available']) {
						$pqav = "";
					}
					else {
						$pqav = $m['product_q_available'];
					}
					
					if ($unit_name) {
						echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_quantity']."</b></td><td class=\"fillEE\">".$unit_name." ".$pqav."&nbsp;</td></tr>";
						
						echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_arriving_quantity']."</b></td><td class=\"fillOE\">".$unit_name." ".$m['product_q_arriving']."&nbsp;(".$m['product_arriving_time'].")</td></tr>";
					}
					
					echo "</table>";
					echo "</td></tr>";
					
					if (($viewmode == 1) OR ($m['product_id'] == $selectedproduct)) {
						//display product's resources (if any)
						$sqlpd = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_RESOURCES." WHERE prodres_product_id='".$m['product_id']."' ORDER BY prodres_name";
						if($rpd = F_aiocpdb_query($sqlpd, $db)) {
							if($mpd = F_aiocpdb_fetch_array($rpd)) {
								echo "<tr class=\"edge\">";
								echo "<td class=\"edge\" valign=\"top\" colspan=\"2\">";
								echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">";
								echo "<tr class=\"fill0\"><th class=\"fill0\">".$l['w_resources']."</th></tr>";
								echo "<tr class=\"fillE\"><td class=\"fillEE\">";
								echo "<ul>";
								// get target name
								$restarget = "_self";
								$sqlrt = "SELECT * FROM ".K_TABLE_FRAME_TARGETS." WHERE target_id='".$mpd['prodres_target']."' LIMIT 1";
								if($rrt = F_aiocpdb_query($sqlrt, $db)) {
									if($mrt = F_aiocpdb_fetch_array($rrt)) {
										$restarget = $mrt['target_name'];
									}
								}
								else {
									F_display_db_error();
								}
								echo "<li><a href=\"".$mpd['prodres_link']."\" target=\"".$restarget."\">".$mpd['prodres_name']."</a></li>";
								while($mpd = F_aiocpdb_fetch_array($rpd)) {
									echo "<li><a href=\"".$mpd['prodres_link']."\" target=\"".$restarget."\">".htmlentities($mpd['prodres_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></li>";
								}
								echo "</ul>";
								echo "</td></tr>";
								echo "</table>";
								echo "</td></tr>";
							}
						}
						else {
							F_display_db_error();
						}
						
						//display product description
						$prod_desc = F_decode_field($m['product_description']);
						if ($prod_desc) {
							echo "<tr class=\"edge\">";
							echo "<td class=\"edge\" valign=\"top\" colspan=\"2\">";
							echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">";
							echo "<tr class=\"fill0\"><th class=\"fill0\">".$l['w_description']."</th></tr>";
							echo "<tr class=\"fillE\"><td class=\"fillEE\">".$prod_desc."</td></tr>";
							echo "</table>";
							echo "</td></tr>";
						}
					}
					echo "</table>";
					echo "</td></tr>";
				}
				else { //compact mode --------------------------------------------------------------
					echo "<tr class=\"fill".$rowclass."\">";
					
					echo "<td class=\"fill".$rowclass."O\">";
					echo "<a href=\"javascript:FJ_submit_products_form('".$firstrow."','".urlencode($order_field)."','0',".$m['product_category_id'].",".$m['product_id'].");\">".$m['product_code']."</a>";
					
					echo "</td>";
					
					echo "<td class=\"fill".$rowclass."E\">";
					echo "<a href=\"javascript:FJ_submit_products_form('".$firstrow."','".urlencode($order_field)."','0',".$m['product_category_id'].",".$m['product_id'].");\">".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					
					// noscript alternative for search engines
					echo "<noscript><a href=\"cp_show_ec_products.".CP_EXT."?pid=".$m['product_id']."\">".htmlentities($m['product_code'], ENT_NOQUOTES, $l['a_meta_charset'])." - ".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></noscript>";
					
					echo "</td>";
					
					echo "<td class=\"fill".$rowclass."O\">";
					//NET COST
					echo "".K_MONEY_CURRENCY_UNICODE_SYMBOL."&nbsp;".F_FormatCurrency($m['product_cost'])."";
					echo "</td>";
					
					echo "<td class=\"fill".$rowclass."E\">";
					if ($unit_name) {
						if (!$m['product_q_available']) {
							echo "&nbsp;";
						}
						else {
							echo "&nbsp;".$unit_name."&nbsp;".$m['product_q_available']."&nbsp;(+".$m['product_q_arriving'].")";
						}
					}
					echo "</td>";
					
					echo "<td class=\"fill".$rowclass."O\">";
					//display buy button
					F_generic_button("buy".$m['product_id'], $l['w_buy'], "location.replace('cp_show_ec_shopping_cart.".CP_EXT."?npid=".$m['product_id']."')", "cp_show_ec_shopping_cart.".CP_EXT."?npid=".$m['product_id']."");
					echo "</td>";
					
					echo "</tr>\n";
				}
			}
		} //end of while
		echo "</table>";
		echo "</td></tr>";
	}
	else {
		F_display_db_error();
	}
	
	//button for label printing
	if ($_SESSION['session_user_level'] >= K_AUTH_EDIT_PRODUCTS_LEVEL) {
		echo "<tr class=\"edge\">";
		echo "<td class=\"edge\" align=\"center\">";
		//generate a verification code to avoid unauthorized calls to PDF viewer
		$verifycode = F_generate_verification_code($wherequery, 4);
		F_generic_button("pdflabel", $l['w_label'], "PDFLAB=window.open('cp_show_ec_pdf_product_label.".CP_EXT."?wherequery=".urlencode($wherequery)."&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."','PDFLAB','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
		
		echo "</td></tr>";
	}
	echo "</table>";
	// ------------------------------------------------------
	
	//build query for froogle link calling
	$url_request = "a=1";
	if ($wherequery) {
		$url_request .= "&amp;wherequery=".urlencode($wherequery)."";
	}
	if ($product_category_id) {
		$url_request .= "&amp;product_category_id=".$product_category_id."";
	}
	if ($product_manufacturer_id) {
		$url_request .= "&amp;product_manufacturer_id=".$product_manufacturer_id."";
	}
	if ($order_field) {
		$url_request .= "&amp;order_field=".urlencode($order_field)."";
	}
	if ($orderdir) {
		$url_request .= "&amp;orderdir=".$orderdir."";
	}
	
	echo "<br />\n<div align=\"center\">";
	echo "<a href=\"../../public/code/cp_show_ec_products_xml.".CP_EXT."?".$url_request."\" target=\"_blank\"><img src=\"../../pagefiles/xml.gif\" width=\"36\" height=\"14\" alt=\"XML Format\" border=\"0\" /></a> ";
	echo "<a href=\"../../public/code/cp_show_ec_froogle.".CP_EXT."?".$url_request."\" target=\"_blank\"><img src=\"../../pagefiles/froogle_36x14.gif\" width=\"36\" height=\"14\" alt=\"Froogle Datafeed Format\" border=\"0\" /></a>";
	echo "</div><br />";
	
	// --- ------------------------------------------------------
	// --- page jump
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_EC_PRODUCTS." ".$wherequery."";
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	if (!empty($viewmode)) {$param_array .= "&amp;viewmode=".$viewmode."";}
	if (!empty($product_category_id)) {$param_array .= "&amp;product_category_id=".$product_category_id."";}
	if (!empty($product_manufacturer_id)) {$param_array .= "&amp;product_manufacturer_id=".$product_manufacturer_id."";}
	if (!empty($productssearch)) {$param_array .= "&amp;productssearch=".$productssearch."";}
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
	//display link to shopping cart
	if ($userlevel > 0) {
		echo "<br /><a href=\"cp_show_ec_shopping_cart.".CP_EXT."\"><b>".$l['t_shopping_cart']." &gt;&gt;</b></a>";
	}
	
	return;
}

// ------------------------------------------------------------
// Show select form for products
// ------------------------------------------------------------
function F_show_select_products($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $product_category_id, $product_manufacturer_id, $viewmode, $selectedproduct;
	global $changecategory, $changemanufacturer;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_tree.'.CP_EXT);

if(!$selectedproduct) {$selectedproduct=0;}
if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}
if ($changecategory OR $changemanufacturer) {
	$selectedproduct = 0;
	$firstrow = 0;
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_productsshow" id="form_productsshow">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_productscat_select'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="product_category_id" id="product_category_id" size="0" onchange="document.form_productsshow.changecategory.value=1; document.form_productsshow.submit()">
<?php 
if(!$product_category_id) {echo "<option value=\"\" selected=\"selected\">".$l['d_all_categories']."</option>";}
else {echo "<option value=\"\">".$l['d_all_categories']."</option>";}

$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
if (!empty($product_manufacturer_id)) {$noscriptlink .= "product_manufacturer_id=".$product_manufacturer_id."&amp;";}
if (!empty($viewmode)) {$noscriptlink .= "viewmode=".$viewmode."&amp;";}
if (!empty($order_field)) {$noscriptlink .= "order_field=".urlencode($order_field)."&amp;";}
if (!empty($orderdir)) {$noscriptlink .= "orderdir=".$orderdir."&amp;";}
$noscriptlink .= "product_category_id=";
F_form_select_tree($product_category_id, false, K_TABLE_EC_PRODUCTS_CATEGORIES, "prodcat", $noscriptlink); ?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_manufacturer', 'h_productsed_manufacturer'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changemanufacturer" id="changemanufacturer" value="0" />
<select name="product_manufacturer_id" id="product_manufacturer_id" size="0" onchange="document.form_productsshow.changemanufacturer.value=1; document.form_productsshow.submit()">
<option value="">&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." WHERE 1 ORDER BY manuf_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['manuf_id']."\"";
		if($m['manuf_id'] == $product_manufacturer_id) {
			 echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['manuf_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

<!-- SELECT view mode ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_mode', 'h_list_mode'); ?></b></td>
<td class="fillOE">
<select name="viewmode" id="viewmode" size="0" onchange="document.form_productsshow.selectedproduct.value=''; document.form_productsshow.submit()">
<?php
echo "<option value=\"0\"";
if (!$viewmode) {echo " selected=\"selected\"";}
echo ">".$l['w_compact']."</option>";

echo "<option value=\"2\"";
if ($viewmode==2) {echo " selected=\"selected\"";}
echo ">".$l['w_essential']."</option>";

echo "<option value=\"1\"";
if ($viewmode==1) {echo " selected=\"selected\"";}
echo ">".$l['w_full']."</option>";
?> 
</select>
<noscript>
<ul>
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
echo "<li><a href=\"".$noscriptlink."viewmode=0\">".$l['w_compact']."</a></li>\n";
echo "<li><a href=\"".$noscriptlink."viewmode=2\">".$l['w_essential']."</a></li>\n";
echo "<li><a href=\"".$noscriptlink."viewmode=1\">".$l['w_full']."</a></li>\n";
?>
</ul>
</noscript>
</td>
</tr>
<!-- END view mode ==================== -->

<!-- SELECT ORDER mode ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_order_by', 'h_list_order_by'); ?></b></td>
<td class="fillEE">
<select name="order_field" id="order_field" size="0" onchange="document.form_productsshow.submit()">
<?php

echo "<option value=\"product_category_id,product_name\"";
if($order_field=="product_category_id,product_name") {echo" selected=\"selected\"";}
echo">".$l['w_category']." : ".$l['w_name']."</option>";

echo "<option value=\"product_category_id, product_code\"";
if($order_field=="product_category_id, product_code") {echo" selected=\"selected\"";}
echo">".$l['w_category']." : ".$l['w_code']."</option>";

echo "<option value=\"product_name\"";
if($order_field=="product_name") {echo" selected=\"selected\"";}
echo">".$l['w_name']."</option>";

echo "<option value=\"product_code\"";
if($order_field=="product_code") {echo" selected=\"selected\"";}
echo">".$l['w_code']."</option>";

echo "<option value=\"product_barcode\"";
if($order_field=="product_barcode") {echo" selected=\"selected\"";}
echo">".$l['w_barcode']."</option>";

echo "<option value=\"product_category_id\"";
if($order_field=="product_category_id") {echo" selected=\"selected\"";}
echo">".$l['w_category']."</option>";

echo "<option value=\"product_cost\"";
if($order_field=="product_cost") {echo" selected=\"selected\"";}
echo">".$l['w_cost']."</option>";

echo "<option value=\"product_description\"";
if($order_field=="product_description") {echo" selected=\"selected\"";}
echo">".$l['w_description']."</option>";

echo "<option value=\"product_date_added DESC\"";
if($order_field=="product_date_added DESC") {echo" selected=\"selected\"";}
echo">".$l['w_date']."</option>";

echo "<option value=\"product_q_sold DESC\"";
if($order_field=="product_q_sold DESC") {echo" selected=\"selected\"";}
echo">".$l['w_top_seller']."</option>";

?> 
</select>
</td>
</tr>
<!-- END ORDER mode ==================== -->

</table>

</td>
</tr>
</table>
<br />
<!-- SHOW products ==================== -->
<?php 
F_show_products($product_category_id, $product_manufacturer_id, $viewmode, $selectedproduct, $wherequery, $order_field, $orderdir, $firstrow, K_MAX_ROWS_PER_PAGE);
?>
<!-- END SHOW products ==================== -->

<input type="hidden" name="selectedproduct" id="selectedproduct" value="<?php echo $selectedproduct; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
</form>
<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_products_form(newfirstrow, neworder_field, neworderdir, productscategory, productselected) {
	document.form_productsshow.product_category_id.value=productscategory;
	document.form_productsshow.selectedproduct.value=productselected;
	document.form_productsshow.order_field.value=neworder_field;
	document.form_productsshow.orderdir.value=neworderdir;
	document.form_productsshow.firstrow.value=newfirstrow;
	document.form_productsshow.submitted.value=1;
	document.form_productsshow.submit();
}

document.form_productsshow.product_category_id.focus();
//]]>
</script>
<!-- END Cange focus to product_id select -->

<?php
} //end of function

// ------------------------------------------------------------
// Display single product data
// ------------------------------------------------------------
function F_display_single_product($pid) {
	global $l, $db, $selected_language, $aiocp_dp;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if(!F_count_rows(K_TABLE_EC_PRODUCTS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
	}
	else { //the table is not empty
		$wherequery = "WHERE product_id='".$pid."'";
		F_show_products("", "", 1, $pid, $wherequery, "", "", 0, K_MAX_ROWS_PER_PAGE);
	} 
}

// ------------------------------------------------------------
// Show search form for products
// ------------------------------------------------------------
function F_search_products($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $product_category_id, $product_manufacturer_id, $viewmode, $selectedproduct;
	global $term, $submitted, $productssearch, $addterms;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);}
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	if(!isset($orderdir)) {$orderdir=1;} 
	else {$orderdir=intval($orderdir);}
	if(!$product_category_id) {$product_category_id="0";} // All categories
	if(!$selectedproduct) {$selectedproduct=0;}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}
	
if(!F_count_rows(K_TABLE_EC_PRODUCTS)) { //if the table is void (no items) display message
	echo "<h2>".$l['m_databasempty']."</h2>";
}
else { //the table is not empty

// ---------------------------------------------------------------

if($productssearch OR $submitted) { // Submitting query (search results)
	if(isset($term) AND ($term != "")) {
		$wherequery = "WHERE (";
		$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
		$size = sizeof($terms);
		//redundant check for security feature
		if($addterms != "AND"){$addterms = "OR";}
		$wherequery .= "(";
		for($i=0;$i<$size;$i++) {
			if($i>0) {$wherequery .= " ".$addterms." ";}
			$wherequery .= "((product_name LIKE '%$terms[$i]%')";
			$wherequery .= " OR (product_code LIKE '%$terms[$i]%')";
			$wherequery .= " OR (product_manufacturer_code LIKE '%$terms[$i]%')";
			$wherequery .= " OR (product_barcode LIKE '%$terms[$i]%')";
			$wherequery .= " OR (product_inventory_code LIKE '%$terms[$i]%')";
			$wherequery .= " OR (product_alternative_codes LIKE '%$terms[$i]%')";
			$wherequery .= " OR (product_description LIKE '%$terms[$i]%'))";
		}
		$wherequery .= ")";
		$wherequery .= ")"; // close WHERE clause
	}
	F_show_products($product_category_id, $product_manufacturer_id, $viewmode, $selectedproduct, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
} //end if($productssearch OR $submitted)

// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_productssearch" id="form_productssearch">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right">
<b><?php echo F_display_field_name('w_keywords', 'h_search_keywords'); ?></b>
</td>
<td class="fillOE">
<input type="text" name="term" id="term" value="<?php echo htmlentities($term, ENT_COMPAT, $l['a_meta_charset']); ?>" />
</td></tr>

<?php
if($addterms == "OR") {
echo "<tr class=\"fillE\"><td class=\"fillEO\">&nbsp;</td><td class=\"fillEE\"><input type=\"radio\" name=\"addterms\" value=\"AND\" /> ".$l['d_search_all']."</td></tr>";
echo "<tr class=\"fillO\"><td class=\"fillOO\">&nbsp;</td><td class=\"fillOE\"><input type=\"radio\" name=\"addterms\" value=\"OR\" checked=\"checked\" /> ".$l['d_search_any']."</td></tr>";
}
else {
echo "<tr class=\"fillE\"><td class=\"fillEO\">&nbsp;</td><td class=\"fillEE\"><input type=\"radio\" name=\"addterms\" value=\"AND\" checked=\"checked\" /> ".$l['d_search_all']."</td></tr>";
echo "<tr class=\"fillO\"><td class=\"fillOO\">&nbsp;</td><td class=\"fillOE\"><input type=\"radio\" name=\"addterms\" value=\"OR\" /> ".$l['d_search_any']."</td></tr>";
}
?>

<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_productscat_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="product_category_id" id="product_category_id" size="0" onchange="document.form_productsshow.changecategory.value=1; document.form_productsshow.submit()">
<?php 
if(!$product_category_id) {echo "<option value=\"\" selected=\"selected\">".$l['d_all_categories']."</option>";}
else {echo "<option value=\"\">".$l['d_all_categories']."</option>";}
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
if (!empty($product_manufacturer_id)) {$noscriptlink .= "product_manufacturer_id=".$product_manufacturer_id."&amp;";}
if (!empty($viewmode)) {$noscriptlink .= "viewmode=".$viewmode."&amp;";}
if (!empty($order_field)) {$noscriptlink .= "order_field=".urlencode($order_field)."&amp;";}
if (!empty($orderdir)) {$noscriptlink .= "orderdir=".$orderdir."&amp;";}
$noscriptlink .= "product_category_id=";
F_form_select_tree($product_category_id, false, K_TABLE_EC_PRODUCTS_CATEGORIES, "prodcat", $noscriptlink); ?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_manufacturer', 'h_productsed_manufacturer'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changemanufacturer" id="changemanufacturer" value="0" />
<select name="product_manufacturer_id" id="product_manufacturer_id" size="0" onchange="document.form_productsshow.changemanufacturer.value=1; document.form_productsshow.submit()">
<option value="">&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." WHERE 1 ORDER BY manuf_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['manuf_id']."\"";
		if($m['manuf_id'] == $product_manufacturer_id) {
			 echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['manuf_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

<!-- SELECT ORDER mode ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_order_by', ''); ?></b></td>
<td class="fillEE">
<select name="order_field" id="order_field" size="0">
<?php

echo "<option value=\"product_category_id,product_name\"";
if($order_field=="product_category_id,product_name") {echo" selected=\"selected\"";}
echo">".$l['w_category']." : ".$l['w_name']."</option>";

echo "<option value=\"product_category_id, product_code\"";
if($order_field=="product_category_id, product_code") {echo" selected=\"selected\"";}
echo">".$l['w_category']." : ".$l['w_code']."</option>";

echo "<option value=\"product_name\"";
if($order_field=="product_name") {echo" selected=\"selected\"";}
echo">".$l['w_name']."</option>";

echo "<option value=\"product_code\"";
if($order_field=="product_code") {echo" selected=\"selected\"";}
echo">".$l['w_code']."</option>";

echo "<option value=\"product_barcode\"";
if($order_field=="product_barcode") {echo" selected=\"selected\"";}
echo">".$l['w_barcode']."</option>";

echo "<option value=\"product_category_id\"";
if($order_field=="product_category_id") {echo" selected=\"selected\"";}
echo">".$l['w_category']."</option>";

echo "<option value=\"product_cost\"";
if($order_field=="product_cost") {echo" selected=\"selected\"";}
echo">".$l['w_cost']."</option>";

echo "<option value=\"product_description\"";
if($order_field=="product_description") {echo" selected=\"selected\"";}
echo">".$l['w_description']."</option>";

echo "<option value=\"product_date_added DESC\"";
if($order_field=="product_date_added DESC") {echo" selected=\"selected\"";}
echo">".$l['w_date']."</option>";

echo "<option value=\"product_q_sold DESC\"";
if($order_field=="product_q_sold DESC") {echo" selected=\"selected\"";}
echo">".$l['w_top_seller']."</option>";

?> 
</select>
</td>
</tr>
<!-- END ORDER mode ==================== -->
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="selectedproduct" id="selectedproduct" value="<?php echo $selectedproduct; ?>" />
<input type="hidden" name="productssearch" id="productssearch" value="" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<?php F_submit_button("form_productssearch","productssearch",$l['w_search']); ?>
</td></tr>
</table>
</form>

<?php
// ---------------------------------------------------------------
} //end of else for void table
?>

<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_products_form(newfirstrow, neworder_field, neworderdir, productscategory, productselected) {
	document.form_productssearch.product_category_id.value=productscategory;
	document.form_productssearch.selectedproduct.value=productselected;
	document.form_productssearch.order_field.value=neworder_field;
	document.form_productssearch.orderdir.value=neworderdir;
	document.form_productssearch.firstrow.value=newfirstrow;
	document.form_productssearch.submitted.value=1;
	document.form_productssearch.submit();
}
//]]>
</script>
<!-- END Submit form ==================== -->

<?php
} //end of function

// ----------------------------------------------------------
// read category data
// ----------------------------------------------------------
function F_get_product_category_data($categoryid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." WHERE prodcat_id='".$categoryid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$prodcat->sub_id = $m['prodcat_sub_id'];
			$prodcat->level = $m['prodcat_level'];
			$prodcat->name = $m['prodcat_name'];
			$prodcat->description = $m['prodcat_description'];
			$prodcat->code = $m['prodcat_code'];
			$prodcat->image = $m['prodcat_image'];
			return $prodcat;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ----------------------------------------------------------
// get unit of measure name
// ----------------------------------------------------------
function F_get_unit_name($unit_id) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$this_unit = "";
	
	$sql = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id='".$unit_id."'";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$this_unit = $m['unit_name'];
		}
	}
	else {
		F_display_db_error();
	}
	
	return $this_unit;
}


// ------------------------------------------------------------
// Display products in froogle data feed format (tab delimited text)
//
// BASIC FILE FORMAT
// + The basic file format has the following required parameters:
//   - Tab-delimited text file
//   - First line of the file is the header � must contain field names, all lower-case
//   - Use the field names from the table below, and in the same column order
//   - One line per item (use a newline or carriage return to terminate the line)
//   - File encoding is LATIN1 (ASCII is fine, as it is a subset of LATIN1)
// + The following field elements are forbidden as part of the basic format. If you want to
//   include them, you must use the extended format. If you accidentally include them as
//   part of the basic format, products that contain errors will be dropped from the feed.
//   - Tabs, carriage returns, or newline characters may not be included inside any
//     field, including the description.
//   - Exactly one tab must separate each field. If there are extra tabs inserted
//     between fields in a line, or at the end of a line, that product will be dropped.
//   - HTML tags, comments, and escape sequences may not be included �
//     description must be plain text.
// ------------------------------------------------------------
function F_show_products_froogle($product_category_id, $product_manufacturer_id, $wherequery, $order_field, $orderdir) {
	global $l, $db, $selected_language;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_ec_vat.'.CP_EXT);
	
	$froogle_feed = "";
	
	//initialize variables
	$userlevel = $_SESSION['session_user_level'];
	
	if(!$order_field) {$order_field = "product_name";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_EC_PRODUCTS)) { //if the table is void (no items) display message
		return FALSE;
	}
	
	if($product_manufacturer_id) {
		if (!$wherequery) {$wherequery = "WHERE (product_manufacturer_id='".$product_manufacturer_id."')";}
		else {$wherequery .= " AND (product_manufacturer_id='".$product_manufacturer_id."')";}
	}
	
	if($product_category_id) {
		$sqlc = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." WHERE prodcat_id=".$product_category_id." LIMIT 1";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['prodcat_level']>$userlevel) {
					return FALSE;
				}
				if (!$wherequery) {$wherequery = "WHERE (product_category_id='".$product_category_id."')";}
				else {$wherequery .= " AND (product_category_id='".$product_category_id."')";}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	
	//print header
	$froogle_feed = "product_url";
	$froogle_feed .= "\tname";
	$froogle_feed .= "\tdescription";
	$froogle_feed .= "\tprice";
	$froogle_feed .= "\timage_url";
	$froogle_feed .= "\tcategory";
	$froogle_feed .= "\toffer_id";
	$froogle_feed .= "\tmanufacturer_id";
	$froogle_feed .= "\tcurrency";
	$froogle_feed .= "\n";
	
	$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." ".$wherequery." ORDER BY ".$full_order_field."";	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {			
			//get category data
			$catdata = F_get_product_category_data($m['product_category_id']);
			$category_name = F_decode_field($catdata->name);
			//check authorization rights
			if ($userlevel >= $catdata->level) {
				
				// product_url
				$froogle_feed .= "".K_PATH_PUBLIC_CODE."cp_show_ec_products.".CP_EXT."?pid=".$m['product_id'].""; 
				//name
				$froogle_feed .= "\t".substr(F_froogle_text($m['product_name']),0,80)."";
				//description
				$prod_desc = F_decode_field($m['product_description']);
				$froogle_feed .= "\t".substr(F_froogle_text($prod_desc),0,65536)."";
				//price (without taxes)
				$froogle_feed .= "\t".number_format(round($m['product_cost'],2) + 0.00, 2, '.', '')."";
				//image_url
				$froogle_feed .= "\t".F_resolve_url_path(K_PATH_PUBLIC_CODE.K_PATH_IMAGES_PRODUCTS."s_".$m['product_image'])."";
				//category
				$tempcatdata = $catdata;
				while ($tempcatdata->sub_id > 0) { //build category name
					$tempcatdata = F_get_product_category_data($tempcatdata->sub_id);
					$category_name = F_decode_field($tempcatdata->name)." > ".$category_name;
				}
				$froogle_feed .= "\t".$category_name."";
				//offer_id
				$froogle_feed .= "\t".$m['product_code']."";
				//manufacturer_id
				$froogle_feed .= "\t".$m['product_manufacturer_code']."";
				//currency
				$froogle_feed .= "\t".strtolower(K_MONEY_CURRENCY_ISO_ALPHA_CODE)."";
				$froogle_feed .= "\n"; //end of line
			}
		} //end of while
	}
	else {
		F_display_db_error();
	}
	
	return $froogle_feed;
}

// ------------------------------------------------------------
// Convert string in simple text removing 
// tabs, carriage returns, or newline characters
// ------------------------------------------------------------
function F_froogle_text($string) {
	$returntext = stripslashes($string); //strip out slashes
	$returntext = unhtmlentities($returntext); //convert html entities
	$returntext = preg_replace("'<[^>]*?>'si", "", $returntext); //strip out all tags
	$repTable = array("\t" => " ","\n" => " ","\r" => " ","\0" => " ","\x0B" => " "); //remove some special chars
	$returntext = strtr($returntext, $repTable);
	$returntext = preg_replace("'[\s]+'si", " ",  $returntext); //remove multiple spaces
	return trim($returntext);
}

// ------------------------------------------------------------
// Display products in XML format
// ------------------------------------------------------------
function F_show_products_xml($product_category_id, $product_manufacturer_id, $wherequery, $order_field, $orderdir) {
	global $l, $db, $selected_language;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_ec_vat.'.CP_EXT);
	
	$xmldata = "";
	
	//initialize variables
	$userlevel = $_SESSION['session_user_level'];
	
	if(!$order_field) {$order_field = "product_name";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_EC_PRODUCTS)) { //if the table is void (no items) display message
		return FALSE;
	}
	
	//send page header
	$xmldata .= "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
	
	$xmldata .= "<!DOCTYPE catalog [\n<!ENTITY % HTMLlat1 PUBLIC\n\"-//W3C//ENTITIES Latin 1 for XHTML//EN\n\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml-lat1.ent\">\n%HTMLlat1;\n]>\n";
	

	
	
	if($product_manufacturer_id) {
		if (!$wherequery) {$wherequery = "WHERE (product_manufacturer_id='".$product_manufacturer_id."')";}
		else {$wherequery .= " AND (product_manufacturer_id='".$product_manufacturer_id."')";}
	}
	
	if($product_category_id) {
		$sqlc = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." WHERE prodcat_id=".$product_category_id." LIMIT 1";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['prodcat_level']>$userlevel) {
					return FALSE;
				}
				if (!$wherequery) {$wherequery = "WHERE (product_category_id='".$product_category_id."')";}
				else {$wherequery .= " AND (product_category_id='".$product_category_id."')";}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	$xmldata .= "<catalog>\n";
	$xmldata .= "<aiocp_version>".K_AIOCP_VERSION."</aiocp_version>\n";
	$xmldata .= "<date>".gmdate("Y-m-d H:i:s")."</date>\n";
	$xmldata .= "<site>".K_PATH_HOST."</site>\n";
	$xmldata .= "<catalog>".K_PATH_PUBLIC_CODE."cp_show_ec_products.".CP_EXT."</catalog>\n";
	
	$xmldata .= "<products>\n";
	
	$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." ".$wherequery." ORDER BY ".$full_order_field."";	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {			
			//get category data
			$catdata = F_get_product_category_data($m['product_category_id']);
			$category_name = F_decode_field($catdata->name);
			//check authorization rights
			if ($userlevel >= $catdata->level) {
				$xmldata .= "\n<item about=\"".htmlentities($m['product_code'], ENT_NOQUOTES, $l['a_meta_charset'])." - ".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\">\n";
				
				$xmldata .= "\t<product_id>".$m['product_id']."</product_id>\n";
				$xmldata .= "\t<product_url>".K_PATH_PUBLIC_CODE."cp_show_ec_products.".CP_EXT."?pid=".$m['product_id']."</product_url>\n"; 
				$xmldata .= "\t<product_code>".htmlentities($m['product_code'], ENT_NOQUOTES, $l['a_meta_charset'])."</product_code>\n";
				$xmldata .= "\t<product_manufacturer_code>".htmlentities($m['product_manufacturer_code'], ENT_NOQUOTES, $l['a_meta_charset'])."</product_manufacturer_code>\n";
				$xmldata .= "\t<product_barcode>".htmlentities($m['product_barcode'], ENT_NOQUOTES, $l['a_meta_charset'])."</product_barcode>\n";
				$xmldata .= "\t<product_inventory_code>".htmlentities($m['product_inventory_code'], ENT_NOQUOTES, $l['a_meta_charset'])."</product_inventory_code>\n";
				$xmldata .= "\t<product_alternative_codes>".htmlentities($m['product_alternative_codes'], ENT_NOQUOTES, $l['a_meta_charset'])."</product_alternative_codes>\n";
				$xmldata .= "\t<product_category_id>".$m['product_category_id']."</product_category_id>\n";
				$xmldata .= "\t<product_category_name>".htmlentities($category_name, ENT_NOQUOTES, $l['a_meta_charset'])."</product_category_name>\n";
				
				//category
				$tempcatdata = $catdata;
				while ($tempcatdata->sub_id > 0) { //build category name
					$tempcatdata = F_get_product_category_data($tempcatdata->sub_id);
					$category_name = F_decode_field($tempcatdata->name)." > ".$category_name;
				}
				
				$xmldata .= "\t<product_category_tree>".htmlentities($category_name, ENT_NOQUOTES, $l['a_meta_charset'])."</product_category_tree>\n";
				$xmldata .= "\t<product_manufacturer_id>".$m['product_manufacturer_id']."</product_manufacturer_id>\n";
				if (isset($m['product_manufacturer_id']) AND $m['product_manufacturer_id']) {
					$sqlmf = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." WHERE manuf_id='".$m['product_manufacturer_id']."' LIMIT 1";
					if($rmf = F_aiocpdb_query($sqlmf, $db)) {
						if($mmf = F_aiocpdb_fetch_array($rmf)) {
							$xmldata .= "\t<product_manufacturer_name>".htmlentities($mmf['manuf_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</product_manufacturer_name>\n";
							$xmldata .= "\t<product_manufacturer_url>".htmlentities(urldecode($mmf['manuf_url']))."</product_manufacturer_url>\n";	
						}
					}
					else {
						F_display_db_error();
					}
				}
				
				$xmldata .= "\t<product_manufacturer_link>".htmlentities($m['product_manufacturer_link'], ENT_NOQUOTES, $l['a_meta_charset'])."</product_manufacturer_link>\n";
				$xmldata .= "\t<product_name>".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</product_name>\n";
				$xmldata .= "\t<product_description>".F_decode_field($m['product_description'])."</product_description>\n";
				$xmldata .= "\t<product_warranty>".$m['product_warranty']."</product_warranty>\n";
				$xmldata .= "\t<product_warranty_id>".$m['product_warranty_id']."</product_warranty_id>\n";
				$xmldata .= "\t<product_warranty_url>".K_PATH_PUBLIC_CODE."cp_show_ec_warranty.".CP_EXT."?warranty_id=".$m['product_warranty_id']."</product_warranty_url>\n";

				$xmldata .= "\t<product_image>".F_resolve_url_path(K_PATH_PUBLIC_CODE.K_PATH_IMAGES_PRODUCTS."s_".$m['product_image'])."</product_image>\n";
				$xmldata .= "\t<product_transportable>".$m['product_transportable']."</product_transportable>\n";
				$xmldata .= "\t<product_weight_per_unit>".$m['product_weight_per_unit']."</product_weight_per_unit>\n";
				$xmldata .= "\t<product_length>".$m['product_length']."</product_length>\n";
				$xmldata .= "\t<product_width>".$m['product_width']."</product_width>\n";
				$xmldata .= "\t<product_height>".$m['product_height']."</product_height>\n";
				$xmldata .= "\t<product_unit_of_measure_id>".$m['product_unit_of_measure_id']."</product_unit_of_measure_id>\n";
				$xmldata .= "\t<product_unit_of_measure_name>".F_get_unit_name($m['product_unit_of_measure_id'])."</product_unit_of_measure_name>\n";
				$xmldata .= "\t<product_cost>".F_FormatCurrency($m['product_cost'])."</product_cost>\n";
				$xmldata .= "\t<product_tax_id>".$m['product_tax']."</product_tax_id>\n";
				$xmldata .= "\t<product_tax2_id>".$m['product_tax2']."</product_tax2_id>\n";
				$xmldata .= "\t<product_tax3_id>".$m['product_tax3']."</product_tax3_id>\n";
				$xmldata .= "\t<product_q_available>".$m['product_q_available']."</product_q_available>\n";
				$xmldata .= "\t<product_q_arriving>".$m['product_q_arriving']."</product_q_arriving>\n";
				$xmldata .= "\t<product_arriving_time>".$m['product_arriving_time']."</product_arriving_time>\n";
				$xmldata .= "\t<currency>".K_MONEY_CURRENCY_ISO_ALPHA_CODE."</currency>\n";
				
				$xmldata .= "</item>\n";

			}
		} //end of while
	}
	else {
		F_display_db_error();
	}
	$xmldata .= "</products>\n";
	$xmldata .= "</catalog>\n";
	return $xmldata;
}


/**
* Select subcategories
* @param 
* @access public
*/
function F_select_subcategories($selected_id) {
	return F_select_subcategories_level($selected_id, "");
}

/**
* Explore tree recursively
* @param 
* @access public
*/
function F_select_subcategories_level($selected_id, $subcatquery) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$subcatquery = "";
	// search sub categories
	$sqlsc = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." WHERE prodcat_sub_id=".$selected_id."";
	if($rsc = F_aiocpdb_query($sqlsc, $db)) {
		while($msc = F_aiocpdb_fetch_array($rsc)) {
			$subcatquery .= " OR product_category_id='".$msc['prodcat_id']."'";
			$subcatquery .= F_select_subcategories_level($msc['prodcat_id'], $subcatquery);
		}
	}
	else {
		F_display_db_error();
	}
	
	return $subcatquery;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
