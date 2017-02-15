<?php

require CORE_INCLUDE_PATH . DS . 'classes' . DS . 'Abstract.php';
class MySQL extends Database_Abstract
{
    public $name = 'mysql';
    protected $_nullDate = '0000-00-00 00:00:00';
    protected $_nameQuote = '`';
	
    public function __construct($options) 
	{
        $host = array_key_exists('host', $options) ? $options['host'] : 'localhost';
        $user = array_key_exists('user', $options) ? $options['user'] : '';
        $password = array_key_exists('password', $options) ? $options['password'] : '';
        $database = array_key_exists('database', $options) ? $options['database'] : '';
        $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        $select = array_key_exists('select', $options) ? $options['select'] : true;

        // perform a number of fatality checks, then return gracefully
        if (!function_exists('mysql_connect')) 
		{  
            $this->_errorNum = 1;
            $this->_errorMsg = 'The MySQL adapter "mysql" is not available.';
            return;
        }

        // connect to the server
        if (!($this->_resource = @mysql_connect($host, $user, $password, true))) 
		{
            $this->_errorNum = 2;
            $this->_errorMsg = 'Could not connect to MySQL';
            return;
        }

        // finalize initialization
        parent::__construct($options);

        // select the database
        if ($select) 
		{
            $this->select($database);
        }
    }
    public function select($database) 
	{
        if (!$database) 
		{
            return false;
        }

        if (!mysql_select_db($database, $this->_resource)) 
		{
            $this->_errorNum = 3;
            $this->_errorMsg = 'Could not connect to database';
            return false;
        }

        // if running mysql 5, set sql-mode to mysql40 - thereby circumventing strict mode problems
        if (strpos($this->getVersion(), '5') === 0) 
		{
            $this->setQuery("SET sql_mode = 'MYSQL40'");
            $this->query();
        }

        return true;
    }
    public function query() 
	{
        if (!is_resource($this->_resource)) 
		{
            return false;
        }

        // Take a local copy so that we don't modify the original query and cause issues later
        $sql = $this->_sql;
        if ($this->_limit > 0 || $this->_offset > 0) 
		{
            $sql .= ' LIMIT ' . max($this->_offset, 0) . ', ' . max($this->_limit, 0);
        }
        if ($this->_debug) 
		{
            $this->_ticker++;
            $this->_log[] = $sql;
        }
        $this->_errorNum = 0;
        $this->_errorMsg = '';
        $this->_cursor = mysql_query($sql, $this->_resource);

        if (!$this->_cursor) 
		{
            $this->_errorNum = mysql_errno($this->_resource);
            $this->_errorMsg = mysql_error($this->_resource) . " SQL=$sql";

            if ($this->_debug) 
			{
              throw new Exception(" $this->_errorNum . ' - ' . $this->_errorMsg",500);
            }
            return false;
        }
        return $this->_cursor;
    }
    public function getEscaped($text, $extra = false) 
	{
        $result = mysql_real_escape_string($text, $this->_resource);
        if ($extra) 
		{
            $result = addcslashes($result, '%_');
        }
        return $result;
    }
    public function setUTF() 
	{
        mysql_query("SET NAMES 'utf8'", $this->_resource);
    }
    public function hasUTF() 
	{
        $verParts = explode('.', $this->getVersion());
        return ($verParts[0] == 5 || ($verParts[0] == 4 && $verParts[1] == 1 && (int) $verParts[2] >= 2));
    }
    public function getAffectedRows() 
	{
        return mysql_affected_rows($this->_resource);
    }
    public function explain() 
	{
        $temp = $this->_sql;
        $this->_sql = "EXPLAIN $this->_sql";

        if (!($cur = $this->query())) 
		{
            return null;
        }
        $first = true;

        $buffer = '<table id="explain-sql">';
        $buffer .= '<thead><tr><td colspan="99">' . $this->getQuery() . '</td></tr>';
        while ($row = mysql_fetch_assoc($cur)) 
		{
            if ($first) 
			{
                $buffer .= '<tr>';
                foreach ($row as $k => $v) 
				{
                    $buffer .= '<th>' . $k . '</th>';
                }
                $buffer .= '</tr>';
                $first = false;
            }
            $buffer .= '</thead><tbody><tr>';
            foreach ($row as $k => $v) 
			{
                $buffer .= '<td>' . $v . '</td>';
            }
            $buffer .= '</tr>';
        }
        $buffer .= '</tbody></table>';
        mysql_free_result($cur);

        $this->_sql = $temp;

        return $buffer;
    }
    public function queryBatch($abort_on_error=true, $p_transaction_safe = false) 
	{
        $this->_errorNum = 0;
        $this->_errorMsg = '';
        if ($p_transaction_safe) 
		{
            $this->_sql = rtrim($this->_sql, "; \t\r\n\0");
            $si = $this->getVersion();
            preg_match_all("/(\d+)\.(\d+)\.(\d+)/i", $si, $m);
            if ($m[1] >= 4) 
			{
                $this->_sql = 'START TRANSACTION;' . $this->_sql . '; COMMIT;';
            } 
			else if ($m[2] >= 23 && $m[3] >= 19) 
			{
                $this->_sql = 'BEGIN WORK;' . $this->_sql . '; COMMIT;';
            } 
			else if ($m[2] >= 23 && $m[3] >= 17) 
			{
                $this->_sql = 'BEGIN;' . $this->_sql . '; COMMIT;';
            }
        }
        $query_split = $this->splitSql($this->_sql);
        $error = 0;
        foreach ($query_split as $command_line) 
		{
            $command_line = trim($command_line);
            if ($command_line != '') 
			{
                $this->_cursor = mysql_query($command_line, $this->_resource);
                if ($this->_debug) 
				{
                    $this->_ticker++;
                    $this->_log[] = $command_line;
                }
                if (!$this->_cursor) 
				{
                    $error = 1;
                    $this->_errorNum .= mysql_errno($this->_resource) . ' ';
                    $this->_errorMsg .= mysql_error($this->_resource) . " SQL=$command_line <br />";
                    if ($abort_on_error) 
					{
                        return $this->_cursor;
                    }
                }
            }
        }
        return $error ? false : true;
    }
    public function getNumRows($cur=null) 
	{
        return mysql_num_rows($cur ? $cur : $this->_cursor);
    }
    public function loadResult() 
	{
        if (!($cur = $this->query())) 
		{
            return null;
        }
        $ret = null;
        if ($row = mysql_fetch_row($cur)) 
		{
            $ret = $row[0];
        }
        mysql_free_result($cur);
        return $ret;
    }
    public function loadResultArray($numinarray = 0) 
	{
        if (!($cur = $this->query())) 
		{
            return null;
        }
        $array = array();
        while ($row = mysql_fetch_row($cur)) 
		{
            $array[] = $row[$numinarray];
        }
        mysql_free_result($cur);
        return $array;
    }
    public function loadAssoc() 
	{
        if (!($cur = $this->query())) 
		{
            return null;
        }
        $ret = null;
        if ($array = mysql_fetch_assoc($cur)) 
		{
            $ret = $array;
        }
        mysql_free_result($cur);
        return $ret;
    }
    public function loadAssocList($key='') 
	{
        if (!($cur = $this->query())) 
		{
            return null;
        }
        $array = array();
        while ($row = mysql_fetch_assoc($cur)) 
		{
            if ($key) 
			{
                $array[$row[$key]] = $row;
            } 
			else 
			{
                $array[] = $row;
            }
        }
        mysql_free_result($cur);
        return $array;
    }
    public function loadObject() 
	{
        if (!($cur = $this->query())) 
		{
            return null;
        }
        $ret = null;
        if ($object = mysql_fetch_object($cur)) 
		{
            $ret = $object;
        }
        mysql_free_result($cur);
        return $ret;
    }
    public function loadObjectList($key='') 
	{
        if (!($cur = $this->query())) 
		{
            return null;
        }
        $array = array();
        while ($row = mysql_fetch_object($cur)) 
		{
            if ($key) 
			{
                $array[$row->$key] = $row;
            } 
			else 
			{
                $array[] = $row;
            }
        }
        mysql_free_result($cur);
        return $array;
    }

