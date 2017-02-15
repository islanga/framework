<?php

abstract class Me_Session_Handler_Abstract {

  
  public $sessionLifetime;

  /**
   *  Constructor of class
   *
   *  @return void
   */
  protected function __construct() {
    $this->sessionLifetime = get_cfg_var("session.gc_maxlifetime");
    session_set_save_handler(
      array(&$this, 'open'),
      array(&$this, 'close'),
      array(&$this, 'read'),
      array(&$this, 'write'),
      array(&$this, 'destroy'),
      array(&$this, 'gc')
    );
    register_shutdown_function('session_write_close');
    session_start();
  }

  /**
   *  Regenerates the session id.
   *
   *  <b>Call this method whenever you do a privilege change!</b>
   *
   *  @return void
   */
  public function regenerate_id() {

    // saves the old session's id
    $oldSessionID = session_id();

    // regenerates the id
    // this function will create a new session, with a new id and containing the data from the old session
    // but will not delete the old session
    session_regenerate_id();

    // because the session_regenerate_id() function does not delete the old session,
    // we have to delete it manually
    $this->destroy($oldSessionID);
  }

  public  function rememberMe($seconds = 0) {

    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
      $seconds,
      $cookieParams['path'],
      $cookieParams['domain'],
      $cookieParams['secure']
    );

    $this->regenerate_id();
  }

  public function forgetMe() {
    $this->rememberMe(0);
  }

  abstract public function get_users_online();
  abstract protected function open($save_path, $session_name);
  abstract public function close();
  abstract public function write($session_id, $session_data);
  abstract protected function destroy($session_id);
  abstract protected function gc($maxlifetime);
}
