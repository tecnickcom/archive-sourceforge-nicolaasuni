<?php
//============================================================+
// File name   : cp_functions_polls_vote.php                   
// Begin       : 2001-10-10                                    
// Last Update : 2008-07-06
//                                                             
// Description : Vote polls (K_TABLE_POLLS_VOTES table)        
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

// ------------------------------------------------------------
// Show poll vote form
// ------------------------------------------------------------
function F_show_poll_form($poll_language, $poll_id, $fullselect) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $changelanguage, $changepoll, $polloption_id, $poll_id, $menu_mode;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	
	// Initialize variables
	$userid = $_SESSION['session_user_id'];
	$userip = $_SESSION['session_user_ip'];
	$userlevel = $_SESSION['session_user_level'];
	if(!$poll_language) {$poll_language = $selected_language;}
	$actualdate = strtotime(gmdate("Y-m-d H:i:s",time())); //get UTC UNIX timestamp
	
	switch($menu_mode) {
		case unhtmlentities($l['w_results']):
		case $l['w_results']: {
			echo "<a href=\"cp_polls_results.".CP_EXT."?poll_language=".$poll_language."&amp;poll_id=".$poll_id."\">".$l['w_results']."</a>\n";

			echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			echo "//<![CDATA[\n";
			if(K_USE_FRAMES) { //reload all frames from index (if javascript enable)
				echo "var targetpage = escape(\"cp_polls_results.".CP_EXT."?poll_language=".$poll_language."&poll_id=".$poll_id."\");\n";
				echo "top.location.replace(\"../code/index.".CP_EXT."?load_page=\" + targetpage);\n";
			}
			else { //reload page
				echo "top.location.replace(\"cp_polls_results.".CP_EXT."?poll_language=".$poll_language."&poll_id=".$poll_id."\");\n";
			}
			echo "//]]>\n";
			echo "</script>\n";
			exit;
			break;
		}
		
		case unhtmlentities($l['w_vote']):
		case $l['w_vote']:{ // a vote button has been pressed
			
			$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE ((poll_level<=".$userlevel.") AND (poll_id=".$poll_id.")) LIMIT 1";
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					if($m['poll_level']>$userlevel) {
						F_print_error("WARNING", $l['m_authorization_deny']);
						F_logout_form();
						return;
					}
				}
			}
			
			if($polloption_id) { //if an option has been selected
				//check if user has already voted
				if($userid==1) { //if anonymous check also the IP
					$sql = "SELECT * FROM ".K_TABLE_POLLS_VOTES." WHERE (pollvote_userid='".$userid."' AND pollvote_userip='".$userip."' AND pollvote_pollid='".$poll_id."')";
				}
				else {
					$sql = "SELECT * FROM ".K_TABLE_POLLS_VOTES." WHERE pollvote_userid='".$userid."' AND pollvote_pollid='".$poll_id."'";
				}
				if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_vote_already']);
				}
				else { //add
					$sql = "INSERT IGNORE INTO ".K_TABLE_POLLS_VOTES." (
					pollvote_pollid, 
					pollvote_optionid, 
					pollvote_userid, 
					pollvote_userip
					) VALUES (
					'".$poll_id."', 
					'".$polloption_id."', 
					'".$userid."', 
					'".$userip."')";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
				}
			}
			else {
				F_print_error("WARNING", $l['m_vote_unselect']);
			}
			break;
		}
		default :{ 
			break;
		}
	} //end of switch
	

	if((!$poll_id) OR $changelanguage) {
		$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE ((poll_level<=".$userlevel.") AND (poll_language='".$poll_language."') AND ((poll_date_start <= $actualdate) OR (poll_date_start = 0)) AND ((poll_date_end >= $actualdate) OR (poll_date_end = 0))) ORDER BY poll_name LIMIT 1";
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
			$poll_level = $m['poll_level'];
		}
	}
	else {
		F_display_db_error();
	}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_pollvote" id="form_pollvote">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php if($fullselect) {

//display language selector only if enabled languages are more than one
if (F_count_rows(K_TABLE_LANGUAGE_CODES, "WHERE language_enabled=1") > 1) {
?>
<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_pollvote_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="poll_language" id="poll_language" size="0" onchange="document.form_pollvote.changelanguage.value=1; document.form_pollvote.submit()">
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
</td>
</tr>
<!-- END SELECT language ==================== -->
<?php
}
else {
	echo "<input type=\"hidden\" name=\"poll_language\" id=\"poll_language\" value=\"".$poll_language."\" />";
}
?>

<!-- SELECT poll ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_poll', 'h_pollvote_poll'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changepoll" id="changepoll" value="0" />
<select name="poll_id" id="poll_id" size="0" onchange="document.form_pollvote.changepoll.value=1; document.form_pollvote.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE ((poll_level<=".$userlevel.") AND (poll_language='".$poll_language."') AND ((poll_date_start <= $actualdate) OR (poll_date_start = 0)) AND ((poll_date_end >= $actualdate) OR (poll_date_end = 0))) ORDER BY poll_name";
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
<td class="fillOO" colspan="2"><hr /></td>
</tr>

<!-- END SELECT poll ==================== -->
<?php 
} //end if($fullselect) 
else {
?>
<input type="hidden" name="poll_language" id="poll_language" value="<?php echo $poll_language; ?>" />
<input type="hidden" name="poll_id" id="poll_id" value="<?php echo $poll_id; ?>" />
<?php
}

if (!isset($poll_description)) {
	$poll_description = "";
}
?>

<!-- VOTE TABLE ==================== -->

<tr class="fillE">
<td class="fillEO" colspan="2"><?php echo F_evaluate_modules($poll_description); ?></td>
</tr>
<?php 
$rowclass = "O"; $rowodd=0;
if($poll_id) { //display polls options
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
			
			echo "<tr class=\"fill".$rowclass."\">";
			echo "<td class=\"fill".$rowclass."E\" colspan=\"2\">";
			echo "<input type=\"radio\" name=\"polloption_id\" value=\"".$m['polloption_id']."\" />";
			echo "".htmlentities($m['polloption_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
			echo "</td>";
			echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
}
?>

</table>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php F_submit_button("form_pollvote","menu_mode",$l['w_vote']); ?>
<?php F_submit_button("form_pollvote","menu_mode",$l['w_results']); ?>
</td></tr>

<!-- END OF VOTE TABLE ==================== -->
</table>
</form>

<?php
} //end of function

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
