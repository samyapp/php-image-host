<?php
/*

PHP Image Host
www.phpace.com/php-image-host

Copyright (c) 2004,2008,2009 Sam Yapp

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
class userSession
{

	var $app = null;
	
	var $userid = 0;
	var $user = 0;
	var $loggedin = false;
	var $suspended = false;
	var $unconfirmed = false;
	var $loginerror = '';
	var $banned = false;

	function __construct(&$app)
	{
		$this->app =& $app;
	}
	
	function userSession(&$app)
	{
		$this->__construct($app);
	}

    function getAnonymousUser()
    {
        $user = $this->app->users->getuser(array('username'=>$this->app->config->anonymous_account));
        return $user;
    }

	function init()
	{
		if( $this->app->config->ban_ips && $this->app->ipbanned() ){
			$this->banned = true;
		}
		else{

			if( $this->app->getParamStr('login') != '' ){
				$uname = $this->app->getParamStr('username');
				$upass = $this->app->getParamStr('password');
				$this->user = $this->app->users->getuser(array('username'=>$uname, 'password'=>$upass));
				if( $this->user ){
					if( $this->user->status == 1 ){
						$_SESSION['user_loggedin'] = true;
						$_SESSION['user_userid'] = $this->user->user_id;
						$this->loggedin = true;
						$ip = isset($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
						$ups = array('lastlogin'=>date('Y-m-d H:i:s'), 'loginip'=>$ip);
						if( $this->user->account_type == 'paid' && $this->user->paid_until < date('Y-m-d') ){
							$ups['account_type'] = 'free';
						}	
						$this->app->users->updateusers(array($this->user->user_id),$ups);
					}
					elseif( $this->user->status == 0 ){
						$this->unconfirmed = true;
						$this->user = null;
					}
					else{
						$this->suspended = true;
						$this->user = null;
					}
				}
				else{
					$this->loginerror = 'Invalid username / password.';
					$this->user = null;
				}
			}
			elseif( $this->app->getParamInt('logout') != 0 ){
				unset($_SESSION['user_loggedin']);
				unset($_SESSION['user_userid']);
			}
	
		}
		if( !$this->banned && !$this->loggedin && isset($_SESSION['user_loggedin']) ){
			$this->userid = (int)$_SESSION['user_userid'];
			$this->user = $this->app->users->getuser(array('id'=>$this->userid));
			if( $this->user  ){
				switch( $this->user->status ){
					case 1: $this->loggedin = true; break;
					case 0: $this->unconfirmed = true; $this->user = $this->userid = 0; break;
					case 2: $this->suspended = true; $this->user = $this->userid = 0; break;
				}
			}
			else{
				$this->userid = 0;
				unset($_SESSION['user_loggedin']);
				unset($_SESSION['user_userid']);
			}
		}
	}
}