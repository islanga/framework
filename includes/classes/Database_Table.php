<?php
class Database_Table {

  /**
   * Name of the table in the db schema relating to child class
   *
   * @var 	string
   * @access	protected
   */
  protected $_tbl = '';
  /**
   * Name of the primary key field in the table
   *
   * @var		string
   * @access	protected
   */
  protected $_tbl_key = '';
  /**
   * Database connector
   *
   * @var		Database
   * @access	protected
   */
  protected $_db = null;
  /**
   * Single Intance of  Table
   *
   * @var	Table
   * @access	private
   */
  private static $instance = null;

  /**
   * Object constructor to set table and key field
   *
   * Can be overloaded/supplemented by the child class
   *
   * @access protected
   * @param string $table name of the table in the db schema relating to child class
   * @param string $key name of the primary key field in the table
   * @param object $db JDatabase object
   */
  protected function __construct() {
    
  }

  public static function Singleton() {
    if (self::$instance == null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getDBO() {
    return $this->_db;
  }

  /**
   * Set the internal database object
   *
   * @param	object	$db	A Database based object
   * @return	void
   */
  public function setDBO($db) {
    $this->_db = $db;
  }

  /**
   * Gets the internal table name for the object
   *
   * @return string
   */
  public function getTableName() {
    return $this->_tbl;
  }

  /**
   * Gets the internal primary key name
   *
   * @return string
   */
  public function getKeyName() {
    return $this->_tbl_key;
  }

  /**
   * Resets the default properties
   * @return	void
   */
  public function reset() {
    $k = $this->_tbl_key;
    foreach ($this->getProperties() as $name => $value) {
      if ($name != $k) {
        $this->$name = $value;
      }
    }
  }

  /**
   * Binds a named array/hash to this object
   *
   * Can be overloaded/supplemented by the child class
   *
   * @access	public
   * @param	$from	mixed	An associative array or object
   * @param	$ignore	mixed	An array or space separated list of fields not to bind
   * @return	boolean
   */
  public function bind($from, $ignore=array()) {
    $fromArray = is_array($from);
    $fromObject = is_object($from);

    if (!$fromArray && !$fromObject) {
      $this->setError(get_class($this) . '::bind failed. Invalid from argument');
      return false;
    }
    if (!is_array($ignore)) {
      $ignore = explode(' ', $ignore);
    }
    foreach ($this->getProperties() as $k => $v) {
      // internal attributes of an object are ignored
      if (!in_array($k, $ignore)) {
        if ($fromArray && isset($from[$k])) {
          $this->$k = $from[$k];
        } else if ($fromObject && isset($from->$k)) {
          $this->$k = $from->$k;
        }
      }
    }
    return true;
  }

  /**
   * Loads a row from the database and binds the fields to the object properties
   *
   * @access	public
   * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
   * @return	boolean	True if successful
   */
  public function load($oid=null) {
    $k = $this->_tbl_key;

    if ($oid !== null) {
      $this->$k = $oid;
    }

    $oid = $this->$k;

    if ($oid === null) {
      return false;
    }
    $this->reset();

    $db = $this->getDBO();

    $query = 'SELECT *'
      . ' FROM ' . $db->nameQuote($this->_tbl)
      . ' WHERE ' . $this->_tbl_key . ' = ' . $db->Quote($oid);

    $db->setQuery($query);
    if ($result = $db->loadAssoc()) {
      return $this->bind($result);
    } else {
        throw new Exception($db->getErrorMsg());
      return false;
    }
  }

  /**
   * Inserts a new row if id is zero or updates an existing row in the database table
   *
   * Can be overloaded/supplemented by the child class
   *
   * @access public
   * @param boolean If false, null object variables are not updated
   * @return null|string null if successful otherwise returns and error message
   */
  public function store($updateNulls=false) {

    $k = $this->_tbl_key;

    if ($this->$k) {
      $ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
    } else {
      $ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
    }
    if (!$ret) {

      return false;
    } else {
      return true;
    }
  }

  /**
   * Default delete method
   *
   * can be overloaded/supplemented by the child class
   *
   * @access public
   * @return true if successful otherwise returns and error message
   */
  public function delete($oid=null) {

    $k = $this->_tbl_key;
    if ($oid) {
      $this->$k = intval($oid);
    }

    $query = 'DELETE FROM ' . $this->_db->nameQuote($this->_tbl) .
      ' WHERE ' . $this->_db->nameQuote($this->_tbl_key) . ' = ' . $this->_db->Quote($this->$k);
    $this->_db->setQuery($query);

    if ($this->_db->query()) {
      return true;
    } else {
      $this->setError($this->_db->getErrorMsg());
      return false;
    }
  }

  /**
   * Generic save function
   *
   * @access	public
   * @param	array	Source array for binding to class vars
   * @param	string	Filter for the order updating
   * @param	mixed	An array or space separated list of fields not to bind
   * @returns TRUE if completely successful, FALSE if partially or not succesful.
   */
  public function save($source, $order_filter='', $ignore='') {
    if (!$this->bind($source, $ignore)) {
      return false;
    }

    if (!$this->store()) {
      return false;
    }


    return true;
  }

  /**
   * Export item list to xml
   *
   * @access public
   * @param boolean Map foreign keys to text values
   */
  function toXML($mapKeysToText=false) {
    $xml = '<record table="' . $this->_tbl . '"';

    if ($mapKeysToText) {
      $xml .= ' mapkeystotext="true"';
    }
    $xml .= '>';
    foreach (get_object_vars($this) as $k => $v) {
      if (is_array($v) or is_object($v) or $v === NULL) {
        continue;
      }
      if ($k[0] == '_') { // internal field
        continue;
      }
      $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
    }
    $xml .= '</record>';

    return $xml;
  }

  /**
   * Returns an associative array of object properties
   *
   * @access    public
   * @param    boolean $public If true, returns only the public properties
   * @return    array
   * @see        get()
   */
  public function getProperties($public = true) {
    $vars = get_object_vars($this);
    if ($public) {
      foreach ($vars as $key => $value) {
        if ('_' == substr($key, 0, 1)) {
          unset($vars[$key]);
        }
      }
    }
    return $vars;
  }

  /**
   *
   * @param struct $where
   * @param string $order
   * @param int $limit
   * @param int $offset
   * @return struct
   */
  public function fetchAll($where=array(), $order='', $limit=0, $offset=0) { 
    $db = $this->getDBO();
    $query = " SELECT * FROM  ". $db->nameQuote($this->_tbl);

    if ($where) {
      $query .= " WHERE 1";
      foreach ($where as $key => $val) {
        $key =  $db->nameQuote($key);
        $val =  $db->isQuoted($key) ? $db->Quote($val) : (int) $val;
        $query .= " AND $key  = $val";
      }
    }
    if ($order != '') {
      $query .= " ORDER BY  " . $order . "";
    }
    if ($limit) {
      $query .= " LIMIT " . $limit;
      if ($offset) {
        $query .= $offset;
      }
    }

    $db->setQuery($query);
    return $db->loadAssocList();
  }

  /**
   *
   * @param array $field
   * @param string $order
   * @param int $limit
   * @param int $offset
   * @return struct
   */
  public function fetch(Array $field, $where=array(), $order='', $limit=0, $offset=0) {
    $db = $this->getDBO();
    $query = " SELECT `%s` FROM  ".$db->nameQuote($this->_tbl);
    $query = sprintf($query, implode("`,`", $field));
    if ($where) {
      $query .= " WHERE 1";
      foreach ($where as $key => $val) {
        $key = $db->nameQuote($key);
        $val = $db->isQuoted($key) ? $db->Quote($val) : (int) $val;
        $query .= " AND $key  = $val";
      }
    }
    if ($order != '') {
      $query .= " ORDER BY '" . $order . "'";
    }
    if ($limit) {
      $query .= " LIMIT " . $limit;
      if ($offset) {
        $query .= $offset;
      }
    }
    $db->setQuery($query);
    return $db->loadAssocList();
  }
}