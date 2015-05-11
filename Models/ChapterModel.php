<?php
class ChapterModel extends Model
{
	public $param;

	public function __construct($dispatcher)
	{
		$role = $dispatcher->Core->User->getRole();
		$config = $dispatcher->Core->Config->courses;
		if(($role == "role_student" && $config != 2) || ($role == "role_professor" && $config < 1))
			$dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/home');

		parent::__construct($dispatcher);

		$this->param = $this->getParams();
	}

	public function getParams()
	{
		$role = $this->dispatcher->Core->User->getRole();
		$params = $this->dispatcher->params;
		$param = $this->dispatcher->Core->SQL->secure($params[0]);
		
		if(preg_match("(^[0-9]*$)", $param)){
			if($role == "role_professor"){
				$temp = $this->dispatcher->Core->SQL->select(array('table' => 'courses'));
				$courses_id = array();
				for($i=0; $i < count($temp); $i++)
					array_push($courses_id,$temp[$i]->id);
				if(in_array($param, $courses_id))
					return $param;
				else
					$this->dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/courses');
			}
			else if($role == "role_student")
			{
				$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
				$request = "SELECT chapter FROM courses WHERE id IN(SELECT course FROM courses_for WHERE grade=".$user->grade." AND enabled=1);";
				$temp = $this->dispatcher->Core->SQL->query($request);
				$courses_chapter = array();
				for($i=0; $i < count($temp); $i++)
					array_push($courses_chapter,$temp[$i]->chapter);
				if(in_array($param, $courses_chapter))
					return $param;
				else
					$this->dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/courses');
			}
			
		}	
		else
			$this->dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/courses'); 
	}

	public function getProfCourse($id)
	{
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'courses', 'conditions' => 'id='.$id));
	}

	public function getStudentCourse($chapter)
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		$request = "SELECT * FROM courses WHERE chapter=".$chapter." AND id IN(SELECT course FROM courses_for WHERE grade=".$user->grade.");";
		return current($this->dispatcher->Core->SQL->query($request));
	}
}
?>