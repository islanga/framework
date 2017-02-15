<?php
class Course_manController extends BaseController 
{ 
	private $request_params;
	private $courses;
	private $course_id;
	private $student_courses;
	public function __construct()
	{
		parent::__construct($this);	
		$this->request_params = $this->_view->getRequestParams();
		$this->courses = new Course();
		$this->student_courses = new CourseStudent();
        if ($this->_view->getRequestParam('id')) 
		{
            $this->course_id = (int) $this->_view->getRequestParam('id');
        }
	}
	
	public function indexAction() 
	{ 
		$course_list = $this->courses->fetchAll(null, 'cname');
		$this->assign('course_list', $course_list);
		$this->render();
	}
	
	public function addAction()
	{
		$this->render();
	}
		
	public function editAction()
	{
        $this->courses->load($this->course_id);
		$this->assign('course_edit', $this->courses);
		$this->render();
	}

    public function deleteAction() 
	{ 
		$this->courses->delete($this->course_id);		
        $this->_redirect("index");
    }
	
	public function saveAction()
	{
		$this->courses->save($this->request_params);
		$course_list = $this->courses->fetchAll(null, 'cname');
		$this->assign('course_list', $course_list);

		$this->render();
	}
	
    public function __toString() 
	{
        return __CLASS__;
    }
}