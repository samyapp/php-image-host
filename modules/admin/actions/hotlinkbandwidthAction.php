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

class hotlinkbandwidthAction extends Action
{

    function run()
    {
        $updated = false;
        $rewritten = false;
        if( isset($_POST['update']) ){
        	$this->app->config->hotlink_thumbnails = max(0,$this->app->getParamInt('hotlink_thumbnails'));
        	$this->app->config->hotlink_images = max(0,$this->app->getParamInt('hotlink_images'));
        	$this->app->config->monitor_thumbnail_bandwidth = max(0,$this->app->getParamInt('monitor_thumbnail_bandwidth'));
        	$this->app->config->monitor_image_bandwidth = $this->app->getParamInt('monitor_image_bandwidth');
           	$this->app->config->browse_thumb_links = $this->app->getParamInt('browse_thumb_links');
        	$this->app->config->browse_image_links = $this->app->getParamInt('browse_image_links');
        	$this->app->config->browse_thumb_embed = $this->app->getParamInt('browse_thumb_embed');
        	$this->app->config->browse_image_embed = $this->app->getParamInt('browse_image_embed');
        	$this->app->config->browse_thumb_bbcode = $this->app->getParamInt('browse_thumb_bbcode');
        	$this->app->config->browse_image_bbcode = $this->app->getParamInt('browse_image_bbcode');
            $this->app->config->rewrite_urls = $this->app->getParamInt('rewrite_urls');
            $this->app->config->htaccess_no_indexes = $this->app->getParamInt('htaccess_no_indexes');
        	$this->app->savesettings(array(
    			'monitor_image_bandwidth',
    			'monitor_thumbnail_bandwidth',
    			'hotlink_images',
    			'hotlink_thumbnails',
    			'browse_image_embed',
    			'browse_thumb_embed',
    			'browse_image_bbcode',
    			'browse_thumb_bbcode',
    			'browse_image_links',
    			'browse_thumb_links',
                'rewrite_urls',
                'htaccess_no_indexes'
        		)
        	);
        	$updated = true;
        }

        $rewriteRules = $this->app->loadClass('rewriterules');
        $rewrite = $rewriteRules->generateRewriteRules($this->app->config);

        if( $updated ){
        	$htaccess = '';
            $htaccess_filename = APP_DIR.'/.htaccess';
        	if( file_exists($htaccess_filename) && is_writable($htaccess_filename) ){
        		$htaccess = file_get_contents($htaccess_filename);
                $count = 0;
                $htaccess = $rewriteRules->updateRules($htaccess, $rewrite);
        		$fp = fopen($htaccess_filename, 'w');
        		fwrite($fp, $htaccess);
                fclose($fp);
                // file_put_contents($htaccess_filename, $htaccess);
        		$rewritten = true;
        	}
        }
        $this->theme->assign('rewrite', $rewrite);
        $this->theme->assign('updated', $updated);
        $this->theme->assign('rewritten', $rewritten);
    }
}
