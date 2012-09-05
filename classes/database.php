<?php
if( !defined('PIH' ) ) {
    header('HTTP/1.0 404 Not Found');
    exit();
}
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
class database
{

	var $tablePrefix = '';
    var $tablePrefixPlaceholder = '{pa_dbprefix}';
    var $lastError = '';
    var $settings = array(
        'host' => 'localhost',
        'name' => '',
        'password' => '',
        'user' => ''
    );
    public $queries = array();

	function __construct($settings)
	{
		$this->connection = isset($settings['connection']) ? $settings['connection'] : null;
        $this->tablePrefix = isset($settings['prefix']) ? $settings['prefix'] : '';
        $this->settings = $settings;
	}

	function database($settings)
	{
		$this->__construct($settings);
	}

	function setConnection($conn)
	{
		$this->connection = $conn;
	}

    function connect()
    {
        if( !$this->connection ) {
            $this->connection = @mysql_connect(
                                    $this->settings['host'],
                                    $this->settings['user'],
                                    $this->settings['password']
                                 );
            if( $this->connection ) {
                if( !mysql_select_db($this->settings['name']) ){
                    $this->lastError = 'Could not select database';
                }
            }
            else {
                $this->lastError = mysql_error();
            }
        }
        return $this->connection;
    }

	function query($sql, $what = '', $die_on_error = true)
	{
        $this->connect();
        $sql = str_replace($this->tablePrefixPlaceholder, $this->tablePrefix, $sql);
		$result = @mysql_query($sql, $this->connection);
        $this->queries[] = $sql;
		if( !$result && $die_on_error){
			die('Database Error: '.$what.'<br />'.mysql_error().'<br /><br />'.nl2br($sql));
		}
		return $result;
	}

	function fetchObject($sql, $what = '')
	{
		$result = $this->query($sql, $what);
		$object = null;
		if( mysql_num_rows($result) > 0 ){
			$object = mysql_fetch_object($result);
		}
		return $object;
	}

	function fetchObjects($sql, $what = '')
	{
		$result = $this->query($sql, $what);
		$objects = array();
		while( $obj = mysql_fetch_object($result) ){
			$objects[] = $obj;
		}
		return $objects;
	}

	function fetchRow($sql, $what = '')
	{
		$result = $this->query($sql, $what);
		$row = null;
		if( mysql_num_rows($result) > 0 ){
			$row = mysql_fetch_assoc($result);
		}
		return $row;	
	}

	function fetchRows($sql, $what = '')
	{
		$result = $this->query($sql, $what);
		$rows = array();
		while( $row = mysql_fetch_assoc($result) ){
			$rows[] = $row;
		}
		return $rows;
	}

	function fetchCols($sql, $what = '')
	{
		$result = $this->query($sql, $what);
		$cols = array();
		while( $row = mysql_fetch_array($result) ){
			$cols[] = $row[0];
		}
		return $cols;
	}

	function fetchField($sql, $field = 0, $what = '')
	{
		$result = $this->query($sql, $what);
		if( mysql_num_rows($result) > 0 ){
			$row = mysql_fetch_array($res);
			return $row[$field];
		}
	}

	function escape($what)
	{
        $this->connect();
		return mysql_real_escape_string($what, $this->connection);
	}

	function affectedRows()
	{
        $this->connect();
		return mysql_affected_rows($this->connection);
	}

	function lastInsertId()
	{
        $this->connect();
		return mysql_insert_id($this->connection);
	}

    function getTableSchema($table_name)
    {
		$sql = "SHOW COLUMNS FROM {$this->tablePrefix}{$table_name}";
		$cols = $this->fetchObjects($sql, 'Get Table Info');
        $table = array('fields' => array(), 'pk' => '', 'keys' => array());
		foreach( $cols as $c ){
            $col = array(
                'field' => $c->Field,
                'type' => $c->Type,
                'null' => $c->Null == 'NO' ? false : true,
                'default' => $c->Default,
                'extra' => $c->Extra
            );
			$table['fields'][$col['field']] = $col;
			if( strtolower($c->Key) == 'pri' ){
				$table['pk'] = $c->Field;
			}
		}
        $keys = $this->fetchRows("SHOW INDEX FROM {$this->tablePrefix}{$table_name}");
        foreach( $keys as $key ) {
            if( !isset($table['keys'][$key['Key_name']])) {
                $table['keys'][$key['Key_name']] = array();
            }
            $table['keys'][$key['Key_name']][$key['Seq_in_index']-1] = $key['Column_name'];
        }
        return $table;
    }

    function getSchema()
    {
        $schema = array();
        $sql = "SHOW TABLES";
        $tables = $this->fetchCols($sql);
        foreach( $tables as $table_name ) {
            $schema[$table_name] = $this->getTableSchema($table_name);
        }
        return $schema;
    }

