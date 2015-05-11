<?php
//Inclusions
require_once('config.php');
require_once('MVC.php');
require_once('User.php');
require_once('SQL.php');
require_once('Codiad.php');


/**
 * Classe principale initialisant les différents objets.
 */
class Core
{
	public $SQL = NULL;
	public $User = NULL;
	public $Dispatcher = NULL;
	public $Codiad = NULL;
	public $Config = NULL;

	public function __construct()
	{
    	date_default_timezone_set('Europe/Paris');
		$this->SQL = new SQL();
		$this->Config = $this->SQL->selectFirst(array('table' => 'config', 'conditions' => 'id='.USE_CONFIG));
		$this->User = new User($this);
		$this->Codiad = new CodiadInterface();
		$this->Dispatcher = new Dispatcher($this);
	}
}
?>