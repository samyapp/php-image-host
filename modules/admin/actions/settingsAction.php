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

class settingsAction extends Action
{
    function run()
    {
        $updated = false;
        $errors = array();
        $this->app->loadsettings();
        if( $this->app->config->siteurl == '' ) $this->app->config->siteurl = 'http://'.$_SERVER['HTTP_HOST'].'/';
        if( $this->app->config->image_folder == '' ) $this->app->config->image_folder = realpath(dirname(__FILE__).'/../images/');
        if( $this->app->config->thumb_folder == '' ) $this->app->config->thumb_folder = realpath(dirname(__FILE__).'/../thumbs/');
        if( $this->app->config->image_url == '' ) $this->app->config->image_url = $this->app->config->siteurl.'images/';
        if( $this->app->config->thumb_url == '' ) $this->app->config->thumb_url = $this->app->config->siteurl.'thumbs/';
		
		$themes = $this->getThemeNames();
		$languages = $this->getLanguageNames();
		$stylesheets = $this->getStylesheets();
        $branding_fonts = $this->getFonts(APP_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'fonts');
        if( $this->app->getParamStr('update') ){

            $this->app->config->log_image_views = $this->app->getParamInt('log_image_views');
            $this->app->config->image_ratings = $this->app->getParamStr('image_ratings');
            $this->app->config->disable_site = $this->app->getParamInt('disable_site');
            $this->app->config->site_disabled_message = $this->app->getParamStr('site_disabled_message');
			$this->app->config->theme = $this->app->getParamStr('theme');
			if( !in_array($this->app->config->theme, $themes) ) {
				$this->app->config->theme = 'default';
			}
            $this->app->config->stylesheet = $this->app->getParamStr('stylesheet');
            if( !in_array($this->app->config->stylesheet, $stylesheets) ) {
                $this->app->config->stylesheet = 'default/styles/default.css';
            }
			$this->app->config->language = $this->app->getParamStr('language');
			if( !in_array($this->app->config->language, $languages) ) {
				$this->app->config->language = 'default';
			}
            $this->app->config->thumb_format = $this->app->getParamStr('thumb_format');
			
            $this->app->config->show_errors = $this->app->getParamInt('show_errors');
            $this->app->config->payment_email_subject = $this->app->getParamStr('payment_email_subject');
            $this->app->config->payment_email_message = $this->app->getParamStr('payment_email_message');
            $this->app->config->paypal_email = $this->app->getParamStr('paypal_email');
            $this->app->config->paypal_account_email = $this->app->getParamStr('paypal_account_email');
            $this->app->config->payment_notifications = $this->app->getParamInt('payment_notifications');

            $this->app->config->notify_bandwidth_exceeded = $this->app->getParamInt('notify_bandwidth_exceeded');
            $this->app->config->bandwidth_exceeded_from = $this->app->getParamStr('bandwidth_exceeded_from');
            $this->app->config->bandwidth_exceeded_subject = $this->app->getParamStr('bandwidth_exceeded_subject');
            $this->app->config->bandwidth_exceeded_message = $this->app->getParamStr('bandwidth_exceeded_message');

            $this->app->config->ban_ips = $this->app->getParamInt('ban_ips');

            $gmw =  $this->app->getParamInt('gallery_max_image_width');
            $gmh = $this->app->getParamInt('gallery_max_image_height');
            $gmw = max(10,$gmw);
            $gmh = max(10,$gmh);
            $this->app->config->gallery_max_image_width = $gmw;
            $this->app->config->gallery_max_image_height = $gmh;
            if( $this->app->config->paypal_email == '' ){
                $errors[] = 'You must enter a valid paypal email address.';
            }elseif( !$this->app->validateemail($this->app->config->paypal_email) ){
                $errors[] = '"'.htmlspecialchars($this->app->config->paypal_email).'" is not a valid email address. You must enter a valid paypal email address.';
            }
            $this->app->config->email_images_subject = $this->app->getParamStr('email_images_subject');
            $this->app->config->email_images_template = $this->app->getParamStr('email_images_template');

            $this->app->config->temp_dir = $this->app->getParamStr('temp_dir');
            if( !is_dir($this->app->config->temp_dir ) ) {
                $errors[] = 'You must specify an existing directory for the temporary directory.';
                if( $this->app->config->temp_dir != '' ) {
                    $errors[] = 'The directory "'.$this->app->config->temp_dir.'" does not exist.';
                }
            }
            elseif( !is_writable($this->app->config->temp_dir) ) {
                $errors[] = 'The temporary directory "'.$this->app->config->temp_dir.'" must be writable by this script. Try changing the permissions to 0777.';
            }

            $this->app->config->branding_font = $this->app->getParamStr('branding_font');
            $this->app->config->signup_email_from = $this->app->getParamStr('signup_email_from');
            $this->app->config->signup_email_subject = $this->app->getParamStr('signup_email_subject');
            $this->app->config->signup_email_template = $this->app->getParamStr('signup_email_template');
            $this->app->config->reminder_email_from = $this->app->getParamStr('reminder_email_from');
            $this->app->config->reminder_email_subject = $this->app->getParamStr('reminder_email_subject');
            $this->app->config->reminder_email_template = $this->app->getParamStr('reminder_email_template');
            $this->app->config->email_confirmation = $this->app->getParamInt('email_confirmation');
            $this->app->config->email_confirmation_key = $this->app->getParamStr('email_confirmation_key');
            if( !$this->app->validateemail($this->app->config->signup_email_from) ) $errors[] = 'You must enter a valid email address to use as the "From" address on new account emails.';
            if( !$this->app->validateemail($this->app->config->reminder_email_from) ) $errors[] = 'You must enter a valid email address to use as the "From" address on password reminder emails.';
            if( $this->app->config->email_confirmation_key == '' ) $this->app->config->email_confirmation_key = substr(md5($_SERVER['HTTP_HOST'].time()),10);

            $this->app->config->admin_username = $this->app->getParamStr('admin_username');
            $this->app->config->admin_password = $this->app->getParamStr('admin_password');
            $this->app->config->admin_email = $this->app->getParamStr('admin_email');
            $this->app->config->allow_uploads = $this->app->getParamInt('allow_uploads');
            $this->app->config->allow_signups = $this->app->getParamInt('allow_signups');
            $this->app->config->sitename = $this->app->getParamStr('sitename');
            $this->app->config->siteurl = $this->app->getParamStr('siteurl');
            $this->app->config->thumbnail_width = $this->app->getParamInt('thumbnail_width');
            $this->app->config->thumbnail_height = $this->app->getParamInt('thumbnail_height');
            $this->app->config->image_folder =$this->app->getParamStr('image_folder');
            $this->app->config->image_url = $this->app->getParamStr('image_url');
            $this->app->config->thumb_folder = $this->app->getParamStr('thumb_folder');
            $this->app->config->thumb_url = $this->app->getParamStr('thumb_url');
            $this->app->config->branding_text = $this->app->getParamStr('branding_text');
            $this->app->config->branding_size = min(30,max(6,$this->app->getParamInt('branding_size')));
            $this->app->config->branding_color = preg_replace('/[^0-9a-f]/i', '',strtolower($this->app->getParamStr('branding_color')));
            $this->app->config->branding_bgcolor = preg_replace('/[^0-9a-f]/i', '',strtolower($this->app->getParamStr('branding_bgcolor')));
            $this->app->config->min_branding_width = $this->app->getParamInt('min_branding_width', 200);
            $this->app->config->min_branding_height = $this->app->getParamInt('min_branding_height', 100);
            $this->app->config->branding_color = str_pad($this->app->config->branding_color,6,'f');
            if( strlen($this->app->config->branding_color) > 6 ) $this->app->config->branding_color = substr($this->app->config->branding_color,0,6);

            $this->app->config->branding_bgcolor = str_pad($this->app->config->branding_bgcolor,6,'0');
            if( strlen($this->app->config->branding_bgcolor) > 6 ) $this->app->config->branding_bgcolor = substr($this->app->config->branding_bgcolor,0,6);

            $this->app->config->branding_color = '#'.$this->app->config->branding_color;
            $this->app->config->branding_bgcolor = '#'.$this->app->config->branding_bgcolor;

            $this->app->config->browse_per_page = max(1, $this->app->getParamInt('browse_per_page'));
            $this->app->config->browse_images = $this->app->getParamInt('browse_images');
            $this->app->config->browse_checked_only = $this->app->getParamInt('browse_checked_only');
            $this->app->config->browse_max_image_width = $this->app->getParamInt('browse_max_image_width');
            $this->app->config->upload_public_default = $this->app->getParamInt('upload_public_default');
            $this->app->config->random_per_page = $this->app->getParamInt('random_per_page');

            $this->app->config->imagick_path = $this->app->getParamStr('imagick_path');
            if( preg_match('#^.+[^/\\\\]$#', $this->app->config->imagick_path ) ) {
                $this->app->config->imagick_path .= DIRECTORY_SEPARATOR;
            }
            $this->app->config->debug_imagick = $this->app->getParamInt('debug_imagick');

            $this->app->config->branding_transparency = min(100,max(0,$this->app->getParamInt('branding_transparency')));
            if( $this->app->config->thumbnail_width < 1 || $this->app->config->thumbnail_height < 1 )
                $errors[] = 'You must specify the thumbnail width and height.';
            if( !preg_match('/^http.*\/$/i', $this->app->config->image_url ) )
                $errors[] = 'You must enter the full url to the image folder.';
            if( !preg_match('/^http.*\/$/i', $this->app->config->thumb_url ) )
                $errors[] = 'You must enter the full url to the thumbnail image folder.';
            if( !@is_dir($this->app->config->image_folder) ){
                $errors[] = 'You must specify the folder to save images in.';
                if( $this->app->config->image_folder != '' ) $errors[] = 'The folder "'.htmlspecialchars($this->app->config->image_folder).'" does not exist.';
            }elseif(  !@is_writable($this->app->config->image_folder) ){
                $errors[] = 'You must set the permissions on the images folder "'.htmlspecialchars($this->app->config->image_folder).'" so the script can write to it.';
            }
            if( !is_dir($this->app->config->thumb_folder) ){
                $errors[] = 'You must specify the folder to save thumbnail images in.';
            }elseif(  !is_writable($this->app->config->thumb_folder) ){
                $errors[] = 'You must set the permissions on the thumbnail images folder "'.htmlspecialchars($this->app->config->thumb_folder).'" so the script can write to it.';
            }

            if( $this->app->config->siteurl == '' ) $errors[] = 'You must enter the full url for your site, including the trailing slash "/".';
            if( !preg_match('/^http:\/\/.*/',$this->app->config->siteurl) )
                $this->app->config->siteurl = 'http://'.$this->app->config->siteurl;
            if( $this->app->config->siteurl[strlen($this->app->config->siteurl)-1] != '/' ) $this->app->config->siteurl.='/';
            if( $this->app->config->admin_email == '' || !$this->app->validateemail($this->app->config->admin_email) ){
                $errors[] = 'You must enter a valid admin email address.';
            }
            if( $this->app->config->admin_username == '' ) $errors[] = 'You must enter an admin username.';
            if( $this->app->config->admin_password == '' ) $errors[] = 'You must enter an admin password.';
            if( count($errors) == 0 ){
                $this->app->savesettings();
        //array('admin_password', 'admin_username', 'admin_email',
        //											'allow_uploads', 'categories', 'sitename', 'siteurl'));
                $updated = true;
            }
        }
		$this->theme->assign('themes', $themes);
		$this->theme->assign('languages', $languages);
        $this->theme->assign('updated', $updated);
        $this->theme->assign('errors', $errors);
        $this->theme->assign('stylesheets', $stylesheets);
        $this->theme->assign('branding_fonts', $branding_fonts);
    }

