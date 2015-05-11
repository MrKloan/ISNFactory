<?php
class ParametersModel extends Model
{
	public function __construct($dispatcher)
	{
		parent::__construct($dispatcher);
		$this->handlePost();
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_edit_pwd']))
		{
			$oldpass = isset($_POST['oldpass']) ? sha1(md5(SALT.$_POST['oldpass'].PEPPER)) : NULL;
			$password = isset($_POST['password']) ? sha1(md5(SALT.$_POST['password'].PEPPER)) : NULL;
			$passconf = isset($_POST['passconf']) ? sha1(md5(SALT.$_POST['passconf'].PEPPER)) : NULL;

			if($oldpass != NULL && $password != NULL && $passconf != NULL)
			{
				$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => 'email='.$this->dispatcher->Core->User->getEmail()));
				//die($oldpass."<br/>".$user->password);
				if(strcmp($oldpass,$user->password) != 0)
				{
					//$_POST['error_log'] = 'L\'ancien mot de passe est erroné.';
					return false;
				}
				else
				{
					if($this->dispatcher->Core->User->updatePassword($password, $passconf))
						$_POST['success_log'] = "Mot de passe correctement mis à jour.";
					else
						$_POST['error_log'] = "Les mots de passe ne correspondent pas.";
				}
			}
			else
				$_POST['error_log'] = 'Certaines données de traitement sont manquantes.';
		}

		parent::handlePost();
	}

	public function getGradeById($id)
	{
		return $this->dispatcher->Core->SQL->selectFirst(array("table"=> "grades", "conditions"=> "id=".$id));
	}

	public function getGradeByTeacher($email)
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array("table"=> "users", "conditions"=> "email=".$email));
		 return $this->dispatcher->Core->SQL->select(array("table"=> "grades", "conditions"=> "teacher=".$user->id));
	}
}
?>