<?php
class FAQModel extends Model
{
	public function __construct($dispatcher)
	{
		$role = $dispatcher->Core->User->getRole();
		$config = $dispatcher->Core->Config->faq;
		if(($role == "role_student" && $config != 2) || ($role == "role_professor" && $config < 1))
			$dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/home');

		
		parent::__construct($dispatcher);
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_faq_create']))
		{
			$question = isset($_POST['question_new']) ? $this->dispatcher->Core->SQL->secure($_POST['question_new']) : NULL;
			$answer = isset($_POST['answer_new']) ? $this->dispatcher->Core->SQL->secure($_POST['answer_new']) : NULL;
			$grades = $this->getGrades();

			if($question !=NULL && $answer!=NULL && !empty($grades)){
				$this->dispatcher->Core->SQL->insert(array('table' => 'faq', 'values' => array(NULL, $question, $answer)));
				$id = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'faq','conditions' => array('question' => $question)));

				$values = array(array());
				for($i=0; $i < count($grades); $i++){
					$values[$i][0] = $id->id;
					$values[$i][1] = $grades[$i]->id;
				}
				
				$this->dispatcher->Core->SQL->insert(array('table' => 'faq_for', 'values' => $values));

				$_POST['success_log'] = 'Votre question a bien été créée.';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
		}
		else if(isset($_POST['submit_faq_edit']))
		{
			$id = isset($_POST['faq_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['faq_id'])))) : NULL;
			$question = isset($_POST['question_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['question_edit']) : NULL;
			$answer =  isset($_POST['answer_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['answer_edit']) : NULL;
			if($id!=NULL && $question !=NULL && $answer!=NULL){
				$this->dispatcher->Core->SQL->update(array('table' => 'faq', 'columns' => array('question', 'answer'), 'values' => array($question,$answer), 'conditions' => array('id' => $id)));
				
				$_POST['success_log'] = 'Votre question a bien été modifiée.';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
		}
		else if(isset($_POST['submit_del_faq']))
		{
			$id = isset($_POST['faq_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['faq_id'])))) : NULL;
			if($id!=NULL){
				$query = $this->dispatcher->Core->SQL->delete(array('table' => 'faq', 'conditions' => 'id='.$id));
				$_POST['success_log'] = 'Votre question a bien été supprimée.';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
		}

		parent::handlePost();
	}

	public function getGrades()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		return $this->dispatcher->Core->SQL->select(array('table' => 'grades', 'conditions' => array('teacher' => $user->id)));	
	}

	public function getProfFAQ()
	{
		
		$grades = $this->getGrades();
		if($grades != false)
		{
			$conditions = "WHERE ";
			for($i=0; $i < count($grades); $i++){
				$conditions .= "grade=".$grades[$i]->id;
				if($i < count($grades)-1)
					$conditions .= " OR ";
			}

			$request = "SELECT * FROM faq WHERE id IN(SELECT faq FROM faq_for ".$conditions.");";
			return $this->dispatcher->Core->SQL->query($request);
		}
	}

	public function getFAQ()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));

		$request = "SELECT * FROM faq WHERE id IN(SELECT faq FROM faq_for WHERE grade=".$user->grade.");";
		return $this->dispatcher->Core->SQL->query($request);
	}
}
?>