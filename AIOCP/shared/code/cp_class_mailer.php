<?php
//============================================================+
// File name   : cp_class_mailer.php                           
// Begin       : 2001-10-20                                    
// Last Update : 2003-10-27                                    
//                                                             
// Description : Extend PHPMailer class with inheritance       
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

require_once('../../shared/config/cp_email_config.'.CP_EXT); //Include default public variables
require_once("../../shared/phpmailer/class.phpmailer.php");

class C_mailer extends PHPMailer {

	/**
     * selected language code (e.g.: eng, ita, ...)
     * @var string array
     */
	var $language;
	
	
	/**
	 * Replace the default SetError
	 * @var string $msg error message
	 * @access public
     * @return void
	 */
	function SetError($msg) {
        $this->error_count++;
        $this->ErrorInfo = $msg;
        F_print_error("ERROR", $msg);
		exit;
    }
	
	
	/**
     * Returns a message in the appropriate language.
     * @var string $key language key
     * @access private
     * @return string
     */
    function Lang($key) {
        if(count($this->language) < 1) {
            $this->SetDefaultLanguage(); // set the default language
        }
        if(isset($this->language[$key])) {
            return $this->language[$key];
        }
        else {
            return "Language string failed to load: " . $key;
        }
    }
    
    
	/**
     * Sets the default language (eng) for all class error messages.  
     * Load error messages in selected language 
     * @access public
     * @return bool
     */
    function SetDefaultLanguage() {
    	require_once('../../shared/config/cp_extension.inc');
		require_once('../config/cp_config.'.CP_EXT);
		require_once('../../shared/code/cp_functions_language.'.CP_EXT);
		global $selected_language;
		
    	$PHPMAILER_LANG = array();
		$PHPMAILER_LANG["provide_address"] = F_word_language($selected_language, "m_mailerr_provide_address"); //'You must provide at least one recipient email address.';
		$PHPMAILER_LANG["mailer_not_supported"] = F_word_language($selected_language, "m_mailerr_mailer_not_supported"); //' mailer is not supported.';
		$PHPMAILER_LANG["execute"] = F_word_language($selected_language, "m_mailerr_execute"); //'Could not execute: ';
		$PHPMAILER_LANG["instantiate"] = F_word_language($selected_language, "m_mailerr_instantiate"); //'Could not instantiate mail function.';
		$PHPMAILER_LANG["connect_host"] = F_word_language($selected_language, "m_mailerr_connect_host"); //'SMTP Error: Could not connect to SMTP host.';
		$PHPMAILER_LANG["authenticate"] = F_word_language($selected_language, "m_mailerr_authenticate"); //'SMTP Error: Could not authenticate.';
		$PHPMAILER_LANG["data_not_accepted"] = F_word_language($selected_language, "m_mailerr_data_not_accepted"); //'SMTP Error: Data not accepted.';
		$PHPMAILER_LANG["file_access"] = F_word_language($selected_language, "m_mailerr_file_access"); //'Could not access file: ';
		$PHPMAILER_LANG["file_open"] = F_word_language($selected_language, "m_mailerr_file_open"); //'File Error: Could not open file: ';
		$PHPMAILER_LANG["encoding"] = F_word_language($selected_language, "m_mailerr_encoding"); //'Unknown encoding: ';
		$PHPMAILER_LANG["from_failed"] = F_word_language($selected_language, "m_mailerr_from_failed"); //'The following From address failed: ';
		$PHPMAILER_LANG["recipients_failed"] = F_word_language($selected_language, "m_mailerr_recipients_failed"); //'SMTP Error: The following recipients failed: ';
		
        $this->language = $PHPMAILER_LANG;
    	
        return true;
    }

} //end of class

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
