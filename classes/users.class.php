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

class users{

	var $errors = array();
	var $orderbys = array('name'=>'Name', 'username'=>'Username', 'joined'=>'Date Joined',
												'lastlogin'=>'Last Login Date', 'images'=>'Total Images', 'email'=>'Email Address',
												'status'=>'Account Status', 'type'=>'Account Type', 'bandwidth'=>'Bandwidth Used',
												'storage'=>'Storage Used');

    var $app = null;

    function __construct($app)
    {
        $this->app = $app;
        $this->ace = $app;
    }

	function users($app)
    {
        $this->__construct($app);
	}

	function adduser($username, $password, $email, $name, $status = -1){
		if( !preg_match('/^[a-z0-9]{2,20}$/i', $username) ) $this->errors [] = 'Your username must be between 2 and 20 alphanumeric (a-z0-9) characters long.';
		if( !preg_match('/^[a-z0-9]{6,20}$/i', $password) ) $this->errors[] = 'Your password must be between 6 and 20 alphanumeric (a-z0-9) characters long.';
		settype($status, 'integer');
		$status = min(2, max(-1,$status));
		if( $email == '' ){
			$this->errors[] = 'You must enter your email address.';
		}elseif( !$this->ace->validateemail($email) ){
			$this->errors[] = '"'.htmlspecialchars($email).'" is not a valid email address.';
		}
		if( !preg_match('/[a-z]{2,}/i', $name) ) $this->errors[] = 'You must enter your name.';
		if( count($this->errors) == 0 ){
			$sql = "SELECT username, email FROM users WHERE username='".mysql_real_escape_string($username)."' OR email='".mysql_real_escape_string($email)."' ";
			$res = $this->ace->query($sql, 'Check for existing username or email');
			while( list($uname, $em) = mysql_fetch_row($res) ){
				if( strtolower($uname) == strtolower($username) ){
					$this->errors[] = 'The username "'.$username.'" is already in our database. Please choose a different username.';
				}elseif( strtolower($em) == strtolower($email) ){
					$this->errors[] = 'The email address "'.$email.'" is already in our database.';
				}
			}
			if( count($this->errors) == 0 ){
				if( $status == -1 ){
					$status = $this->ace->config->email_confirmation == 1 ? 0 : 1;
				}
				$ip = isset($_SERVER['X_FORWARDED_FOR']) ? mysql_real_escape_string($_SERVER['X_FORWARDED_FOR']) : mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
				$sql = "INSERT INTO users (username, pass, email, name, status, joined, updated, ip, lastlogin, loginip) ";
				$sql .="VALUES ('$username', '$password', '$email', '".mysql_real_escape_string($name)."', $status, now(), now(), '$ip', now(), '$ip') ";
				$res = $this->ace->query($sql, 'Add New Member');
				$userid = mysql_insert_id();
				if( $userid != 0 ){
					mkdir($this->ace->config->image_folder.$username);
					chmod($this->ace->config->image_folder.$username, 0777);
					mkdir($this->ace->config->thumb_folder.$username);
					chmod($this->ace->config->thumb_folder.$username, 0777);
				}
				return $userid;
			}
		}
		return 0;
	}

