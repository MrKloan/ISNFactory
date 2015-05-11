<?php
class CoursesModel extends Model
{
	public function __construct($dispatcher)
	{
		$role = $dispatcher->Core->User->getRole();
		$config = $dispatcher->Core->Config->courses;
		if(($role == "role_student" && $config != 2) || ($role == "role_professor" && $config < 1))
			$dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/home');

		parent::__construct($dispatcher);
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_course_create']))
		{
			$number = isset($_POST['number_course_create']) ? $this->dispatcher->Core->SQL->secure($_POST['number_course_create']) : NULL;
			$name = isset($_POST['name_course_create']) ? $this->dispatcher->Core->SQL->secure($_POST['name_course_create']) : NULL;
			$grades = isset($_POST['grades_course_create']) ? unserialize(base64_decode($_POST['grades_course_create'])) : NULL;

			if($number!=NULL && $name!=NULL && $grades!=NULL && is_array($grades))
			{
				$this->dispatcher->Core->SQL->insert(array('table' => 'courses', 'values' => array(NULL,$number,$name,NULL,NULL)));
				$id = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'courses','conditions' => array('title' => $name)));

				$values = array(array());
				for($i=0; $i < count($grades); $i++){
					$values[$i][0] = $id->id;
					$values[$i][1] = $grades[$i];
					$values[$i][2] = 0;
				}
				
				$this->dispatcher->Core->SQL->insert(array('table' => 'courses_for', 'values' => $values));

				$_POST['success_log'] = 'Le cours a bien été créé.';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';

		}
		else if(isset($_POST['submit_course_edit']))
		{
			$id = isset($_POST['course_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['course_id'])))) : NULL;
			$description = isset($_POST['course_edit_description']) ? $this->dispatcher->Core->SQL->secure($_POST['course_edit_description']) : NULL;
			if($id != NULL)
			{
				$this->dispatcher->Core->SQL->update(array('table' => 'courses', 'columns' => 'description', 'values' => $description, 'conditions' => 'id='.$id));

				$_POST['success_log'] = 'Le cours a bien été modifié.';

				if(isset($_FILES['course_file']))
				{
					$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
					$classe = isset($_POST['course_directory']) ? $this->accents($this->dispatcher->Core->SQL->secure($_POST['course_directory'])) : NULL;
				
					if($user!=NULL && $classe!=NULL && $id!=NULL){
						if(!file_exists(FILES."Courses/".$user->lastname."/".$classe))
							mkdir(FILES."Courses/".$user->lastname."/".$classe, 0777, true);

						$directory = FILES."Courses/".$user->lastname."/".$classe."/";
						$taille = 20971520; //20Mo en octets
						$extension = array(".pdf",".zip",".rar");
						$file = basename($this->accents($_FILES['course_file']['name']));

						if(filesize($_FILES['course_file']['tmp_name']) > $taille)
							return false;
						else if(!in_array(strrchr($_FILES['course_file']['name'], '.'), $extension))
							return false;
						else{
							if(move_uploaded_file($_FILES['course_file']['tmp_name'], $directory.$file)){
								$course = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'courses', 'conditions' => 'id='.$id));
								$fichier = str_replace(FILES,WEB_ROOT."/Files/", $directory).$file;
								if($course->files_url != NULL)
								{
									$files = unserialize(base64_decode($course->files_url));
									array_push($files, $fichier);
									$this->dispatcher->Core->SQL->update(array('table' => 'courses', 'columns' => 'files_url', 'values' => base64_encode(serialize($files)), 'conditions' => 'id='.$id));
								}
								else
									$this->dispatcher->Core->SQL->update(array('table' => 'courses', 'columns' => 'files_url', 'values' => base64_encode(serialize(array($fichier))), 'conditions' => 'id='.$id));
								return true;
							}
						}
					}
				}
			}
			else
				$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
		}
		else if(isset($_POST['submit_dispo_ok']))
		{
			$id = isset($_POST['course_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['course_id'])))) : NULL;
			if($id != NULL)
				$this->dispatcher->Core->SQL->update(array('table' => 'courses_for', 'columns' => 'enabled', 'values' => '0', 'conditions' => 'course='.$id));
		}
		else if(isset($_POST['submit_dispo_ko']))
		{
			$id = isset($_POST['course_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['course_id'])))) : NULL;
			if($id != NULL)
				$this->dispatcher->Core->SQL->update(array('table' => 'courses_for', 'columns' => 'enabled', 'values' => '1', 'conditions' => 'course='.$id));
		}
		else if(isset($_POST['submit_course_del']))
		{
			$id = isset($_POST['course_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['course_id'])))) : NULL;

			if($id != NULL){
				$this->dispatcher->Core->SQL->delete(array('table' => 'courses', 'conditions' => 'id='.$id));

				$_POST['success_log'] = 'Le cours a bien été supprimé.';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
		}
		else if(isset($_POST['submit_del_file']))
		{
			$id = isset($_POST['file_id']) ? explode("_", str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['file_id']))))): NULL;
			if($id != NULL)
			{
				$course = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'courses', 'conditions' => 'id='.$id[0]));
				$files = unserialize(base64_decode($course->files_url));
				if(unlink(str_replace(WEB_ROOT."/Files/",FILES,$files[$id[1]])))
				{
					unset($files[$id[1]]);
					if(count($files) == 0)
						$files = NULL;
					else
						$files = base64_encode(serialize(array_values($files)));
					$this->dispatcher->Core->SQL->update(array('table' => 'courses', 'columns' => 'files_url', 'values' => $files, 'conditions' => 'id='.$id[0]));
				}
			}
		}

		parent::handlePost();
	}

