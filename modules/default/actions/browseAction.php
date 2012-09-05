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

class browseAction extends action
{

	function init()
	{
		if( !$this->config->browse_images ){
			header('location: '.$this->app->url());
			exit();
		}
	}

	function run()
	{
		$heading = '';
		$id = null;
		$image = null;
		$prev_image = null;
		$next_image = null;
		$first =0;
		$image_n = $this->app->getParamInt('i', 1);
		$gallery_id = $this->app->getParamInt('g');
		$images = $this->app->loadClass('images');
		$gallery = null;
        $order = 'uploaded';
        if( $this->app->getParamStr('top') && $this->app->config->image_ratings != 'off' ) {
            $order = 'rating';
        }
        elseif( $this->app->getParamStr('popular') && $this->app->config->log_image_views ) {
            $order = 'views';
        }
		if( $gallery_id ){
			$gallery = $images->getgallery($gallery_id, true);
		}
		$username = $this->app->getParamStr('username');
		$options = array('public'=>1);
		if( $username ){
			$options['username'] = $username;
		}
		if( $gallery ){
			$options['gallery_id'] = $gallery->gallery_id;
		}
		if( $this->app->config->browse_checked_only ){
			$options['checked'] = 1;
		}
		$options['count'] = true;
		$total_images = $images->getimages($options);
		$image_n = min($total_images-1,max($image_n,0));
		unset($options['count']);

		$per_page = $this->app->config->browse_per_page;
		$total_pages = ceil($total_images / $per_page);
		$page = floor($image_n/$per_page)+1;
		$page = min(max(1,$page),$total_pages);
		$view = $this->app->getParamInt('view');
		if( !$view ){
			$image_n = ($page - 1) * $per_page;
		}
		if( $view ){
			$id = $this->app->getParamInt('id');
			if( $id ){
				$image_n = $images->getImagePos($id, $this->app->config->browse_checked_only == 1 ? 1 : null, 1);
			}
			elseif( preg_match('#^([a-z0-9_-]+)/([a-z0-9_-]+)\.(jpg|png|gif)$#i', 
                    $this->app->getParamStr('img'), $matches ) ) {
				$image = $images->getimage(array('path'=>$matches[1].'/'.$matches[2]));
				if( $image ){
					$image_n = $images->getImagePos($image->image_id,$this->app->config->browse_checked_only == 1 ? 1 : null, 1);
				}
			}
			$prev_image = $next_image = $image = null;
			$first = $image_n;
			$limit = 3;
			if( $first == 0){
				$limit = 2;
			}
			else{
				$first -= 1;
			}
			$imgs = $images->getimages($options, $order, 'DESC', $first, $limit);
			if( $limit == 3 ){
				@list($prev_image, $image, $next_image) = $imgs;
			}
			else{
				@list($image, $next_image) = $imgs;
			}
			if( $id ){
				$image = $images->getimage(array('image_id'=>$id));
			}
            if( $image ) {
                if( $this->app->config->image_ratings != 'off' && isset($_POST['rating']) ) {
                    for( $i = 1; $i <= 10; $i++ ) {
                        if( isset($_POST['rate_'.$i]) ) {
                            $this->helper('images')->rateImage($image, $i);
                            $i = 11;
                        }
                    }
                }
                elseif( $this->app->config->log_image_views ) {
                    $this->helper('images')->logView($image);
                }
            }
		}
		else{
			$imgs = $images->getimages($options, $order, 'DESC', $image_n, $per_page);
		}
		if( $imgs ){
            if( $order == 'uploaded' ) {
    			$heading = 'Browse images';
                if( $username ){
                    $heading .= ' '.$this->app->translate('from').' &quot;'.htmlspecialchars($username).'&quot;';
                }
                if( $gallery ){
                    $heading .= ' '.$this->app->translate('in gallery').' &quot;'.htmlspecialchars($gallery->gallery_name).'&quot;';
                }
            }
            elseif( $order == 'rating' ) {
                $heading = $this->app->translate('Top Rated Images');
            }
            elseif( $order == 'views' ) {
                $heading = $this->app->translate('Most Viewed Images');
            }
		}
		else{
			$this->theme->templateContent = $this->theme->_t('Browse - No Images Content');
			$this->theme->templateName = 'pagecontent';
		}
        $extraoptions = '';
        if( $order == 'rating' ) {
            $extra_options = '&top=images';
        }
        elseif( $order == 'views' ){
            $extra_options = '&popular=images';
        }
		foreach( array( 'extra_options',
				'heading', 'username', 'image_n','gallery', 'imgs','total_images','per_page',
				'total_pages', 'page', 'view', 'id', 'image', 'prev_image', 'next_image',
				'first','gallery_id'
				)
				as $v_name ) {
			$this->theme->assign($v_name, $$v_name);
		}
	}

}

?>
