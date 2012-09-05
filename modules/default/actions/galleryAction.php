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

class galleryAction extends action
{

	function run()
	{
		$un = $this->app->getParamStr('u');
		$gn = $this->app->getParamStr('g');
		$iname = $this->app->getParamStr('i');
		$images = $this->app->loadClass('images');
		$gallery = $images->getgallerybyusernameimage($un, $gn, $iname);

		if( $gallery ){
			$gurl = $this->url('gallery', 'g='.$gallery->gallery_name.'&u='.$gallery->username);

			if( $gallery->image  ){	// viewing the image in the gallery

				$tpl = join("", file(INCLUDE_DIR . DIRECTORY_SEPARATOR . 'gallery-image.htm'));
				// display the image
				// if next, display next, if prev, display prev

				$gwidth = $gallery->image->width;
				$gheight = $gallery->image->height;
				$mw = $this->app->config->gallery_max_image_width;
				$mh = $this->app->config->gallery_max_image_height;

				if( $gwidth > $mw || $gheight > $mh ){
					$dx = (double)$gwidth / (double)$mw;
					$dy = (double)$gheight / (double)$mh;
					$d = $dx > $dy ? $dx : $dy;
					$gwidth = (int)((double)$gwidth / $d);
					$gheight = (int)((double)$gheight / $d);
				}

				$s = array('{nextimagename}','{nextimageurl}', '{nextimagethumb}', '{previmagename}', '{previmageurl}',
							'{previmagethumb}', '{image_name}', '{image_url}', '{image_width}', '{image_height}',
							'{image_size}', '{image_thumb}', '{gallery_width}', '{gallery_height}');

				$r = array('', '', '', '', '', '', $gallery->image->name.'.'.$gallery->image->type,
								$gallery->image->image_url,
								$gallery->image->width, $gallery->image->height, 
								$gallery->image->filesize, $gallery->image->thumb_url,
								$gwidth, $gheight);

				if( $gallery->nextimage == -1 ){
					$tpl = preg_replace('/<nextimage>.*?<\/nextimage>/is', '', $tpl);
				}else{
					$tpl = preg_replace('/<nonextimage>.*?<\/nonextimage>/is', '', $tpl);
					$img = &$gallery->images[$gallery->nextimage];
					$r[0] = $img->name.'.'.$img->type;
					$r[1] = $gurl.'&i='.$r[0];
					$r[2] = $img->thumb_url;
				}

				if( $gallery->previmage == -1 ){
					$tpl = preg_replace('/<previmage>.*?<\/previmage>/is', '', $tpl);
				}else{
					$tpl = preg_replace('/<noprevimage>.*?<\/noprevimage>/is', '', $tpl);
					$img = &$gallery->images[$gallery->previmage];
					$r[3] = $img->name.'.'.$img->type;
					$r[4] = $gurl.'&i='.$r[3];
					$r[5] = $img->thumb_url;
				}

				$tpl = preg_replace('/<[\/]{0,1}(next|prev)image>/is', '', $tpl);
				$tpl = str_replace($s, $r, $tpl);

			}else{	// display the gallery

				$tpl = join("", file(INCLUDE_DIR . DIRECTORY_SEPARATOR . 'gallery.htm'));

				// find the <images> bit of the template & add all images

				if( count($gallery->images) == 0 ){	// no images?
					$tpl = preg_replace('/<images>.*?<\/images>/is', '', $tpl);
				}else{
					$tpl = preg_replace('/<noimages>.*?<\/noimages>/is', '', $tpl);
					if( preg_match('/<images>(.*)<\/images>/is', $tpl, $match ) ){
						$itpl = '';
						$ss = array('{thumb_url}', '{image_url}', '{image_name}', '{image_width}', '{image_height}');
						foreach( $gallery->images as $i ){
							$rr = array($i->thumb_url, $gurl.'&i='.$i->name.'.'.$i->type, $i->name.'.'.$i->type, $i->width, $i->height);
							$itpl.= str_replace($ss, $rr, $match[1]);
						}
						$tpl = preg_replace('/<images>.*<\/images>/is', $itpl, $tpl);
					}
				}

				$tpl = preg_replace('/<[\/]{0,1}(noimages|images)>/is', '', $tpl);

			}

			// general template stuff
			//	1) do ads
			// 	2) do username, gallery name, gallery intro, number of images, site name, site url

			$s = array('{username}', '{gallery_name}', '{gallery_intro}', '{images}', '{sitename}', '{siteurl}',
							'{gallery_url}');
			$r = array($gallery->username, $gallery->gallery_name, nl2br(htmlspecialchars($gallery->gallery_intro)),
							count($gallery->images), htmlspecialchars($this->app->config->sitename), $this->app->config->siteurl,
							$gurl);

			$adcnt = substr_count($tpl, '{bannerad}');
            if( $adcnt > 0 ) {
                $banners = $this->app->loadClass('adrotator');
                $banners->preload($adcnt);
                for( $i = 0; $i < $adcnt; $i++ ) {
                    $tpl = preg_replace('#{bannerad}#', $banners->display(true), $tpl, 1);
                }
            }
			$tpl = str_replace($s, $r, $tpl);
			$this->theme->layoutName = '';
			$this->theme->assign('templateContent',$tpl);
			$this->theme->templateName = 'pagecontent';
		}else{
			$this->theme->assign('templateContent',$this->theme->_t('Gallery Not Found Content'));
			$this->theme->templateName = 'pagecontent';
		}
	}
}

?>
