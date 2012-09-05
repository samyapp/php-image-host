<?php

if( !defined('PIH' ) ) {
    header('HTTP/1.0 404 Not Found');
    exit();
}
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
class action
{
    var $requireLogin = false;
	/**
	  * The application object
	  */
	var $app = null;
	
	/**
	  * The config object (from the app)
	  */
	var $config = null;
	
	/**
	  * The Theme object (from the app)
	  */
	var $theme = null;
	
	function __construct($app)
	{
		$this->app = $app;
		$this->config = $app->config;
		$this->theme = $app->theme;
	}
	
	function action($app)
	{
		$this->__construct($app);
	}
	
	/**
	  * Do the action stuff!
	  */
	function run(){}

	/**
	  * Init stuff
	  */
	function init(){}
	
	function url($action = null, $query_string = null)
	{
		return $this->app->url($action, $query_string);
	}

    public function helper($name)
    {
        return $this->app->helper($name);
    }

}