    function createTableSQL($table_name, $defn)
    {
        $sqls = array();
        foreach( $defn['fields'] as $name => $def ) {
            $sqls[] = $this->fieldDefnToSQL($name, $def);
        }
        if( $defn['pk'] != '' ) {
            $sqls[] = "PRIMARY KEY({$defn['pk']})";
        }
        foreach( $defn['keys'] as $name => $fields ) {
			if( $name != 'PRIMARY'){
				$sqls[] = "INDEX $name(".join(', ', $fields).")";
			}
        }
        $table_name = $this->tablePrefix . $table_name;
        $sql = "CREATE TABLE {$table_name} (\n" . join(",\n", $sqls).")";
//		echo "<hr>$sql<hr>";print_r($defn);
        return $sql;
    }

    function fieldDefnToSQL($name, $def)
    {
        $field = $name . ' ' .$def['type'] . ($def['null'] ? ' NULL ' : ' NOT NULL ') . ' ' . $def['extra'];
        if( $def['default'] ) {
            $field .= " DEFAULT '".$this->escape($def['default'])."' ";
        }
        return $field;
    }

    function keyDefnToSQL($name, $def)
    {
        $key = "$name(".join(',',$def).")";
        return $key;
    }

    function alterTableSQL($table_name, $new_fields, $new_keys = array())
    {
        $sql = '';
        $fields = array();
        foreach( $new_fields as $name => $def ) {
            $fields[] = $this->fieldDefnToSQL($name, $def);
        }
        $keys = array();
        foreach( $new_keys as $name => $def ) {
            $keys[] = $this->keyDefnToSQL($name, $def);
        }
        $table = $this->tablePrefix.$table_name;
        $alters = array();
        if( count($fields) ) {
            $alters[] = "ALTER TABLE $table ADD COLUMN (" . join(",\n", $fields).")";
        }
        if( count($keys)) {
            foreach( $keys as $key ) {
                $alters[] = "ALTER TABLE $table ADD INDEX $key;";
            }
        }
        return $alters;
    }

    function getSchemaVersion($schema = null)
    {
        $version = '1.3.4';
        if( !$schema ) {
            $sql = "SELECT dbversion FROM settings";
            $res = $this->query($sql, '', false);
            if( $res ) {
                $version = mysql_result($res,0,0);
            }
        }
        else if( isset($schema['settings']['fields']['dbversion'])) {
            $version = $schema['settings']['fields']['dbversion']['default'];
        }
        return $version;
    }

    function getUpgradeSQL($newSchema)
    {
        $sql = array();
        $schema = $this->getSchema();
        foreach( $newSchema as $table_name => $defn ) {
            if( !isset($schema[$table_name])) {
                $sql['Create Table "'.$table_name.'"'] = $this->createTableSQL($table_name, $defn);
            }
            else {
                $new_fields = array();
                $new_keys = array();
                foreach( $defn['fields'] as $field_name => $def ) {
                    if( !isset($schema[$table_name]['fields'][$field_name]) ) {
                        $new_fields[$field_name] = $def;
                    }
                }
                foreach( $defn['keys'] as $key_name => $def ) {
                    if( !isset($schema[$table_name]['keys'][$key_name]) ) {
                        $new_keys[$key_name] = $def;
                    }
                }
                $alters = $this->alterTableSQL($table_name, $new_fields, $new_keys);
                if( count($alters) > 0 ) {
                    $sql['Alter Table "'.$table_name.'"'] = $alters;
                }
            }
        }
        if( count($sql) ) {
            $sql['Update Version'] = "UPDATE settings SET dbversion = '".$this->escape($newSchema['settings']['fields']['dbversion']['default'])."'";
        }
        return $sql;
    }

    function setupSchema($newSchema)
    {
        $schema = $this->getSchema();
        $sql = $this->getUpgradeSQL($newSchema);
        if( count($sql) > 0 ) {
            foreach( $sql as $name => $statements ) {
                if( !is_array($statements)) {
                    $statements = array($statements);
                }
                foreach( $statements as $st ) { //echo "E: $st<br />";
                    $this->query($st);
                }
            }
        }
        return true;
    }

    public function backup($dir)
    {
        $tables = $this->fetchCols("SHOW TABLES");
        $files = array();
        foreach( $tables as $table ) {
            $filename = $dir . DIRECTORY_SEPARATOR . $table.'.sql';
            $sql = "SELECT * INTO OUTFILE '".$this->escape($filename)."' FROM $table";
            $this->query($sql);
            $files[] = $filename;
        }
        return $files;
    }

}

