<?php
class Student_courseController extends BaseController 
{
	private $request_params;
	private $students; 
	private $course_students;
	private $courses;
	private $student_id;
	public function __construct()
	{ 
		parent::__construct($this); 	
		$this->request_params = $this->_view->getRequestParams();
		$this->students = new Student();
		$this->course_students = new CourseStudent();
		$this->courses = new Course;
        if ($this->_view->getRequestParam('id')) 
		{
            $this->student_id = (int) $this->_view->getRequestParam('id');
        }
	}
	public function indexAction() 
	{ 
		$student_lists = $this->courses->fetchAll(null, 'cname');
		$this->assign('course_lists', $student_lists);
		$this->render();
	}
	
    public function __toString() 
	{
        return __CLASS__;
    }
}