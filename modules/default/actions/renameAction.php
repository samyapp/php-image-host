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

class renameAction extends action
{

	var $requireLogin = true;
	
	function run()
	{
		$this->app->loadClass('images');
		$this->app->images->setuser($this->app->userSession->user);

		$uploaderrors = '';
		$uploaded = array();
		$ids = array();
		$errors = array();
		$renamed = false;
		$image = null;
		$iid = 0;
		$name = '';
		
		//$pagecontent->display('Rename Image');

// if the user is not allowed to rename images, tell them.

		if( !$this->app->userSession->user->rename_images ){
			$this->theme->templateName = 'pagecontent';
			$this->theme->assign('templateContent',$this->theme->_t('Cannot Rename Content'));
		}
		else {

			$iid = $this->app->getParamInt('i');
			$image = 0;
			if( $iid != 0 ) $image = $this->app->images->getimage(array('userid'=>$this->app->userSession->user->user_id, 'id'=>$iid));
			if( !$image ) $iid = 0;

			if( !$image  ){
				$this->theme->templateName = 'pagecontent';
				$this->theme->assign('templateContent' ,$this->theme->_t('No Image Selected Content'));
			}
			else {
				if( $this->app->getParamStr('rename') != '' ){
					$name = $this->app->getParamStr('name');
					if( $this->app->images->renameimage($image, $name) ){
						$renamed = true;
					}else{
						$errors = $this->app->images->errors;
					}
				}

				foreach( array('errors', 'renamed', 'image', 'iid', 'name') as $var_name ) {
					$this->theme->assign($var_name, $$var_name);
				}
			}
		}
	}
}
