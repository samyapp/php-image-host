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

class imagesHelper extends helper
{
    /**
     * Logs a view of an image
     * @param image object from database $image
     */
    public function logView($image)
    {
        if( is_object($image) ) {
            if( $this->app->config->log_image_views ) {
                if( !isset($_SESSION['pih_image_views'][$image->image_id])) {
                    $sql = "
                        UPDATE images SET views=views+1 WHERE image_id = {$image->image_id}
                    ";
                    $this->app->db->query($sql, 'Log image view');
                    $image->views++;
                    if( !isset($_SESSION['pih_image_views']) ) {
                        $_SESSION['pih_image_views'] = array();
                    }
                    $_SESSION['pih_image_views'][$image->image_id] = 1;
                }
            }
        }
    }

    public function rateImage($image, $rating)
    {
        settype($rating, 'integer');

        if( is_object($image) && $rating > 0 && $rating <= 10 ) {
            if( $this->app->config->image_ratings != 'off' ) {
                if( $this->app->userSession->loggedin || $this->app->config->image_ratings == 'anyone' ) {
                    if( !isset($_SESSION['pih_rated_images'][$image->image_id]) ) {
                        $sql = "
                            UPDATE images SET rating = ( ( rating * votes ) + {$rating} ) / (votes+1), votes = votes+1
                                WHERE image_id = {$image->image_id}
                        ";
                        $this->app->db->query($sql, 'Rate Image');
                        $update = $this->app->db->fetchObject("SELECT rating, votes FROM images WHERE image_id = {$image->image_id} ");
                        if( $update ) {
                            $image->rating = ceil($update->rating);
                            $image->votes = $update->votes;
                        }
                        if( !isset($_SESSION['pih_rated_images'])) {
                            $_SESSION['pih_rated_images'] = array();
                        }
                        $_SESSION['pih_rated_images'][$image->image_id] = 1;
                        return 1;
                    }
                    else {
                        return -1;
                    }
                }
            }
        }
        return 0;
    }
}
