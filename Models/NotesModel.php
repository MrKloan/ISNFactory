<?php
class NotesModel extends Model
{
	public function __construct($dispatcher)
	{
		$role = $dispatcher->Core->User->getRole();
		$config = $dispatcher->Core->Config->notes;
		if(($role == "role_student" && $config != 2) || ($role == "role_professor" && $config < 1))
			$dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/home');

		parent::__construct($dispatcher);
	}

	public function handlePost()
	{
		if(isset($_POST['submit_new_note']))
		{
			$grade = isset($_POST['for_grade']) ? $this->dispatcher->Core->SQL->secure($_POST['for_grade']) : NULL;
			$name = isset($_POST['note_name']) ? $this->dispatcher->Core->SQL->secure($_POST['note_name']) : NULL;
			$coeff = isset($_POST['coeff']) ? $this->dispatcher->Core->SQL->secure($_POST['coeff']) : NULL;
			$date = isset($_POST['date']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['date']))) : NULL;

			if($grade!=NULL && $name!=NULL && $coeff!=NULL)
			{
				$this->dispatcher->Core->SQL->insert(array('table' => 'works', 'values' => array(NULL, $name, 'controle', $date, $coeff)));
				$work = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'works', 'conditions' => array('title' => $name, 'date_end' => $date)));
				$this->dispatcher->Core->SQL->insert(array('table' => 'works_for', 'values' => array($work->id, $grade)));
			
				$_POST['success_log'] = 'Votre interrogation a bien été enregistrée.';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
		}
		else if(isset($_POST['submit_edit_note']))
		{
			$id = isset($_POST['note_id']) ? explode("_", str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['note_id']))))) : NULL;
			$note = isset($_POST['new_note_update']) ? $this->dispatcher->Core->SQL->secure($_POST['new_note_update']) : NULL;

			if($id!=NULL && $note!=NULL)
			{
				if(!$this->dispatcher->Core->SQL->selectFirst(array('table' => 'notes', 'conditions' => array('student' => $id[1], 'work' => $id[0]))))
					$this->dispatcher->Core->SQL->insert(array('table' => 'notes', 'values' => array(NULL, $id[1], $id[0], $note)));
				else
					$this->dispatcher->Core->SQL->update(array('table' => 'notes', 'columns' => 'note', 'values' => $note, 'conditions' => array('student' => $id[1], 'work' => $id[0])));
				
				$_POST['success_log'] = 'La note a bien été enregistrée.';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
		}
		else if(isset($_POST['submit_del_work']))
		{
			$id = isset($_POST['work_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['work_id'])))) : NULL;

			if($id!=NULL)
				$this->dispatcher->Core->SQL->delete(array('table' => 'works', 'conditions' => 'id='.$id));
		}
		else if(isset($_POST['submit_trimesters']))
		{
			$trimester1 = isset($_POST['trimester1']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['trimester1']))) : NULL;
			$trimester2 = isset($_POST['trimester2']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['trimester2']))) : NULL;
			$trimester3 = isset($_POST['trimester3']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['trimester3']))) : NULL;

			if($trimester1!=NULL && $trimester2!=NULL && $trimester3!=NULL)
			{
				$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));

				if(($trimesters = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'trimesters', 'conditions' => 'teacher='.$user->id))) == false)
					$this->dispatcher->Core->SQL->insert(array('table' => 'trimesters', 'values' => array(NULL, $user->id, $trimester1, $trimester2, $trimester3)));
				else
					$this->dispatcher->Core->SQL->update(array('table' => 'trimesters', 'columns' => array('trimester1','trimester2','trimester3'), 'values' => array($trimester1, $trimester2, $trimester3), 'conditions' => 'id='.$trimesters->id));
				
				$_POST['success_log'] = 'Vos modifications ont bien été prises en compte.';
			}
			else
				 $_POST['error_log'] = 'Certaines données de traitement sont manquantes.';

		}
		parent::handlePost();
	}

	public function getGrades()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		return $this->dispatcher->Core->SQL->select(array('table' => 'grades', 'conditions' => array('teacher' => $user->id)));	
	}

	public function getProfWorks($grade)
	{
		$request = "SELECT work FROM works_for WHERE grade=".$grade->id.";";
		$query = $this->dispatcher->Core->SQL->query($request);
		
		if($query){
			$conditions = "WHERE ";
			for($i=0; $i < count($query); $i++){
				$conditions .= "id=".$query[$i]->work;
				if($i < count($query)-1)
					$conditions .= " OR ";
			}

			$request = "SELECT * FROM works ".$conditions.";";
			return $this->dispatcher->Core->SQL->query($request);
		}
		else
			return false;
	}

	public function getStudentsByGrade($grade)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'users', 'conditions' => 'grade='.$grade));
	}

	public function getNote($work, $student)
	{
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'notes', 'conditions' => array('student' => $student, 'work' => $work)));
	}

	public function getTrimesters()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'trimesters', 'conditions' => array('teacher' => $user->id)));
	}
}
?>