<?php
//============================================================+
// File name   : inc_gestpay_crypt.php                         
// Begin       : 2002-09-13                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Cryptography module                           
//               GestPay from Banca Sella (www.sellanet.it)    
//                                                             
// Implementation of GestPayCrypt e GestPayCryptHS classes     
// from original Java Class module (Autor: Sellanet ver. 1.0)  
//                                                             
// To use GestPayCryptHS you must install                      
// curl (http://curl.haxx.se)                                  
//                                                             
// Note: all parameters must be strings.                       
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

// Path curl 
define("CURL_BIN", "/usr/local/bin/curl");

class GestPayCrypt {
	var $ShopLogin; // Shop Login che identifica l'esercente
	var $Currency; // Codice che identifica la divisa in cui è denominato l'importo
	var $Amount; // Importo della transazione
	var $ShopTransactionID; // Identificativo attribuito alla transazione dall'esercente
	var $BuyerName; // Nome e cognome dell'acquirente
	var $BuyerEmail; // Indirizzo email dell'acquirente
	var $Language; // Codice della lingua per la comunicazione con l'acquirente
	var $CustomInfo; // Stringa che contiene informazione specifiche dell'esercente
	var $AuthorizationCode; // Codice di autorizzazione della transazione
	var $ErrorCode; // Codice d'errore
	var $ErrorDescription; // Descrizione dell'errore
	var $BankTransactionID; // Identificativo attribuito alla transazione da GestPay
	var $AlertCode; // Codice alert
	var $AlertDescription; // Descrizione alert
	var $EncryptedString; // Stringa cifrata
	var $ToBeEncript;
	var $Decrypted;
	var $TransactionResult; // Esito transazione
	var $ProtocolAuthServer; //protocollo (http o https)
	var $DomainName; //nome del dominio del server della banca
	var $SocketPort; //default connection port
	var $SocketTimeout; //max timeout connection in seconds
	var $separator; // separatore per parametri
	var $errDescription; // descrizione errore
	var $errNumber; // codice errore
	
	var $ScriptEncrypt;
	var $ScriptDecrypt;
	
	var $SendCardInfo; // se vero invia i dati della carta di credito
	var $CardNumber; // Numero carta di credito
	var $ExpMonth; // Mese di scadenza carta di credito
	var $ExpYear; // Anno di scadenza carta di credito
	
	/**
	* constructor
	*/
	function GestPayCrypt() {
		// Initialize variables
		$this->ShopLogin = "";
		$this->Currency = "";
		$this->Amount = "";
		$this->ShopTransactionID = "";
		$this->CardNumber = "";
		$this->ExpMonth = "";
		$this->ExpYear = "";
		$this->BuyerName = "";
		$this->BuyerEmail = "";
		$this->Language = "";
		$this->CustomInfo = "";
		$this->AuthorizationCode = "";
		$this->ErrorCode = "";
		$this->ErrorDescription = "";
		$this->BankTransactionID = "";
		$this->AlertCode = "";
		$this->AlertDescription = "";
		$this->EncryptedString = "";
		$this->ToBeEncript = "";
		$this->Decrypted = "";
		$this->ProtocolAuthServer = "http";
		$this->DomainName = "ecomm.sella.it";
		$this->SocketPort = 80;
		$this->SocketTimeout = 60;
		
		$this->separator ="*P1*";
		$this->errDescription ="";
		$this->errNumber="0";
		
		$this->ScriptEncrypt = "/CryptHTTP/Encrypt.asp";
		$this->ScriptDecrypt = "/CryptHTTP/Decrypt.asp";
		
		$this->SendCardInfo = false;
		$this->CardNumber = "";
		$this->ExpMonth = "";
		$this->ExpYear = "";
	}
	
	// set values methods
	
