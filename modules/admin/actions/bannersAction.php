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

class bannersAction extends Action
{
    function run()
    {
        $rotator = $this->app->loadClass('adrotator');
        $message = '';
        $name = $content = $group = '';
        $live = 0;

        $aid = $this->app->getParamInt('a');
        $ad = 0;
        if( $aid != 0 ){
            $ad = $rotator->getad($aid);
        	if( !$ad ) $aid = 0;
        }

        if( $ad && $this->app->getParamStr('update') ){
        	$ad->content = $this->app->getParamStr('content');
            $ad->group = $this->app->getParamStr('group');
            $ad->live = $this->app->getParamInt('live');
            $rotator->updatead($aid, $ad->group, $ad->content, $ad->live);
        	$message = 'Ad "'.htmlspecialchars($ad->name).'" Updated.';
        }

        $ids = isset($_POST['ids']) ? $this->app->getids($_POST['ids']) : array();

        if( count($ids) > 0 ){
        	if( $this->app->getParamStr('delete') != '' ){
        		$message = $rotator->deleteads($ids).' Ads Deleted.';
            }elseif( $this->app->getParamStr('reset') != '' ){
                $message = $rotator->resetviews($ids).' Ads Reset To 0 Views.';
            }elseif( $this->app->getParamStr('setlive') != '' ){
                $message = $rotator->changestatus($ids, 1).' Ads Status Changed To "Live".';
            }elseif( $this->app->getParamStr('notlive') != '' ){
                $message = $rotator->changestatus($ids, 0).' Ads Status Changed To "Not Live".';
            }
        }elseif( $this->app->getParamStr('add') != '' ){
            $name = $this->app->getParamStr('name');
            $content = $this->app->getParamStr('content');
            $group = $this->app->getParamStr('group');
            $live = $this->app->getParamInt('live');
            if( $name != '' ){
                $aid = $rotator->addad($name, $group, $content, $live);
                $ad = $rotator->getad($aid);
                $message = 'Ad Added.';
            }else{
                $message = 'You must enter a name for this new ad.';
            }
        }
        foreach( array('message', 'content', 'ad', 'name', 'group', 'rotator', 'live') as $v ) {
            $this->theme->assign($v, $$v);
        }
    }
}
