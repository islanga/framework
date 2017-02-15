<?php
require_once "Registry/Abstract.php";

class Registry extends Registry_Abstract
{

  private static $objects = array();
  private static $instance;

  //prevent directly access.
  private function __construct() {
    
  }

  //prevent clone.
  public function __clone() {

  }

  /**
   * singleton method used to access the object
   * @access public
   */
  public static function singleton() {
    if (!isset(self::$instance)) {
      self::$instance = new self();
    } else {

    }
    return self::$instance;
  }

  /*
   *  get object protected function you can use only
   *  inside from this class.
   */

  protected function get($key) {
    return $this->exists($key) ? self::$objects[$key]:"";
  }

  /*
   *  set object protected function you can use only
   *  inside from this class.
   */

  protected function set($key, $val) {
    if(!$this->exists($key)) {
      self::$objects[$key] = $val;
    }
  }

  protected function exists($key){
    return array_key_exists($key, self::$objects);
  }

  /*
   *  get stored object
   */

  static function getObject($key) {

    return self::singleton()->get($key);
  }

  /*
   *  store object
   */

  static function storeObject($key, $instance) {

    return self::singleton()->set($key, $instance);
  }
}
?>
