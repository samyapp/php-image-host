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

class paymentsAction extends Action
{
    function run()
    {
        $payments = $this->app->loadClass('paypalpayments');
        $message = '';
        $errors = array();
        $criteria = array();

        $ids = isset($_POST['ids']) ? $this->app->getids($_POST['ids']) : array();
        if( count($ids) > 0 ){
            if( $this->app->getParamStr('delete') != '' ){
                $message = $payments->deletepayments($ids)." payment(s) deleted.";
            }
        }

        $perpage = $this->app->getParamInt('perpage', 10);

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

        $username = $this->app->getParamStr('username');
        $email = $this->app->getParamStr('email');
        $paypalemail = $this->app->getParamStr('paypalemail');
        $userid = $this->app->getParamInt('userid');
        $txnid = $this->app->getParamStr('txnid');

        $orderby = $this->app->getParamStr('orderby');
        $orderdir = $this->app->getParamStr('orderdir');

        if( !isset($payments->orderbys[$orderby]) ) $orderby = 'dated';
        $orderdir = $this->app->getParamStr('orderdir', 'asc');
        if( $orderdir != 'asc' && $orderdir != 'desc' ) $orderdir = 'asc';

        if( $username != '' ) $criteria['username'] = $username;
        if( $email != '' ) $criteria['email'] = $email;
        if( $paypalemail != '' ) $criteria['paypalemail'] = $paypalemail;
        if( $txnid != '' ) $criteria['txnid'] = $txnid;
        if( $userid != 0 ) $criteria['userid'] = $userid;

        if( $todate != '' ) $criteria['to'] = $todate;
        if( $fromdate != '' ) $criteria['from'] = $fromdate;

        $criteria['count'] = true;

        $numpayments = $payments->getpayments($criteria);

        $numpages = ceil($numpayments / $perpage);

        $page = $this->app->getParamInt('page', 1);
        if( $page < 1 ) $page = 1;
        if( $page > $numpages ) $page =$numpages;

        $first = ($page-1) * $perpage;
        $last = min($numpayments,(($page) * $perpage));

        $criteria['count'] = false;

        if( $numpayments > 0 ) $pays = $payments->getpayments($criteria, $orderby, $orderdir, $first, $perpage);

        $pageurl = $this->url('payments', "fromdate=$fromdate&todate=$todate&email=$email&paypalemail=$paypalemail&userid=$userid&txnid=$txnid&username=$username&perpage=$perpage&page={page}&orderby=$orderby&orderdir=$orderdir");

        foreach( array('pageurl', 'numpayments', 'last', 'first', 'page', 'perpage',
                        'numpages', 'todate', 'fromdate', 'txnid', 'userid', 'paypalemail',
                        'email', 'username', 'orderby', 'orderdir', 'ids',
                        'payments', 'message', 'errors') as $v ) {
                        
            $this->theme->assign($v, $$v);
        }
    }
}