	function SetShopLogin($xstr) {
		$this->ShopLogin = $xstr;
	}
	function SetCurrency($xstr) {
		$this->Currency = $xstr;
	}
	function SetAmount($xstr) {
		$this->Amount = $xstr;
	}
	function SetShopTransactionID($xstr) {
		$this->ShopTransactionID = $xstr;
	}
	function SetBuyerName($xstr) {
		$this->BuyerName = $xstr;
	}
	function SetBuyerEmail($xstr) {
		$this->BuyerEmail = $xstr;
	}
	function SetLanguage($xstr) {
		$this->Language = $xstr;
	}
	function SetCustomInfo($xstr) {
		$this->CustomInfo = $xstr;
	}
	function SetEncryptedString($xstr) {
		$this->EncryptedString = $xstr;
	}
	function SetSendCardInfo($xstr) {
		$this->SendCardInfo = $xstr;
	}
	function SetCardNumber($xstr) {
		$this->CardNumber = $xstr;
	}
	function SetExpMonth($xstr) {
		$this->ExpMonth = $xstr;
	}
	function SetExpYear($xstr) {
		$this->ExpYear = $xstr;
	}
	
	// get values methods
	
	function GetShopLogin() {
		return $this->ShopLogin;
	}
	function GetCurrency() {
		return $this->Currency;
	}
	function GetAmount() {
		return $this->Amount;
	}
	function GetShopTransactionID() {
		return $this->ShopTransactionID;
	}
	function GetBuyerName() {
		return $this->BuyerName;
	}
	function GetBuyerEmail() {
		return $this->BuyerEmail;
	}
	function GetCustomInfo() {
		return $this->CustomInfo;
	}
	function GetAuthorizationCode() {
		return $this->AuthorizationCode;
	}
	function GetErrorCode() {
		return $this->ErrorCode;
	}
	function GetErrorDescription() {
		return $this->ErrorDescription;
	}
	function GetBankTransactionID() {
		return $this->BankTransactionID;
	}
	function GetTransactionResult() {
		return $this->TransactionResult;
	}
	function GetAlertCode() {
		return $this->AlertCode;
	}
	function GetAlertDescription() {
		return $this->AlertDescription;
	}
	function GetEncryptedString() {
		return $this->EncryptedString;
	}
	
	/**
	* Encript string and verify it online
	*/
	function Encrypt() {
		
		$this->ErrorCode = "0";
		$this->ErrorDescription = "";
		
		if (strlen($this->ShopLogin) <= 0) {
			$this->ErrorCode="546";
			$this->ErrorDescription="IDshop not valid";
			return false;
		}
		if (strlen($this->Currency) <= 0) {
			$this->ErrorCode="552";
			$this->ErrorDescription="Currency not valid";
			return false;
		}
		if (strlen($this->Amount) <= 0) {
			$this->ErrorCode="553";
			$this->ErrorDescription="Amount not valid";
			return false;
		}
		if (strlen($this->ShopTransactionID) <= 0) {
			$this->ErrorCode="551";
			$this->ErrorDescription="Shop Transaction ID not valid";
			return false;
		}
		
		$this->ToBeEncript = "";
		
		$this->Set_ToBeEncrypt($this->Currency, "PAY1_UICCODE");
		$this->Set_ToBeEncrypt($this->Amount, "PAY1_AMOUNT");
		$this->Set_ToBeEncrypt($this->ShopTransactionID, "PAY1_SHOPTRANSACTIONID");
		$this->Set_ToBeEncrypt($this->BuyerName, "PAY1_CHNAME");
		$this->Set_ToBeEncrypt($this->BuyerEmail, "PAY1_CHEMAIL");
		$this->Set_ToBeEncrypt($this->Language, "PAY1_IDLANGUAGE");
		$this->Set_ToBeEncrypt($this->CustomInfo, "");
		
		if ($this->SendCardInfo) {
			$this->Set_ToBeEncrypt($this->CardNumber, "PAY1_CARDNUMBER");
			$this->Set_ToBeEncrypt($this->ExpMonth, "PAY1_EXPMONTH");
			$this->Set_ToBeEncrypt($this->ExpYear, "PAY1_EXPYEAR");
		}
		
		$this->ToBeEncrypt = str_replace(" ", "§", $this->ToBeEncrypt);
		
		$uriString = $this->ScriptEncrypt."?a=".$this->ShopLogin."&b=".substr($this->ToBeEncrypt, strlen($this->separator));
		
		$this->EncryptedString = $this->HttpGetResponse($this->DomainName, $uriString, true);
		
		$this->EncryptedString = $this->HttpGetResponse($this->DomainName, $uriString, true);
		
		if ($this->EncryptedString == -1) {
			return false;
		}
		
		return true;
	}
	
