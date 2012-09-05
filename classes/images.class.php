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

set_time_limit(0);

class images{

	var $errors = array();
	var $orderbys = array('name'=>'Name', 'uploaded'=>'Uploaded', 'size'=>'File Size', 'format'=>'Format', 'bandwidth'=>'Bandwidth',
                            'views' => 'Views', 'rating' => 'Rating', 'votes' => 'Votes', 'filesize' => 'Filesize');
	var $imagetypes = array(IMAGETYPE_JPEG=>'jpg', IMAGETYPE_PNG=>'png', IMAGETYPE_GIF=>'gif');
	var $user = 0;
    var $app = null;

    function __construct($app)
    {
        $this->app = $app;
		$this->ace =& $app;
    }

	function images(&$ace){
        $this->__construct($ace);
	}

	function setuser(&$user){
		$this->user =& $user;
	}

	function resizeimage($src, $mwidth, $mheight, $destroy = false){
		$w = imagesx($src);
		$h = imagesy($src);
		if( $w > $mwidth || $h > $mheight ){
			$dx = (double)$w / (double)$mwidth;
			$dy = (double)$h / (double)$mheight;
			$d = $dx > $dy ? $dx : $dy;
			$nw = (int)((double)$w / $d);
			$nh = (int)((double)$h / $d);
			$dest = imagecreatetruecolor($nw, $nh);
            $trans = imagecolortransparent($src);
            imagealphablending($dest, false);
            if( $trans >= 0 ) {
                $toriginal = imagecolorsforindex($src, $trans);
                $tc = imagecolorallocate($dest, $toriginal['red'], $toriginal['green'], $toriginal['blue']);
                imagefilledrect($dest, 0, 0, $nw, $nh, $tc);
                imagealphablending($dest, false);
                imagecolortransparent($dest, $tc); echo "Transparentising!";
            }
			imagecopyresampled($dest, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagealphablending($dest, true);
			if( $destroy ) imagedestroy($src);
            imagesavealpha($dest, true);
			return $dest;
		}
		return $src;
	}

	function deleteimages($ids, $userid = 0){
		$ids = $this->ace->getids($ids);
		if( count($ids) > 0 ){
			$criteria = array('ids'=>$ids);
			if( $userid != 0 ) $criteria['userid'] = $userid;
			$imgs = $this->getimages($criteria);
			$iids = array();
			if( count($imgs) > 0 ){
				$userbandwidths = array();
				foreach( $imgs as $i){
					if( !isset($userbandwidths[$i->user_id]) ) $userbandwidths[$i->user_id] = 0;
					$userbandwidths[$i->user_id] += $i->bandwidthr;
					$iids[] = $i->image_id;
					$fname = $this->ace->config->image_folder.$i->username.'/'.$i->name.'.'.$i->type;
					$tname = $this->ace->config->thumb_folder.$i->username.'/'.$i->name.'.'.$i->thumb_type;
					if( file_exists($fname) ){
						unlink($fname);
					}
					if( file_exists($tname) ){
						unlink($tname);
					}
				}
				$sql = "DELETE FROM images WHERE image_id IN (".join(",",$iids).") ";
				$this->ace->query($sql, 'Delete Images');
				$deleted = $this->app->db->affectedRows();
				$cnt = 0;
				foreach( $userbandwidths as $id=>$bw ){
					if( $bw > 0 ){
						$sql = "UPDATE {pa_dbprefix}users SET deleted_images_bandwidth=deleted_images_bandwidth+$bw WHERE user_id=$id ";
						$this->ace->query($sql, 'Update Bandwidth Used');
						if( $cnt % 5 ) sleep(1);
					}
				}
				return $deleted;
			}
		}
		return 0;
	}

	function getImagePos($id, $checked = null, $public = null)
	{
		settype($id, 'integer');
		$sql = "SELECT COUNT(*) FROM {pa_dbprefix}images WHERE image_id > $id ";
		if( null != $public ){
			$sql .= " AND public = ".(int)$public." ";
		}
		if( null != $checked ){
			$sql .= " AND checked = ".(int)$checked." ";
		}
		$res = $this->ace->query($sql);
		return mysql_result($res,0,0);
	}

	function getimages($criteria = array(), $orderby = 'name', $orderdir = 'asc', $first = 0, $limit = 0){
		$justcount = isset($criteria['count']) && $criteria['count'] == true ? true : false;
		if( $justcount ){
			$sql = "SELECT COUNT(*) ";
		}else{
			$ipath = mysql_real_escape_string($this->ace->config->image_url);
			$tpath = mysql_real_escape_string($this->ace->config->thumb_url);
            $uncategorized = $this->app->db->escape($this->app->translate('Uncategorized'));
			$sql = "
                SELECT i.*, u.username, u.email, g.gallery_name,
                IF(i.category_id=0,'{$uncategorized}', c.category_name) AS category_name,
                CONCAT('{$ipath}',u.username,'/', i.name, '.', type) AS image_url,
                CONCAT('{$tpath}',u.username,'/', i.name, '.', thumb_type) AS thumb_url
            ";
		}
		$sql .="
            FROM {pa_dbprefix}images i
            LEFT OUTER JOIN {pa_dbprefix}galleries g ON i.gallery_id=g.gallery_id
            LEFT OUTER JOIN {pa_dbprefix}categories c ON i.category_id=c.category_id
            JOIN {pa_dbprefix}users u ON i.user_id=u.user_id
        ";
		$wheres = array();
		foreach( $criteria as $c=>$v){
			switch( $c ){
				case 'ids': $ids = $this->ace->getids($v);$ids[] = 0; $wheres[] = " i.image_id IN (".join(",",$ids).") "; break;
				case 'name': $wheres[] = " i.name LIKE '".str_replace("*", "%", mysql_real_escape_string($v))."' "; break;
				case 'uploaded': $wheres[] = " TO_DAYS(i.uploaded)+$v>=TO_DAYS(NOW()) "; break;
				case 'height': settype($v, 'integer'); $wheres[] = " i.height=$v "; break;
				case 'width': settype($v, 'integer'); $wheres[] = " i.width=$v "; break;
				case 'username': $wheres[] = " u.username LIKE '".str_replace("*", "%",mysql_real_escape_string($v))."' "; break;
				case 'userid': case 'user_id': settype($v, 'integer'); $wheres[] = " i.user_id=$v "; break;
				case 'format': $wheres[] = "i.type='".mysql_real_escape_string($v)."' "; break;
				case 'minsize': settype($v, 'integer'); $wheres[] = "i.filesize>=".($v*1024)." "; break;
				case 'maxsize': settype($v, 'integer'); $wheres[] = "i.filesize<=".($v*1024)." "; break;
				case 'uploadedbefore': $wheres[] = "date_format(i.uploaded, '%Y-%m-%d')<='".mysql_real_escape_string($v)."' "; break;
				case 'uploadedafter': $wheres[] = "date_format(i.uploaded, '%Y-%m-%d')>='".mysql_real_escape_string($v)."' "; break;
				case 'ip': $wheres[] = "i.ip LIKE '".mysql_real_escape_string(str_replace('*', '%', $v))."' "; break;
				case 'checked': settype($v, 'integer'); $wheres[] = "i.checked=$v "; break;
				case 'public': settype($v, 'integer'); $wheres[] = "i.public=$v "; break;
				case 'galleryid': case 'gallery_id': settype($v, 'integer'); $wheres[] = "i.gallery_id=$v "; break;
				case 'galleryname': $wheres[] = "g.gallery_name LIKE '".mysql_real_escape_string(str_replace('*', '%', $v))."' "; break;
				case 'bandwidth': settype($v, 'integer'); $wheres[] = "i.bandwidth/(1024*1024)>=$v "; break;
				case 'id': case 'image_id': $wheres[] = "i.image_id = ".(int)$v; break;
                case 'category_id': $wheres[] = "i.category_id = ".(int)$v; break;
			}
		}
		if( count($wheres) > 0 ) $sql .= "WHERE ".join(" AND ", $wheres)." ";
		if( !$justcount ){
			$ob = 'i.name';
			switch( $orderby ){
				case 'bandwidth': $ob = 'i.bandwidth'; break;
				case 'name': $ob = 'i.name'; break;
				case 'uploaded': $ob = 'i.uploaded'; break;
				case 'format': $ob = 'i.type'; break;
				case 'size': case 'filesize': $ob = 'i.filesize'; break;
				case 'username': $ob = 'u.username'; break;
				case 'random': $ob = 'RAND() '; $orderdir = ''; break;
                case 'rating': $ob = 'i.rating'; break;
                case 'votes': $ob = 'i.votes'; break;
                case 'views': $ob = 'i.views'; break;
			}
			if( strtolower($orderdir) != 'desc' ) $orderdir = '';
			if( $orderby == 'uploaded' ){
				$ob = 'i.uploaded ' . $orderdir . ', i.image_id ';
			}
			$sql .= "ORDER BY $ob $orderdir ";
			if($limit > 0 ){
				$sql .= "LIMIT ".max(0,(int)$first).",".(int)$limit." ";
			}
		}
		$res = $this->ace->query($sql, 'Get Images');
		if( $justcount ){
			return mysql_result($res,0,0);
		}else{
			$imgs = array();
			while( $i = mysql_fetch_object($res) ){
				$i->bandwidthk = number_format($i->bandwidth/1024,2);
				$i->bandwidthr = $i->bandwidth;
				$i->bandwidth = number_format($i->bandwidth/(1024*1024),2);
                $i->rating = ceil($i->rating);
//				$i->storage = number_format($i->storage/(1024*1024),2);
				$imgs[] = $i;
			}
			return $imgs;
		}
		return 0;
	}

	function brand_image($img){
		$font = APP_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . $this->app->config->branding_font;
		if( !$img  || !$this->user  || $this->user->add_branding == 0 ) return false;
		$fg = $this->hextorgb($this->ace->config->branding_color);
		$bg = $this->hextorgb($this->ace->config->branding_bgcolor);
		$bgcol = imagecolorallocatealpha($img,$bg['red'], $bg['green'], $bg['blue'],($this->ace->config->branding_transparency)*1.27);
		$col = imagecolorallocate($img,$fg['red'], $fg['green'], $fg['blue']);
		if( function_exists('imagettfbbox') && $font != '' && file_exists($font) ){
			$bbox = imagettfbbox ( $this->ace->config->branding_size, 0, $font, $this->ace->config->branding_text);
			$width = $bbox[2] - $bbox[0];
			$height = $bbox[1] - $bbox[7];
			$xoff = $bbox[0];
			$yoff = $bbox[1];
			$x = imagesx($img)-$xoff-$width-5;
			$y = imagesy($img)-$yoff;
			imagefilledrectangle($img,0,imagesy($img)-$height, imagesx($img), imagesy($img), $bgcol);
			imagettftext($img, $this->ace->config->branding_size, 0, $x, $y, $col, $font, $this->ace->config->branding_text);
		}else{
			$width = imagefontwidth(2)*strlen($this->ace->config->branding_text);
			$height = imagefontheight(2)+4;
			imagefilledrectangle($img,0,imagesy($img)-$height, imagesx($img), imagesy($img), $bgcol);
			imagestring($img, 2, imagesx($img)-$width-4, imagesy($img)-$height,$this->ace->config->branding_text, $col);
		}
		return true;
	}

	function addimage($fname, $userid, $file, $gallery = 0, $checked = 0, $public = 0){
        $font = APP_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . $this->app->config->branding_font;
		if( !$this->user  ){
			$this->errors[] = 'You must specify a valid user account.';
			return 0;
		}elseif( $this->user->images >= $this->user->max_images && $this->user->max_images > 0 ){
			$this->errors[] = 'You are already using all of your image storage allowance ('.$this->user->max_images.' images uploaded.)';
			return 0;
		}
        $thumb_type = 'jpg';
		settype($gallery, 'integer');
		settype($public, 'integer');
		if( !isset($this->user->galleries[$gallery]) ) $gallery = 0;
		settype($checked, 'integer');
		$ip = isset($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$ip = mysql_real_escape_string($ip);
		$userid = $this->user->user_id;
        if( $fname == '' ) $fname = $file;
        $fname = strtolower($fname);
        $fname = preg_replace('/^.*[\/\\\]/i','',$fname);
        $fname = preg_replace('/\..*$/i', '', $fname);
        $fname = preg_replace('/[^a-z0-9]/i', '', $fname);
        if( $fname != '' ){
            if( (int)filesize($file) <= $this->user->max_upload_size * 1024 ){
                if( $this->user->storage == 0 || $this->user->storage_used  < $this->user->storage ){

                $image = $this->getimage(array('path'=>$this->user->username.'/'.$fname));

                if( $image ){
                    $sql = "SELECT COUNT(*) FROM images WHERE user_id={$this->user->user_id} AND name LIKE '".$fname."_%' ";
                    $res = $this->ace->query($sql, 'Count Same Name Images');
                    $num = mysql_result($res,0,0);
                    $fname .= "_".($num+1);
                }
                $fname = strtolower($fname);
                $name = $fname;
                $info = getimagesize($file);
                if( $info ){
                    if( isset($this->imagetypes[$info[2]]) ){
                        $width = $info[0];
                        $height = $info[1];
                        $type = $this->imagetypes[$info[2]];
                        $size = (int)filesize($file);
                        // are we saving thumbnail in original image format?
                        if( $this->app->config->thumb_format == 'original' ) {
                            $thumb_type = $type;
                        }
                        if( !$this->app->config->imagick_path ) { // using gd
                            $imgfuncs = array(IMAGETYPE_JPEG=>'imagecreatefromjpeg', IMAGETYPE_PNG=>'imagecreatefrompng',
                                                    IMAGETYPE_GIF=>'imagecreatefromgif');
                            $img = 0;
                            $modified = false;
                            // check dimensions... resize if neccessary
                            $func = $imgfuncs[$info[2]];
                            $img = $func($file);
                            if( $img ){
                                if( $width > $this->user->max_image_width || $height > $this->user->max_image_height ){
                                    if( $this->user->auto_resize == 1 ){
                                        $img = $this->resizeimage($img, $this->user->max_image_width, $this->user->max_image_height, true);
                                        $modified = true;
                                        $width = imagesx($img);
                                        $height = imagesy($img);
                                    }else{
                                        $this->errors[] = 'Image "'.htmlspecialchars($fname).'" is too large ('.$width.'x'.$height.'). Max size allowed is '.$this->user->max_image_width.'x'.$this->user->max_image_height.'.';
                                        imagedestroy($img);
                                        return false;
                                    }
                                }
                                // if we are auto-detecting best thumbnail type...
                                if( $this->app->config->thumb_format == 'auto' ) {
                                    imagesavealpha($thumb, true);
                                    $thumb_type = $type;
                                }
                                // create thumbnail
                                $thumb = $this->resizeimage($img, $this->ace->config->thumbnail_width, $this->ace->config->thumbnail_height, false);
                            }else{
                                $this->errors[] = 'Error reading image "'.htmlspecialchars($fname).'". ';
                                return 0;
                            }
                            if( $this->user->add_branding == 1 && $this->ace->config->min_branding_width < imagesx($img)
                                    && $this->ace->config->min_branding_height < imagesy($img)){
                                if( !imageistruecolor($img) ){
                                    $tmp = imagecreatetruecolor(imagesx($img), imagesy($img));
                                    imagecopy($tmp, $img, 0, 0, 0,0,imagesx($img), imagesy($img));
                                    imagedestroy($img);
                                    $img = $tmp;
                                }
                                $this->brand_image($img);
                                $modified = true;
                            }

                            if( $this->user->auto_jpeg ){
                                $type = 'jpg';
                            }elseif($type == 'gif' && $modified && !function_exists('imagegif') ){
                                $type = 'png';
                            }
                            $sql = "INSERT INTO images (name, user_id, type, width, height, ";
                            $sql .="uploaded, filesize, ip, checked, gallery_id, public, thumb_type) ";
                            $sql .="VALUES ('$fname', $userid,'$type', ";
                            $sql .="$width, $height, now(),$size, '$ip', $checked, $gallery, $public, '{$thumb_type}') ";
                            $res = $this->ace->query($sql, 'Add Image');
                            $id = mysql_insert_id();
                            if( $id ){
                                $iname = $this->ace->config->image_folder.$this->user->username.DIRECTORY_SEPARATOR.$fname.'.'.$type;
                                if( $this->user->auto_jpeg == 1 ){
                                    imagejpeg($img, $iname, $this->user->jpeg_quality);
                                }else{
                                    if( !$modified ){
                                        copy($file, $iname);
                                    }else{
                                        if( $type == 'jpg' ){
                                            imagejpeg($img, $iname, $this->user->jpeg_quality);
                                        }elseif( $type == 'gif' ){
                                            imagegif($img, $iname);
                                        }else{
                                            imagepng($img, $iname);
                                        }
                                    }
                                }
                                chmod($iname, 0666);
                                $fsize = (int)filesize($iname);
                                $sql = "UPDATE images SET filesize=$fsize WHERE image_id=$id ";
                                $this->ace->query($sql, 'Set Image File Size');
                                $tname = $this->ace->config->thumb_folder.$this->user->username.'/'.$fname.'.'.$thumb_type;
                                $this->saveImage($thumb, $tname, $this->user->jpeg_quality);
                                return $id;
                            }else{
                                $this->errors[] = 'A database error occurred whilst attempting to add the image "'.htmlspecialchars($name).'". Please try again later.';
                            }
                        }
                        else {  // add with imagemagick

                            if( $this->app->config->thumb_format == 'auto' ) {
                                $thumb_type = $type;
                            }
                            $cmd = $this->app->config->imagick_path . '/convert'." ".$file.' -quality '.$this->user->jpeg_quality.'% ';
                            $modified = false;
                            if( $width > $this->user->max_image_width || $height > $this->user->max_image_height ){
                                if( $this->user->auto_resize == 1 ){
                                    $modified = true;
                                   $cmd .= '-geometry '.$this->user->max_image_width.'x'.$this->user->max_image_height.' ';
                                }else{
                                    $this->errors[] = 'Image "'.htmlspecialchars($fname).'" is too large ('.$width.'x'.$height.'). Max size allowed is '.$this->user->max_image_width.'x'.$this->user->max_image_height.'.';
                                    return false;
                                }
                            }
                            if( $this->user->add_branding == 1 ) {
                                $fontsize = $this->app->config->branding_size;
                                $cmd .= " -gravity SouthEast -font \"$font\" -stroke black -pointsize $fontsize -strokewidth 2 -annotate 0 \"".($this->app->config->branding_text)."\" -stroke none -fill white -pointsize $fontsize -annotate 0 \"".($this->app->config->branding_text)."\" ";
                                $modified = true;
                            }

                            if( $this->user->auto_jpeg ){
                                $type = 'jpg';
                            }
                            $iname = $this->app->config->image_folder.$this->user->username.DIRECTORY_SEPARATOR.$fname.'.'.$type;
                            $tname = $this->app->config->thumb_folder.$this->user->username.DIRECTORY_SEPARATOR.$fname.'.'.$thumb_type;

                            if( !$modified ) {
                                copy($file, $iname);
                            }
                            else {
                                $cmd .= $iname;
                                if( $this->app->config->debug_imagick ) {
                                    echo $cmd;
                                    $cmd .= ' 2>&1 ';
                                }
                                passthru($cmd);
                                $info = getimagesize($iname);
                                $width = $info[0];
                                $height = $info[1];
                            }
                            $size = (int)filesize($iname);
                            // create thumbnail
                            list($twidth, $theight) = $this->calcThumbDims($width, $height);
                            $tcmd = '"'.$this->app->config->imagick_path.'/convert" -resize '.$twidth.'x'.$theight.' '.$file.' '.$tname;
                            if( $this->app->config->debug_imagick ) {
                                echo $tcmd;
                                $tcmd .= ' 2>&1';
                            }
                            passthru($tcmd);
                            chmod($iname, 0666);
                            chmod($tname, 0777);

                            $sql = "INSERT INTO images (name, user_id, type, width, height, ";
                            $sql .="uploaded, filesize, ip, checked, gallery_id, public, thumb_type) ";
                            $sql .="VALUES ('$fname', $userid,'$type', ";
                            $sql .="$width, $height, now(),$size, '$ip', $checked, $gallery, $public, '{$thumb_type}') ";
                            $res = $this->ace->query($sql, 'Add Image');
                            $id = mysql_insert_id();

                            if( $id ){
                                return $id;
                            }else{
                                $this->errors[] = 'A database error occurred whilst attempting to add the image "'.htmlspecialchars($name).'". Please try again later.';
                            }

                        }
                    }else{
                        $this->errors[] = 'The file "'.htmlspecialchars($fname).'" is not a supported image format.';
                    }
                }else{
                    $this->errors[] = 'The file "'.htmlspecialchars($fname).'" is not a supported image format.';
                }
                }else{
                    $this->errors[] = 'You do not have enough free storage space to upload this image. Please delete some existing images or upgrade your account.';
                }

            }else{
                $this->errors[] = 'The size of the file you uploaded exceeds the maximum file size allowed ('.($this->user->max_upload_size*1024).' bytes). Please try resizing your image or saving as a compressed jpeg before uploading again.';
            }
        }else{
            $this->errors[] = 'You must enter a name for your image. The name must only contain the characters a-z and 0-9.';
        }
		return 0;
	}

	function hextorgb($hex){
		$hex = preg_replace('/[^0-9a-f]/i', '', $hex);
		$hex = str_pad($hex,6,'0');
		$ret = array(	'red' => hexdec(substr($hex, 0, 2)),
									'green' => hexdec(substr($hex, 2, 2)),
									'blue' => hexdec(substr($hex, 4, 2))
		);
		return $ret;
	}

	function getimage($criteria = array()){
		$ipath = mysql_real_escape_string($this->ace->config->image_url);
		$tpath = mysql_real_escape_string($this->ace->config->thumb_url);
		$sql = "SELECT i.*, u.username, g.gallery_name, ";
		$sql .="CONCAT('$ipath', u.username, '/',i.name, '.', type) AS image_url,  ";
		$sql .="CONCAT('$tpath', u.username, '/',i.name, '.', thumb_type) AS thumb_url ";
		$sql .="FROM images i LEFT OUTER JOIN galleries g ON i.gallery_id = g.gallery_id ";
		$sql .=",users u ";
		$wheres = array('i.user_id=u.user_id');
		foreach( $criteria as $c=>$v ){
			switch( $c ){
				case 'type': $wheres[] = "i.type='".mysql_real_escape_string($v)."' "; break;
				case 'image_id': case 'id': settype($v, 'integer'); $wheres[]=" i.image_id=$v "; break;
				case 'name': $wheres[] = " i.name='".mysql_real_escape_string($v)."' "; break;
				case 'userid': case 'user_id': settype($v, 'integer'); $wheres[] = " i.user_id=$v "; break;
				case 'path': $wheres[] = "CONCAT(u.username, '/', i.name)='".mysql_real_escape_string($v)."' "; break;
			}
		}
		if( count($wheres) > 0 ) $sql .= "WHERE ".join(" AND ", $wheres)." ";
		$res = $this->ace->query($sql, 'Get Image');
		$img = mysql_fetch_object($res);
		if( $img ){
			$img->bandwidth = number_format($img->bandwidth/(1024*1024),2);
		}
		return $img;
	}

	function setPrivacy($image_id, $user_id, $public = 0)
	{
		$sql = 'UPDATE {pa_dbprefix}images SET public = '.(int)$public.' WHERE image_id='.(int)$image_id.' AND user_id = '.(int)$user_id;
		$this->ace->query($sql, 'Set Image Privacy');
	}

	function updateimages($ids, $vars){
		$ids = $this->ace->getids($ids);
		if( !is_array($vars) ) $vars = array($vars);
		$ups = array();
		foreach( $vars as $n=>$v ){
			switch( $n ){
				case 'status': $ups[] = "status=".(int)$v." "; break;
				case 'checked': settype($v, 'integer'); $ups[] = "checked=$v "; break;
                case 'views':
                case 'rating':
                case 'votes':
                    $ups[] = "$n = ".(int)$v;
                    break;
			}
		}
		if( count($ids) && count($ups) ){
			$sql = "UPDATE {pa_dbprefix}images SET ".join(",",$ups)." WHERE image_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Update Images');
			return mysql_affected_rows();
		}else{
			return 0;
		}
	}

	function setchecked($ids, $checked = 1 ){
		$ids = $this->ace->getids($ids);
		settype($checked, 'integer');
		if( count($ids)  ){
			$sql = "UPDATE {pa_dbprefix}images SET checked=$checked WHERE image_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Set Image Status');
			return $this->app->db->affectedRows();
		}else{
			return 0;
		}

	}

    function setViews($ids, $views ){
		$ids = $this->ace->getids($ids);
		settype($views, 'integer');
		if( count($ids)  ){
			$sql = "UPDATE {pa_dbprefix}images SET views=$views WHERE image_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Set Image Views');
			return $this->app->db->affectedRows();
		}else{
			return 0;
		}

	}

    function setRatingVotes($ids, $rating, $votes ){
		$ids = $this->ace->getids($ids);
		settype($votes, 'integer');
        settype($rating, 'double');
        $rating = max(0, min($rating,10));
		if( count($ids)  ){
			$sql = "UPDATE {pa_dbprefix}images SET votes=$votes, rating=$rating WHERE image_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Set Image Rating and Votes');
			return $this->app->db->affectedRows();
		}else{
			return 0;
		}

	}

	function setpublic($ids, $public = 0){
		$ids = $this->ace->getids($ids);
		settype($public, 'integer');
		if( count($ids)  ){
			$sql = "UPDATE {pa_dbprefix}images SET public=$public WHERE image_id IN (".join(",",$ids).") ";
			if( $this->user ) $sql .= "AND user_id={$this->user->user_id} ";
			$this->ace->query($sql, 'Set Image Public');
			return $this->app->db->affectedRows();
		}else{
			return 0;
		}
	}

	function suspendusers($ids){
		$ids = $this->ace->getids($ids);
		if( count($ids) > 0 ){
			$sql = "UPDATE {pa_dbprefix}users SET status=2 WHERE user_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Suspend Users');
			return mysql_affected_rows();
		}
		return 0;
	}

    function calcThumbDims($w, $h)
    {
        $dims = array($w,$h);
        if( $w > $this->app->config->thumbnail_width || $h > $this->app->config->thumbnail_height ) {
            $dx = (double)$w / (double)$this->app->config->thumbnail_width;
            $dy = (double)$h / (double)$this->app->config->thumbnail_height;
            $d = (double)max($dx,$dy);
            $dims[0] = (double)$w / $d;
            $dims[1] = (double)$h / $d;
        }
        return $dims;
    }

	function resizeexistingimage(&$image, $newwidth, $newheight, $copy = false){
        $thumb_type = $image->thumb_type;
		if( $newwidth == $image->width && $newheight == $image->height || ($newwidth == 0 && $newheight == 0)){
			$this->errors[] = 'You must enter a new width and / or height for this image.';
			return false;
		}
		if( $newwidth > $this->user->max_image_width || $newheight > $this->user->max_image_height ){
			$this->errors[] = 'The maximum width and height you are allowed is '.$this->user->max_image_width.'x'.$this->user->max_image_height.'.';
			return false;
		}
		$imgfuncs = array(IMAGETYPE_JPEG=>'imagecreatefromjpeg', IMAGETYPE_PNG=>'imagecreatefrompng',
											IMAGETYPE_GIF=>'imagecreatefromgif');
		$iname= $this->ace->config->image_folder.$this->user->username.'/'.$image->name.'.'.$image->type;
		$info = getimagesize($iname);
		if( $info ){
			$width = $info[0];
			$height = $info[1];

			// get new width and height...
			// and check new width and height are ok...
            $rethumb = false;
			if( $newwidth == 0 ){
				$d = (double)((double)$height / (double)$newheight);
				$newwidth = (int)((double)$width / $d);
				if( $newwidth > $this->user->max_image_width ){
					$this->errors[] = 'The new height you entered results in a new width larger than your maximum allowed image width.';
					return false;
				}elseif( $newwidth < 1 ){
					$newwidth = 1;
				}
			}elseif( $newheight == 0 ){
				$d = (double)((double)$width / (double)$newwidth);
				$newheight = (int)((double)$height / $d);
				if( $newheight > $this->user->max_image_height ){
					$this->errors[] = 'The new width you entered results in a new height larger than your maximum allowed image height.';
					return false;
				}elseif( $newheight < 1 ){
					$newheight = 1;
				}
			}else{
                $rethumb = true;
            }

			$name = $image->name;
			if( $copy == true ){
				$name.= '_'.$newwidth.'x'.$newheight;
			}
			// check that the name is ok (and change it if it isn't )

			$ex = $this->getimage(array('userid'=>$this->user->user_id, 'name'=>$name, 'type'=>$image->type));

			if( $ex && $ex->image_id != $image->image_id ){
				$sql = "SELECT COUNT(*) FROM images WHERE user_id={$this->user->user_id} AND name LIKE '".$name."_%' AND type='{$image->type}' ";
				$res = $this->ace->query($sql, 'Count Same Name Images');
				$num = mysql_result($res,0,0);
				$name .= "_".($num+1);
			}
            $tpath = $this->ace->config->thumb_folder.$this->user->username.'/';
			$ipath = $this->ace->config->image_folder.$this->user->username.'/';

            if( $this->app->config->imagick_path  ) {   // use imagemagick
                list($twidth, $theight) = $this->calcThumbDims($newwidth, $newheight);
                if( $copy ) {
                    $ip = isset($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
                    $ip = mysql_real_escape_string($ip);
                    $sql = "INSERT INTO images (name, user_id, type, width, height, ";
                    $sql .="uploaded, filesize, ip, checked, gallery_id, thumb_type) ";
                    $sql .="VALUES ('$name', {$this->user->user_id},'{$image->type}', ";
                    $sql .="$newwidth, $newheight, now(),0, '$ip', {$image->checked},0, '{$thumb_type}') ";
                    $res = $this->ace->query($sql, 'Add Image');

                    $id = mysql_insert_id();

                    $convert = '"'.$this->app->config->imagick_path . 'convert" ';
                    $cmd = $convert . ' -geometry '.$newwidth.'x'.$newheight.'! '.$ipath.$image->name.'.'.$image->type.' '.$ipath.$name.'.'.$image->type;
                    if( $this->app->config->debug_imagick ) {
                        echo $cmd;
                        $cmd .= ' 2>&1 ';
                    }
                    passthru($cmd);
                    $cmd = $convert . ' -geometry '.$twidth.'x'.$theight.'! '.$tpath.$image->name.'.'.$image->thumb_type.' '.$tpath.$name.'.'.$image->thumb_type;
                    if( $this->app->config->debug_imagick ) {
                        echo $cmd;
                        $cmd .= ' 2>&1 ';
                    }
                    passthru($cmd);
                    $fsize = (int)filesize($ipath.$name.'.'.$image->type);
                    $sql = "UPDATE images SET filesize=$fsize WHERE image_id=$id ";
                    $this->ace->query($sql, 'Set Image File Size');
                }
                else {
                    $mogrify = '"'.$this->app->config->imagick_path . 'mogrify" ';
                    $cmd = $mogrify . ' -geometry '.$newwidth.'x'.$newheight.'! '.$ipath.$image->name.'.'.$image->type;
                    if( $this->app->config->debug_imagick ) {
                        echo $cmd;
                        $cmd .= ' 2>&1 ';
                    }
                    passthru($cmd);
                    $size = (int)filesize($ipath.$image->name.'.'.$image->type);
                    $cmd = $mogrify . ' -geometry '.$twidth.'x'.$theight.'! ' . $tpath.$image->name.'.'.$image->thumb_type;
                        if( $this->app->config->debug_imagick ) {
                            echo $cmd;
                            $cmd .= ' 2>&1 ';
                        }
                    passthru($cmd);
                    $sql = "UPDATE {pa_dbprefix}images SET filesize=$size, width=$newwidth, height=$newheight WHERE image_id = {$image->image_id}";
                    $this->app->db->query($sql);
                    return true;
                }
            }
            else {  // use gd
                $lfunc = $imgfuncs[$info[2]];
                $img = $lfunc($iname);
                $dest = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($dest, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagedestroy($img);
                $savetype = $image->type;
                if( $image->type == 'gif' && !function_exists('imagegif') ){
                    $savetype = 'jpg';
                }
                if( !$copy ){
                    // delete existing image
                    unlink($ipath.$image->name.'.'.$image->type);
                    // save image
                    switch( $savetype ){
                        case 'png':
                            imagepng($dest, $ipath.$name.'.'.$savetype);
                            break;
                        case 'jpg':
                            imagejpeg($dest, $ipath.$name.'.'.$savetype, $this->user->jpeg_quality);
                            break;
                        case 'gif':
                            imagegif($dest, $ipath.$name.'.'.$savetype);
                            break;
                        default: die("ERROR"); break;
                    }
                    // update database with new width, height and type
                    $fsize = filesize($ipath.$name.'.'.$savetype);
                    $sql = "UPDATE {pa_dbprefix}images SET name='".mysql_real_escape_string($name)."', filesize=$fsize, width=$newwidth, height=$newheight, type='$savetype' WHERE image_id={$image->image_id} ";
                    $this->ace->query($sql, 'Resize Image');
                    return true;
                }else{	// add new image and save as... with thumbnail...
                    $ip = isset($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
                    $ip = mysql_real_escape_string($ip);
                    $sql = "INSERT INTO images (name, user_id, type, width, height, ";
                    $sql .="uploaded, filesize, ip, checked, gallery_id, thumb_type) ";
                    $sql .="VALUES ('$name', {$this->user->user_id},'$savetype', ";
                    $sql .="$newwidth, $newheight, now(),0, '$ip', {$image->checked},0, '{$thumb_type}') ";
                    $res = $this->ace->query($sql, 'Add Image');

                    $id = mysql_insert_id();

                    if( $id ){
                        $iname = $ipath.$name.'.'.$savetype;
                        $tname = $tpath.$name.'.'.$thumb_type;
                        switch( $savetype ){
                            case 'png':
                                imagepng($dest, $ipath.$name.'.'.$savetype);
                                break;
                            case 'gif':
                                imagegif($dest, $ipath.$name.'.'.$savetype);
                                break;
                            case 'jpg':
                                imagejpeg($dest, $ipath.$name.'.'.$savetype, $this->user->jpeg_quality);
                                break;
                        }

                        $thumb = $this->resizeimage($dest, $this->ace->config->thumbnail_width, $this->ace->config->thumbnail_height, true);
                        $this->saveImage($thumb, $tname, $this->user->jpeg_quality);
                        //imagejpeg($thumb, $tname, $this->user->jpeg_quality);
                        imagedestroy($thumb);
                        $fsize = (int)filesize($iname);
                        $sql = "UPDATE images SET filesize=$fsize WHERE image_id=$id ";
                        $this->ace->query($sql, 'Set Image File Size');

                        return true;
                    }else{
                        $this->errors[] = 'A server error occurred while attempting to resize your image. Please try again later.';
                    }
                }
            }

		}else{
			$this->errors[] = 'A server error occurred while attempting to resize your image. Please try again later.';
		}
		return false;
	}

	function rotateimage(&$image, $angle){
		$angle = 360 - $angle;
		if( !in_array($angle, array(90,180,270) ) ){
			$this->errors[] = 'You can only rotate an image 90, 180 or 270 degrees.';
			return false;
		}
        if( !$this->app->config->imagick_path ) {
            return $this->rotateImageGD($image, $angle);
        }
        else {
            return $this->rotateImageMagick($image, $angle);
        }
		return false;
	}

    function rotateImageMagick($image, $angle)
    {
        $angle = 360 - $angle;
		$iname= $this->ace->config->image_folder.$this->user->username.DIRECTORY_SEPARATOR.$image->name.'.'.$image->type;
        $mogrify = $this->app->config->imagick_path . 'mogrify';
        $name = $image->name;
        $tpath = $this->ace->config->thumb_folder.$this->user->username.DIRECTORY_SEPARATOR;
        $ipath = $this->ace->config->image_folder.$this->user->username.DIRECTORY_SEPARATOR;
        $tname = str_replace("\\", "/", $tpath.$image->name.'.'.$image->thumb_type);
        $mogrify = str_replace("\\", "/", $mogrify);
        $iname = str_replace("\\", "/", $iname);
        $cmd = '"'.$mogrify.'" -rotate '.$angle.' '.$iname;
        if( $this->app->config->debug_imagick ) {
            echo $cmd;
            $cmd .= ' 2>&1 ';
        }
        passthru($cmd);

        $cmd = '"'.$mogrify.'" -rotate '.$angle.' '.$tname;
        if( $this->app->config->debug_imagick ) {
            echo $cmd;
            $cmd .= ' 2>&1 ';
        }
        passthru($cmd);

        $fsize = (int)filesize($iname);
        $width = $image->width;
        $height = $image->height;
        if( $angle == 90 || $angle == 270 ) {
            $temp = $width;
            $width = $height;
            $height = $temp;
        }
        $sql = "UPDATE images SET filesize=$fsize, width=$width, height=$height WHERE image_id = {$image->image_id} ";
        $this->app->db->query($sql, 'Update after rotating image');
        return true;
    }

    function rotateImageGD($image, $angle)
    {
		$imgfuncs = array(IMAGETYPE_JPEG=>'imagecreatefromjpeg', IMAGETYPE_PNG=>'imagecreatefrompng',
											IMAGETYPE_GIF=>'imagecreatefromgif');
		$iname= $this->ace->config->image_folder.$this->user->username.'/'.$image->name.'.'.$image->type;
		$info = getimagesize($iname);
		if( $info  ){

			$lfunc = $imgfuncs[$info[2]];
			$img = $lfunc($iname);
			$dest = imagerotate($img, $angle, 0);
            $width = imagesx($dest);
            $height = imagesy($dest);

            imagedestroy($img);
			$savetype = $image->type;
			if( $image->type == 'gif' && !function_exists('imagegif') ){
				$savetype = 'jpg';
			}
			$name = $image->name;
			$tpath = $this->ace->config->thumb_folder.$this->user->username.'/';
			$ipath = $this->ace->config->image_folder.$this->user->username.'/';
			$tname = $tpath.$image->name.'.'.$image->thumb_type;

			// delete existing image
			unlink($ipath.$image->name.'.'.$image->type);
			// save image
			switch( $savetype ){
				case 'png':
					imagepng($dest, $ipath.$name.'.'.$savetype);
					break;
				case 'jpg':
					imagejpeg($dest, $ipath.$name.'.'.$savetype, $this->user->jpeg_quality);
					break;
				case 'gif':
					imagegif($dest, $ipath.$name.'.'.$savetype);
					break;
				default: die("ERROR"); break;
			}
			// update database with new width, height and type
			$fsize = filesize($ipath.$name.'.'.$savetype);
			$sql = "UPDATE {pa_dbprefix}images SET width=$width, height=$height, filesize=$fsize, type='$savetype' WHERE image_id={$image->image_id} ";
			$this->ace->query($sql, 'Rotate Image');

			$thumb = $this->resizeimage($dest, $this->ace->config->thumbnail_width, $this->ace->config->thumbnail_height, true);
			$this->saveImage($thumb, $tname, $this->user->jpeg_quality);
			imagedestroy($thumb);
			return true;
		}else{
			$this->errors[] = 'A server error occurred while attempting to resize your image. Please try again later.';
		}
    }

	function renameimage(&$image, $newname){
		$newname = preg_replace('/\..*$/i', '', $newname);
		$newname = str_replace(' ', '_', $newname);
		$newname = preg_replace('/[^a-z0-9_-]/i', '', $newname);
		if( !preg_match('/^[a-z0-9].*[a-z0-9]$/i', $newname) ){
			$this->errors[] = 'The image name must begin and end with a letter or number, and can contain only a-z, 0-9, - or _ characters.';
			return false;
		}
		if( $newname == $image->name ){
			$this->errors[] = 'You must enter a new name for the image.';
			return false;
		}
		$ex = $this->getimage(array('userid'=>$this->user->user_id, 'name'=>$newname));
		if( $ex ){
			$this->errors[] = 'An image named "'.$newname.'.'.$image->type.'" already exists. Please choose a different name.';
			return false;
		}
		$tdir = $this->ace->config->thumb_folder.$this->user->username.'/';
		$idir = $this->ace->config->image_folder.$this->user->username.'/';
		rename($tdir.$image->name.'.'.$image->thumb_type, $tdir.$newname.'.'.$image->thumb_type);
		rename($idir.$image->name.'.'.$image->type, $idir.$newname.'.'.$image->type);
		$image->name = $newname;
		$sql = "UPDATE {pa_dbprefix}images SET name='".mysql_real_escape_string($newname)."' WHERE image_id={$image->image_id} ";
		$this->ace->query($sql, 'Rename Image');
		return true;
	}

	function getgallery($id, $no_user = false){
		settype($id, 'integer');
		$sql = "SELECT g.*, COUNT(i.image_id) AS images, u.username ";
		$sql .="FROM {pa_dbprefix}galleries g LEFT OUTER JOIN {pa_dbprefix}images i ";
		$sql .="ON g.gallery_id=i.gallery_id ";
		$sql .="JOIN {pa_dbprefix}users u ON u.user_id = g.user_id ";
		$sql .="WHERE g.gallery_id=$id ";
		if( !$no_user ){
			$sql .= " AND g.user_id={$this->user->user_id} ";
		}
		$sql .="GROUP BY g.gallery_id ";
		$res = $this->ace->query($sql, 'Get Gallery');
		$gal = mysql_fetch_object($res);
		return $gal;
	}

	function getgallerybyname($name){
		$sql = "SELECT * FROM {pa_dbprefix}galleries WHERE user_id={$this->user->user_id} AND gallery_name='".mysql_real_escape_string($name)."' ";
		$res = $this->ace->query($sql, 'Get Gallery');
		return mysql_fetch_object($res);
	}

	function addgallery($name, $intro){
		$name = str_replace(' ', '-', $name);
		if( !preg_match('/^[a-z0-9_-]{1,30}$/i', $name) ){
			$this->errors[] = 'Your gallery name can only contain alphanumeric characters and the characters "-" and "_".';
			return 0;
		}
		if($this->getgallerybyname($name) ){
			$this->errors[] = 'You already have a gallery named "'.$name.'". Please enter a unique name for your new gallery.';
			return 0;
		}
		$sql = "INSERT INTO {pa_dbprefix}galleries (user_id, gallery_name, gallery_intro) ";
		$sql .="VALUES ({$this->user->user_id}, '".mysql_real_escape_string($name)."', '".mysql_real_escape_string($intro)."') ";
		$this->ace->query($sql, 'Add Gallery');
		return mysql_insert_id();
	}

	function updategallery($id, $name, $intro){
		settype($id, 'integer');
		$name = str_replace(' ', '-', $name);
		if( !preg_match('/^[a-z0-9_-]{1,30}$/i', $name) ){
			$this->errors[] = 'Your gallery name can only contain alphanumeric characters and the characters "-" and "_".';
			return 0;
		}
		$ex = $this->getgallerybyname($name);
		if( $ex && $ex->gallery_id != $id ){
			$this->errors[] = 'You already have a gallery named "'.$name.'". Please enter a unique name for this gallery.';
			return 0;
		}
		$sql = "UPDATE {pa_dbprefix}galleries SET gallery_name='".mysql_real_escape_string($name)."', gallery_intro='".mysql_real_escape_string($intro)."' ";
		$sql .="WHERE gallery_id=$id AND user_id={$this->user->user_id} ";
		$this->ace->query($sql, 'Update Gallery');
		return mysql_affected_rows();
	}

	function deletegallery($id){
		settype($id, 'integer');
		$sql = "DELETE FROM {pa_dbprefix}galleries WHERE gallery_id=$id AND user_id={$this->user->user_id} ";
		$this->ace->query($sql, 'Delete Gallery');
		$deleted = mysql_affected_rows();
		if( $deleted ){
			$sql = "UPDATE {pa_dbprefix}images SET gallery_id=0 WHERE gallery_id=$id AND user_id={$this->user->user_id} ";
			$this->ace->query($sql, 'Update Images Gallery');
		}
		return $deleted;
	}

	function addtogallery($ids, $g){
		settype($g, 'integer');
		if( $g != 0 ){
			$gal = $this->getgallery($g);
			if( !$gal ){
				$this->errors[] = 'The gallery you specified is not one of your galleries.';
				return false;
			}
		}
		$ids = $this->ace->getids($ids);
		if( count($ids) == 0 ){
			$this->errors[] = 'You must select the images to set to this gallery by checking the checkbox next to each images.';
			return false;
		}
		$sql = "UPDATE {pa_dbprefix}images SET gallery_id=$g WHERE image_id IN (".join(",",$ids).") AND user_id={$this->user->user_id} ";
		$res = $this->ace->query($sql, 'Set Image Gallery');
		return mysql_affected_rows();
	}

	function getgallerybyusernameimage($u, $n, $i){
		$sql = "SELECT g.*, u.* FROM {pa_dbprefix}galleries g, {pa_dbprefix}users u ";
		$sql .="WHERE g.user_id=u.user_id AND g.gallery_name='".mysql_real_escape_string($n)."' AND u.username='".mysql_real_escape_string($u)."' ";
		$res = $this->ace->query($sql, 'Get Gallery By User And Name');
		$gal = mysql_fetch_object($res);
		if( $gal ){
			$gal->images = $this->getimages(array('userid'=>$gal->user_id, 'galleryid'=>$gal->gallery_id));
			$gal->image = 0;
			if( preg_match('/^([a-z0-9_-]{1,})\.(jpg|gif|png)$/i', $i, $match) ){
				$gal->image = $this->getimage(array('userid'=>$gal->user_id, 'name'=>$match[1], 'type'=>$match[2]));
				if( $gal->image ){
					// get next and previous images...
					$gal->nextimage = -1;
					$gal->previmage = -1;
					for( $a = 0; $a < count($gal->images); $a++){
						if( $gal->image->image_id == $gal->images[$a]->image_id ){
							if( $a > 0 ) $gal->previmage = $a-1;
							if( $a < count($gal->images)-1 ) $gal->nextimage = $a+1;
							$a = count($gal->images);
						}
					}
				}
			}
		}
		return $gal;
	}

	function getgalleries($criteria = array(), $orderby = '', $orderdir = 'desc', $first = 0, $limit = -1){
		$counting = isset($criteria['count']) ? $criteria['count'] : false;
		$sql = "SELECT ".($counting ? "COUNT(*) " : "g.*, u.username, COUNT(i.image_id) AS images ");
		$sql .= "FROM {pa_dbprefix}users u, {pa_dbprefix}galleries g ";
		if( !$counting) $sql .= "LEFT OUTER JOIN {pa_dbprefix}images i ON g.gallery_id=i.gallery_id ";
		$wheres = array("g.user_id=u.user_id ");
		foreach( $criteria as $c=>$v ){
			switch( $c ){
				case 'name': $wheres[] = "g.gallery_name LIKE '".mysql_real_escape_string(str_replace('*', '%', $v))."' "; break;
				case 'username': $wheres[] = "u.username LIKE '".mysql_real_escape_string(str_replace('*', '%', $v))."' "; break;
				case 'ids': $ids = $this->ace->getids($v); if( count($ids) ) $wheres[] = "g.gallery_id IN (".join(",",$ids).") "; break;
				case 'userid': settype($v, 'integer'); $wheres[] = "g.user_id=$v "; break;
			}
		}
		$sql .= "WHERE ".join(" AND ", $wheres)." ";
		$orderdir = strtolower($orderdir);
		if( !in_array($orderdir , array('asc', 'desc')) ) $orderdir = 'asc';
		if( !$counting ){
			$sql .= "GROUP BY g.gallery_id ";
			switch( $orderby ){
				case 'name': $orderby = 'g.gallery_name'; break;
				case 'username': $orderby = 'u.username'; break;
				default: $orderby = "u.username $orderdir, g.gallery_name "; break;
			}
			$sql .= "ORDER BY $orderby $orderdir ";
			settype($first, 'integer');
			settype($limit, 'integer');
			if( $limit > 0 ){	
				$sql .= "LIMIT ".max(0,(int)$first).", ".(int)$limit." ";
			}
			$res = $this->ace->query($sql, 'Get Galleries');
			$gals = array();
			while( $g = mysql_fetch_object($res) ) $gals[] = $g;
			return $gals;
		}else{
			$res = $this->ace->query($sql, 'Get Gallery Count');
			return mysql_result($res,0,0);
		}
		return array();
	}

	function deletegalleries($ids, $delimages = false){
		$ids = $this->ace->getids($ids);
		$deleted = 0;
		if( count($ids) > 0 ){
			$sql = "DELETE FROM {pa_dbprefix}galleries WHERE gallery_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Delete Galleries');
			$deleted = mysql_affected_rows();
			if( $delimages ){
				$sql = "SELECT image_id FROM {pa_dbprefix}images WHERE gallery_id IN (".join(",",$ids).") ";
				$res = $this->ace->query($sql, 'Get Gallery Images');
				$imgs = array();
				while( list($id) = mysql_fetch_row($res) ) $imgs[] = $id;
				if( count($imgs) > 0 ) $this->deleteimages($imgs);
			}else{
				$sql = "UPDATE {pa_dbprefix}images SET gallery_id=0 WHERE gallery_id IN (".join(",",$ids).") ";
				$this->ace->query($sql, 'Update Images Galleries');
			}
		}
		return $deleted;
	}

    function saveImage($image, $fname, $jpeg_quality = 70)
    {
        $format = 'jpg';
        if( preg_match('#\.(jpg|gif|png)$#', $fname, $match) ) {
            $format = $match[1];
        }
        switch( $format ) {
            case 'jpg':
                imagejpeg($image, $fname, $jpeg_quality);
                break;
            case 'png':
                imagepng($image, $fname);
                break;
            case 'gif':
                imagegif($image, $fname);
                break;
        }
        chmod($fname, 0777);
    }

    function updateCaptions($img_ids, $captions, $descriptions = null)
    {
        if( !$this->user ) {
            return 0;
        }
        // check the image ids belong to this user
        $ids = array();
        foreach( $img_ids as $id ) {
            settype($id, 'integer');
            $ids[] = $id;
        }
        $updated = 0;
        if( count($ids) ) {
            $ids = join(',',$ids);
            $sql = "
                SELECT image_id, caption, description
                FROM images
                WHERE user_id = {$this->user->user_id}
                AND image_id IN ({$ids})
            ";
            $imgs = $this->app->db->fetchObjects($sql);
            foreach( $imgs as $img ) {
                $ups = array();
                if( isset($captions[$img->image_id]) ) {
                    $caption = substr(trim($captions[$img->image_id]), 0, 60);
                    if( $caption != $img->caption ) {
                        $ups[] = "caption = '".$this->app->db->escape($caption)."' ";
                    }
                }
                if( isset($descriptions[$img->image_id]) ) {
                    $description = substr(trim($descriptions[$img->image_id]), 0, 255);
                    if( $description != $img->description ) {
                        $ups[] = "description = '".$this->app->db->escape($description)."' ";
                    }
                }
                if( count($ups) ) {
                    $sql = "UPDATE images SET ".join(",",$ups)." WHERE image_id = {$img->image_id} ";
                    $this->app->db->query($sql);
                    $updated++;
                }
                if( $updated % 10 == 0 ) {
                    usleep(100000);
                }
            }
        }
        return $updated;
    }
}

?>