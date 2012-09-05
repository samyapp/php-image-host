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

class galleriesAction extends Action
{
    function run()
    {
        $images = $this->app->loadClass('images');

        $pagesizes = array(10, 25, 50, 100, 250, 500, 1000);

        $message = '';
        $errors = array();
        $criteria = array();

        $ids = isset($_POST['ids']) ? $this->app->getids($_POST['ids']) : array();
        if( count($ids) > 0 ){
            if( $this->app->getParamStr('delete') != '' ){
                $delimages = $this->app->getParamInt('deleteimages');
                $message .= $images->deletegalleries($ids, $delimages)." galleries ".($delimages ? 'and all images in them ' : '')."deleted.";
            }
        }

        $perpage = $this->app->getParamInt('perpage', 10);

        $name = $this->app->getParamStr('name');
        $username = $this->app->getParamStr('username');

        $orderby = $this->app->getParamStr('orderby');
        $orderdir = $this->app->getParamStr('orderdir');

        if( $username != '' ) $criteria['username'] = $username;
        if( $name != '' ) $criteria['name'] = $name;

        $criteria['count'] = true;

        $numgalleries = $images->getgalleries($criteria);

        $numpages = ceil($numgalleries / $perpage);

        $page = $this->app->getParamInt('page', 1);
        if( $page < 1 ) $page = 1;
        if( $page > $numpages ) $page =$numpages;

        $first = ($page-1) * $perpage;
        $last = min($numgalleries,(($page) * $perpage));

        $criteria['count'] = false;
        $gals = null;
        if( $numgalleries > 0 ){
            $gals = $images->getgalleries($criteria, $orderby, $orderdir, $first, $perpage);
        }

        $pageurl = $this->url('galleries',"name=$name&username=$username&perpage=$perpage&page={page}&orderby=$orderby&orderdir=$orderdir");

        foreach( array('pageurl', 'name', 'username', 'orderby', 'orderdir', 'perpage',
                    'message', 'errors', 'numgalleries', 'first', 'last', 'page',
                    'numpages', 'gals') as $v ) {
            $this->theme->assign($v, $$v);
        }
    }
}
