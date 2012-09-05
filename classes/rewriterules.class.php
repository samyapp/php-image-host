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
class rewriterules
{
    var $beginMarker = '##begin phpimagehost htaccess and rewrite rules##';
    var $endMarker = '##end phpimagehost rewrite rules##';
    var $app = null;

    function __construct($app)
    {
        $this->app = $app;
    }

    function rewriterules($app)
    {
        $this->__construct($app);
    }

/**
 * Generate .htaccess mod_rewrite rules from config settings
 * @param array $config Associative array of configurations settings
 * @return string The mod_rewrite rules to use
 */
    function generateRewriteRules($config)
    {
        // generate mod-rewrite htaccess rules
        $rewrite = array($this->beginMarker);
        if( $config->htaccess_no_indexes ) {
            $rewrite[] = "\n# Don't allow directory browsing (for images / thumbs / etc)";
            $rewrite[] = "Options -Indexes\n\n";
        }
        if( !$config->hotlink_thumbnails || !$config->hotlink_images || $config->rewrite_old_urls 
    		|| $config->monitor_thumbnail_bandwidth || $config->monitor_image_bandwidth 
            || $config->rewrite_urls) {
            $rewrite[] = "<ifModule mod_rewrite.c>";
            $rewrite[] = "Options +FollowSymLinks";
        	$rewrite[] = "RewriteEngine On";
            if( $config->rewrite_old_urls ) {
                $rewrite[] = $this->rewriteOldURLs();
            }
            $rewrite[] = $this->getImageBandwidthRules($config);
            $rewrite[] = $this->getImageHotlinkRules($config);
            $rewrite[] = $this->getThumbnailHotlinkRules($config);
            $rewrite[] = $this->getThumbnailBandwidthRules($config);
            $rewrite[] = $this->getURLRewriteRules($config);
            $rewrite[] = "</ifModule>";
        }
        $rewrite[] = $this->endMarker."\n\n";
        return join("\n", $rewrite);
    }

    function rewriteOldURLs()
    {
        $rewrites = array();
//        $rewrites[] = 'RewriteRule images\.php index.php?cmd=myimages [NC,L]';
//        $rewrites[] = 'RewriteRule ([a-z]+)\.php index.php?cmd=$1 [NC,L]';
        return join("\n", $rewrites);
    }

    function getURLRewriteRules($config)
    {
        $rewrite = array();
        if( $config->rewrite_urls ) {
            $rewrite[] = 'RewriteCond %{REQUEST_URI} !modules';
            $rewrite[] = 'RewriteCond %{SCRIPT_FILENAME} !-f';
            $rewrite[] = 'RewriteCond %{SCRIPT_FILENAME} !-d';
            $rewrite[] = 'RewriteRule ^(.*)$ index.php?cmd=$1';
        }
        return join("\n", $rewrite);
    }

    function getImageBandwidthRules($config)
    {
        $rewrite = array();
        if( $config->monitor_image_bandwidth ){
            $rewrite[] = '#Monitor Image Bandwidth';
            switch( $config->monitor_image_bandwidth  ){
                case 1:
                    if( $config->hotlink_images ){
                        $rewrite[] = '#Only monitor hotlinked images';
                        $rewrite[] = 'RewriteCond %{HTTP_REFERER} !'.$_SERVER['HTTP_HOST'].' [NC]';
                    }
                    break;
                case 2:
                    $rewrite[] = '#Do not log bandwidth for admin or image owner';
                    $rewrite[] = 'RewriteCond %{HTTP_REFERER} !'.$_SERVER['HTTP_HOST'].'/.*(admin/|(images|rotate|resize|rename)\.php) [NC]';
                    break;
                case 3:
                    $rewrite[] = '#Do not log bandwidth for admin image views';
                    $rewrite[] = 'RewriteCond %{HTTP_REFERER} !'.$_SERVER['HTTP_HOST'].'/.*admin/ [NC]';
                    break;
            }
            $rewrite[] = 'RewriteRule images/([a-z0-9A-Z_-]{1,})/([A-Za-z0-9_-]{1,})\.(jpg|gif|png)$ index.php?cmd=bandwidth&u=$1&i=$2.$3 [L]';
            $rewrite[] = '';
        }
        return join("\n", $rewrite);
    }

    function getThumbnailBandwidthRules($config)
    {
        $rewrite = array();
        if( $config->monitor_thumbnail_bandwidth ){
            $rewrite[] = '#Monitor Thumbnail Bandwidth';
            switch( $config->monitor_thumbnail_bandwidth  ){
                case 1:
                    if( $config->hotlink_thumbnails ){
                        $rewrite[] = '#Only monitor hotlinked thumbnails';
                        $rewrite[] = 'RewriteCond %{HTTP_REFERER} !'.$_SERVER['HTTP_HOST'].' [NC]';
                    }
                    break;
                case 2:
                    $rewrite[] = '#Do not log bandwidth for admin or image owner';
                    $rewrite[] = 'RewriteCond %{HTTP_REFERER} !'.$_SERVER['HTTP_HOST'].'/.*(admin/|(images|rotate|resize|rename)\.php) [NC]';
                    break;
                case 3:
                    $rewrite[] = '#Do not log bandwidth for admin image views';
                    $rewrite[] = 'RewriteCond %{HTTP_REFERER} !'.$_SERVER['HTTP_HOST'].'/.*admin/ [NC]';
                    break;
            }
            $rewrite[] = 'RewriteRule thumbs/([A-Za-z0-9_-]{1,})/([A-Za-z0-9_-]{1,})\.(jpg|gif|png)$ index.php?cmd=bandwidth&u=$1&i=$2.$3&t=1 [L]';
            $rewrite[] = '';
        }
        return join("\n", $rewrite);
    }

    function getImageHotlinkRules($config)
    {
        $rewrite = array();
        if( !$config->hotlink_images ){
            $rewrite[] = '#No Image Hotlinking';
            $rewrite[] = 'RewriteCond %{HTTP_REFERER} !'.$_SERVER['HTTP_HOST'].' [NC]';
            $rewrite[] = 'RewriteCond %{HTTP_REFERER} !^$';
            $rewrite[] = 'RewriteRule images/(.+) '.$config->siteurl.'browse.php?view=1&img=$1 [L,R]';
            $rewrite[] = '';
        }
        return join("\n", $rewrite);
    }

    function getThumbnailHotlinkRules($config)
    {
        $rewrite = array();
       	if( !$config->hotlink_thumbnails ){
       		$rewrite[] = '#No Thumbnail Hotlinking';
       		$rewrite[] = 'RewriteCond %{HTTP_REFERER} !'.$_SERVER['HTTP_HOST'].' [NC]';
       		$rewrite[] = 'RewriteCond %{HTTP_REFERER} !^$';
       		$rewrite[] = 'RewriteRule thumbs/.* '.$config->siteurl . 'hotthumb.png [L,R]';
       		$rewrite[] = '';
      	}
        return join("\n", $rewrite);
    }

    function updateRules($content, $rules)
    {
        $regex = '/'.$this->beginMarker.'.*'.$this->endMarker.'\s*/is';
        if( preg_match($regex, $content) ){
            $content = preg_replace($regex, str_replace('$','\$',$rules), $content, -1);
        }
        else{
            $content .= "\n\n" . $content;
        }
        return $content;
    }
}