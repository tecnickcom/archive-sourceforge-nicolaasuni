<?php
//============================================================+
// File name   : cp_contact_us.php                             
// Begin       : 2001-11-07                                    
// Last Update : 2008-07-06
//                                                             
// Description : Contact Us Form Mail                          
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

$pagelevel = 0;
require_once('../../shared/code/cp_authorization.'.CP_EXT);
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT);

$thispage_title = $l['t_contact_us'];

require_once('../../shared/code/cp_functions_language.'.CP_EXT);
$doc_charset = F_word_language($selected_language, "a_meta_charset");

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; 

//print company addresses:
require_once('../../shared/code/cp_functions_company_data.'.CP_EXT);  
$companydata = F_get_company_data();

require_once('../../shared/code/cp_form_mailer.'.CP_EXT);


if (isset($_REQUEST["name"])) {
	$name = $_REQUEST["name"];
} else {
	$name = "";
}
if (isset($_REQUEST["email"])) {
	$email = $_REQUEST["email"];
} else {
	$email = "";
}
if (isset($_REQUEST["subject"])) {
	$subject = $_REQUEST["subject"];
} else {
	$subject = "";
}
if (isset($_REQUEST["message"])) {
	$message = $_REQUEST["message"];
} else {
	$message = "";
}

?>


<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', ''); ?></b></td>
<td class="fillOE">
<?php echo $companydata['name']; ?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_address', ''); ?></b></td>
<td class="fillEE">
<?php echo $companydata['address']; ?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_city', ''); ?></b></td>
<td class="fillOE">
<?php echo $companydata['city']; ?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_state', ''); ?></b></td>
<td class="fillEE">
<?php echo $companydata['state']; ?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_postcode', ''); ?></b></td>
<td class="fillOE">
<?php echo $companydata['postcode']; ?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_country', ''); ?></b></td>
<td class="fillEE">
<?php echo $companydata['country']; ?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_phone', ''); ?></b></td>
<td class="fillOE">
<?php echo $companydata['telephone']; ?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_fax', ''); ?></b></td>
<td class="fillEE">
<?php echo $companydata['fax']; ?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_url', ''); ?></b></td>
<td class="fillOE">
<?php echo $companydata['url']; ?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_email', ''); ?></b></td>
<td class="fillEE">
<a href="mailto:<?php echo $companydata['email']; ?>"><?php echo $companydata['email']; ?></a>
</td>
</tr>

</table>
</td>
</tr>

</table>

<br /><br />

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_contactus" id="form_contactus">

<!-- specify to whom you wish for your form results to be mailed (comma separated emails): -->
<input type="hidden" name="ff_to" id="ff_to" value="<?php echo $companydata['email']; ?>" />
<input type="hidden" name="ff_cc" id="ff_cc" value="" />
<input type="hidden" name="ff_bcc" id="ff_bcc" value="" />

<!-- specify email type: true=html, false=text -->
<input type="hidden" name="ff_html" id="ff_html" value="true" />

<!-- if true show additional user data information at the end of email -->
<input type="hidden" name="ff_userdata" id="ff_userdata" value="true" />

<!-- redirect the user to a different URL after send -->
<input type="hidden" name="ff_redirect" id="ff_redirect" value="" />


<!-- sort form fields (a|r|o:fieldname,fieldname,...) means: a=alphabetic, r=reverse alphabetic, o=custom order -->
<input type="hidden" name="ff_sort" id="ff_sort" value="o:name,email" />

<!-- if true include not filled fields (true|false) -->
<input type="hidden" name="ff_print_blank" id="ff_print_blank" value="true" />

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="name,email,subject,message" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name'].",".$l['w_email'].",".$l['w_subject'].",".$l['w_message']; ?>" />

<!-- format of fields (regular expression) -->
<input type="hidden" name="x_email" id="x_email" value="^([a-zA-Z0-9_\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$" />
<input type="hidden" name="xl_email" id="xl_email" value="<?php echo $l['w_email']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge"><?php echo $l['w_send_message']; ?></th>
</tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', ''); ?></b></td>
<td class="fillOE"><input type="text" name="name" id="name" value="<?php echo stripslashes($name); ?>" size="40" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_email', ''); ?></b></td>
<td class="fillEE"><input type="text" name="email" id="email" value="<?php echo $email; ?>" size="40" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_subject', ''); ?></b></td>
<td class="fillOE"><input type="text" name="subject" id="subject" value="<?php echo stripslashes($subject); ?>" size="40" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_message', ''); ?></b></td>
<td class="fillEE"><textarea cols="40" rows="5" name="message" id="message"><?php echo htmlentities(stripslashes($message), ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
F_submit_button("form_contactus","menu_mode",$l['w_clear']); 
F_submit_button("form_contactus","menu_mode",$l['w_send']); 
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