	function getuser($criteria){
		$wheres = array(" p.type_type=u.account_type ");
		foreach( $criteria as $c=>$v ){
			switch( $c ){
				case 'id': settype($v, 'integer'); $wheres[] = "u.user_id=$v "; break;
				case 'username': $wheres[] = "u.username='".mysql_real_escape_string($v)."' "; break;
				case 'password': $wheres[] = "u.pass='".mysql_real_escape_string($v)."' "; break;
				case 'email': $wheres[] = "u.email='".mysql_real_escape_string($v)."' "; break;
			}
		}
		$sql = "SELECT u.*,p.*,  COUNT(i.image_id) AS images, (u.deleted_images_bandwidth+SUM(i.bandwidth))/(1024*1024) AS bandwidth_used, SUM(i.filesize)/(1024*1024) AS storage_used ";
		$sql .="FROM account_types p,  users u LEFT OUTER JOIN images i ON u.user_id=i.user_id ";
		$sql .="WHERE ".join(' AND ', $wheres)." ";
		$sql .="GROUP BY u.user_id ";
		$res = $this->ace->query($sql, 'Get User');
		$user = mysql_fetch_object($res);

		if( $user ){
            if( !$user->bandwidth_used && $user->deleted_images_bandwidth){
                $user->bandwidth_used = $user->deleted_images_bandwidth / (1024*1024);
            }
			$user->bandwidth_used = number_format($user->bandwidth_used,2);
			$user->storage_used = number_format($user->storage_used,2);
			$user->galleries = array();
			$sql = "SELECT g.*, COUNT(i.image_id) AS images ";
			$sql .="FROM {pa_dbprefix}galleries g LEFT OUTER JOIN images i ";
			$sql .="ON g.gallery_id=i.gallery_id ";
			$sql .="WHERE g.user_id={$user->user_id} ";
			$sql .="GROUP BY g.gallery_id ";
			$sql .="ORDER BY g.gallery_name ";
			$res = $this->ace->query($sql, 'Get User Galleries');
			while( $gal = mysql_fetch_object($res) ) $user->galleries[$gal->gallery_id] = $gal;
		}
		return $user;
	}

	function confirmuser($username, $cid){
		$sql = "SELECT user_id, name, status FROM users ";
		$sql .="WHERE username='".mysql_real_escape_string($username)."' ";
		$sql .="AND LEFT(MD5(CONCAT(email,username,'".mysql_real_escape_string($this->ace->config->email_confirmation_key)."')),12)='".mysql_real_escape_string($cid)."' ";
		$res = $this->ace->query($sql, 'Get User Confirmation');
		if( mysql_num_rows($res) == 1 ){
			$user = mysql_fetch_object($res);
			if( $user->status < 2 ){
				$sql = "UPDATE users SET status=1 WHERE user_id=".$user->user_id;
				$this->ace->query($sql, 'Set User Confirmed');
				$user->status = 1;
				return $user;
			}else{
				return -1;
			}
		}else{
			$this->errors[] = 'Invalid username or confirmation id.';
		}
		return 0;
	}

	function getusers($criteria = array(), $orderby = '', $orderdir = 'asc', $first = 0, $limit = 0){
		$wheres = array(" p.type_type=u.account_type  ");
		foreach( $criteria as $c=>$v ){
			switch( $c ){
				case 'id': settype($v, 'integer'); $wheres[] = "u.user_id=$v "; break;
				case 'username': $wheres[] = "u.username LIKE '".str_replace('*', '%',mysql_real_escape_string($v))."' "; break;
				case 'name': $wheres[] = "u.name LIKE '".str_replace('*', '%',mysql_real_escape_string($v))."' "; break;
				case 'password': $wheres[] = "u.pass='".mysql_real_escape_string($v)."' "; break;
				case 'email': $wheres[] = "u.email LIKE '".str_replace('*', '%',mysql_real_escape_string($v))."' "; break;
				case 'status': settype($v, 'integer'); $wheres[] = "u.status=$v "; break;
				case 'account': $wheres[] = "u.account_type='".mysql_real_escape_string($v)."' "; break;
				case 'joinedbefore': $wheres[] = "date_format(u.joined, '%Y-%m-%d')<='".mysql_real_escape_string($v)."' "; break;
				case 'joinedafter': $wheres[] = "date_format(u.joined, '%Y-%m-%d')>='".mysql_real_escape_string($v)."' "; break;
				case 'loggedinbefore': $wheres[] = "date_format(u.lastlogin, '%Y-%m-%d')<='".mysql_real_escape_string($v)."' "; break;
				case 'loggedinafter': $wheres[] = "date_format(u.lastlogin, '%Y-%m-%d')>='".mysql_real_escape_string($v)."' "; break;
				case 'bandwidth': case 'nobandwidth': $wheres[] = "p.bandwidth>0 "; break;
				case 'exceeding': settype($v, 'integer'); $wheres[] = "u.bandwidth_exceeded=$v "; break;
			}
		}
		$counting = isset($criteria['count']) ? $criteria['count'] : false;
		$sql = "SELECT u.*,p.*,  COUNT(i.image_id) AS images, (u.deleted_images_bandwidth+SUM(i.bandwidth))/(1024*1024) AS bandwidth_used, SUM(i.filesize)/(1024*1024) AS storage_used ";
		$sql .="FROM account_types p,  users u LEFT OUTER JOIN images i ON u.user_id=i.user_id ";
		$sql .="WHERE ".join(' AND ', $wheres)." ";
		$sql .="GROUP BY u.user_id ";
		if( isset($criteria['bandwidth']) ) $sql .= "HAVING (deleted_images_bandwidth+SUM(i.bandwidth))/(1024*1024)>p.bandwidth ";
		if( isset($criteria['nobandwidth']) ) $sql .= "HAVING (deleted_images_bandwidth+SUM(i.bandwidth))/(1024*1024)<=p.bandwidth ";
		if( $counting ){
			$res = $this->ace->query($sql, 'Count Users');
			return mysql_num_rows($res);
		}
		$ob = 'u.name';
		switch( $orderby ){
			case 'username':
			case 'email':
			case 'name':
			case 'status':
			case 'joined':
			case 'lastlogin':
				$ob = "u.$orderby";
				break;
			case 'images': $ob = 'images'; break;
			case 'type': $ob = 'u.account_type'; break;
			case 'bandwidth': $ob = 'bandwidth_used'; break;
			case 'storage': $ob = 'storage_used'; break;
		}
		$sql .="ORDER BY $ob $orderdir ";
		if( $limit > 0 ){
			settype($limit, 'integer');
			$first = max(0,(int)$first);
			$sql .= "LIMIT $first, $limit ";
		}
		$res = $this->ace->query($sql, 'Get Users'); 
		$ms = array();
		while($m = mysql_fetch_object($res) ){
			$m->bandwidth_used = number_format($m->bandwidth_used,2);
			$m->storage_used = number_format($m->storage_used,2);
			$ms[] = $m;
		}
		return $ms;
	}

