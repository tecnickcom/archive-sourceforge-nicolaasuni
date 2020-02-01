<?php
//============================================================+
// File name   : cp_functions_polls_results.php
// Begin       : 2001-10-11
// Last Update : 2008-07-06
// 
// Description : polls results with statistics
//				(analyze K_TABLE_POLLS_VOTES table)
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

// ------------------------------------------------------------
// Show poll results
// $barsmaxlength : max length of the graph bars in pixels
// $barswidth : width of the graph bars in pixels (10, 15, 20, 25, 30, 35, 40)
// ------------------------------------------------------------
function F_show_poll_results($poll_language, $poll_id, $fullselect, $barsmaxlength, $barswidth) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $changelanguage, $changepoll;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	
// Initialize variables
if(!$poll_language) {$poll_language = $selected_language;}

if((!$poll_id) OR $changelanguage) {
	$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE poll_language='".$poll_language."' ORDER BY poll_name LIMIT 1";
}
else {
	$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE poll_id=".$poll_id." LIMIT 1";
}
if($r = F_aiocpdb_query($sql, $db)) {
	if($m = F_aiocpdb_fetch_array($r)) {
		$poll_id = $m['poll_id'];
		$poll_language = $m['poll_language'];
		$poll_name = $m['poll_name'];
		$poll_description = $m['poll_description'];
		$poll_date_start = $m['poll_date_start'];
		$poll_date_end = $m['poll_date_end'];
	}
}
else {
	F_display_db_error();
}
?>

<!-- ====================================================== -->
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<?php if($fullselect) {

//display language selector only if enabled languages are more than one
if (F_count_rows(K_TABLE_LANGUAGE_CODES, "WHERE language_enabled=1") > 1) {
?>

<tr class="edge">
<td class="edge">

<!-- SELECT  language ==================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_polllanguage" id="form_polllanguage">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_pollvote_language'); ?></b></td>
<td class="fillOE">
<select name="poll_language" id="poll_language" size="0" onchange="document.form_polllanguage.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $poll_language) {
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
<input type="hidden" name="changelanguage" id="changelanguage" value="1" />
</td>
</tr>
</table>

</form>

</td></tr>
<!-- END SELECT language ==================== -->
<?php
}
?>


<tr class="edge">
<td class="edge">

<!-- SELECT poll ==================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_pollid" id="form_pollid">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_poll', 'h_pollvote_poll'); ?></b></td>
<td class="fillEE" colspan="4">
<select name="poll_id" id="poll_id" size="0" onchange="document.form_pollid.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE poll_language='".$poll_language."' ORDER BY poll_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['poll_id']."\"";
		if($m['poll_id'] == $poll_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['poll_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" colspan="4"><hr /></td>
</tr>

<!-- END SELECT poll ==================== -->

<?php 
} //end if($fullselect) 

if (!isset($poll_description)) {
	$poll_description = "";
}
?>

<!-- VOTE TABLE ==================== -->

<tr class="fill">
<th class="edge" colspan="4"><b><?php echo F_evaluate_modules($poll_description); ?></b></th>
</tr>

<tr class="fill">
<th class="fillO"><?php echo $l['w_option']; ?></th>
<th class="fillE"><?php echo $l['w_votes']; ?></th>
<th class="fillO"><?php echo $l['w_percentage']; ?></th>
<th class="fillE"><?php echo $l['w_graph']; ?></th>
</tr>

<?php 
if($poll_id) { //display polls options

//count all votes for selected poll
$sqls = "SELECT COUNT(*) FROM ".K_TABLE_POLLS_VOTES." WHERE pollvote_pollid=".$poll_id."";
if($rs = F_aiocpdb_query($sqls, $db)) {
	if($ms = F_aiocpdb_fetch_array($rs)) {
		$totalvotes = $ms['0'];
	}
}
else {
	F_display_db_error();
}

	$sql = "SELECT * FROM ".K_TABLE_POLLS_OPTIONS." WHERE polloption_pollid='".$poll_id."' ORDER BY polloption_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd = 0;
			} else {
				$rowclass = "E";
				$rowodd = 1;
			}
			
			echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."O\">";
			echo "".htmlentities($m['polloption_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
			echo "</td>";
			//count votes for each option
			$sql2 = "SELECT COUNT(*) FROM ".K_TABLE_POLLS_VOTES." WHERE pollvote_pollid=".$poll_id." AND pollvote_optionid=".$m['polloption_id']."";
			if($r2 = F_aiocpdb_query($sql2, $db)) {
				if($m2 = F_aiocpdb_fetch_array($r2)) {
					echo "<td class=\"fill".$rowclass."E\" align=\"right\">";
					echo $m2['0']; //votes for selected option
					echo "</td><td class=\"fill".$rowclass."O\" align=\"right\">";
					if ($totalvotes > 0) {
						$votepercent = 100*$m2['0']/$totalvotes;
					}
					else {
						$votepercent = 0;
					}
					printf("%.2f",round($votepercent,2)); //percentage for selected option
					echo " %";
					echo "</td><td class=\"fill".$rowclass."E\">";
					$barlength = round(($votepercent * $barsmaxlength)/100);
					//echo "<img src=\"".K_PATH_IMAGES_BARS."h/".$barswidth."/".$barscolor.".gif\" border=\"0\" width=\"".$barlength."\" height=\"".$barswidth."\" />";
					echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=".$barlength."&amp;h=".$barswidth."\" border=\"0\" width=\"".$barlength."\" height=\"".$barswidth."\" />";
					echo "</td>";
				}
			}
			else {
				F_display_db_error();
			}
			echo "";
			echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
}

//change style for each row
if (isset($rowodd) AND ($rowodd)) {
	$rowclass = "O";
} else {
	$rowclass = "E";
}
if(!isset($totalvotes)) {
	$totalvotes = "";
}
			
echo "<tr class=\"fill".$rowclass."\">";
echo "<td class=\"fill".$rowclass."O\">".$l['w_total']."</td>";
echo "<td class=\"fill".$rowclass."E\" align=\"right\">".$totalvotes."</td>";
echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;</td>";
echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;</td>";
?>
</tr>

</table>

<input type="hidden" name="poll_language" id="fpr_poll_language" value="<?php echo $poll_language; ?>" />
<input type="hidden" name="changepoll" id="fpr_changepoll" value="1" />
</form>
<!-- END OF VOTE TABLE ==================== -->

</td>
</tr>
</table>

<p><?php echo $l['d_vote_disclaimer']; ?></p>
<!-- ====================================================== -->
<?php
}// end of function

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
