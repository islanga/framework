<?php

class IndexController extends BaseController 
{
    //put your code here
	private $request_params;
	private $students;
	private $courses;
	private $course_id;
	private $course_students;
	private $student_id;
	private $course_results;
    public function __construct() 
	{
        parent::__construct($this);
		$this->request_params 	= $this->_view->getRequestParams();
		$this->students 		= new Student();
		$this->courses 			= new Course();
		$this->course_students 	= new CourseStudent();
		$this->course_results 	= $this->courses->fetchAll(null, 'cname');	
        if ($this->_view->getRequestParam('id')) 
		{
            $this->student_id = (int) $this->_view->getRequestParam('id');
        }
    }

    public function indexAction() 
	{
		$this->assign('course_results', $this->course_results);

        $this->render();
    }

	public function editAction()
	{
		$course_results = $this->courses->fetchAll(null, 'cname');
		$this->assign('course_results', $course_results);

		$this->students->load($this->student_id);
		$course_id 		= $this->course_students->course_id($this->students->sno);
		$course_ids 	= explode("*|*", $course_id[0]['cid']);
		foreach ($course_ids as $key => $value):
			try {
					$this->courses->load($value);
					$c_id[] = $this->courses->cid;
				} catch (Exception $ex) {

				}
		endforeach;

		$this->assign("c_id", $c_id);
		$this->assign('students', $this->students);
		$this->render();
	}
	
	public function saveAction()
	{
		$student_params = array();
		$student_params = $this->request_params;
		$errorMessage = array();

		if (empty($student_params["cid"])) {
            $errorMessage[] = "Course is a required Attribute";
        }
		if (empty($student_params["sname"])) {
            $errorMessage[] = "Surname is a required Attribute";
        }
		if (empty($student_params["init"])) {
            $errorMessage[] = "Initials is a required Attribute";
        }
		if (empty($student_params["fname"])) {
            $errorMessage[] = "First Name is a required Attribute";
        }
		if (empty($student_params["title"])) {
            $errorMessage[] = "Title is a required Attribute";
        }
		if (empty($student_params["msname"])) {
            $errorMessage[] = "Maiden Name is a required Attribute";
        }
		if (empty($student_params["day"])) {
            $errorMessage[] = "Day is a required Attribute";
        }
		if (empty($student_params["month"])) {
            $errorMessage[] = "Month is a required Attribute";
        }
		if (empty($student_params["year"])) {
            $errorMessage[] = "Year is a required Attribute";
        }
		if (empty($student_params["sex"])) {
            $errorMessage[] = "Sex is a required Attribute";
        }
		if (empty($student_params["lang"])) {
            $errorMessage[] = "Language is a required Attribute";
        }
		if (empty($student_params["idno"])) {
            $errorMessage[] = "Identity Number is a required Attribute";
        }
		if (empty($student_params["telh"])) {
            $errorMessage[] = "Home Telephone is a required Attribute";
        }
		if (empty($student_params["telw"])) {
            $errorMessage[] = "Work Telephone is a required Attribute";
        }
		if (empty($student_params["cel"])) {
            $errorMessage[] = "Cellphone is a required Attribute";
        }
		if (empty($student_params["fax"])) {
            $errorMessage[] = "Fax is a required Attribute";
        }
		if (empty($student_params["email"])) {
            $errorMessage[] = "Email is a required Attribute";
        }
		if (empty($student_params["address"])) {
            $errorMessage[] = "Address is a required Attribute";
        }

		$day = $student_params['day'];
		$month = $student_params['month'];
		$year = $student_params['year'];
		unset($student_params['day']);
		unset($student_params['month']);
		unset($student_params['year']);
		$student_params['dob'] = $day . '/' . $month . '/' . $year;
		if (count($errorMessage)) 
		{
			$this->assign('errorMessage', $errorMessage);
			$this->assign('course_results', $this->course_results);
			$this->assign('cid', $student_params['cid']);
			$this->assign('students', arrayToObject($student_params));
		} 
		else 
		{ 
			$this->students->save($student_params);

			$id = $this->students->getDBO()->insertid();
			$student_params['sno'] = $id;
			$student_params['cid'] = implode('*|*', $student_params['cid']);
			$student_params['year'] = date('Y');
			$this->course_students->save($student_params);
			
			if (isset($student_params['contact_flag']))
			{
				$students = $this->students->fetch(array('email','sname','fname'));
				foreach ($students as $key => $value):
					$email[] = $value['email'];
					$fname[] = $value['fname'];
					$sname[] = $value['sname'];
				endforeach;
				$email = implode(',',$email);
				$fname = implode(' ,',$fname);
				$sname = implode(' ,',$sname);
				
				$subject = 'Registration Confirmation';
				$message = 'List of Names: ' . $fname . '&lt;p&gt;';
				$message .= 'List of Surnames: ' . $fname;
				$email_class = new AttachmentEmail($email, $subject, $message);
				$email_class->mail();
			}
		}
		$this->render();
	}

    public function __toString() {
        return __CLASS__;
    }
}
?>