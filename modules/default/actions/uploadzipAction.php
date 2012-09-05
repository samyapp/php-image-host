<?php
/*

PHP Image Host
www.phpace.com/php-image-host

Copyright (c) 2004,2008-2009 Sam Yapp

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

class uploadzipAction extends action
{
	var $requireLogin = true;
	
	function run()
	{
		$this->app->loadClass('images');
		$this->app->images->setuser($this->app->userSession->user);

		$errors = array();
		$uploaded = array();
		$ids = array();
		$public = $this->app->config->upload_public_default;
		$user = $this->app->userSession->user;
		$gallery = $this->app->getParamInt('gallery_id');
		$userid = $user->user_id;
		if( $this->app->getParamStr('upload') != '' && $user->allow_zip_uploads && $this->app->config->allow_uploads
            && extension_loaded('zlib')){
			$public = $this->app->getParamInt('public');
			$id = 'images';
			if( isset($_FILES[$id]['tmp_name']) && is_uploaded_file($_FILES[$id]['tmp_name']) ){
				$fname = $_FILES[$id]['tmp_name'];
                // is it a zip archive?
                if( preg_match('#\.zip$#i', $_FILES[$id]['name']) ) {
                    $helper = $this->app->loadClass('ziphelper');
                    $helper->gallery = $gallery;
                    $helper->public = $public;
                    $ids = $helper->process_zip($fname, $this->app->images, $user);
                    $errors = $helper->errors;
                    // foreach image in the archive...
/*                    $iid = $this->app->images->addimage($name, $userid, $fname, $gallery, 0, $public);
        			if( $iid ){
            			$user = $this->app->users->getuser(array('id'=>$user->user_id));
                		$ids[] = $iid;
                    }
    				else{
        				$errors[] = $_FILES[$id]['name'] . '<br />'.join("<br />\n",$this->app->images->errors);
            			$this->app->images->errors = array();
                	}
*/                    // end foreach
    				if( count($ids) ){
        				$uploaded = $this->app->images->getimages(array('ids'=>$ids));
                    }
				}
			}
		}
		foreach( array('errors','uploaded','ids','public','user','gallery') as $var_name ) {
			$this->theme->assign($var_name, $$var_name);
		}
		$this->theme->assign('config', $this->app->config);
	}
}