	function updateusers($ids, $vars){
		$ids = $this->ace->getids($ids);
		$wheres = array();
		$newname = '';
		foreach( $vars as $n=>$v ){
			if( $n == 'username' ) $newname = $v;
			$wheres[] = "$n='".mysql_real_escape_string($v)."' ";
		}
		if( count($ids) && count($vars) ){

			if( count($ids) == 1 && $newname != ''){
				$user = $this->getuser(array('id'=>$ids[0]));
				if( $user && $user->username != $newname ){
					$user2 = $this->getuser(array('username'=>$newname));
					if( $user2 ){
						$this->errors[] = 'A user with the username "'.htmlspecialchars($newname).'" already exists.';
						return 0;
					}
					rename($this->ace->config->image_folder.$user->username, $this->ace->config->image_folder.$newname);
					rename($this->ace->config->thumb_folder.$user->username, $this->ace->config->thumb_folder.$newname);
				}
			}

			$sql = "UPDATE users SET ".join(', ',$wheres)." WHERE user_id IN (".join(',',$ids).") ";
			$this->ace->query($sql, 'Update Users');
			$this->canceluseroverbandwidth();
			return mysql_affected_rows();
		}
		return 0;
	}

	function deleteusers($ids){
		$deleted = 0;
		$ids = $this->ace->getids($ids);
		if( count($ids) > 0 ){
			$sql = "SELECT username FROM users WHERE user_id IN (".join(",",$ids).") ";
			$res = $this->ace->query($sql, 'Get Usernames');
			$usernames = array();
			while( list($uname) = mysql_fetch_row($res) ) $usernames[] = $uname;
			if( count($usernames) > 0 ){
				$sql = "SELECT i.name, i.type, u.username, i.thumb_type ";
				$sql .="FROM images i, users u ";
				$sql .="WHERE i.user_id=u.user_id AND i.user_id IN (".join(",",$ids).") ";
				$res = $this->ace->query($sql, 'Get Members IMages');
//				echo "Listing images to delete<br />";
				while( list($name,$type,$username, $thumb_type) = mysql_fetch_row($res) ){
					$fname = $this->ace->config->image_folder.$username.'/'.$name.'.'.$type;
//					echo "Attempt to delete $fname...";
					if( file_exists($fname) ){
						unlink($fname); //echo "$fname unlinked...";
					}else{
//						die("$fname does not exist...");
					}
					$tname = $this->ace->config->thumb_folder.$username.'/'.$name.'.'.$thumb_type;
//					echo "Attempt to delete $tname...";
					if( file_exists($tname) ){
						unlink($tname); //echo "$tname unlinked...";
					}else{
//						die("$tname does not exist...");
					}
				}


				foreach( $usernames as $uname ){
//					echo "Unlinking $uname folders...<br />";
					$dname = $this->ace->config->image_folder.$uname;
					if( !preg_match('/\./i', $dname) )
						@rmdir($dname);
					$dname = $this->ace->config->thumb_folder.$uname;
					if( !preg_match('/\./i', $dname) )
						@rmdir($dname);
				}

				$sql = "DELETE FROM images WHERE user_id IN (".join(",",$ids).") ";
				$this->ace->query($sql, 'Delete Images');
			}
			$sql = "DELETE FROM users WHERE user_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Delete users');
			$deleted = mysql_affected_rows();
		}
		return $deleted;
	}