    function getFonts($dir)
    {
        $d = dir($dir);
        $fonts = array();
        if( $d ) {
            while( false !== ($entry = $d->read() ) ) {
                if( preg_match('#\.ttf$#i', $entry ) ) {
                    $fonts[] = $entry;
                }
            }
            $d->close();
        }
        return $fonts;
    }

	function getDirNames($path, $ignore = array() )
	{
		$d = dir($path);
        $names = array();
		if( $d ) {
			while( false !== ($entry = $d->read() ) ) {
				if( $entry[0] != '.' && !in_array($entry, $ignore) && is_dir($path . DIRECTORY_SEPARATOR . $entry) ) {
					$names[] = $entry;
				}
			}
		}
		return $names;
	}
	
	function getThemeNames()
	{
		$theme_dir = APP_DIR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'themes';
		$names = $this->getDirNames($theme_dir, array('custom', 'default'));
		array_unshift($names, 'default');
		return $names;
	}

    function getStylesheets()
    {
        $stylesheets = array();
        $theme_dir = APP_DIR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'themes';
        $d = dir($theme_dir);
        if( $d ) {
            while( false !== ($entry = $d->read() ) ) {
                $path = $theme_dir . DIRECTORY_SEPARATOR . $entry;
                if( $entry[0] != '.' && is_dir($path) ) {
                    if( is_dir($path. DIRECTORY_SEPARATOR . 'styles') ) {
                        $spath = $path . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR;
                        $d2 = dir($spath);
                        $spath = $entry . '/styles/';
                        while( false !== ($entry2 = $d2->read() ) ) {
                            if( preg_match('#\.css#i', $entry2) ) {
                                $stylesheets[] = $spath . $entry2;
                            }
                        }
                        $d2->close();
                    }
                }
            }
            $d->close();
        }
        return $stylesheets;
    }

	function getLanguageNames()
	{
		$lang_dir = APP_DIR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'languages';
		$languages = array('default');
		$d = dir($lang_dir);
		while( false !== ($entry = $d->read() ) ) {
			if( preg_match('#^([a-z0-9_-]+)\.php$#i', $entry, $match) ) {
				if( $match[1] != 'default' && $match[1] != 'custom' ) {
					$languages[] = $match[1];
				}
			}
		}
		return $languages;
	}
	
}
