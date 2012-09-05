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

class joinAction extends action
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
		if( !$this->app->config->allow_signups ){
			// if new sign ups have been disabled via the admin, display a message about this, then exit.
			$this->theme->assign('templateContent', $this->theme->_t('No New Members Content'));
			$this->theme->templateName = 'pagecontent';
		}
	
		// initilize vars for the sign up form.

		$username = $this->app->getParamStr('username');
		$password = $this->app->getParamStr('password');
		$rpassword = $this->app->getParamStr('rpassword');
		$email = $this->app->getParamStr('email');
		$remail = $this->app->getParamStr('remail');
		$name = preg_replace('#[^a-z0-9'."'".'_ ,-]#i', '',$this->app->getParamStr('name'));

		$joined = false;
		$errors = array();

		// if the user has submitted the sign-up form, process it.
		// the code checks that various fields are valid, and that there is not already a user
		// with this username or email address.

		if( $this->app->getParamStr('join') != '' ){
			if( !preg_match('/^[a-z0-9]{2,20}$/i', $username) ) $errors[]='Your username must be between 6 and 20 alphanumeric (a-z0-9) characters long.';
			if( !preg_match('/^[a-z0-9]{6,20}$/i', $password) ) $errors[]='Your password must be between 6 and 20 alphanumeric (a-z0-9) characters long.';
			if( $password != $rpassword ) $errors[]='You must enter your password twice to confirm your choice.';
			if( $email == '' ){
				$errors[]='You must enter your email address.';
			}elseif( !$this->app->validateemail($email) ){
				$errors[]='You must enter a valid email address.';
			}elseif( $email != $remail ){
				$errors[]='You must confirm your email address by entering it twice.';
			}
			if( !preg_match('/[a-z]{2,}/i', $name) ) $errors[]='You must enter your name.';
			if( count($errors) == 0 ) {
				$sql = "SELECT username, email FROM users WHERE username='".mysql_real_escape_string($username)."' OR email='".mysql_real_escape_string($email)."' ";
				$res = $this->app->query($sql, 'Check for existing username or email');
				while( list($uname, $em) = mysql_fetch_row($res) ){
					if( strtolower($uname) == strtolower($username) ){
						$errors[]='The username "'.$username.'" is already in our database. Please choose a different username.';
					}
					elseif( strtolower($em) == strtolower($email) ){
						$errors[]='The email address "'.$email.'" is already in our database.';
					}
				}
				if( $this->app->getParamInt('agree') == 0 ){
					$errors[]='You must confirm that you have read and agree to our terms &amp; conditions.';
				}
				if( count($errors) == 0 ) {
					// call the function that actually adds the user to the database.
					if( $this->app->users->adduser($username, $password, $email, $name) ){
						$joined = true;

						// send a confirmation email - if the admin requires new users to confirm their email address
						// then this includes instructions on how to do so.

						$from = $this->app->config->signup_email_from;
						$subject = str_replace('{sitename}', $this->app->config->sitename, $this->app->config->signup_email_subject);
						$message = $this->app->config->signup_email_template;
						if( $this->app->config->email_confirmation == 0 ){
							$message = preg_replace('/<confirm>.*?<\/confirm>/is', '', $message);
						}
						else{
							$message = preg_replace('/<[\/]{0,1}confirm>/is', '', $message);
						}
						$cid = substr(md5($email.$username.$this->app->config->email_confirmation_key),0,12);
                        $curl = $this->url('confirm', 'username='.$username.'&cid='.$cid);
						$message = str_replace(array('{confirmid}', '{name}', '{username}', '{password}', '{sitename}',
																		'{siteurl}','{confirmurl}'),
															array($cid, $name, $username, $password,
																	$this->app->config->sitename, $this->app->config->siteurl, $curl),
															$message);
						$headers = "From: $from\r\nReply-To: $from\r\nReturn-Path: $from\r\nErrors-To: $from";
						@mail($email, $subject, $message, $headers);
					}
				}
			}
		}
		if( $joined ) {
			$this->theme->templateName = 'pagecontent';
			$this->theme->assign('templateContent', $this->theme->_t(
													$this->app->config->email_confirmation ? 'Signed Up - Unconfirmed Content' : 'Signed Up - Complete Content',
													array(), true));
		}
		else {
			foreach( array('username' ,'password','rpassword','email','remail','name','errors' ) as $var_name ) {
				$this->theme->assign($var_name, $$var_name);
			}
		}
	}
}
