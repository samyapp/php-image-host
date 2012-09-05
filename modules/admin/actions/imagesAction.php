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

class imagesAction extends Action
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
                if( $this->app->getParamInt('banips') == 1 || $this->app->getParamInt('suspendusers') == 1 ){
                    $imgs = $images->getimages(array('ids'=>$ids));
                    $ips = array();
                    $usrs = array();
                    foreach( $imgs as $i ){
                        $ips[$i->ip] = true;
                        $usrs[$i->user_id] = true;
                    }
                    $ips = array_keys($ips);
                    $usrs = array_keys($usrs);
                    if( $this->app->getParamInt('banips') && count($ips) ){
                        $message = $this->app->banips($ips)." i.p. addresses banned. ";
                    }
                    if( count($usrs) && $this->app->getParamInt('suspendusers') != 0 ){
                        $message .= $images->suspendusers($usrs).' member(s) suspended. ';
                    }
                }

                $message .= $images->deleteimages($ids)." image(s) deleted.";
            }elseif( $this->app->getParamStr('setchecked') != '' ){
                $message = $images->setchecked($ids, true).' image(s) marked as "Checked".';
            }elseif( $this->app->getParamStr('setunchecked') != '' ){
                $message = $images->setchecked($ids, false).' image(s) marked as "Unchecked".';
            }elseif( $this->app->getParamStr('setviews') != '' ) {
                $message = $images->setViews($ids, $this->app->getParamInt('views')).' image(s) views updated.';
            }elseif( $this->app->getParamStr('setrating') != '' ) {
                $message = $images->setRatingVotes($ids, $this->app->getParamInt('rating'), $this->app->getParamInt('votes')).' image(s) ratings and votes updated.';
            }

        }

        $perpage = $this->app->getParamInt('perpage', 30);
        $display = 'table';

        $fromdate = $todate = '';
        $from = $this->app->getParamStr('fromdate');
        if( $from != '' ){
            if( ($tm = @strtotime($from)) !== -1 ){
                $fromdate = date('Y-m-d', $tm);
            }
        }
        $to = $this->app->getParamStr('todate');
        if( $to != '' ){
            if( ($tm = @strtotime($to)) !== -1 ){
                $todate = date('Y-m-d', $tm);
            }
        }

        $bandwidth = $this->app->getParamInt('bandwidth', 0);
        $name = $this->app->getParamStr('name');
        $filesize = $this->app->getParamInt('filesize', 0);
        $sizetype = $this->app->getParamStr('sizetype');
        $format = $this->app->getParamStr('format');
        $username = $this->app->getParamStr('username');
        $gallery = $this->app->getParamStr('gallery');
        $checked = $this->app->getParamInt('checked', -1);
        $ip = $this->app->getParamStr('ip');

        $orderby = $this->app->getParamStr('orderby');
        $orderdir = $this->app->getParamStr('orderdir');

        if( !isset($images->orderbys[$orderby]) ) $orderby = 'name';
        $orderdir = $this->app->getParamStr('orderdir', 'asc');
        if( $orderdir != 'asc' && $orderdir != 'desc' ) $orderdir = 'asc';

        if( $username != '' ) $criteria['username'] = $username;
        if( $name != '' ) $criteria['name'] = $name;
        if( $sizetype != '' ){
            if( $sizetype == '>' ){
                $criteria['minsize'] = $filesize;
            }else{
                $criteria['maxsize'] = $filesize;
            }
        }

        if( $format != '' ) $criteria['format'] = $format;
        if( $todate != '' ) $criteria['uploadedbefore'] = $todate;
        if( $fromdate != '' ) $criteria['uploadedafter'] = $fromdate;
        if( $checked != -1 ) $criteria['checked'] = $checked;
        if( $gallery != '' ) $criteria['galleryname'] = $gallery;
        if( $ip != '' ) $criteria['ip'] = $ip;

        $criteria['count'] = true;

        $numimages = $images->getimages($criteria);

        $numpages = ceil($numimages / $perpage);

        $page = $this->app->getParamInt('page', 1);
        if( $page < 1 ) $page = 1;
        if( $page > $numpages ) $page =$numpages;

        $first = ($page-1) * $perpage;
        $last = min($numimages,(($page) * $perpage));

        $criteria['count'] = false;
        $imges = null;
        if( $numimages > 0 ) $imges = $images->getimages($criteria, $orderby, $orderdir, $first, $perpage);

        $pageurl = $this->url('myimages', "checked=$checked&gallery=$gallery&ip=$ip&fromdate=$fromdate&todate=$todate&name=$name&username=$username&filesize=$filesize&format=$format&sizetype=$sizetype&perpage=$perpage&page={page}&orderby=$orderby&orderdir=$orderdir");

        foreach( array('pageurl', 'numimages', 'imges', 'images', 'last', 'first',
                        'page', 'perpage', 'numpages', 'ip', 'gallery',
                        'checked', 'fromdate', 'todate', 'format', 'sizetype',
                        'name', 'username', 'orderby', 'orderdir', 'filesize',
                        'bandwidth', 'display', 'message', 'errors') as $v ) {
                        
            $this->theme->assign($v, $$v);
        }
    }
}
