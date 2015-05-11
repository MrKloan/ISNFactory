<?php
/**
 * Contrôleur utilisé pour la page d'accueil du site.
 * Override du constructeur de la classe Controller pour rediriger correctement l'utilisateur sans prendre en compte les paramètres de l'url.
 */
class IndexController extends Controller
{
	public function __construct($dispatcher)
	{
		if($dispatcher->Core->User->getRole() != NULL)
		{
			switch($dispatcher->Core->User->getRole())
			{
				case "role_professor":
					$dispatcher->redirect(WEB_ROOT.'/extranet/professor/home');
					break;
				case "role_admin":
					$dispatcher->redirect(WEB_ROOT.'/extranet/admin/home');
					break;
				case "role_student":
					$dispatcher->redirect(WEB_ROOT.'/extranet/student/home');
					break;
				default: 
					$dispatcher->redirect(WEB_ROOT);
			}
		}
		else if($dispatcher->action == 'index')
		{
			$this->dispatcher = $dispatcher;
			$this->loadModel($dispatcher->controller);
			$file = VIEWS.$dispatcher->controller.'.php';

			require_once(CORE.'recaptchalib.php');
			require_once(VIEWS.'header.php');
			require_once($file);
			require_once(VIEWS.'footer.php');
		}
		else
			$dispatcher->redirect(WEB_ROOT);
	}
}
?>