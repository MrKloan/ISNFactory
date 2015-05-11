<?php
class MailsModel extends Model
{
	public function __construct($dispatcher)
	{
		$role = $dispatcher->Core->User->getRole();
		$config = $dispatcher->Core->Config->mails;
		if(($role == "role_student" && $config != 2) || ($role == "role_professor" && $config < 1))
			$dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/home');

		parent::__construct($dispatcher);
	}

	public function handlePost()
	{
		if(isset($_POST['submit_mail']))
		{
			$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
			$object = isset($_POST['object_mail']) ? $this->dispatcher->Core->SQL->secure($_POST['object_mail']) : NULL;
			$content = isset($_POST['content_mail']) ? $this->dispatcher->Core->SQL->secure($_POST['content_mail']) : NULL;
			$receivers = isset($_POST['dest']) ? $_POST['dest'] : NULL;

			if($user!=NULL && $object!=NULL && $content!=NULL && $receivers!=NULL)
			{
				$this->dispatcher->Core->SQL->insert(array('table' => 'mails', 'values' => array(NULL,$user->id,$object,$content,"NOW()",NULL,NULL)));
				$mail = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'mails', 'conditions' => 'content='.$content));

				$receivers_id = array();
				for($i=0; $i < count($receivers); $i++)
				{
					$name = explode(" ",$receivers[$i]);
					$temp = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('firstname' => $name[0], 'lastname' => $name[1])));
					array_push($receivers_id,$temp->id);
				}

				$values = array(array());
				for($i=0; $i < count($receivers); $i++)
				{
					$values[$i][0] = $mail->id;
					$values[$i][1] = $receivers_id[$i];
				}
				$this->dispatcher->Core->SQL->insert(array('table' => 'mails_for', 'values' => $values));

				$_POST['success_log'] = 'Votre message a bien été envoyé.';

				if($user->grade)
				{
					$grade = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => array('id' => $user->grade)));
					if(in_array($grade->teacher, $receivers_id))
						$this->dispatcher->Core->SQL->insert(array('table' => 'notifications', 'values' => array(NULL, "E-Mail", "Nouvel E-Mail de ".$user->firstname." ".$user->lastname." de ".$grade->grade.".", $grade->id)));
				}
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_del_mail']))
		{
			$id = isset($_POST['mail_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['mail_id'])))) : NULL;

			if($id!=NULL)
			{
				$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
				$this->dispatcher->Core->SQL->delete(array('table' => 'mails_for', 'conditions' => array('mail' => $id, 'user' => $user->id)));

				$mails = $this->dispatcher->Core->SQL->select(array('table' => 'mails_for', 'conditions' => array('mail' => $id, 'user' => $user->id)));
				if($mails == false)
					$this->dispatcher->Core->SQL->delete(array('table' => 'mails', 'conditions' => 'id='.$id));
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		parent::handlePost();
	}

	public function getGrades()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		return $this->dispatcher->Core->SQL->select(array('table' => 'grades', 'conditions' => array('teacher' => $user->id)));	
	}

	public function getProfReceivers()
	{
		$grades = $this->getGrades();
		$receivers = array();
		for($i=0;$i < count($grades); $i++)
		{
			$receivers[$grades[$i]->grade] = $this->dispatcher->Core->SQL->select(array('table' => 'users', 'conditions' => array('grade' => $grades[$i]->id)));
		}
		return $receivers;
	}

	public function getStudentReceivers()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		$grade = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => array('id' => $user->grade)));
		
		$receivers = array();
		$receivers["Professeur"] = $this->dispatcher->Core->SQL->select(array('table' => 'users','conditions' => array('id' => $grade->teacher)));
		$receivers[$grade->grade] = $this->dispatcher->Core->SQL->select(array('table' => 'users', 'conditions' => array('grade' => $grade->id)));

		return $receivers;
	}

	public function getEmails()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		$request = "SELECT * FROM mails WHERE id IN (SELECT mail FROM mails_for WHERE user=".$user->id.") ORDER BY(sended_at) DESC;";
		return $this->dispatcher->Core->SQL->query($request);
	}

	public function getUser($id)
	{
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => 'id='.$id));
	}
}
?>