<?php
class UsersModel extends Model
{
	public function __construct($dispatcher)
	{
		parent::__construct($dispatcher);
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_admin_create']))
		{
			$lastname = isset($_POST['new_user_lastname']) ? $this->dispatcher->Core->SQL->secure($_POST['new_user_lastname']) : NULL;
			$firstname = isset($_POST['new_user_firstname']) ? $this->dispatcher->Core->SQL->secure($_POST['new_user_firstname']) : NULL;
			$email = isset($_POST['new_user_email']) ? $this->dispatcher->Core->SQL->secure($_POST['new_user_email']) : NULL;
			$password = isset($_POST['new_user_password']) ? sha1(md5(SALT.$_POST['new_user_password'].PEPPER)) : NULL;
			$passconf = isset($_POST['new_user_passconf']) ? sha1(md5(SALT.$_POST['new_user_passconf'].PEPPER)) : NULL;
			$grade = isset($_POST['new_user_grade']) && $_POST['new_user_grade'] != 'NULL' ? $this->dispatcher->Core->SQL->secure($_POST['new_user_grade']) : NULL;
			$role = isset($_POST['new_user_role']) ? $this->dispatcher->Core->SQL->secure($_POST['new_user_role']) : NULL;

			if($this->dispatcher->Core->User->register($firstname, $lastname, $email, $password, $passconf, $grade, $role, 1))
				$_POST['success_log'] = "Le nouvel utilisateur a bien été créé.";

		}
		else if(isset($_POST['submit_del_user']))
		{
			$id = isset($_POST['user_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['user_id'])))) : NULL;
			
			if($id != NULL)
			{
				$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('id' => $id)));
				if($user != false)
				{
					$this->dispatcher->Core->Codiad->deleteAccount($user->email);
					$query = $this->dispatcher->Core->SQL->delete(array('table' => 'users', 'conditions' => 'id='.$id));
					$_POST['success_log'] = "L'utilisateur ".$user->firstname." ".$user->lastname." a bien été supprimé.";
				}
				else
					$_POST['error_log'] = "Cet utilisateur n'existe pas.";
			}
			else
				$_POST['error_log'] = "Certaines données de traitement son manquantes.";
		}
		else if(isset($_POST['submit_admin_edit']))
		{
			$flag = false;
			$id = isset($_POST['user_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['user_id'])))) : NULL;
			$email = isset($_POST['edit_user_email']) ? $this->dispatcher->Core->SQL->secure($_POST['edit_user_email']) : NULL;
			$password = isset($_POST['edit_user_password']) && !empty($_POST['edit_user_password']) ? sha1(md5(SALT.$_POST['edit_user_password'].PEPPER)) : NULL;
			$passconf = isset($_POST['edit_user_passconf']) && !empty($_POST['edit_user_passconf']) ? sha1(md5(SALT.$_POST['edit_user_passconf'].PEPPER)) : NULL;
			$grade = isset($_POST['edit_user_grade']) ? $this->dispatcher->Core->SQL->secure($_POST['edit_user_grade']) : NULL;
			$role = isset($_POST['edit_user_role']) ? $this->dispatcher->Core->SQL->secure($_POST['edit_user_role']) : NULL;
			$validated = isset($_POST['edit_user_validated']) ? '1' : '0';

			if($password != $passconf)
			{
				$_POST['error_log'] = "Les mots de passe ne correspondent pas.";
				return false;
			}

			$request = "UPDATE users SET";
			if($email != NULL)
			{
				$request .= " email='".$email."'";
				$flag = true;
			}
			if($password != NULL)
			{
				if($flag)
					$request .= ',';
				$request .= " password='".$password."'";
				$flag = true;
			}
			if($grade != NULL)
			{
				if($flag)
					$request .= ',';
				$request .= " grade='".$grade."'";
				$flag = true;
			}
			if($role != NULL)
			{
				if($flag)
					$request .= ',';
				$request .= " role='".$role."'";
				$flag = true;
			}
			if($flag)
				$request .= ',';
			$request .= " validated=".$validated;
			
			$request .= ' WHERE id='.$id.';';

			$query = $this->dispatcher->Core->SQL->query($request);
		}
		else if(isset($_POST['submit_prof_create']))
		{
			$firstname = isset($_POST['user_firstname_create']) ? $this->dispatcher->Core->SQL->secure($_POST['user_firstname_create']) : NULL;
			$lastname = isset($_POST['user_lastname_create']) ? $this->dispatcher->Core->SQL->secure($_POST['user_lastname_create']) : NULL;
			$email = isset($_POST['user_mail_create']) ? $this->dispatcher->Core->SQL->secure($_POST['user_mail_create']) : NULL;
			$password = isset($_POST['user_password_create']) ? sha1(md5(SALT.$_POST['user_password_create'].PEPPER)) : NULL;
			$passconf = isset($_POST['user_passconf_create']) ? sha1(md5(SALT.$_POST['user_passconf_create'].PEPPER)) : NULL;
			$grade = isset($_POST['user_grade_create']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['user_grade_create'])))) : NULL;

			if($this->dispatcher->Core->User->register($firstname, $lastname, $email, $password, $passconf, $grade, "role_student", 1))
				$_POST['success_log'] = "Le nouvel utilisateur a bien été créé.";

		}
		else if(isset($_POST['submit_prof_edit']))
		{
			$flag = false;
			$id = isset($_POST['user_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['user_id'])))) : NULL;
			$email = isset($_POST['user_mail_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['user_mail_edit']) : NULL;
			$password = isset($_POST['user_password_edit']) && !empty($_POST['user_password_edit']) ? sha1(md5(SALT.$_POST['user_password_edit'].PEPPER)) : NULL;
			$passconf = isset($_POST['user_passconf_edit']) && !empty($_POST['user_passconf_edit']) ? sha1(md5(SALT.$_POST['user_passconf_edit'].PEPPER)) : NULL;
			$grade = isset($_POST['user_grade_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['user_grade_edit']) : NULL;

			if($password != $passconf)
			{
				$_POST['error_log'] = "Les mots de passe ne correspondent pas.";
				return false;
			}

			$request = "UPDATE users SET";
			if($email != NULL)
			{
				$request .= " email='".$email."'";
				$flag = true;
			}
			if($password != NULL)
			{
				if($flag)
					$request .= ',';
				$request .= " password='".$password."'";
				$flag = true;
			}
			if($grade != NULL)
			{
				if($flag)
					$request .= ',';
				$request .= " grade='".$grade."'";
				$flag = true;
			}
			
			$request .= ' WHERE id='.$id.';';

			$query = $this->dispatcher->Core->SQL->query($request);
		}
		else if(isset($_POST['submit_prof_class_create']))
		{
			$grade = isset($_POST['grade_create']) ? $this->dispatcher->Core->SQL->secure($_POST['grade_create']) : NULL;

			if($grade != NULL)
			{
				$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
				$exists = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => array('grade' => $grade)));

				if(!$exists)
				{
					$id = false;
					$diff = $this->getDiffGrades();

					$this->dispatcher->Core->SQL->insert(array('table' => 'grades', 'values' => array(NULL, $grade, $user->id, 1)));
					$newGrade = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => array('grade' => $grade)));
					$subname = substr($grade, 0, strpos($grade, ' '));

					for($i=0 ; $i < count($diff) ; $i++)
					{
						if(substr($diff[$i]->grade, 0, strpos($diff[$i]->grade, ' ')) == $subname)
						{
							$id = $diff[$i]->id;
							break;
						}
					}

					//S'il existe une autre classe du même niveau...
					if($id)
					{
						//Ajout aux projets
						$projects = $this->dispatcher->Core->SQL->select(array('table' => 'projects_for', 'conditions' => array('grade' => $id)));
						for($i=0 ; $i < count($projects) ; $i++)
							$this->dispatcher->Core->SQL->insert(array('table' => 'projects_for', 'values' => array($projects[$i]->project, $newGrade->id, $projects[$i]->enabled)));
					
						//Ajout aux différents travaux
						$works = $this->dispatcher->Core->SQL->select(array('table' => 'works_for', 'conditions' => array('grade' => $id)));
						for($i=0 ; $i < count($works) ; $i++)
							$this->dispatcher->Core->SQL->insert(array('table' => 'works_for', 'values' => array($works[$i]->work, $newGrade->id)));
					
						//Ajout des entrées de FAQ
						$faq = $this->dispatcher->Core->SQL->select(array('table' => 'faq_for', 'conditions' => array('grade' => $id)));
						for($i=0 ; $i < count($faq) ; $i++)
							$this->dispatcher->Core->SQL->insert(array('table' => 'faq_for', 'values' => array($faq[$i]->faq, $newGrade->id)));
					
						//Ajout des différents liens
						$links = $this->dispatcher->Core->SQL->select(array('table' => 'links_for', 'conditions' => array('grade' => $id)));
						for($i=0 ; $i < count($links) ; $i++)
							$this->dispatcher->Core->SQL->insert(array('table' => 'links_for', 'values' => array($links[$i]->link, $newGrade->id)));
					}

					$_POST['success_log'] = "La classe ".$grade." a bien été créée.";
				}
				else
					$_POST['error_log'] = "La classe ".$grade." existe déjà.";
			}
		}
		else if(isset($_POST['submit_prof_del_class']))
		{
			$id = isset($_POST['grade_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['grade_id'])))) : NULL;

			if($id != NULL)
			{
				$exists = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => array('id' => $id)));

				if($exists)
				{
					$this->dispatcher->Core->SQL->delete(array('table' => 'grades', 'conditions' => 'id='.$id));
					$_POST['success_log'] = "La classe ".$exists->grade." a bien été supprimée.";
				}
				else
					$_POST['error_log'] = "Cette classe n'existe pas.";
			}
		}
		else if(isset($_POST['submit_prof_valid']))
		{
			$id = isset($_POST['valid_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['valid_id'])))) : NULL;
			
			if($id!=NULL)
				$query = $this->dispatcher->Core->SQL->update(array('table' => 'users', 'columns' => 'validated', 'values' => '1', 'conditions' => 'id='.$id));
		}
		else if(isset($_POST['submit_prof_refuse']))
		{
			$id = isset($_POST['valid_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['valid_id'])))) : NULL;
			
			if($id!=NULL)
				$query = $this->dispatcher->Core->SQL->delete(array('table' => 'users', 'conditions' => 'id='.$id));
		}
		else if(isset($_POST['submit_register_ok']))
		{
			$grade = isset($_POST['grade_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['grade_id'])))) : NULL;
			if($grade!=NULL)
				$this->dispatcher->Core->SQL->update(array('table' => 'grades', 'columns' => 'allow_register', 'values' => '1', 'conditions' => 'id='.$grade));
		}
		else if(isset($_POST['submit_register_ko']))
		{
			$grade = isset($_POST['grade_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['grade_id'])))) : NULL;
			if($grade!=NULL)
				$this->dispatcher->Core->SQL->update(array('table' => 'grades', 'columns' => 'allow_register', 'values' => '0', 'conditions' => 'id='.$grade));
		}

		parent::handlePost();
	}

	public function getUsers()
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'users'));
	}

	public function getUsersByRole($role)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'users', 'conditions' => array('role' => $role)));
	}

	public function getUsersByGrade($grade)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'users', 'conditions' => array('grade' => $grade)));
	}

	public function getUsersByValidatedGrade($grade,$validated)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'users', 'conditions' => array('grade' => $grade, 'validated' => (string)$validated)));
	}

	public function getGrades()
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'grades'));
	}

	public function getGradesByTeacher()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		return $this->dispatcher->Core->SQL->select(array('table' => 'grades', 'conditions' => array('teacher' => $user->id)));
	}

	/**
	 * Récupère toutes les classes de niveau différent (Première, Terminale...).
	 * @return array
	 */
	public function getDiffGrades()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));

		$request = "SELECT * FROM grades WHERE teacher=".$user->id." GROUP BY SUBSTRING(grade, 1, INSTR(grade,' ')-1);";
		return $this->dispatcher->Core->SQL->query($request);
	}
}
?>