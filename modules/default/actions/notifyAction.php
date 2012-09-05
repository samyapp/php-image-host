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
require_once dirname(__FILE__).'/../classes/paypal_ipn.class.php';

class notifyAction extends Action
{

	function run()
	{
		$debug = array();
		$ipn = new paypal_ipn($this->app->config->paypal_email, 0);
		$payments = $this->app->loadClass('paypalpayments');
		$users = $this->app->loadClass('users');
		$config = $this->app->config;
		// 1) Check it is a valid paypal ipn post...
		if( $ipn->process() == true ){
			// 2) check that the payment has been made to us
			if( $ipn->data['receiver_email'] == $config->paypal_email || $ipn->data['receiver_email'] == $config->paypal_account_email){
				$debug[] =  "Business checked ok...\n";
				// 3) check that payment made by a valid user (and get that user)
				$username = $ipn->data['item_number'];
				$user = $users->getuser(array('username'=>$username));
				if( $user ){
					$debug[] = "Got User<br />\n";
					// 4a) Handle subscription signup notifications...
					if( $ipn->data['txn_type'] == 'subscr_signup' ){
						$debug[] = "Signup notification<br />\n";
						// i) if user doesn't already have this subscription id, check that the months and price is valid and update user...
						if( $ipn->data['subscr_id'] != $user->sub_id ){
							$months = 0;
							if( preg_match('/^([0-9]+).*?m/i', $ipn->data['period3'],$match ) ) {
								$months = (int)$match[1];
							}
							$amount = (float)$ipn->data['amount3'];
							if( in_array($months, array(1,3,6,12) )){
								$avar = 'price_'.$months;
								$realprice = $config->$avar;
								if( $realprice == $amount ){
									$ups = array('sub_id'=>$ipn->data['subscr_id'], 'sub_months'=>$months, 'sub_amount'=>$amount);
									$users->updateusers(array($user->user_id),$ups);
								}
							}
						}
						$debug[] = "Signup notification processed...<br />";

					}
					// 4b) Handle subscription payments...
					elseif ( $ipn->data['txn_type'] == 'subscr_payment'){
						$debug[] = "Subscription payment<br />";
						// i) check that the payment is complete

						if( $ipn->isComplete() ){
							$debug[] = "Status is complete<br />\n";
							// ii) check that the currency is correct...
							if( $ipn->data['mc_currency'] == 'USD' ){
								$debug[] = "currency ok<br />";
								// iii) check that the price / duration is valid - first see if the user has these values set...
								$valid = false;
								$amount = (float)$ipn->data['mc_gross'];
								$months = 0;
								if( $user->sub_id != '' && $user->sub_id == $ipn->data['subscr_id'] ){
									if( $user->sub_amount != 0 && $user->sub_amount == $amount ){
										$months = $user->sub_months;
										$valid = true;
									}
								}
								// couldn't validate payment against user, so check our plans instead...
								if( !$valid ){
									if( $config->price_1 == $amount ){
										$months = 1;
									}elseif( $config->price_3 == $amount ){
										$months = 3;
									}elseif( $config->price_6 == $amount ){
										$months = 6;
									}elseif( $config->price_12 == $amount ){
										$months = 12;
									}
									if( $months != 0 ) $valid = true;
								}
								// if everything so far is valid, continue					

								if( $valid ){
									$debug[] = "Valid Payment<br />";
									// confirm this isn't a duplicate payment...
									if( !$payments->getpayments(array('count'=>true,'txnid'=>$ipn->data['txn_id'])) ){
										$debug[] = "Not a duplicate<br />";
										// add this payment to the database
										$pid = $payments->addpayment($ipn->data['txn_id'], $ipn->data['subscr_id'], 
																									$user->user_id,$ipn->data['payer_email'], 
																									$ipn->data['mc_gross'], 1, $ipn->data);

										if( $pid ){
											$debug[] = "Payment added<br />\n";
											// set the user to "paid account" and extend their days of paid 
											$users->upgradeuser($user,$months,$ipn);
											// if we are sending upgrade confirmation emails, send 1 :)
											$debug[] = "Payment added OK!";
										} // end payment inserted ok
									}else{ // end no duplicate payment
										$debug[] = "Duplicate payment\n";
									}
								}else{ // end everything is valid
									$debug[] = "Everything is not valid\n";
								}
							}else{ // end currency ok
								$debug[] = "Wrong currency\n";
							}
						}else{ // end complete
							$debug[] = "Payment is  not complete.";
						}
					}else{ // end payment type check
						$debug[] = "Payment type is wrong\n";
					}
				}else{ // end user check
					$debug[] = "User doesn't exist.\n";
				}
			}else{ // end business check
				$debug[] = "Receiver email (".$ipn->data['receiver_email']." != {$config->paypal_email}\n";
			}
		}else{
			$debug[] = "No bloody ipn.";
			switch( $ipn->error_code ){
				case ERR_INVALID: break;
				case ERR_SOCKETS: break;
			}
		}
		print_r($debug);
		exit();
	}
}