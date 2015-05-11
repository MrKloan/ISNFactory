<?php
/**
 *
 */
class ForgotController extends Controller
{
	public function __construct($dispatcher)
	{
		if($dispatcher->Core->User->getRole() !== false)
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
		else
		{
			$this->dispatcher = $dispatcher;
			$this->loadModel($dispatcher->controller);
			$file = VIEWS.$dispatcher->controller.'.php';

			require_once(VIEWS.'header.php');
			require_once($file);
			require_once(VIEWS.'footer.php');
		}
	}
}
?>