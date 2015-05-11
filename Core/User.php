<?php
/**
 * Classe centralisant les informations relatives aux utilisateurs.
 */
class User
{
	private $Core = NULL;

	private $firstname = NULL;
	private $lastname = NULL;
	private $email = NULL;
	private $role = NULL;
	private $grade = NULL;
	private $lastLogin = NULL;
	private $lastIP = NULL;

	public function __construct($core)
	{
		$this->Core = $core;

		$this->startSession();
		if($this->Core->Config->maintenance == 0)
		{
			$this->firstname = (isset($_SESSION['firstname'])) ? $_SESSION['firstname'] : NULL;
			$this->lastname = (isset($_SESSION['lastname'])) ? $_SESSION['lastname'] : NULL;
			$this->email = (isset($_SESSION['email'])) ? $_SESSION['email'] : NULL;
			$this->role = (isset($_SESSION['role'])) ? $_SESSION['role'] : NULL;
			$this->grade = (isset($_SESSION['grade'])) ? $_SESSION['grade'] : NULL;
			$this->lastLogin = (isset($_SESSION['lastLogin'])) ? $_SESSION['lastLogin'] : NULL;
			$this->lastIP = (isset($_SESSION['lastIP'])) ? $_SESSION['lastIP'] : NULL;
		}
		else
			$this->disconnect();
	}

