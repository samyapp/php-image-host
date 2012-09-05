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

class bandwidthAction extends Action
{

	function run()
	{
		// get the image to view by username and image name
		$u = preg_replace('/[^a-z0-9_-]/i', '', $this->app->getParamStr('u'));
		$i = preg_replace('/[^a-z0-9_.-]/i', '',$this->app->getParamStr('i'));

		$iname = $this->app->config->image_folder.$u.'/'.$i;
		$tname = $this->app->config->thumb_folder.$u.'/'.$i;
		$dot = strpos($i, '.');
		$ctype = 'image/pjpeg';

		$getthumb = isset($_REQUEST['t']);

		if( $dot != -1 ){
			$type = substr($i, $dot+1);
			if( $type != 'jpg' ) {
				$ctype = 'image/'.$type;
			}
		}

		$sql = "SELECT u.bandwidth_exceeded, i.image_id FROM {pa_dbprefix}images i, {pa_dbprefix}users u ";
		$sql .="WHERE CONCAT(i.name, '.', i.type)='".mysql_real_escape_string($i)."' ";
		$sql .="AND u.username='".mysql_real_escape_string($u)."' AND i.user_id=u.user_id ";
		$res = $this->app->query($sql, 'Get Image And user');

		$img = mysql_fetch_object($res);

		header("Status: 200 Ok");
		header("Content-Disposition: inline;");
		header('Pragma:');
		header('Cache-Control:');

		if( $img && file_exists($iname) ){

			if( $img->bandwidth_exceeded && !preg_match('/admin\//i', $_SERVER['HTTP_REFERER']) ){

				header("Content-Type: image/png");
				readfile(dirname(__FILE__).'/../bandwidth_exceeded.png');

			}else{
				$lastModified = filemtime($iname);
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
				header("Content-Type: $ctype");
				header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600 * 72) . " GMT");
				ob_start();
				readfile($getthumb ? $tname : $iname);
				ob_end_flush();
				$fsize = $getthumb  ? (int)filesize($tname) : 'filesize';
				$sql = "UPDATE images SET bandwidth=bandwidth+$fsize WHERE image_id={$img->image_id} ";
				mysql_query($sql);
			}
		}else{
			header("Content-Type: image/png");
			readfile(dirname(__FILE__).'/../not_found.png');
		}
		exit();
	}
}