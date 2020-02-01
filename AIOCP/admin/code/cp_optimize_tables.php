<?php
//============================================================+
// File name   : cp_optimize_tables.php                        
// Begin       : 2001-09-12                                    
// Last Update : 2008-07-06
//                                                             
// Description : Optimize MySQL tables                         
//                - If the table has deleted or split rows,    
//                  repair the table.                          
//                - If the index pages are not sorted, sort    
//                  them.                                      
//                - If the statistics are not up to date       
//                  (and the repair couldn't be done by sorting
//                  the index), update them.                   
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

$pagelevel = K_AUTH_ADMIN_CP_OPTIMIZE_TABLES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_optimize_tables'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
if (($menu_mode == $l['w_optimize']) OR ($menu_mode == unhtmlentities($l['w_optimize'])) ) {
	$tablelist = "";
	$sql = "SHOW TABLES";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
				$tablelist .= ", ".$m['0'];
		}
	}
	else {
		F_display_db_error();
	}
	$tablelist = substr($tablelist,1);
	$sql = "OPTIMIZE TABLE $tablelist";
	if(!$result = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
?>
<p><b><?php echo $l['d_optimization_done']; ?></b></p>
<p><b><a href="cp_layout_main.<?php echo CP_EXT; ?>">&lt;&lt;&nbsp;<?php echo $l['t_homepage']; ?></a></b></p>
<?php
}
else{

 echo $l['d_optimize_tables']; ?>
<br />

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_optimizetables" id="form_optimizetables">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php F_submit_button("form_optimizetables","menu_mode",$l['w_optimize']); ?>
</form>

<?php
} // end of else
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