	function upgradeusers($ids, $days){
		$ids = $this->ace->getids($ids);
		if( count($ids) > 0 ){
			settype($days, 'integer');
			if( $days > 0 ){
				$now = date("Y-m-d");
				$sql = "UPDATE users SET account_type='paid', bandwidth_exceeded=0, ";
				$sql .="paid_until=IF(paid_until>='$now',";
				$sql .="paid_until + INTERVAL $days DAY,";
				$sql .="now()+ INTERVAL $days DAY) ";
				$sql .="WHERE user_id IN (".join(",",$ids).") ";
				$res = $this->ace->query($sql, 'Upgrade Users');
				return mysql_affected_rows();
			}
		}
		return 0;
	}

	function upgradeuser($user, $months, $ipn){
		settype($months, 'integer');
		if( isset($user->user_id ) ){
			if( $months > 0 ){

				// if the user is currently free, use today as starting date
				// otherwise add the months onto the users current expiry date

				// update the user, set bandwidth_exceeded to 0, paid_until, etc.

				$dt = date('Y-m-d');

				if( $user->paid_until != '' && !preg_match('/^0000-00-00/i', $user->paid_until ) && $user->account_type == 'paid' ){
					$olddate = date('Y-m-d', strtotime($user->paid_until));
					if( $olddate > $dt ) $dt = $olddate;
				}

				$dtm = strtotime($dt);
				$y = date('Y', $dtm);
				$m = (int)date('m', $dtm);
				$d = (int)date('d', $dtm);

				$newdate = date('Y-m-d', mktime(0,0,0,$m+$months, $d, $y));

				$sql = "UPDATE users SET account_type='paid', ";
				$sql .="bandwidth_exceeded=0,bandwidth_reset=now(), deleted_images_bandwidth=0, ";
				$sql .="paid_until='$newdate' ";
				$sql .="WHERE user_id={$user->user_id} ";
				$res = $this->ace->query($sql, 'Upgrade User');

				$sql = "UPDATE {pa_dbprefix}images SET bandwidth=0 WHERE user_id={$user->user_id} ";
				$this->ace->query($sql, 'Reset Upgraded Users Image Bandwidth');

				if( $this->ace->config->payment_notifications ){
					$to = $user->email;
					$from = $this->ace->config->admin_email;
					$subject = $this->ace->config->payment_email_subject;
					$message = $this->ace->config->payment_email_message;
					$s = array('{username}', '{sitename}', '{siteurl}', '{amount}', 
											'{date}','{paypalemail}', '{expirydate}');
					$r = array($user->username, $this->ace->config->sitename, 
											$this->ace->config->siteurl, 
											'$'.number_format($ipn->data['mc_gross'], 2),
											date('l jS F Y'),$ipn->data['payer_email'], 
											date('jS F Y', strtotime($newdate)) );
					$message = str_replace($s, $r, $message);
					@mail($to, $subject, $message, "From: $from\r\nReply-To: $from\r\nErrors-To: $from\r\nReturn-Path: $from");
				}
				return 1;
			}
		}
		return 0;
	}

