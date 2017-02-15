<?php
require_once CORE_INCLUDE_PATH . DS. "classes" . DS ."Database_Table.php";
class Course extends Database_Table 
{
  public $cid;
  public $cname;
 
  public function __construct() 
  {
    $this->_tbl = "courses";
    $this->_tbl_key = "cid";
    $this->_db = parent::Singleton()->getDBO();
  }
}
?>