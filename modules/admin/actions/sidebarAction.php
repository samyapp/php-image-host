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

class sidebarAction extends Action
{
    function run()
    {
        if( isset($_POST['update']) ){
        	$this->app->config->sidebar_top_html = $this->app->getParamStr('sidebar_top_html');
        	$this->app->config->sidebar_bottom_html = $this->app->getParamStr('sidebar_bottom_html');
        	$this->app->config->sidebar_account_pos = $this->app->getParamStr('sidebar_account_pos');
        	$this->app->config->sidebar_images = max(0,$this->app->getParamInt('sidebar_images'));
            $this->app->config->sidebar_image_width = max(1,$this->app->getParamInt('sidebar_image_width'));
            $this->app->config->sidebar_image_type = $this->app->getParamStr('sidebar_image_type');
            $this->app->config->sidebar_news_items = $this->app->getParamInt('sidebar_news_items');
            $this->app->savesettings(array(
                    'sidebar_images',
                    'sidebar_image_type',
                    'sidebar_top_html',
                    'sidebar_bottom_html',
                    'sidebar_account_pos',
                    'sidebar_image_width',
                    'sidebar_news_items'
                )
            );
        }
    }
}

