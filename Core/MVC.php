<?php
/**
 * Le Dispatcher est la classe principale de la MVC.
 * Il gère la relation entre le Contrôleur, les Modèles et la Vue.
 */
class Dispatcher
{
	public $Core = NULL;

	private $url = NULL;
	public $action = NULL;
	public $controller = NULL;
	public $view = NULL;
	public $params = NULL;
	public $model = NULL;

	public function __construct($core)
	{
		$this->Core = $core;
		
		$this->parseUrl();
		$this->loadController();
	}

	/**
	 * Fractionne l'URL et enregistre les informations dans les variables du Dispatcher.
	 */
	private function parseUrl()
	{
		$this->url = str_replace(WEB_ROOT, '', $_SERVER['REQUEST_URI']);
		$url = trim($this->url, '/');
		$params = explode('/', $url);
		$this->action = !empty($params[0]) ? $params[0] : "index";
		$this->controller = isset($params[1]) ? $params[1] : "index";
		$this->view = isset($params[2]) ? $params[2] : NULL;
		$this->params = array_slice($params, 3);
	}

	/**
	 * Inclut le fichier Contrôleur requis. Si la page chargée n'appartient ni à la catégorie ('action') accueil ou extranet, l'utilisateur est redirigé vers la page 404.
	 */
	private function loadController()
	{
		if($this->action != "index" && $this->action != "extranet")
			$this->error404();

		$name = ucfirst($this->controller).'Controller';
		$file = CONTROLLERS.$name.'.php';
		
		if(file_exists($file))
		{
			require_once($file);
			new $name($this);
		}
		else
		{
			if(DEBUG)
				die($file);
			else
				$this->error404();
		}
	}

	/**
     * Envoie le header HTTP 301 Moved Permanently puis redirige l'utilisateur vers l'URL indiquée et cesse l'exécution du script.
     */
    public function redirect($url)
    {
    	$url = empty($url) ? '/' : $url;

        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$url);
        die();
    }

	/**
	 * En cas d'erreur 404, envoie les headers HTTP nécessaires, inclut le bon fichier et cesse l'exécution du script.
	 */
    public function error404()
    {
    	if(DEBUG)
    	{
	        header("HTTP/1.0 404 Not Found");
	        header("Status: 404 Not Found");
			require_once(VIEWS.'404.php');
			die();
		}
		else
			$this->redirect(WEB_ROOT);
    }

    public function getError()
    {
    	if(isset($_POST['error_log']))
    		echo('<div id="log-popup" class="alert alert-danger">
                    <a class="close">×</a>
                    <div>'.$_POST['error_log'].'</div>
                  </div>');
    }

    public function getSuccess()
    {
    	if(isset($_POST['success_log']))
    		echo('<div id="log-popup" class="alert alert-success">
                    <a class="close">×</a>
                    <div>'.$_POST['success_log'].'</div>
                  </div>');
    }
}

/**
 * Classe parente de tous les contrôleurs.
 * Charge le Modèle et la Vue requis.
 */
class Controller
{
	protected $dispatcher = NULL;

	public function __construct($dispatcher)
	{
		$this->dispatcher = $dispatcher;
		
		if($this->dispatcher->action == "extranet" && !$this->dispatcher->Core->User->getRole())
			$this->dispatcher->redirect(WEB_ROOT);
		else if($this->dispatcher->view == NULL)
			$this->dispatcher->redirect(WEB_ROOT.'/'.$this->dispatcher->action.'/'.$this->dispatcher->controller.'/home');

		$this->loadModel($this->dispatcher->view);
		$this->loadView();
	}

	/**
	 * Charge le Modèle requis pour la Vue appelée par l'utilisateur.
	 * @param $model Le nom du model à inclure.
	 */
	protected function loadModel($model)
	{
		$name = ucfirst($model).'Model';
		$file = MODELS.$name.'.php';

		if(file_exists($file))
		{
			require_once($file);
			$this->dispatcher->model = new $name($this->dispatcher);
		}
		else
		{
			if(DEBUG)
				die('Model '.$name.' inexistant');
			else
				$this->dispatcher->error404();
		}
	}

	/**
	 * Charge la vue appelée par l'utilisateur via l'url.
	 */
	private function loadView()
	{
		$file = VIEWS.$this->dispatcher->controller.'/'.$this->dispatcher->view.'.php';

		if(file_exists($file))
		{
			require_once(VIEWS.'header.php');
			require_once($file);
			require_once(VIEWS.'footer.php');
		}
		else
		{
			if(DEBUG)
				die('Vue '.$this->dispatcher->view.' inexistante');
			else
				$this->dispatcher->error404();
		}
	}

	public function loadCSS()
	{	
		if($this->dispatcher->action == "index" || $this->dispatcher->action == "extranet" && $this->dispatcher->controller == 'forgot')
			echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"".STYLES.'css/style.css'."\"/>");
		else if($this->dispatcher->action == "extranet")
		{
			if(file_exists(VIEWS.'Styles/css/'.$this->dispatcher->controller.'/style.css'))
				echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"".STYLES.'css/'.$this->dispatcher->controller.'/style.css'."\"/>");
			if(file_exists(VIEWS.'Styles/css/'.$this->dispatcher->controller.'/'.$this->dispatcher->view.'.css'))
				echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"".STYLES.'css/'.$this->dispatcher->controller.'/'.$this->dispatcher->view.'.css'."\"/>");
		}
	}
}

/**
 * Classe parente de tous les Modèles.
 * Couche de liaison permettant aux Vues d'interagir avec la base de données.
 */
class Model
{
	protected $dispatcher = NULL;

	public function __construct($dispatcher)
	{
		$this->dispatcher = $dispatcher;
		$this->handlePost();
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_disconnect']))
			if($this->dispatcher->Core->User->disconnect())
		        $this->dispatcher->redirect(WEB_ROOT);
	}

	public function isTerminale()
	{
		$request = "SELECT SUBSTRING(grade, 1, INSTR(grade,' ')-1) as grade FROM grades WHERE id=".$this->dispatcher->Core->User->getGrade().";";
		$query = $this->dispatcher->Core->SQL->query($request);

		if($query[0]->grade == 'Terminale')
			return true;
		else
			return false;
	}
}
?>