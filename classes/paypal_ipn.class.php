<?php
/*

PHP Image Host
www.phpace.com/php-image-host

Copyright (c) 2004,2008 Sam Yapp

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/
@set_time_limit(60); // Attempt to double default time limit incase we switch to Get

if(phpversion() < '4.3.0' || !function_exists('file_get_contents')){
	function file_get_contents($ipnget){
		$ipnget = @file($ipnget);
		return $ipnget[0];
	}
}

define('ERR_NONE', 1);
define('ERR_BUSINESS', -1);
define('ERR_INVALID', -2);
define('ERR_SOCKETS', -3);

class paypal_ipn{

	var $testing_domain = 'www.eliteweaver.co.uk';
	var $paypal_domain = 'www.paypal.com';
	var $data = array();
	var $error_code;

	function paypal_ipn($paypal_email, $testing = 0){
		$this->paypal_email = $paypal_email;
		$this->testing = $testing;
	}

	function process($headers = true){
		$this->data = array();
		$this->error_code = ERR_NONE;
		if (phpversion() <= '4.0.6'){
			$_SERVER = ($HTTP_SERVER_VARS);
			$_POST = ($HTTP_POST_VARS);
		}
		if( !isset($_POST['txn_id']) && !isset($_POST['txn_type'])){
			$this->error_code = ERR_INVALID;
			if( $headers ){
				@header("Status: 404 Not Found");
				exit();
			}
			return false;
		}

		if( $headers ) @header("Status: 200 OK"); // prevents ipn reposts on some servers
		$postipn = 'cmd=_notify-validate';

		foreach($_POST as $ipnkey => $ipnval){
			if(get_magic_quotes_gpc()){
				$ipnval = stripslashes ($ipnval); // Fix issue with magic quotes
			}
			
			if (!eregi("^[_0-9a-z-]{1,30}$",$ipnkey) || !strcasecmp ($ipnkey, 'cmd')){ // ^ Antidote to potential variable injection and poisoning
				unset ($ipnkey);
				unset ($ipnval);
			} // Eliminate the above

			if(@$ipnkey != '') { // Remove empty keys (not values)
				$this->data[$ipnkey] = $ipnval; // Assign data to new global array
				unset($_POST); // Destroy the original ipn post array, sniff...
				$postipn.='&'.$ipnkey.'='.urlencode($ipnval);
			}
		} // Notify string
		$error=0; // No errors let's hope it's going to stays like this!
		// IPN validation mode 1: Live Via PayPal Network

		if($this->testing == 0){
			$domain = $this->paypal_domain;
		}else{
			// IPN validation mode 2: Test Via EliteWeaver UK
			$domain = "www.eliteweaver.co.uk";
		}
		
		// Post back the reconstructed instant payment notification

		$socket = @fsockopen($domain,80,$errno,$errstr,30);
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header.= "User-Agent: PHP/".phpversion()."\r\n";
		$header.= "Referer: ".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].@$_SERVER['QUERY_STRING']."\r\n";
		$header.= "Server: ".$_SERVER['SERVER_SOFTWARE']."\r\n";
		$header.= "Host: ".$domain.":80\r\n";
		$header.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header.= "Content-Length: ".strlen($postipn)."\r\n";
		$header.= "Accept: */*\r\n\r\n";

		//* Note: "Connection: Close" is not required using HTTP/1.0
		// Problem: Now is this your firewall or your ports?

		if(!$socket && !$error){
			// Switch to a Get request for a last ditch attempt!
			$getrq=1;

			$response = @file_get_contents('http://'.$domain.':80/cgi-bin/webscr?'.$postipn);

			if (!$response){
				$this->error_code = ERR_SOCKETS;
				$error=1;
				$getrq=0;
			}
		}else{
			@fputs($socket,$header.$postipn."\r\n\r\n"); // Required on some environments
			while(!feof($socket)){
				$response = fgets ($socket,1024);
			}
		}
		$response = trim ($response); // Also required on some environments

		// IPN was confirmed as both genuine and VERIFIED

		if (!strcmp ($response, "VERIFIED")){
			return true;
		}elseif( !strcmp($response, "INVALID") ){
			$this->error_code = ERR_INVALID;
		}
		return false;
	}

	function checkBusiness($business = ''){
		if( $business == '' ) $business = $this->paypal_email;
		if( $business != $this->data['business'] ){
			return false;
		}else{
			return true;
		}
	}

	function isComplete(){
		if( $this->data['payment_status'] == 'Completed' ){
			return true;
		}else{
			return false;
		}
	}

	function variableAudit($varname, $correct_value ){
		if( !isset($this->data[$varname]) || $this->data[$varname] != $correct_value ) return false;
		return true;
	}

}

?>
