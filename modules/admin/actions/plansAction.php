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
class plansAction extends Action
{
    function run()
    {
        $sql = "SELECT * FROM account_types  ";
        $res = $this->app->query($sql, 'Get Account Types');

        $errors = '';
        $message = '';
        if( mysql_num_rows($res)<2 ) die("Error retrieving account details from database.");

        $plans = array();
        while( $p = mysql_fetch_object($res) ) $plans[$p->type_type] = $p;

        // create anonymous plan if not exists
        // would be better to have this in upgrade script,
        // but this is simpler for now
        if( !isset($plans['anonymous']) ) {
            $sql = "INSERT INTO account_types (type_type, max_images, max_upload_size) VALUES ('anonymous',0, 1024) ";
            $this->app->db->query($sql, 'Create Anonymous Account');
            header('Location:'.$this->app->url('plans'));
            exit();
        }

        if( $this->app->getParamStr('update') != '' ){

            $this->app->config->home_page_show_plans = $this->app->getParamInt('home_page_show_plans');
            $this->app->config->upgrade_show_plans = $this->app->getParamInt('upgrade_show_plans');
            $this->app->config->anonymous_uploads = $this->app->getParamInt('anonymous_uploads');
            // handle creating anonymous user account
            $anonac = $this->app->getParamStr('anonymous_account');
            if( $anonac != '' ) {
                if( $anonac != $this->app->config->anonymous_account ) {
                    // check if it exists
                    $acc = $this->app->loadClass('users')->getUser(array('username'=>$anonac));
                    if( $acc ) {
                        if( $acc->type_type != 'anonymous' ) {
                            $errors = 'The account name "'.$anonac.'" cannot be an anonymous uploads account as its type is "'.$acc->type_type.'"';
                        }
                        else {
                            $this->app->config->anonymous_account = $anonac;
                        }
                    }
                    else {
                        // create it if not
                        $acc = $this->app->loadClass('users')->createAnonymousAccount($anonac);
                        if( $acc ) {
                            $this->app->config->anonymous_account = $anonac;
                        }
                    }
                }
            }

            if( $this->app->config->anonymous_uploads && !$this->app->config->anonymous_account && !$errors ) {
                $this->app->config->anonymous_account = '';
                $errors = 'You must specify an account name to use for anonymous uploads. This should either be
                            a new (unused) account name, or the name of an account previously used for anonymous (not-logged-in) uploads.';
            }

            $this->app->savesettings(array('home_page_show_plans','upgrade_show_plans', 'anonymous_uploads', 'anonymous_account'));

            $ints = array('max_images', 'max_upload_size', 'max_image_width',
                            'max_image_height','bandwidth', 'storage','email_friends',
                            'auto_jpeg', 'auto_resize', 'jpeg_quality','add_branding',
                            'simultaneous_uploads', 'images_per_page', 'max_galleries',
                            'rename_images', 'resize_images', 'rotate_images',
                            'allow_zip_uploads', 'zip_uploads_max_images',
                            'zip_uploads_max_size');
            foreach( $ints as $i ){
                $n1 = $i.'1';
                $n2 = $i.'2';
                $n3 = $i.'3';
                $plans['free']->$i = $this->app->getParamInt($n1);
                $plans['paid']->$i = $this->app->getParamInt($n2);
                $plans['anonymous']->$i = $this->app->getParamInt($n3);
            }
            $plans['free']->captions = $this->app->getParamStr('captions1');
            if( !in_array($plans['free']->captions, array('none', 'captions', 'descriptions') ) ) {
                $plans['free']->captions = 'none';
            }
            $plans['paid']->captions = $this->app->getParamStr('captions2');
            if( !in_array($plans['paid']->captions, array('none', 'captions', 'descriptions') ) ) {
                $plans['paid']->captions = 'none';
            }

            $plans['free']->type_name = $this->app->getParamStr('type_name1', 'Free');
            $plans['paid']->type_name = $this->app->getParamStr('type_name2', 'Paid');
            $plans['paid']->cost_1 = $this->app->getParamDouble('cost_1', 4.95);
            $plans['paid']->cost_3 = $this->app->getParamDouble('cost_3', 4.95);
            $plans['paid']->cost_6 = $this->app->getParamDouble('cost_6', 4.95);
            $plans['paid']->cost_12 = $this->app->getParamDouble('cost_12', 4.95);

            if( $plans['free']->type_name == '' || $plans['paid']->type_name == '' ){
                $errors = '<div class="errors">You must enter a name for each plan.</div>';
            }elseif( !$errors ){
                $sql = "UPDATE account_types SET type_name='".mysql_real_escape_string($plans['free']->type_name)."',
                        captions='{$plans['free']->captions}' ";
                foreach( $ints as $i ){
                    $sql .= ','.$i.'='.$plans['free']->$i.' ';
                }
                $sql.= "WHERE type_type='free' ";
                $res = $this->app->query($sql, 'Update Free Plan');

                $sql = "UPDATE account_types SET cost_1 = '0'";
                foreach( $ints as $i ){
                    $sql .= ','.$i.'='.$plans['anonymous']->$i.' ';
                }
                $sql.= "WHERE type_type='anonymous' ";
                $res = $this->app->query($sql, 'Update Anonymous Upload Settings');

                $sql = "UPDATE account_types SET type_name='".mysql_real_escape_string($plans['paid']->type_name)."', ";
                $sql .="captions='{$plans['paid']->captions}', ";
                $sql .="cost_1='".$plans['paid']->cost_1."', ";
                $sql .="cost_3='".$plans['paid']->cost_3."', ";
                $sql .="cost_6='".$plans['paid']->cost_6."', ";
                $sql .="cost_12='".$plans['paid']->cost_12."' ";
                foreach( $ints as $i ){
                    $sql .= ','.$i.'='.$plans['paid']->$i.' ';
                }
                $sql.= "WHERE type_type='paid' ";
                $res = $this->app->query($sql, 'Update Paid Plan');
                $message =  '<div class="errors">Plan Specifications Updated</div>';

                if( !isset($users) ){
                    $users = $this->app->loadClass('users');
                }
                $users->canceluseroverbandwidth();
            }
        }
        foreach( array('plans','errors', 'message') as $var ) {
            $this->theme->assign($var, $$var);
        }
    }
}