	/**
	* decrypt a string
	*/
	function Decrypt() {
		
		$this->ErrorCode = "0";
		$this->ErrorDescription = "";
		
		if (strlen($this->ShopLogin) <= 0) {
			$this->ErrorCode="546";
			$this->ErrorDescription="IDshop not valid";
			return false;
		}
		if (strlen($this->EncryptedString) <= 0) {
			$this->ErrorCode="1009";
			$this->ErrorDescription="String to Decrypt not valid";
			return false;
		}
		
		$uriString = $this->ScriptDecrypt."?a=".$this->ShopLogin."&b=".$this->EncryptedString;
		
		$this->Decrypted = $this->HttpGetResponse($this->DomainName, $uriString, false);
		
		if ($this->Decrypted == -1) {
			return false;
		}
		elseif (empty($this->Decrypted)) {
			$this->ErrorCode = "9999";
			$this->ErrorDescription = "Void String";
			return false;
		}
		
		$this->Decrypted = str_replace("§", " ", $this->Decrypted);
		
		if (!$this->Parsing($this->Decrypted)) {
			return false;
		}
		
		return true;
	}
	
	/**
	* parse a string
	*/
	function Parsing($StringToBeParsed) {
		
		$this->ErrorCode="";
		$this->ErrorDescription="";
		
		$data_array = explode($this->separator, $StringToBeParsed);
		
		foreach ($data_array as $tagPAY1) {
			$tagPAY1val = explode("=", $tagPAY1);
			
			if (ereg("^PAY1_UICCODE", $tagPAY1)) {
				$this->Currency = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_AMOUNT", $tagPAY1)) {
				$this->Amount = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_SHOPTRANSACTIONID", $tagPAY1)) {
				$this->ShopTransactionID = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_CHNAME", $tagPAY1)) {
				$this->BuyerName = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_CHEMAIL", $tagPAY1)) {
				$this->BuyerEmail = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_AUTHORIZATIONCODE", $tagPAY1)) {
				$this->AuthorizationCode = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_ERRORCODE", $tagPAY1)) {
				$this->ErrorCode = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_ERRORDESCRIPTION", $tagPAY1)) {
				$this->ErrorDescription = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_BANKTRANSACTIONID", $tagPAY1)) {
				$this->BankTransactionID = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_ALERTCODE", $tagPAY1)) {
				$this->AlertCode = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_ALERTDESCRIPTION", $tagPAY1)) {
				$this->AlertDescription = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_CARDNUMBER", $tagPAY1)) {
				$this->CardNumber = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_EXPMONTH", $tagPAY1)) {
				$this->ExpMonth = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_EXPYEAR", $tagPAY1)) {
				$this->ExpYear = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_IDLANGUAGE", $tagPAY1)) {
				$this->Language = $tagPAY1val[1];
			}
			elseif (ereg("^PAY1_TRANSACTIONRESULT", $tagPAY1)) {
				$this->TransactionResult = $tagPAY1val[1];
			}
			else {
				$this->CustomInfo .= $tagPAY1.$this->separator;
			}
		}
		
		//remove last separator from CustomInfo
		$this->CustomInfo = substr($this->CustomInfo, 0, - strlen($this->separator));
		
		return true;
	}
	
	/**
	*
	*/
	function Set_ToBeEncrypt($variable_value, $variable_name) {
		if (strlen($variable_name) > 0) {
			$equal = "=";
		}
		else {
			$equal = "";
		}
		if (strlen($variable_value) > 0) {
			$this->ToBeEncrypt .= $this->separator."".$variable_name."".$equal."".$variable_value;
		}
	}
	
