<?php
//============================================================+
// File name   : cp_checkframe.php                             
// Begin       : 2001-09-05                                    
// Last Update : 2005-06-29                                    
//                                                             
// Description : Check if page is loaded in the right frame    
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

if(K_CHECK_JAVASCRIPT) {
	//echo "<noscript><meta http-equiv='refresh' content='0;url=".K_REDIRECT_JAVASCRIPT_ERROR."' /></noscript>\n";
}
if(K_USE_FRAMES) {
?>
<script language="JavaScript" type="text/javascript">
//<![CDATA[
if(window.name != "<?php echo K_MAIN_FRAME_NAME; ?>") {
	document.write("<meta http-equiv='refresh' content='0;url=../code/index.<?php echo CP_EXT; ?>?load_page="+escape(document.location.href)+"' />");
}
//]]>
</script>
<?php
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