	public function getDiffGrades()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));

		$request = "SELECT * FROM grades WHERE teacher=".$user->id." GROUP BY SUBSTRING(grade, 1, INSTR(grade,' ')-1);";
		return $this->dispatcher->Core->SQL->query($request);
	}

	public function getIdGrades($valeur)
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));

		$request = "SELECT id FROM grades WHERE teacher=".$user->id." AND SUBSTRING(grade, 1, INSTR(grade,' ')-1)='".$valeur."';";
		return $this->dispatcher->Core->SQL->query($request);
	}

	/**
	* Fonction renvoyant les cours créés par le professeur
	*/
	public function getCoursesByGrades($grades)
	{
		$conditions = "WHERE ";
		for($i=0; $i < count($grades); $i++){
			$conditions .= "grade=".$grades[$i];
			if($i < count($grades)-1)
				$conditions .= " OR ";
		}

		$request = "SELECT course FROM courses_for ".$conditions.";";

		$query = $this->dispatcher->Core->SQL->query($request);
		
		if($query){
			$conditions = "WHERE ";
			for($i=0; $i < count($query); $i++){
				$conditions .= "id=".$query[$i]->course;
				if($i < count($query)-1)
					$conditions .= " OR ";
			}

			$request = "SELECT * FROM courses ".$conditions.";";
			return $this->dispatcher->Core->SQL->query($request);
		}
		else
			return false;
	}

	public function getCoursesFor($grades)
	{
		$array = array();
		for($i=0; $i < count($grades); $i++)
			array_push($array,$this->dispatcher->Core->SQL->select(array('table' => 'courses_for', 'conditions' => 'grade='.$grades[$i])));
		return $array;
	}

	/**
	* Fonction renvoyant les cours disponible pour un élève
	*/
	public function getCourses()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));

		$request = "SELECT * FROM courses WHERE id IN(SELECT course FROM courses_for WHERE grade=".$user->grade." AND enabled=1);";
		return $this->dispatcher->Core->SQL->query($request);
	}

	public function accents($str, $charset='utf-8')
	{
	    $str = htmlentities($str, ENT_NOQUOTES, $charset);
	    
	    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	    
	    return $str;
	}
}
?>