	function checkbandwidth(){
		if( date('Y-m-d')>$this->ace->config->bandwidth_checked ){
			$users = $this->getusers(array('bandwidth'=>1, 'exceeding'=>0));
			if( count($users) > 0 ){
				$ids = array();
				foreach( $users as $u ){
					$ids[] = $u->user_id;
				}
				$sql = "UPDATE {pa_dbprefix}users SET bandwidth_exceeded=1 WHERE user_id IN (".join(",",$ids).") ";
				$this->ace->query($sql, 'Set Bandwidth Exceeded');
				if( $this->ace->config->notify_bandwidth_exceeded ){
					$from = $this->ace->config->bandwidth_exceeded_from;
					$subject = $this->ace->config->bandwidth_exceeded_subject;
					$message = $this->ace->config->bandwidth_exceeded_message;
					$s = array('{sitename}', '{siteurl}','{name}', '{username}', '{bandwidth_used}', '{bandwidth_allowed}', '{date}', '{time}');
					$headers = "From: $from\r\nReply-To: $from\r\nErrors-To: $from\r\nReturn-Path: $from";
					foreach( $users as $u ){
						$to = $u->email;
						$r = array($this->ace->config->sitename, $this->ace->config->siteurl,$u->name, $u->username, $u->bandwidth_used, $u->bandwidth, date('l jS F Y'), date('H:i'));
						@mail($to, $subject, str_replace($s, $r, $message), $headers);
					}
				}
			}
			$sql = "UPDATE {pa_dbprefix}settings SET bandwidth_checked=now() ";
			$this->ace->query($sql, 'Update Bandwidth Checked');
		}
	}

	function canceluseroverbandwidth(){

		$users = $this->getusers(array('nobandwidth'=>1,'exceeding'=>1));
		$ids = array();
		foreach( $users as $u) $ids[] = $u->user_id;
		if( count($ids) > 0 ){
			$sql = "UPDATE {pa_dbprefix}users SET bandwidth_exceeded=0 ";
			$sql .="WHERE user_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Cancel Nonover bandwidth');
			return mysql_affected_rows();
		}
		return 0;
	}

	function resetbandwidth(){
		if( date('Y-m-d')>$this->ace->config->bandwidth_reset_checked ){
			$sql = "SELECT user_id FROM {pa_dbprefix}users WHERE TO_DAYS(bandwidth_reset)+30<TO_DAYS(now()) ";
			$res = $this->ace->query($sql, 'Get Users To Update');
			$ids = array();
			while( $u = mysql_fetch_object($res) ){
				$ids[] = $u->user_id;
			}
			if( count($ids) ){
				$sql = "UPDATE {pa_dbprefix}users SET deleted_images_bandwidth=0, bandwidth_exceeded=0, bandwidth_reset=now() ";
				$sql .="WHERE user_id IN (".join(",",$ids).") ";
				$this->ace->query($sql, 'Reset User Bandwidth');
				$sql = "UPDATE {pa_dbprefix}images SET bandwidth=0 WHERE user_id IN (".join(",",$ids).") ";
				$this->ace->query($sql, 'Reset Image Bandwidth');
			}
			$sql = "UPDATE {pa_dbprefix}settings SET bandwidth_reset_checked=now() ";
			$this->ace->query($sql, 'Update Bandwidth Checked');
		}
	}

    function createAnonymousAccount($username)
    {
        $name = $this->app->db->escape($username);
        $pass = $this->app->db->escape(md5(mt_rand(10000,9999999).time()));
        $email = '';
        $sql = "
            INSERT INTO users
                    (username, pass, email, name, status, joined, account_type)
            VALUES ('$name', '$pass', '$email', 'Anonymous', 1, now(), 'anonymous')
        ";
        $this->app->db->query($sql);
        $userid = $this->app->db->lastInsertId();
        if( $userid != 0 ){
            mkdir($this->app->config->image_folder.$username);
			chmod($this->app->config->image_folder.$username, 0777);
			mkdir($this->app->config->thumb_folder.$username);
			chmod($this->app->config->thumb_folder.$username, 0777);
        }
		return $userid;
    }
}

?>
