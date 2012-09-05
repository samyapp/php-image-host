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

class news{

    public $app = null;

	public function __construct($app)
    {
		$this->app = $app;
	}

    public function getNews($options, $limit = 0)
    {
        $wheres = array();
        foreach( $options as $name => $value ) {
            switch( $name ) {
                case 'status':
                    if( !is_array($value) ) {
                        $value = array($value);
                    }
                    $escaped = array();
                    foreach( $value as $v  ) {
                        $escaped[] = "'".$this->app->db->escape($v)."'";
                    }
                    $wheres[] = " n.status IN (".join(',',$escaped).") ";
                    break;
                case 'published':
                    $wheres[] = "n.published <= now() ";
                    break;
                case 'id':
                    $ids = array();
                    if( is_array($value) ) {
                        foreach( $value as $id ) {
                            $ids[] = (int)$id;
                        }
                    }
                    else {
                        $ids[] = (int)$value;
                    }
                    $wheres[] = "n.news_id IN (".join(',',$ids).")";
                    break;
            }
        }
        $where = '';
        if( count($wheres) ) {
            $where = " WHERE ".join(' AND ', $wheres);
        }
        $lim = '';
        if( $limit > 0 ) {
            $lim = " LIMIT 0, ".(int)$limit;
        }
        $sql = "
            SELECT n.* FROM news n
            $where
            ORDER BY published DESC, news_id DESC
            $lim
        ";
        return $this->app->db->fetchObjects($sql, 'Get News');
    }

    public function addNews($headline, $summary = '', $details = '', $status = 'unpublished', $published = null)
    {
        $headline = $this->app->db->escape($headline);
        $summary = $this->app->db->escape($summary);
        $details = $this->app->db->escape($details);
        if( !in_array($status, array('unpublished', 'published', 'archived', 'hidden'))) {
            $status = 'unpublished';
        }
        $status = $this->app->db->escape($status);
        if( !$published || !preg_match('#^2[0-9]{3}-[0-9]{2}-[0-9]{2}#', $published ) ) {
            $published = date('Y-m-d');
        }
        $published = $this->app->db->escape($published);
        $sql = "
            INSERT INTO news (headline, summary, details, status, published)
                    VALUES ('$headline', '$summary', '$details', '$status', '$published')
        ";
        $this->app->db->query($sql, 'Add news');
        return $this->app->db->lastInsertId();
    }

    public function deleteNews($ids)
    {
        if( !is_array($ids) ) {
            $ids = array($ids);
        }
        $ins = array();
        foreach( $ids as $i ) {
            $ins[] = (int)$i;
        }
        if( count($ins) > 0 ) {
            $sql = "DELETE FROM news WHERE news_id IN (".join(',',$ins).")";
            $this->app->db->query($sql, 'Delete News');
            return $this->app->db->affectedRows();
        }
        return 0;
    }

    public function updateNews($news_id, $updates = array())
    {
        $ups = array();
        settype($news_id, 'integer');
        foreach( $updates as $field => $value ) {
            switch( $field ) {
                case 'headline':
                case 'summary':
                case 'details':
                case 'status':
                    $ups[] = "$field = '".$this->app->db->escape($value)."' ";
                    break;
                case 'published':
                    if( preg_match('#^2[0-9]{3}-[0-9]{2}-[0-9]{2}$#', $value) ) {
                        $ups[] = "published = '".$value."'";
                    }
                    break;
            }
        }
        if( count($ups) ) {
            $sql = "UPDATE news SET ".join(",", $ups)." WHERE news_id = $news_id ";
            $this->app->db->query($sql, 'Update News');
            return $this->app->db->affectedRows();
        }
        return 0;
    }

    public function getNewsMonths($published = true)
    {
        $sql = "
            SELECT date_format(published, '%Y-%m') AS year_month,
                    date_format(published, '%Y') AS year,
                    date_format(published, '%M') AS month,
                    COUNT(news_id)
                    FROM news
                    WHERE published <= now()
                    GROUP BY date_format(published, '%Y-%m')
                    ORDER BY published DESC
        ";
        if( $published ) {
            $sql .= " AND status IN ('published', 'archived') ";
        }
        return $this->app->db->fetchObjects($sql, 'Get News Months');
    }

    public function getCurrentNews($limit = 0)
    {
        return $this->getNews(array('published' => true, 'status' => 'published'), $limit);
    }

    public function getPublicNews($limit = 0)
    {
        return $this->getNews(array('published'=>true, 'status' => array('published', 'archived')), $limit);
    }

    public function getArchivedNews($limit = 0)
    {
        return $this->getNews(array('published'=>true, 'status' => array('archived')), $limit);
    }

    public function getNewsById($id, $public = false)
    {
        $options = array('id'=>$id);
        if( $public ) {
            $options['published'] = true;
            $options['status'] = array('published', 'archived');
        }
        $results = $this->getNews($options);
        if( count($results) > 0 ){
            return $results[0];
        }
        return null;
    }
}

?>