	/**
	*
	*/
	function HttpGetResponse($hostname, $document, $crypt) {
		$sErr = "";
		$response = "";
		
		if ($crypt) {
			$req = "crypt";
		}
		else {
			$req = "decrypt";
		}
		
		$line = $this->HttpGetLine($hostname, $document);
		
		if ($line == -1) {
			return -1;
		}
		
		if (preg_match("/#".$req."string#([\w\W]*)#\/".$req."string#/", $line, $reg)) {
			$response = trim($reg[1]);
		}
		elseif (preg_match("/#error#([\w\W]*)#\/error#/", $line, $reg)) {
			$sErr = explode("-", $reg[1]);
			
			if (empty($sErr[0]) && empty($sErr[1])) {
				$this->ErrorCode = "9999";
				$this->ErrorDescription = "Unknown error";
			}
			else {
				$this->ErrorCode = trim($sErr[0]);
				$this->ErrorDescription = trim($sErr[1]);
			}
			return -1;
		}
		else {
			$this->ErrorCode = "9999";
			$this->ErrorDescription = "Response from server not valid";
			return -1;
		}
		
		return $response;
	}
	
	/**
	*
	*/
	function HttpGetLine($hostname, $document) {
		$answer = "";
		
		$fp = fsockopen($hostname, $this->SocketPort, $errno, $errstr, $this->SocketTimeout);
		if (!$fp) {
			$this->ErrorCode = "9999";
			$this->ErrorDescription = "Impossible to connect to host: ".$hostname."<br />(".$errno.") ".$errstr."";
			return -1;
		}
		else {
			fputs($fp, "GET ".$document." HTTP/1.0\r\n\r\n");
		}
		
		while (fgets($fp, 4096) != "\r\n") {}; // Salta gli header
		
		// Legge solo la prima riga
		$answer = fgets($fp, 4096);
		
		fclose($fp);
		return $answer;
	}
}

/**
* Class GestPayCryptHS
* HTTPS version of GestPayCrypt
*/
class GestPayCryptHS extends GestPayCrypt {
	/**
	* constructor
	*/
	function GestPayCryptHS() {
		// Initialize variables
		$this->ShopLogin = "";
		$this->Currency = "";
		$this->Amount = "";
		$this->ShopTransactionID = "";
		$this->CardNumber = "";
		$this->ExpMonth = "";
		$this->ExpYear = "";
		$this->BuyerName = "";
		$this->BuyerEmail = "";
		$this->Language = "";
		$this->CustomInfo = "";
		$this->AuthorizationCode = "";
		$this->ErrorCode = "";
		$this->ErrorDescription = "";
		$this->BankTransactionID = "";
		$this->AlertCode = "";
		$this->AlertDescription = "";
		$this->EncryptedString = "";
		$this->ToBeEncript = "";
		$this->Decrypted = "";
		$this->ProtocolAuthServer = "https";
		$this->DomainName = "ecomm.sella.it";
		$this->SocketPort = 80;
		$this->SocketTimeout = 60;
		
		$this->separator ="*P1*";
		$this->errDescription ="";
		$this->errNumber="0";
		
		$this->ScriptEncrypt = "/CryptHTTP/Encrypt.asp";
		$this->ScriptDecrypt = "/CryptHTTP/Decrypt.asp";
		
		$this->SendCardInfo = false;
		$this->CardNumber = "";
		$this->ExpMonth = "";
		$this->ExpYear = "";
	}
	
	function HttpGetLine($hostname, $document) {
		$exec_str = CURL_BIN." -m 120 "."\"".$this->ProtocolAuthServer."://".$hostname."".$document."\" -L";
		
		exec($exec_str, $ret_arr, $ret_num);
		
		if ($ret_num != 0) {
			$this->ErrorCode = "9999";
			$this->ErrorDescription = "Error while executing: ".$exec_str;
			return -1;
		}
		
		if (!is_array($ret_arr)) {
			$this->ErrorCode = "9999";
			$this->ErrorDescription = "Error while executing: ".$exec_str." - "."\$ret_arr is not an array";
			return -1;
		}
		return $ret_arr[0];
	}
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>

