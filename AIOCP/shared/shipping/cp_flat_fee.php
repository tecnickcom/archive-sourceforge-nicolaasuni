<?php
//============================================================+
// File name   : cp_flat_fee.php                               
// Begin       : 2002-08-08                                    
// Last Update : 2002-11-06                                    
//                                                             
// Description : Shipping Module for e-commerce                
//				FLAT FEE                                       
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

/*
// input variables
$total_net
$total_tax
$total_weight
$total_volume
$total_items
$total_parcels 
$shipping_state
$shipping_postcode
$shipping_country
*/

//output variables
$transport = true;
$transport_carriage = $l['w_free_carriage'];
$transport_net = 0;
$transport_tax = 0;
$transport_tax2 = 0;
$transport_tax3 = 0;

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
