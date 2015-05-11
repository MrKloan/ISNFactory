<?php
class ProfessorController extends Controller
{
	public function __construct($dispatcher)
	{
		$role = $dispatcher->Core->User->getRole();
		if($role != NULL && $role != "role_professor")
			$dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/home');
		parent::__construct($dispatcher);
	}
}
?>