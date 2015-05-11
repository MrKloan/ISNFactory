<?php
class ForgotModel extends Model
{
	public $token = NULL;

	public function __construct($dispatcher)
	{
		$this->dispatcher = $dispatcher;
		$this->checkToken();
		$this->handlePost();

		parent::__construct($dispatcher);
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_new_pwd']))
		{
			if(!empty($_POST['password']) && !empty($_POST['passconf']))
			{
				$password = sha1(md5(SALT.$_POST['password'].PEPPER));
				$passconf = sha1(md5(SALT.$_POST['passconf'].PEPPER));
				if($password == $passconf)
				{
					$this->dispatcher->Core->SQL->update(array('table' => 'users', 'columns' => 'password', 'values' => $password, 'conditions' => 'id='.$this->token->user));
					$this->dispatcher->Core->SQL->delete(array('table' => 'forgot', 'conditions' => 'token='.$this->token->token));
					$this->dispatcher->redirect(WEB_ROOT);
				}
			}
			else
    			$_POST['error_log'] = "Les mots de passe ne correspondent pas.";
		}

		parent::handlePost();
	}

	public function checkToken()
	{
		$token = $this->dispatcher->view;
		if($token != NULL)
		{
			$data = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'forgot', 'conditions' => array('token' => $token)));
			if($data != false)
			{
				//Si la requête a plus d'une journée, on la supprime
				if(strtotime(date('Y-m-d H:i:s')) - strtotime($data->date) > 60*60*24)
					$this->dispatcher->Core->SQL->delete(array('table' => 'forgot', 'conditions' => 'token='.$token));
				else
				{
					$this->token = $data;
					return true;
				}
			}
		}
		$this->dispatcher->redirect(WEB_ROOT);
	}
}
?>