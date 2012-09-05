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

class ipsAction extends Action
{
    function run()
    {
        $added = 0;
        $deleted = 0;
        if( $this->app->getParamStr('unban') && isset($_POST['ids']) ){
            $deleted = $this->app->unbanips($_POST['ids']);
        }elseif( $this->app->getParamStr('ban') != '' ){
            $tmp = explode("\n",$this->app->getParamStr('tobebanned'));
            $ips = array();
            foreach( $tmp as $i ){
                $i = trim($i);
                if( $i != '' ) $ips[] = $i;
            }
            if( count($ips) ){
                $added = $this->app->banips($ips);
            }
        }
        $this->theme->assign('added', $added);
        $this->theme->assign('deleted', $deleted);
    }
}