	public function register($firstname, $lastname, $email, $password, $passconf, $grade, $role, $validated)
	{
		if($this->Core->Config->maintenance != 0)
		{
			$_POST['error_log'] = "Le site est actuellement en maintenance.";
			return false;
		}
		else if(!$this->Core->Config->allow_register)
		{
			$_POST['error_log'] = "L'enregistrement est actuellement désactivé.";
			return false;
		}
		
		if($firstname!=NULL && $lastname!=NULL && $email!=NULL && $password!=NULL && $passconf!=NULL /*&& $grade!=NULL*/ && $role!=NULL && (string)$validated!=NULL)
		{
			$firstname = ucfirst(strtolower($firstname));
			$lastname = strtoupper($lastname);
			if(!mb_eregi("^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$", $email))
			{
				$_POST['error_log'] = "Veuillez saisir une adresse e-mail valide.";
				return false;
			}
			if($password != $passconf)
			{
				$_POST['error_log'] = "Les mots de passe ne correspondent pas.";
				return false;
			}

			$user = $this->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $email)));

			//Si l'utilisateur existe déjà
			if($user !== false)
			{
				$_POST['error_log'] = "Cette adresse e-mail est déjà utilisée.";
				return false;
			}
			else
			{
				if($role != "role_admin")
					$this->Core->Codiad->createAccount($email, $password);
				
				$this->Core->SQL->insert(array('table' => 'users', 'values' => array(NULL, $firstname, $lastname, $email, $password, $grade, $role, 'NOW()', NULL, NULL, '0', (string)$validated)));
				return true;
			}
		}
		else
		{
			$_POST['error_log'] = "Certaines données de traitement sont manquantes.";
			return false;
		}
	}

	/**
	 * Vérification des informations saisies par l'utilisateur lors de la connexion.
	 * Si elles sont exactes, ses informations sont mises à jour dans a BDD puis il est connecté et redirigé vers son home sur l'extranet. Sinon, renvoie false.
	 * @param $email L'adresse email de l'utilisateur, utilisée pour se connecter
	 * @param $password Le mot de passe de l'utilisateur
	 */
	public function connect($email, $password)
	{
		if($this->Core->Config->maintenance != 0)
		{
			$_POST['error_log'] = "Le site est actuellement en maintenance.";
			return false;
		}
		if(!mb_eregi("^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$", $email))
		{
			$_POST['error_log'] = "Veuillez saisir une adresse e-mail valide.";
			return false;
		}
		
		$shield = $this->Core->SQL->selectFirst(array('table' => 'login_security', 'conditions' => array('ip' => $_SERVER['REMOTE_ADDR'], 'account' => $email)));
		//Si il n'y a aucune entrée, elle est créée
		if($shield === false)
		{
			$this->Core->SQL->insert(array('table' => 'login_security', 'values' => array(NULL, $_SERVER['REMOTE_ADDR'], $email, '0', NULL)));	
			$shield = $this->Core->SQL->selectFirst(array('table' => 'login_security', 'conditions' => array('ip' => $_SERVER['REMOTE_ADDR'], 'account' => $email)));
		}
		//Si l'entrée existe mais que la connexion est bloquée, on interrompt la tentative de connexion
		else if($shield->blocked_at != NULL && strtotime(date('Y-m-d H:i:s')) - strtotime($shield->blocked_at) < strtotime('+'.$this->Core->Config->shield_duracy.' minutes', strtotime($shield->blocked_at)) - strtotime($shield->blocked_at))
		{
			$_POST['error_log'] = "De trop nombreuses tentatives de connexion ont été détéctées pour ce compte. Veuillez patienter avant de vous reconnecter.";
			return false;
		}
		//Si l'entrée existe, que l'utilisateur était bloqué mais que le temps d'attente est expiré, on met à jour la ligne
		else if($shield->blocked_at != NULL && strtotime(date('Y-m-d H:i:s')) - strtotime($shield->blocked_at) > strtotime('+'.$this->Core->Config->shield_duracy.' minutes', strtotime($shield->blocked_at)) - strtotime($shield->blocked_at))
			$this->Core->SQL->update(array('table' => 'login_security', 'columns' => array('attempts', 'blocked_at'), 'values' => array('0', NULL), 'conditions' => array('ip' => $_SERVER['REMOTE_ADDR'], 'account' => $email)));

		//On vérifie que l'utilisateur existe
		$user = $this->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $email, 'password' => $password)));
		
		if($user === false)
		{
			$shield->attempts++;
			$this->Core->SQL->update(array('table' => 'login_security', 'columns' => array('attempts'), 'values' => array($shield->attempts), 'conditions' => array('ip' => $_SERVER['REMOTE_ADDR'], 'account' => $email)));
			
			if($shield->attempts >= $this->Core->Config->shield_attempts)
			{
				$student = $this->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => 'email='.$email));
				if($student)
					$this->Core->SQL->insert(array('table' => 'notifications', 'values' => array(NULL, "Blocage", "L'adresse IP ".$shield->ip." a tentée de se connecter ".$this->Core->Config->shield_attempts." fois au compte de l'élève ".$student->firstname." ".$student->lastname.".", $student->grade)));

				$this->Core->SQL->update(array('table' => 'login_security', 'columns' => array('attempts', 'blocked_at'), 'values' => array('0', 'NOW()'), 'conditions' => array('ip' => $_SERVER['REMOTE_ADDR'], 'account' => $email)));
			}

			$_POST['error_log'] = "Identifiants incorrects.";
			return false;
		}
		else if(!$user->validated)
		{
			$_POST['error_log'] = "Votre compte n'a pas été validé.";
			return false;
		}
		else
		{
			//Mise à jour du shield
			$this->Core->SQL->update(array('table' => 'login_security', 'columns' => array('attempts', 'blocked_at'), 'values' => array('0', NULL), 'conditions' => array('ip' => $_SERVER['REMOTE_ADDR'], 'account' => $email)));

			//Mise à jour de l'utilisateur
			$user->nb_connection++;
			$this->Core->SQL->update(array('table' => 'users', 'columns' => array('last_login', 'last_ip', 'nb_connection'), 'values' => array('NOW()', $_SERVER['REMOTE_ADDR'], $user->nb_connection), 'conditions' => array('email' => $email, 'password' => $password)));
			$this->setUser($user->firstname, $user->lastname, $user->email, $user->role, $user->grade, $user->last_login, $user->last_ip);
			return true;
		}
	}

	/** 
     * Garbage collector & arrêt des sessions.
     */
    public function disconnect()
    {
        $this->unsetUser();
    	session_unset();
        session_destroy();
        setcookie(session_name(),"",time()-1);
        return true;
    }

    public function updatePassword($password, $passconf)
    {
    	if($password != $passconf)
    		return false;
    	else
    	{
    		$this->Core->Codiad->updatePassword($this->getEmail(), $password);
			$this->Core->SQL->update(array('table' => 'users', 'columns' => 'password', 'values' => $password, 'conditions' => 'email='.$this->getEmail()));
			return true;
		}
    }

    /**
     * Démarrage du système de sessions.
     */
	private function startSession()
    {
		session_name(SESSION_NAME);
        session_start();
    }

    /**
	 * Définit les variables d'instance et de session correspondant à l'utilisateur.
	 * @param $firstname Le prénom del'utilisateur
	 * @param $lastname Le nom de famille de l'utilisateur
	 * @param $email  L'adresse email de l'utilisateur
	 * @param $role Le role de l'utilisateur (étudiant, professeur, admin)
	 * @param $grade La classe de l'utilisateur
	 * @param $lastLogin La dernière date de connexion de l'utilisateur
	 * @param $lastIP La dernière adresse IP de l'utilisateur
	 */
    private function setUser($firstname, $lastname, $email, $role, $grade, $lastLogin, $lastIP)
    {
    	$this->firstname = $_SESSION['firstname'] = $firstname;
    	$this->lastname = $_SESSION['lastname'] = $lastname;
    	$this->email = $_SESSION['email'] = $email;
    	$this->role = $_SESSION['role'] = $role;
    	$this->lastLogin = $_SESSION['lastLogin'] = $lastLogin;
    	$this->lastIP = $_SESSION['lastIP'] = $lastIP;
    	$this->grade = $_SESSION['grade'] = ($this->role == "role_student") ? $grade : NULL;
    }

    /**
	 * Garbage collector des variables d'instance et de session correspondant à l'utilisateur.
	 */
    private function unsetUser()
    {
    	$this->firstname = $_SESSION['firstname'] = NULL;
    	$this->lastname = $_SESSION['lastname'] = NULL;
    	$this->email = $_SESSION['email'] = NULL;
    	$this->role = $_SESSION['role'] = NULL;
    	$this->lastLogin = $_SESSION['lastLogin'] = NULL;
    	$this->lastIP = $_SESSION['lastIP'] = NULL;
    	$this->grade = $_SESSION['grade'] = NULL;
    }

    /**
	 * @return le prénom de l'utilisateur si défini, false sinon.
	 */
	public function getFirstname()
	{
		if($this->firstname != NULL)
			return $this->firstname;
		else
			return false;
	}

	/**
	 * @return le nom de famille de l'utilisateur si défini, false sinon.
	 */
	public function getLastname()
	{
		if($this->lastname != NULL)
			return $this->lastname;
		else
			return false;
	}

	/**
	 * @return l'adresse email de l'utilisateur si défini, false sinon.
	 */
	public function getEmail()
	{
		if($this->email != NULL)
			return $this->email;
		else
			return false;
	}

	/**
	 * @return le rôle de l'utilisateur si défini, false sinon.
	 */
	public function getRole()
	{
		if($this->role != NULL)
			return $this->role;
		else
			return false;
	}

	/**
	 * @return la classe de l'utilisateur si définie, false sinon.
	 */
	public function getGrade()
	{
		if($this->grade != NULL)
			return $this->grade;
		else
			return false;
	}

	/**
	 * @return la dernièère date de connexion de l'utilisateur si définie, false sinon.
	 */
	public function getLastLogin()
	{
		if($this->lastLogin != NULL)
			return $this->lastLogin;
		else
			return false;
	}

	/**
	 * @return l'adresse IP utilisée lors de la dernière connexion à ce compte si définie, false sinon.
	 */
	public function getLastIP()
	{
		if($this->lastIP != NULL)
			return $this->lastIP;
		else
			return false;
	}
}
?>