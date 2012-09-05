<?php
if( !defined('PIH' ) ) {
    header('HTTP/1.0 404 Not Found');
    exit();
}

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

class adrotator{

	var $ads = array();
	var $ad = 0;

	function adrotator(&$ace){
		$this->ace =& $ace;
	}

	function getads($group = '', $orderby = 'name', $orderdir = 'asc', $limit = 0, $live = -1){
		$sql = "SELECT * FROM {pa_dbprefix}ads ";
		$wheres = array();
		if( $group != '' ) $wheres[] = " groupname='".mysql_real_escape_string($group)."' ";
		if( $live != -1 ) $wheres[] = " live=$live ";
		if( count($wheres) ) $sql .= "WHERE ".join(" AND ", $wheres)." ";
		$sql .="ORDER BY $orderby $orderdir ";
		if( $limit > 0 ){
			$sql .= "LIMIT 0, ".(int)$limit." ";
		}
		$res = $this->ace->query($sql, 'Get Ads');
		$ads = array();
		while( $a = mysql_fetch_object($res) ) $ads[] = $a;
		return $ads;
	}

	function getad($id){
		$sql = "SELECT * FROM {pa_dbprefix}ads WHERE ad_id=$id ";
		$res = $this->ace->query($sql, 'Get Ad');
		return mysql_fetch_object($res);
	}

	function deleteads($ids){
		$ids = $this->ace->getids($ids);
		if( count($ids) == 0 )return 0;
		$sql = "DELETE FROM {pa_dbprefix}ads WHERE ad_id IN (".join(",",$ids).") ";
		$this->ace->query($sql, 'Delete Ads');
		return mysql_affected_rows();
	}

	function changestatus($ids, $live){
		$ids = $this->ace->getids($ids);
		if( count($ids) ){
			$sql = "UPDATE {pa_dbprefix}ads SET live=$live WHERE ad_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Change Status');
			return mysql_affected_rows();
		}
		return 0;
	}

	function preload($group = '',$n = 1){
		$this->ads = $this->getads($group,'views', 'asc', $n,1);
		$ids = array();
		foreach( $this->ads as $a ) $ids[] = $a->ad_id;
		if( count($ids) > 0 ){
			$sql = "UPDATE {pa_dbprefix}ads SET views=views+1 WHERE ad_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Log Views');
		}
		$this->ad = 0;
	}

	function display($return = false){
		if( count($this->ads) <= $this->ad ) $this->preload();
		if( count($this->ads) <= $this->ad ) return '';
		if( !$return ) echo $this->ads[$this->ad]->content;
		$this->ad++;
		return $this->ads[$this->ad-1]->content;
	}

	function resetviews($ids){
		$ids = $this->ace->getids($ids);
		if( count($ids) ){
			$sql = "UPDATE {pa_dbprefix}ads SET views=0 WHERE ad_id IN (".join(",",$ids).") ";
			$this->ace->query($sql, 'Reset Views');
			return mysql_affected_rows();
		}
		return 0;
	}

	function addad($name, $group, $content, $live = 0){
		$name = mysql_real_escape_string($name);
		$group = mysql_real_escape_string($group);
		$content = mysql_real_escape_string($content);
		$sql = "INSERT INTO {pa_dbprefix}ads (name, groupname, content, live) VALUES ('$name', '$group', '$content', $live) ";
		$res = $this->ace->query($sql, 'Insert Ad');
		return mysql_insert_id();
	}

	function updatead($id, $group, $content, $live){
		settype($live, 'integer');
		$group = mysql_real_escape_string($group);
		$content = mysql_real_escape_string($content);
		$sql = "UPDATE {pa_dbprefix}ads SET groupname='$group', content='$content', live=$live WHERE ad_id=$id ";
		$res = $this->ace->query($sql, 'Upate Ad');
	}

}

?>