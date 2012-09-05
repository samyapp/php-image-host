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

class confirmAction extends action
{
	function init()
	{
		if( $this->app->userSession->loggedin ) {
			header('Location: '.$this->url('myimages'));
			exit();
		}
		elseif( !$this->config->email_confirmation ) {
			header('Location: '. $this->url('login'));
			exit();
		}
	}

	function run()
	{
		// check if the user has submitted the confirmation form 
		// (or has clicked on the url in their sign-up email that contains the confirmation code)

		$username = $this->app->getParamStr('username');
		$cid = $this->app->getParamStr('cid');
		$confirmed = false;
		$suspended = false;
		$errors = array();
		
		if( $this->app->getParamStr('confirm') != '' ){
			$user = $this->app->users->confirmuser($username, $cid);
			if( is_int($user) && $user == -1 ){
				$suspended = true;
			}elseif( !$user ){
				$errors[] = 'Invalid username or confirmation id.';
			}else{
				$confirmed = true;
			}
		}
		$user = null;
		if( $confirmed ){
			// if the user has successfully confirmed their email address, display the
			// relevant message (editable via the "page content" page in the admin area
			$this->theme->templateName = 'pagecontent';
			$this->theme->assign('templateContent', $this->theme->_t('Email Confirmed Content'));
		}elseif( $suspended ){
			header('Location: '.$this->url('suspended'));
			exit();
		}else{
			// if they haven't confirmed the account, display the confirmation required 
			// message and the confirmation form.
			foreach( array('username','cid','confirmed','suspended','errors') as $var_name ) {
				$this->theme->assign($var_name, $$var_name);
			}
		}
	}
}