    public function loadRow() 
	{
        if (!($cur = $this->query()))
		{
            return null;
        }
        $ret = null;
        if ($row = mysql_fetch_row($cur)) 
		{
            $ret = $row;
        }
        mysql_free_result($cur);
        return $ret;
    }
    public function loadRowList($key=null) 
	{
        if (!($cur = $this->query())) 
		{
            return null;
        }
        $array = array();
        while ($row = mysql_fetch_row($cur)) 
		{
            if ($key !== null) 
			{
                $array[$row[$key]] = $row;
            } 
			else 
			{
                $array[] = $row;
            }
        }
        mysql_free_result($cur);
        return $array;
    }
    public function insertObject($table, &$object, $keyName = NULL) 
	{
        $fmtsql = 'INSERT INTO ' . $this->nameQuote($table) . ' ( %s ) VALUES ( %s ) ';
        $fields = array();
        foreach (get_object_vars($object) as $k => $v) 
		{
            if (is_array($v) or is_object($v) or $v === NULL) 
			{
                continue;
            }
            if ($k[0] == '_') 
			{ // internal field
                continue;
            }
            $fields[] = $this->nameQuote($k);
            $values[] = $this->isQuoted($k) ? $this->Quote($v) : (int) $v;
        }
        $this->setQuery(sprintf($fmtsql, implode(",", $fields), implode(",", $values)));
       
        if (!$this->query()) 
		{
            return false;
        }
        $id = $this->insertid();
        if ($keyName && $id) 
		{
            $object->$keyName = $id;
        }
        return true;
    }
    public function updateObject($table, &$object, $keyName, $updateNulls=true) 
	{
        $fmtsql = 'UPDATE ' . $this->nameQuote($table) . ' SET %s WHERE %s';
        $tmp = array();
        foreach (get_object_vars($object) as $k => $v) 
		{
            if (is_array($v) or is_object($v) or $k[0] == '_') 
			{ // internal or NA field
                continue;
            }
            if ($k == $keyName) 
			{ // PK not to be updated
                $where = $keyName . '=' . $this->Quote($v);
                continue;
            }
            if ($v === null) 
			{
                if ($updateNulls) 
				{
                    $val = 'NULL';
                } 
				else 
				{
                    continue;
                }
            } 
			else 
			{
                $val = $this->isQuoted($k) ? $this->Quote($v) : (int) $v;
            }
            $tmp[] = $this->nameQuote($k) . '=' . $val;
        }
        $this->setQuery(sprintf($fmtsql, implode(",", $tmp), $where));
        return $this->query();
    }
    public function insertid() 
	{
        return mysql_insert_id($this->_resource);
    }
    public function getVersion() 
	{
        return mysql_get_server_info($this->_resource);
    }
    public function getTableList() 
	{
        $this->setQuery('SHOW TABLES');
        return $this->loadResultArray();
    }
    public function getTableCreate($tables) 
	{
        settype($tables, 'array'); //force to array
        $result = array();

        foreach ($tables as $tblval) 
		{
            $this->setQuery('SHOW CREATE table ' . $this->getEscaped($tblval));
            $rows = $this->loadRowList();
            foreach ($rows as $row) 
			{
                $result[$tblval] = $row[1];
            }
        }

        return $result;
    }
    public function getTableFields($tables, $typeonly = true)
	{
        settype($tables, 'array'); //force to array
        $result = array();

        foreach ($tables as $tblval) 
		{
            $this->setQuery('SHOW FIELDS FROM ' . $tblval);
            $fields = $this->loadObjectList();

            if ($typeonly) 
			{
                foreach ($fields as $field) 
				{
                    $result[$tblval][$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
                }
            } 
			else 
			{
                foreach ($fields as $field) 
				{
                    $result[$tblval][$field->Field] = $field;
                }
            }
        }

        return $result;
    }
}