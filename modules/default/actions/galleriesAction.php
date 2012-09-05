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

class galleriesAction extends action
{

	var $requireLogin = true;
	
	function run()
	{
		$images = $this->app->loadClass('images');
		$this->app->images->setuser($this->app->userSession->user);
		$users = $this->app->loadClass('users');
		$user = $this->app->userSession->user;
		$gaction = $this->app->getParamStr('gaction');
		$errors = array();
		$config = $this->app->config;
		$message = '';

		if( $user->max_galleries == 0 ){
			$this->theme->template = 'page_content';
			$this->pageContent = $this->theme->_t('Galleries Not Allowed Content');
			return;
		}

		$gid = $this->app->getParamInt('g');
		$gallery = 0;
		if( $gid != 0 ) $gallery = $images->getgallery($gid);
		if( !$gallery  ){
			$gid = 0;
			if( $gaction == 'edit' ) $gaction = '';
		}

		$ids = isset($_POST['ids']) ? $_POST['ids'] : array();
		$emails = array();
		for( $i = 0 ; $i < $user->email_friends; $i++) $emails[$i] = '';
		$msg = $this->app->getParamStr('msg');

		if( $gallery  ){
			if( $this->app->getParamStr('updategallery') != '' ){
				$gallery->gallery_name = $this->app->getParamStr('name');
				$gallery->gallery_intro = $this->app->getParamStr('intro');
				if( $images->updategallery($gid, $gallery->gallery_name, $gallery->gallery_intro) ){
					$gallery = $images->getgallery($gid);
					$message = 'Gallery "'.$gallery->gallery_name.'" Updated.';
					$user->galleries[$gid] = $gallery;
				}else{
					$gaction = 'edit';
					$errors = $images->errors;
				}
			}elseif( $this->app->getParamStr('delete') != '' ){
				if( $images->deletegallery($gid) ){
					$message = 'Gallery "'.$gallery->gallery_name.'" Deleted.';
					unset($user->galleries[$gid]);
					$gid = $gallery = 0;
				}
			}
		}elseif($this->app->getParamStr('addgallery') != '' && $user->max_galleries > count($user->galleries)){
			$name = $this->app->getParamStr('name');
			$intro = $this->app->getParamStr('intro');
			$gid = $images->addgallery($name, $intro);
			if( $gid ){
				$gallery = $images->getgallery($gid);
				$message = 'Gallery "'.$gallery->gallery_name.'" Created.';
				$user->galleries[$gid] = $gallery;
			}else{
				$gaction = 'add';
				$errors = $images->errors;
			}
		}elseif( $this->app->getParamStr('send') != '' ){
		
			// user wants to email links to their galleries to their friends..

			$gals = array();
			foreach( $ids as $i ){
				if( isset($user->galleries[$i]) ){
					$gals[] =& $user->galleries[$i];
				}
			}

			if( count($gals) ){
				$emails = array();
				$sentto = array();
				for( $i = 0; $i < $user->email_friends; $i++){
					if( isset($_POST['to'][$i]) ){
						$email = trim(get_magic_quotes_gpc() == 1 ? stripslashes($_POST['to'][$i]) : $_POST['to'][$i]);
						$emails[$i] = $email;
						if( $email != '' ){
							if( $this->app->validateemail($email) ){
								$sentto[] = $email;
							}else{
								$errors[] = 'The address "'.htmlspecialchars($email).'" is not a valid email address.';
							}
						}
					}
				}
				if( count($sentto) > 0 ){
					$msg = $this->app->getParamStr('msg');
					$msg = preg_replace('#content-type|bcc:|cc:|to:#i', '', $msg);
					if( strlen($msg) > 255 ){
						$errors[] = 'Your message can only contain a maximum of 255 characters. It currently contains '.strlen($msg).'. Please reduce your message length.';
					}elseif( strlen($msg) == 0 ){
						$errors[] = 'You must enter a message to send.';
					}else{
						$headers = "From: {$user->email}\r\nReply-To: {$user->email}\r\nErrors-To: {$config->admin_email}\r\nReturn-Path: {$config->admin_email}";
						$tpl = $config->email_images_template;
						$gallerylinks = array();
						foreach( $gals as $g ){
							$gallerylinks[] = $this->url('gallery','g='.$g->gallery_name.'&u='.$user->username);
						}
						$s = array('{sitename}', '{siteurl}','{message}', '{imagelinks}');
						$r = array($config->sitename, $config->siteurl, $msg,join("\n\n", $gallerylinks));
						$tpl = str_replace($s, $r, $tpl);
						foreach($sentto as $email ){
							@mail($email, $config->email_images_subject, $tpl, $headers);
						}
						$msg = '';
						for( $i = 0; $i < $user->email_friends; $i++) $emails[$i] = '';
						$message = 'An email containing your message and links to your galleries has been sent to '.join(" and ", $sentto).'.';
					}
				}else{
					$errors[] = 'You must enter an email address to send the your galleries to.';
				}
			}else{
				$errors[] = 'You need to check the checkboxes next to the galleries you want to send.';
			}
			if( count($errors) > 0 ) $errors[] = '<br />Please correct these errors and resubmit the <a href="#email">email form</a>.';
		}
		foreach( array(
						'errors','message','user','gid','gallery','emails','gaction','ids','msg'
						)
					as $var_name ) {
			$this->theme->assign($var_name, $$var_name);
		}
	}
}
