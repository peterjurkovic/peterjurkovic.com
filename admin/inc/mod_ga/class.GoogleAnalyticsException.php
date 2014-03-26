<?php
/**
* Google Analytics Exception class
*
* @author Peter Jurkovic (into@peterjurkovic.sk)
* @version 20110411
*/
class GoogleAnalyticsException extends exception{

	
	
	private $log_file = "/ga.ErrorLog.txt";
	
	
	public function __construct($message = false){
		
		if($fp = fopen(dirname(__FILE__).$this->log_file, 'a')){
			$log_msg = date("[Y-m_d H:i:s]"). "- Message: $message \n";
			fwrite($fp, $log_msg);
			fclose($fp);
		}
		
		parent:: __construct( $message);
	
	}

} 


?>