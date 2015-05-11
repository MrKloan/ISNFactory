<?php
class IndexModel extends Model
{
	public function __construct($dispatcher)
	{
		parent::__construct($dispatcher);
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_login']))
		{
			$email = $this->dispatcher->Core->SQL->secure($_POST['email']);
			$password = sha1(md5(SALT.$_POST['password'].PEPPER));

			if($this->dispatcher->Core->User->connect($email, $password))
			{
				//Redirection de l'utilisateur
				switch($user->role)
				{
					case "role_professor":
						$this->dispatcher->redirect(WEB_ROOT.'/extranet/professor/home');
						break;
					case "role_admin":
						$this->dispatcher->redirect(WEB_ROOT.'/extranet/admin/home');
						break;
					default:
						$this->dispatcher->redirect(WEB_ROOT.'/extranet/student/home');
						break;
				}
			}
		}
		else if(isset($_POST['submit_signin']))
		{

			require_once(CORE.'recaptchalib.php');

			$resp = recaptcha_check_answer(PRIVATE_KEY,
			                               $_SERVER["REMOTE_ADDR"],
			                               $_POST["recaptcha_challenge_field"],
			                               $_POST["recaptcha_response_field"]);
			
			if($resp->is_valid)
			{
				$firstname = isset($_POST['firstname']) ? $this->dispatcher->Core->SQL->secure($_POST['firstname']) : NULL;
				$lastname = isset($_POST['lastname']) ? $this->dispatcher->Core->SQL->secure($_POST['lastname']) : NULL;
				$email = isset($_POST['email']) ? $this->dispatcher->Core->SQL->secure($_POST['email']) : NULL;
				$password = isset($_POST['password']) ? sha1(md5(SALT.$_POST['password'].PEPPER)) : NULL;
				$passconf = isset($_POST['passconf']) ? sha1(md5(SALT.$_POST['passconf'].PEPPER)) : NULL;
				$grade = isset($_POST['grade']) ? $this->dispatcher->Core->SQL->secure($_POST['grade']) : NULL;

				if($this->dispatcher->Core->User->register($firstname, $lastname, $email, $password, $passconf, $grade, "role_student", 0))
				{
					$grades = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'grades', 'conditions' => 'id='.$grade));
					$this->dispatcher->Core->SQL->insert(array('table' => 'notifications', 'values' => array(NULL,"Inscription", $firstname." ".$lastname." souhaite s'inscrire en ".$grades->grade, $grade)));

					$_POST['success_log'] = "Votre compte a bien été créé ! Il doit maintenant être validé par un professeur pour devenir accessible.";
				}
			}
			else
				$_POST['error_log'] = "Le captcha n'a pas été correctement saisi.";
		}
		else if(isset($_POST['submit_forgot_pwd']))
		{
			$email = isset($_POST['forgot_email']) ? $this->dispatcher->Core->SQL->secure($_POST['forgot_email']) : NULL;

			if($email != NULL)
			{
				$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $email)));
				if($user !== false)
				{
					$request = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'forgot', 'conditions' => array('user' => $user->id)));
				   
				    if($request != false)
						$this->dispatcher->Core->SQL->delete(array('table' => 'forgot', 'conditions' => array('user' => $user->id)));
					//On génère un nouveau token
					$token = "";
					$values = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					for($i=0 ; $i < 255 ; $i++)
						$token .= $values[rand(0, strlen($values)-1)];
					$this->dispatcher->Core->SQL->insert(array('table' => 'forgot', 'values' => array($user->id, $token, 'NOW()')));

					//On envoie un email à l'utilisateur
					ini_set('SMTP', 'smtp.free.fr');
					ini_set('smtp_port', 587);
					ini_set('sendmail_from', 'isnfactory@free.fr');

					$header = "Content-Type: text/plain;charset=utf-8";
					mail($email, "ISN Factory - Modifier le mot de passe", "Bonjour,\n\nUne requête de réinitialisation de mot de passe a été effectuée pour votre compte ISNFactory.fr. Pour modifier votre mot de passe, rendez-vous sur : http://isnfactory.fr/extranet/forgot/".$token."\n\nSi vous n'êtes pas à l'origine de cette réinitialisation, vous pouvez ne pas tenir compte de cet e-mail.\n\nCet email est un envoi automatique, merci de ne pas y répondre.", $header);
				
					$_POST['success_log'] = "Un e-mail de réinitialisation vient d'être envoyé à l'adresse spécifiée.";
				}
				else
					$_POST['error_log'] = "Aucun utilisateur associé à cette adresse n'a été trouvé.";
			}
			else
				$_POST['error_log'] = "Veuillez spécifier une adresse e-mail valide.";
		}
		parent::handlePost();
	}

	public function getGrades()
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'grades', 'conditions' => 'allow_register=1'));
	}
}
?>