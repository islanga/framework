<?php
class Student_manController extends BaseController 
{ 
	private $request_params;
	private $students;  
	private $course_students;
	private $courses;
	private $student_id;
	private $course_id;
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
		$student_lists = $this->students->fetchAll(null, 'sname');
		$this->assign('course_students', $this->course_students);
		$this->assign('student_lists', $student_lists);
		$this->assign('courses', $this->courses);
		$this->render();
	}
	public function editAction()
	{
		if ($this->_view->getRequestParam('course_id')) 
		{
            $this->course_id = (int) $this->_view->getRequestParam('course_id');
        }
		
		$student_group = $this->students->student_course($this->course_id);
		$this->assign('student_lists', $student_group);
		$this->assign('course_students', $this->course_students);
		$this->assign('courses', $this->courses);

		$this->render();
	}
	
    public function __toString() 
	{
        return __CLASS__;
    }
}