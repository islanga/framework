<?php
require_once CORE_INCLUDE_PATH . DS. "classes" . DS ."Database_Table.php";
class CourseStudent extends Database_Table 
{
  public $cs_id;
  public $sno;
  public $cid;
  public $year;
  public $fmark;
 
  public function __construct() 
  {
    $this->_tbl = "course_students";
    $this->_tbl_key = "cs_id";
    $this->_db = parent::Singleton()->getDBO();
  }
  
  public function course_id($id)
  {
  	$query = "SELECT cid, year FROM $this->_tbl WHERE sno = {$id}";
	$this->_db->setQuery($query);
	$results = $this->_db->loadAssocList();
	return $results;
  }
}
?>