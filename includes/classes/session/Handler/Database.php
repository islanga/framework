<?php

class Me_Session_Handler_Database extends Me_Session_Handler_Abstract {

  private static $_instance = null;

  public static function Init() {
    if(!isset(self::$_instance)){
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   *  Constructor of class
   *
   *  @return void
   */
  protected function __construct() {
      parent::__construct();
  }



  /**
   *  Get the number of online users
   *
   *  This is not 100% accurate. It depends on how often the garbage collector is run
   *
   *  @return integer     approximate number of users curently online
   */
  public function get_users_online() {
    $sql = "SELECT COUNT(session_id) as count FROM session";
    // counts the rows from the database
    $session_table = new Session();
    $db = $session_table->getDBO();
    $db->setQuery($sql);
    // return the number of found rows
    return $db->loadResult();
  }

  

  /**
   *  Custom open() function
   *
   *  @access public
   */
  protected function open($save_path, $session_name) {
    return true;
  }

  /**
   *  Custom close() function
   *
   *  @access public
   */
  public function close() {
    return true;
  }

  /**
   *  Custom read() function
   *
   *  @access private
   */
  public function read($session_id) {

    // reads session data associated with the session id
    // but only if the HTTP_USER_AGENT is the same as the one who had previously written to this session
    // and if session has not expired
   
    
  //  $where["session_id"] = $session_id;
   // $where["http_user_agent"] = $_SERVER['HTTP_USER_AGENT'];


    $sql = "SELECT session_data FROM session WHERE session_id = '{$session_id}'
      AND http_user_agent ='{$_SERVER['HTTP_USER_AGENT']}' AND session_expire > '".time()."'";
    
    $session_table = new Session();
    //$result = $session_table->fetch(array('session_data'), $where);
    
    
    $db = $session_table->getDBO();
    $db->setQuery($sql);
    $session_data = $db->loadResult();
 //   $result = $db->loadAssocList();
   // print_r($result);
    if ($session_data)
      return $session_data;
    return "";
  }

  /**
   *  Custom write() function
   *
   *  @access public
   */
  public function write($session_id, $session_data) {

    // first checks if there is a session with this id
    $session_table = new Session();
    $db = $session_table->getDBO();
    $result = $session_table->fetchAll(array('session_id' => $session_id));
    
   
    
    $query = !count($result) ? "INSERT INTO session VALUES({$db->Quote($session_id)},
        {$db->Quote($_SERVER["HTTP_USER_AGENT"])},{$db->Quote($session_data)},{$db->Quote(time() + $this->sessionLifetime)}) " :
      "UPDATE session SET `http_user_agent` = {$db->Quote($_SERVER["HTTP_USER_AGENT"])},
                            `session_data` = {$db->Quote($session_data)},
                            `session_expire` = {$db->Quote(time() + $this->sessionLifetime)} WHERE `session_id` = {$db->Quote($session_id)} ";
    
    $db->setQuery($query);
    return $db->query();
  }

  /**
   *  Custom destroy() function
   *
   *  @access public
   */
  protected function destroy($session_id) {
    $session_table = new Session();
    // deletes the current session id from the database
    return $session_table->delete($session_id);
  }

  /**
   *  Custom gc() function (garbage collector)
   *
   *  @access public
   */
  protected function gc($maxlifetime) {

    // it deletes expired sessions from database
    $session_table = new Session();
    $db = $session_table->getDBO();
    $query = " DELETE FROM session WHERE session_expire < '" . (time() - $maxlifetime) . "'";
    $db->setQuery($query);
    $db->query();
  }
}