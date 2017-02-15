<?php
class Me_Session_Domain {

  //put your code here
  protected $_domain = "Default";
  protected static $_expiringData = array();
  public $session_object;

  public function __construct($domain = 'Default') {
    if ($domain === '') {
      /**
       * @see Me_Session_Exception
       */
      require_once 'Me/Session/Exception.php';
      throw new Me_Session_Exception('Session domain must be a non-empty string.');
    }
    if ($domain[0] == "_") {
      /**
       * @see Me_Session_Exception
       */
      require_once 'Me/Session/Exception.php';
      throw new Me_Session_Exception('Session domain must not start with an underscore.');
    }

    if (preg_match('#(^[0-9])#i', $domain[0])) {
      /**
       * @see Me_Session_Exception
       */
      require_once 'Me/Session/Exception.php';
      throw new Me_Session_Exception('Session domain must not start with a number.');
    }
    $this->_domain = $domain;

    require_once "Me/Session.php";
    $this->session_object  = Me_Session::factory();
  }

  public function & __get($name) {
    if ($name === '') {
      /**
       * @see Me_Session_Exception
       */
      require_once 'Me/Session/Exception.php';
      throw new Me_Session_Exception("The '$name' key must be a non-empty string");
    }
    if ($name === null) {
      if (isset($_SESSION[$this->_domain])) { // check session first for data requested
        return $_SESSION[$this->_domain];
      } elseif (isset(self::$_expiringData[$this->_domain])) { // check expiring data for data reqeusted
        return self::$_expiringData[$this->_domain];
      } else {
        return $_SESSION[$this->_domain]; // satisfy return by reference
      }
    } else {
      if (isset($_SESSION[$this->_domain][$name])) { // check session first
        return $_SESSION[$this->_domain][$name];
      } elseif (isset(self::$_expiringData[$this->_domain][$name])) { // check expiring data
        return self::$_expiringData[$this->_domain][$name];
      } else {
        return $_SESSION[$this->_domain][$name]; // satisfy return by reference
      }
    }
  }

  public function __set($name, $value) {
    $_SESSION[$this->_domain][$name] = $value;
  }

  public function __isset($name) {
    if ($name === '') {
      /**
       * @see Me_Session_Exception
       */
      require_once 'Me/Session/Exception.php';
      throw new ME_Session_Exception("The '$name' key must be a non-empty string");
    }

    if ($name === null) {
      return ( isset($_SESSION[$this->_domain]) || isset(self::$_expiringData[$this->_domain]) );
    } else {
      return ( isset($_SESSION[$this->_domain][$name]) || isset(self::$_expiringData[$this->_domain][$name]) );
    }
  }

  public function __unset($name) {
    if ($name === '') {
      /**
       * @see Me_Session_Exception
       */
      require_once 'Me/Session/Exception.php';
      throw new Me_Session_Exception("The '$name' key must be a non-empty string");
    }


    $name = (string) $name;

    // check to see if the api wanted to remove a var from a namespace or a namespace
    if ($name === '') {
      unset($_SESSION[$name]);
      unset(self::$_expiringData[$this->_domain]);
    } else {
      unset($_SESSION[$this->_domain][$name]);
      unset(self::$_expiringData[$this->_domain]);
    }

    // if we remove the last value, remove namespace.
    if (empty($_SESSION[$this->_domain])) {
      unset($_SESSION[$this->_domain]);
    }
  }

}