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
class ziphelper
{
    var $errors = array();
    var $images = null;
    var $user = null;
    var $app = null;
    var $public = 0;
    var $gallery = 0;

    function __construct($app)
    {
        $this->app = $app;
    }

    function ziphelper($app)
    {
        $this->__construct($app);
    }

    function user_under_limit($num_so_far)
    {
        if( $this->user->max_images == 0 ) {
            return true;
        }
        if( $this->user->max_images > $num_so_far ){
            return true;
        }
        return false;
    }

    function filesizeOK($filesize)
    {
        if( $this->user->max_upload_size == 0 ) {
            return true;
        }
        if( $filesize <= ($this->user->max_upload_size * 1024)) {
            return true;
        }
        return false;
    }

    function getTempName()
    {
        $dir = $this->makeTempDir();
        $name = $dir . time() . mt_rand(1000,999999);
        return $name;
    }

    function makeTempDir()
    {
        $path = $this->app->config->temp_dir . DIRECTORY_SEPARATOR . session_id() . DIRECTORY_SEPARATOR;
        if( !is_dir($path) ) {
            mkdir($path);
            chmod($path, 0777);
        }
        return $path;
    }

    function process_zip_ext($filename)
    {
        $added = array();
        $zip = zip_open($filename);
        $existing = $this->user->images;
        if( $zip ) { 
            while( ($zip_entry = zip_read($zip) ) && $this->user_under_limit($existing) ) {
                $name = zip_entry_name($zip_entry);
                if( preg_match('#([^/\\\\]+)\.(jpg|gif|png)$#i', $name, $matches ) ) { 
                    $oname = $matches[1];
                    $filesize = zip_entry_filesize($zip_entry);
                    if( $this->filesizeOK($filesize) ) { 
                        $newname = $this->getTempName();
                        if(zip_entry_open($zip, $zip_entry, "r")) { 
                            $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                            zip_entry_close($zip_entry);
                            $fp = fopen($newname, 'w');
                            if( $fp ) { 
                                fwrite($fp, $buf);
                                fclose($fp);
                                $iid = $this->images->addimage($oname, $this->user->user_id, $newname, $this->gallery, 0, $this->public);
                                unlink($newname);
                                if( !$iid ) { 
                                    $this->errors = array_merge($this->errors, $this->images->errors);
                                    $this->images->errors  = array();
                                }
                                else { 
                                    $added[] = $iid;
                                    $existing++;
                                }
                            }
                        }
                    }
                    else{
                        $this->errors[] = htmlspecialchars($name).': '.$this->app->translate('file too large.');
                    }
                }
                elseif( $name[strlen($name)-1] != '/' ) {
                    $this->errors[] = htmlspecialchars($name).': '.$this->app->translate('Unknown file format');
                }
            }
            zip_close($zip);
        }
        else{
            $this->errors[] = $this->app->translate('Unable to read from zip archive');
        }
        return $added;
    }

    function process_zip_pclzip($filename)
    {
        require_once dirname(__FILE__) . '/pclzip.lib.php';
        $added = array();
        $existing = $this->user->images;
        $zip = new PclZip($filename);
        if( $zip ) {
            $list = $zip->listContent();
            if( $list ) {
                $temp_dir = $this->makeTempDir();
                // for each entry
                foreach( $list as $entry ) {
                    if( !$entry['folder'] ) {
                        // get the name
                        $name = $entry['filename'];
                        // if the name is jpg/gif/png
                        if( preg_match('#([^/\\\\]+)\.(jpg|gif|png)$#i', $name, $matches ) ) {
                            $oname = $matches[1].'.'.$matches[2];
                            $filesize = $entry['size'];
                            if( $this->filesizeOK($filesize) ) {
                                $newname = $temp_dir . $oname;
                                // extract the file to the temp name
                                $result = $zip->extractByIndex($entry['index'], PCLZIP_OPT_PATH, $temp_dir, PCLZIP_OPT_REMOVE_ALL_PATH);
                                if($result ) {
/* debugging, hopefully no longer needed
                                   if( $this->app->config->debug_imagick ) {

                                        echo '<hr />Debug: Extracted file should be: ',$newname,"\n<pre>";
                                        print_r($result);
                                        echo '</pre>',"\n";
                                        echo (file_exists($newname) ? ' File Extracted ' : ' File not extracted ');
                                    }
*/
                                    // add the image
                                    $iid = $this->images->addimage($oname, $this->user->user_id, $newname, $this->gallery, 0, $this->public);
                                    // delete the temp file
                                    unlink($newname);
                                    if( !$iid ) {
                                        $this->errors = array_merge($this->errors, $this->images->errors);
                                        $this->images->errors  = array();
                                    }
                                    else {
                                        $added[] = $iid;
                                        $existing++;
                                    }
                                }
                                else {
                                    $this->errors[] = $this->app->translate('Unable to read from zip archive');
                                }
                            }// bad filesize
                            else {
                                $this->errors[] = htmlspecialchars($name).': '.$this->app->translate('file too large.');
                            }
                        }
                        elseif( $name[strlen($name)-1] != '/' ) {
                            $this->errors[] = htmlspecialchars($name).': '.$this->app->translate('Unknown file format');
                        }
                    }
                }
//                rmdir($temp_dir);
            }
            else{
                $this->errors[] = $this->app->translate('Unable to read from zip archive');
            }
        }
        else{
            $this->errors[] = $this->app->translate('Unable to read from zip archive');
        }
        return $added;
    }

    function process_zip($filename, $images, $user)
    {
        $this->images = $images;
        $this->user = $user;
        $added = array();
        $size = (double)(filesize($filename) / (1024 * 1024));
        if( $user->zip_uploads_max_size == 0 || $user->zip_uploads_max_size >= $size ) {
            if( function_exists('zip_open') ) {
                $added = $this->process_zip_ext($filename);
            }
            else{
                $added = $this->process_zip_pclzip($filename);
            }
        }
        else {
            $this->errors[] = $this->app->translate('Zip Archive Too Large');
        }
        return $added;
    }
}