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

class remindAction extends action
{
	function init()
	{
		if( $this->app->userSession->loggedin ){
			header('Location: '.$this->url('myimages'));
			exit();
		}
	}

	function run()
	{

	// init variables

		$username = $this->app->getParamStr('username');
		$email = $this->app->getParamStr('email');
		$reminded = false;
		$remindererror = '';
		if( $email != '' ) $username = '';

		// process the reminder form submission

		if( $this->app->getParamStr('remind') != '' ){
			$criteria = array();
			if( $username != '' ){
				$criteria['username'] = $username;
			}else{
				$criteria['email'] = $email;
			}

			$user = $this->app->users->getuser($criteria);
			if( $user ){
				$reminded = true;
				$from = $this->app->config->reminder_email_from;
				$subject = str_replace('{sitename}', $this->app->config->sitename, $this->app->config->reminder_email_subject);
				$message = $this->app->config->reminder_email_template;
				if( $this->app->config->email_confirmation == 0 || $user->status == 1){
					$message = preg_replace('/<confirm>.*?<\/confirm>/is', '', $message);
				}else{
					$message = preg_replace('/<[\/]{0,1}confirm>/is', '', $message);
				}
				$cid = substr(md5($user->email.$user->username.$this->app->config->email_confirmation_key),0,12);
                $curl = $this->url('confirm', 'username='.$user->username.'&cid='.$cid);
				$message = str_replace(array('{confirmid}', '{name}', '{username}', '{password}', '{sitename}',
																			'{siteurl}', '{confirmurl}'),
																array($cid, $user->name, $user->username, $user->pass, 
																			$this->app->config->sitename,
																			$this->app->config->siteurl,
                                                                            $curl),
																$message);
				$headers = "From: $from\r\nReply-To: $from\r\nReturn-Path: $from\r\nErrors-To: $from";
				@mail($user->email, $subject, $message, $headers);
				$user = null;
			}else{
				$remindererror = 'The email address or username "'.addslashes($email == '' ? $username : $email).'" is not in our database.';
			}
		}

		if( $reminded ) {
			$this->theme->templateName = 'pagecontent';
			$this->theme->assign('templateContent', $this->theme->_t('Reminder Sent Content'));
		}
		else {
			foreach( array('username', 'email', 'remindererror' ) as $var_name ) {
				$this->theme->assign($var_name, $$var_name);
			}
		}
	}
}
