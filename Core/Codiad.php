<?php
/**
 * Interface permettant de lier le système de sessions/comptes utilisateurs du site avec celui de Codiad Web IDE.
 *
 * Fichiers Codiad modifiés :
 *	config.php
 *	common.php
 *	components/right_bar.json
 *	components/editor/init.js
 *	components/autocomplete/init.js
 */
class CodiadInterface
{
	//Gestion des utilisateurs
	/**
	 * Enregistrement des valeurs nécessaires à l'authentification de l'utilisateur Codiad.
	 */
	public static function CheckAuth()
	{
		$db = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Echec de la connexion a la base de donnees');
		@mysqli_select_db($db, DB_BASE) or die('Echec de la connexion a la base de donnees');
		
		$query = mysqli_query($db, "SELECT codiad, maintenance FROM config;");
		$row = mysqli_fetch_array($query);
		$codiad = $row[0];
		$maintenance = $row[1];
		
		mysqli_close($db);
		
		if($maintenance != 0)
		{
			session_unset();
			session_destroy();
			setcookie(session_name(), "", time()-1);
			header('Location: '.dirname(WEB_ROOT));
		}
		else if(!isset($_SESSION['email']) || ($_SESSION['role'] == "role_student" && $codiad < 2) || ($_SESSION['role'] == "role_professor" && $codiad < 1))
			header('Location: '.dirname(WEB_ROOT));
		else
		{
			$_SESSION['user'] = $_SESSION['email'];
			$_SESSION['lang'] = 'fr';
			$_SESSION['theme'] = 'default';
		}
	}

	/**
	 * Création d'un compte utilisateur Codiad.
	 * @param $username
	 * @param $password
	 */
    public function createAccount($username, $password)
    {
		$users = $this->getJSON('users.php');
		$password = sha1(md5($password));
		$users[] = array("username"=>$username,"password"=>$password,"project"=>"");
		$this->saveJSON('users.php', $users);
		$this->setProjects($username, array(""));
    }
    
	/**
	 * Suppression d'un compte utilisateur Codiad.
	 * @param $username
	 */
    public function deleteAccount($username)
    {
		//Suppression de l'utilisateur
		$users = $this->getJSON('users.php');
        $revised_array = array();
		foreach($users as $user => $data)
		{
            if($data['username'] != $username)
                $revised_array[] = array("username"=>$data['username'],"password"=>$data['password'],"project"=>$data['project']);
        }
		$this->saveJSON('users.php', $revised_array);
		
		//Suppression des données d'activité
		$actives = $this->getJSON('active.php');
		foreach($actives as $active => $data)
		{
            if($username == $data['username'])
                unset($actives[$active]);
        }
        $this->saveJSON('active.php', $actives);

        //Suppression du fichier de contrôle d'accès s'il existe
        if(file_exists(CODIAD_DATA.$username.'_acl.php'))
            unlink(CODIAD_DATA.$username.'_acl.php');
    }
    
	/**
	 * Modification du mot de passe de l'utilisateur Codiad.
	 * @param $username
	 * @param $password
	 */
    public function updatePassword($username, $password)
    {
		$users = $this->getJSON('users.php');
        $revised_array = array();
		$password = sha1(md5($password));
        foreach($users as $user => $data)
		{
            if($data['username'] == $username)
                $revised_array[] = array("username"=>$data['username'],"password"=>$password);
			else
                $revised_array[] = array("username"=>$data['username'],"password"=>$data['password'],"project"=>$data['project']);
        }
        $this->saveJSON('users.php', $revised_array);
    }
	
	//Gestion des projets
	/**
	 * Définition des droits d'accès aux projets pour un utilisateur donné.
	 * @param $username
	 * @param $projects
	 */
    public function setProjects($username, $projects)
    {
		//L'utilisateur a accès à tous les projets
		if($projects === false)
		{
            if(file_exists(CODIAD_DATA.$username.'_acl.php'))
                unlink(CODIAD_DATA.$username.'_acl.php');
        }
        //L'utilisateur a un accès restreint aux projets
		else if(is_array($projects))
            $this->saveJSON($username.'_acl.php', $projects);
    }
	
	/**
	 * Ajoute un projet à la liste des projets actifs d'un utilisateur.
	 */
	public function addProject($username, $project)
	{
		$projects = $this->getJSON($username.'_acl.php');
		$projects[] = $project;
		$this->saveJSON($username.'_acl.php', $projects);
	}
	
	/**
	 * Retire un projet à la liste des projets actifs d'un utilisateur.
	 */
	public function removeProject($username, $project)
	{
		$projects = $this->getJSON($username.'_acl.php');
		for($i=0 ; $i < count($projects) ; $i++)
		{
			if($projects[$i] == $project)
				unset($project[$i]);
		}
		$this->saveJSON($username.'_acl.php', $projects);
	}
	
	/**
	 * Création d'un nouveau projet Codiad et du répertoire lié.
	 * @param $name
	 * @param $path
	 */
	public function createProject($name, $path)
	{
		$projects = $this->getJSON('projects.php');
		$projects[] = array("name"=>$name,"path"=>$path);
		$this->saveJSON('projects.php', $projects);
		if(!file_exists(CODIAD_WORKSPACE.$path))
			mkdir(CODIAD_WORKSPACE.$path, 0755, true);
	}
	
	/** 
	 * Suppression d'un projet Codiad et de son répertoire lié en fonction de son l'emplacement.
	 * @param $path
	 */
	public function deleteProject($path)
	{
		$projects = $this->getJSON('projects.php');
		$revised_array = array();
		foreach($projects as $project => $data)
		{
            if($data['path'] != $path)
                $revised_array[] = array("name"=>$data['name'],"path"=>$data['path']);
        }
		$this->saveJSON('projects.php', $revised_array);
		
		if(file_exists(CODIAD_WORKSPACE.$path) && is_dir(CODIAD_WORKSPACE.$path))
			rmdir(CODIAD_WORKSPACE.$path);
	}
    
    //Gestion des données JSON
	/**
	 * Récupération des données JSON de Codiad.
	 * @param file
	 * @return $json
	 */
    private function getJSON($file)
    {
        $path = CODIAD_DATA;
        $json = file_get_contents($path . $file);
        $json = str_replace("|*/?>","",str_replace("<?php/*|","",$json));
        $json = json_decode($json,true);
        return $json;
    }
    
	/** 
	 * Enregistrement des données dans un fichier JSON de Codiad.
	 * @param $file
	 * @param data
	 */
    public function saveJSON($file, $data)
    {
        $path = CODIAD_DATA;
        $data = "<?php/*|" . json_encode($data) . "|*/?>";
        $write = fopen($path . $file, 'w') or die("can't open file ".$path.$file);
        fwrite($write, $data);
        fclose($write);
    }
}
?>