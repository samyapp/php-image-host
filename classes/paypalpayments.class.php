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

class paypalpayments{

	var $orderbys = array('username'=>'Username', 'email'=>'User Email', 'paypal_email'=>'PayPal Email',
												'amount'=>'Amount', 'txn_id'=>'Transaction Id#', 'dated'=>'Date');

	function paypalpayments(&$ace){
		$this->ace =& $ace;
	}

	function addpayment($txnid,$subid, $userid, $paypalemail, $amount, $status, $paypal_data){
		$paypal_data = mysql_real_escape_string(serialize($paypal_data));
		$paypalemail = mysql_real_escape_string($paypalemail);
		$amount = mysql_real_escape_string($amount);
		$status = mysql_real_escape_string($status);
		$txnid = mysql_real_escape_string($txnid);
		$subid = mysql_real_escape_string($subid);

		$sql = "INSERT INTO paypalpayments ";
		$sql .="(txn_id, sub_id, user_id, paypal_email, amount, paypal_data, dated ) ";
		$sql .="VALUES ('$txnid', '$subid', $userid, '$paypalemail', '$amount', '$paypal_data', now()) ";
		$res = @mysql_query($sql);
		if( !$res ){
			return 0;
		}
		return mysql_insert_id();
	}

	function getpayments($criteria, $orderby = 'dated', $orderdir = 'desc', $first = 0, $limit = -1){
		$wheres = array('u.user_id=p.user_id ');
		foreach( $criteria as $c=>$v ){
			switch( $c ){
				case 'username': $wheres[] = "u.username='".mysql_real_escape_string($v)."' "; break;
				case 'userid': settype($v,'integer'); $wheres[] = "p.user_id=$v "; break;
				case 'email': $wheres[] = "u.email='".mysql_real_escape_string($v)."' "; break;
				case 'paypalemail': $wheres[] = "p.paypal_email='".mysql_real_escape_string($v)."' "; break;
				case 'minamount': settype($v, 'double'); $wheres[] = "p.amount>='$v' "; break;
				case 'maxamount': settype($v, 'double'); $wheres[] = "p.amount<='$v' "; break;
				case 'to': $wheres[] = "p.dated<='".mysql_real_escape_string($v)."' "; break;
				case 'from': $wheres[] = "p.dated>='".mysql_real_escape_string($v)."' "; break;
				case 'txnid': $wheres[] = "p.txn_id='".mysql_real_escape_string($v)."' "; break;
				case 'id': settype($id, 'integer'); $wheres[] = "p.payment_id=$v "; break;
				case 'ids': $ids = $this->ace->getids($v); $wheres[] = "p.payment_id IN (".join(",",$ids).") "; break;
				case 'subid': $wheres[] = "p.sub_id='".mysql_real_escape_string($v)."' "; break;
			}
		}
		$counting = isset($criteria['count']) ? $criteria['count'] : false;
		if( $counting ){
			$sql = "SELECT COUNT(*) ";
		}else{
			$sql = "SELECT p.*, u.* ";
		}
		$sql .="FROM paypalpayments p, users u ";
		$sql .="WHERE ".join(" AND ", $wheres)." ";

		if( $counting ){

			$res = $this->ace->query($sql, 'Count Payments');
			return mysql_result($res,0,0);
		}else{
			$ob = 'p.dated';
			switch( $orderby ){
				case 'username':
				case 'email':
					$ob = "u.$orderby ";
					break;
				case 'dated':
				case 'paypal_email':
				case 'txn_id':
				case 'amount':
					$ob = "p.$orderby ";
					break;
			}
			$sql .="ORDER BY $ob $orderdir ";
			if( $limit != -1 ){
				$sql .= "LIMIT ".max(0,(int)$first).",".(int)$limit." ";
			} 
			$res = $this->ace->query($sql, 'Get Payments');
			$ps = array();
			while($p = mysql_fetch_object($res)) $ps[] = $p;
			return $ps;
		}
		return 0;
	}

	function deletepayments($ids){
		$ids = $this->ace->getids($ids);
		if( count($ids) > 0 ){
			$sql = "DELETE FROM paypalpayments WHERE payment_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Delete Payments');
			return mysql_affected_rows();
		}
	}

}

?>
