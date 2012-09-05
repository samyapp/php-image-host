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

class usersAction extends Action
{
    function run()
    {
        $users = $this->app->loadClass('users');

        $femail = $this->app->getParamStr('femail');
        $fusername = $this->app->getParamStr('fusername');
        $fname = $this->app->getParamStr('fname');
        $fstatus = $this->app->getParamInt('fstatus');
        $fpassword = $this->app->getParamStr('fpassword');

        $user = 0;
        $edit = $this->app->getParamInt('edit');
        $userid = $this->app->getParamInt('userid');
        if( $userid != 0 ){
            $user = $users->getuser(array('id'=>$userid));
            if( !$user ){
                $userid = 0;
            }else{
                $edit = 1;
                if( $this->app->getParamStr('update') == ''){
                    $femail = $user->email;
                    $fusername = $user->username;
                    $fname = $user->name;
                    $fstatus = $user->status;
                    $fpassword = $user->pass;
                }
            }
        }

        $message = '';
        $errors = array();
        $criteria = array();
        $statustexts = array('Unconfirmed', 'Confirmed', 'Suspended');
        $ids = isset($_POST['ids']) ? $this->app->getids($_POST['ids']) : array();
        if( count($ids) > 0 ){
            if( $this->app->getParamStr('delete') != '' ){
                $message = $users->deleteusers($ids)." user(s) deleted. ";
            }elseif( $this->app->getParamStr('upgrade') != '' ){
                $extradays = $this->app->getParamInt('extradays');
                if( $extradays > 0 ){
                    $message = $users->upgradeusers($ids, $extradays).' user(s) upgraded to paid account for '.$extradays.' additional days.';
                }
            }elseif( $this->app->getParamStr('downgrade') != '' ){
                $message = $users->updateusers($ids, array('account_type'=>'free', 'paid_until'=>date('Y-m-d'))).' users account type set to "free".';
            }elseif( $this->app->getParamStr('changestatus') != '' ){
                $newstatus = $this->app->getParamInt('newstatus');
                if( $newstatus >= 0 && $newstatus < 3){
                    $message = $users->updateusers($ids, array('status'=>$newstatus)).' users accounts set to "'.$statustexts[$newstatus].'".';
                }
            }
        }elseif( $this->app->getParamStr('update') != '' ){
            if( !$user ){
                $userid = $users->adduser($fusername, $fpassword, $femail, $fname, $fstatus);
                if( $userid ){
                    $message = 'User "'.$fusername.'" Added.';
                    $user = $users->getuser(array('id'=>$userid));
                }else{
                    $errors = $users->errors;
                }
            }else{
                if( $user->username != $fusername ){
                    if( !preg_match('/^([a-z0-9]{2,20})$/i', $fusername) ){
                        $users->errors[] = 'The username must be between 2 and 20 alphanumeric characters long.';
                    }else{
                        if( $users->getuser(array('username'=>$fusername) ) ) $users->errors[] = 'The username "'.$fusername.'" is already taken by another user.';
                    }
                }
                if( !preg_match('/^([a-z0-9]{6,20})$/i', $fpassword) ) $users->errors[] = 'The password must be between 6 and 12 alphanumeric characters long.';
                if( $user->email != $femail ){
                    if( !$this->app->validateemail($femail) ){
                        $users->errors[] = 'You must enter a valid email address.';
                    }else{
                        if( $users->getuser(array('email'=>$femail)) ) $users->errors[] = 'The email address "'.$femail.'" is already in use in another account.';
                    }
                }
                if( count($users->errors) == 0 ){
                    if( $users->updateusers(array($userid), array('username'=>$fusername, 'email'=>$femail,
                                                                        'pass'=>$fpassword, 'name'=>$fname, 'status'=>$fstatus)) ){
                        $message = 'User "'.$fusername.'" Updated.';
                        $user->username = $fusername;
                    }
                }
                $errors = $users->errors;
            }
            $edit = 1;
        }

        $perpage = $this->app->getParamInt('perpage', 30);

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

        $name = $this->app->getParamStr('name');
        $email = $this->app->getParamStr('email');
        $username = $this->app->getParamStr('uname');
        $totalimages = $this->app->getParamInt('images');
        $datetype = $this->app->getParamStr('datetype');
        $datetypes = array('joined'=>'Joined', 'loggedin'=>'Last Loggedin');
        if( !isset($datetypes[$datetype]) ) $datetype = 'joined';
        $status = $this->app->getParamInt('status',-1);

        $bwidth = $this->app->getParamInt('bandwidth');

        $account = $this->app->getParamStr('account');

        $orderby = $this->app->getParamStr('orderby');
        $orderdir = $this->app->getParamStr('orderdir');

        if( !isset($users->orderbys[$orderby]) ) $orderby = 'username';
        $orderdir = $this->app->getParamStr('orderdir', 'asc');
        if( $orderdir != 'asc' && $orderdir != 'desc' ) $orderdir = 'asc';

        if( $bwidth ) $criteria['bandwidth'] = true;
        if( $username != '' ) $criteria['username'] = $username;
        if( $name != '' ) $criteria['name'] = $name;
        if( $email != '' ) $criteria['email'] = $email;
        if( $account != '' ) $criteria['account'] = $account;
        if( $todate != '' ) $criteria[$datetype.'before'] = $todate;
        if( $fromdate != '' ) $criteria[$datetype.'after'] = $fromdate;
        if( $status != -1 ) $criteria['status'] = $status;

        $criteria['count'] = true;

        $numusers = $users->getusers($criteria);

        $numpages = ceil($numusers / $perpage);

        $page = $this->app->getParamInt('page', 1);
        if( $page < 1 ) $page = 1;
        if( $page > $numpages ) $page =$numpages;

        $first = ($page-1) * $perpage;
        $last = min($numusers,(($page) * $perpage));

        $criteria['count'] = false;
        $usrs = null;
        if( $numusers > 0 ) $usrs = $users->getusers($criteria, $orderby, $orderdir, $first, $perpage);

        $pageurl = $this->url('users', 'datetype='.$datetype.'&status='.$status.'&account='.$account.'&email='.$email.'&fromdate='.$fromdate.'&todate='.$todate.'&name='.$name.'&username='.$username.'&perpage='.$perpage.'&page={page}&orderby='.$orderby.'&orderdir='.$orderdir);

        foreach( array('pageurl', 'numusers', 'users','usrs', 'last', 'first',
                        'page', 'perpage', 'numpages', 'datetypes', 'email','user',
                        'edit', 'fromdate', 'todate','datetype','statustexts',
                        'name', 'username', 'orderby', 'orderdir', 'status','account',
                         'message', 'errors', 'fusername', 'femail',
                         'fname', 'fpassword', 'fstatus') as $v ) {
                         
                        
            $this->theme->assign($v, $$v);
        }
    }
}
