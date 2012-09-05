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

class homepageAction extends Action
{
    function run()
    {
        if( isset($_POST['update']) ){
            $this->app->config->home_page_which_images = $this->app->getParamStr('home_page_which_images');
            if( !in_array($this->app->config->home_page_which_images, array('none', 'recent', 'random') ) ){
                $this->app->config->home_page_which_images = 'none';
            }
            $this->app->config->home_page_single_image = $this->app->getParamStr('home_page_single_image');
        	if( !in_array($this->app->config->home_page_single_image, array('none', 'recent', 'random') ) ){
            	$this->app->config->home_page_single_image = 'none';
            }

        	$this->app->config->home_page_images = max(0,$this->app->getParamInt('home_page_images'));
        	$this->app->config->home_page_image_width = max(0,$this->app->getParamInt('home_page_image_width'));
            $this->app->config->home_page_thumb_width = max(0,$this->app->getParamInt('home_page_thumb_width'));
        	$this->app->config->home_page_show_plans = $this->app->getParamInt('home_page_show_plans');
            $this->app->config->homepage_news_items = $this->app->getParamInt('homepage_news_items');
        	$this->app->savesettings(array(
        		'home_page_images',
        		'home_page_which_images',
        		'home_page_single_image',
        		'home_page_image_width',
    			'home_page_thumb_width',
    			'home_page_show_plans',
                'homepage_news_items'
               )
           );
        }
    }
}
