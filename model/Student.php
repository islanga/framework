<?php
require_once CORE_INCLUDE_PATH . DS. "classes" . DS ."Database_Table.php";
class Student extends Database_Table 
{
  public $sno;
  public $sname;
  public $init;
  public $fname;
  public $title;
  public $msname;
  public $dob;
  public $sex;
  public $lang;
  public $idno;
  public $telh;
  public $telw;
  public $cel;
  public $fax;
  public $email;
  public $address;
  public $cid;
  public $contact_flag;
   
  public function __construct() 
  {
    $this->_tbl = "students";
    $this->_tbl_key = "sno";
    $this->_db = parent::Singleton()->getDBO();
  }
  
  public function student_course($cid)
  {
  	$query = "SELECT * FROM {$this->_tbl} AS s, course_students AS cs WHERE s.sno = cs.sno AND FIND_IN_SET({$cid}, REPLACE(cs.cid,'*|*',','))";
	$this->getDBO()->setQuery($query);
	$results = $this->getDBO()->loadAssocList();
	return $results;
  }
}
?>