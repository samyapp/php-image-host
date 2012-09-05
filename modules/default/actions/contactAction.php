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

class contactAction extends action
{
	function run()
	{
		$user = $this->app->userSession->user;
		$name = $this->app->getParamStr('name', $user ? $user->name : '');
		$email = $this->app->getParamStr('email', $user ? $user->email : '');
		$subject = $this->app->getParamStr('subject');
		$subject = preg_replace('#content-type|bcc:|cc:|to:#i', '', $subject);

		$message = $this->app->getParamStr('message');
		$message = preg_replace('#content-type|bcc:|cc:|to:#i', '', $message);
		$errors = array();
		$submitted = false;

		// process contact form submission and send an email if successful

		if( $this->app->getParamStr('submit') ){
			if( $email == '' || !$this->app->validateemail($email) ){
				$errors[] = 'You must enter your email address.';
			}
			if( $name == '' ) $errors[] = 'You must enter your name.';
			if( $subject == '' ) $errors[] = 'You must enter a subject.';
			if( strlen($message) < 10 ) $errors[] = 'You must enter your message.';
			if( count($errors) == 0 ) {
				$to = $this->app->config->admin_email;
				$headers = "From: $email\r\nReply-To: $email\r\nErrors-To: $to\r\nReturn-Path: $to";
				$ip = isset($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
				$message = 'Submission from '.$this->app->config->sitename.' contact form sent by '.$name.' ('.$ip.') on '.date('l jS F Y @ H:i')."\n\n".$message;
				@mail($to, $subject, $message, $headers);
				$submitted = true;
			}
		}
		if( $submitted ) {
			$this->theme->templateName = 'pagecontent';
			$this->theme->templateContent = $this->theme->_t('Contact Submitted Content');
		}
		else {
			foreach( array('errors', 'name', 'subject', 'message', 'email') as $var_name ) {
				$this->theme->assign($var_name, $$var_name);
			}
		}
	}
}
