<?php
class SgbdModel extends Model
{
	public function __construct($dispatcher)
	{
		parent::__construct($dispatcher);
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_sgbd_custom']))
		{
			$request = $_POST['custom_request'];
			
			$query = $this->dispatcher->Core->SQL->query($request);
			$response = $query === true ? '<strong>Request : </strong>'.$request.'<br /><strong>Response : </strong>Modifications appliquées avec succès' : $query;

			$_POST['sgbd_log'] = $response;
		}
		else if(isset($_POST['submit_sgbd_select']))
		{
			$request = "SELECT ".$_POST['sgbd_select_0']." FROM ".$_POST['sgbd_select_1']." WHERE ".$_POST['sgbd_select_2'].";";

			$query = $this->dispatcher->Core->SQL->query($request);
			$response = $query === true ? '<strong>Request : </strong>'.$request.'<br /><strong>Response : </strong>Sélection effectuée avec succès.' : $query;

			$_POST['sgbd_log'] = $response;
		}
		else if(isset($_POST['submit_sgbd_update']))
		{
			$request = "UPDATE ".$_POST['sgbd_update_0']." SET ".$_POST['sgbd_update_1']." WHERE ".$_POST['sgbd_update_2'].";";

			$query = $this->dispatcher->Core->SQL->query($request);
			$response = $query === true ? '<strong>Request : </strong>'.$request.'<br /><strong>Response : </strong>Mise à jour effectuée avec succès.' : $query;

			$_POST['sgbd_log'] = $response;
		}
		else if(isset($_POST['submit_sgbd_insert']))
		{
			$request = "INSERT INTO ".$_POST['sgbd_insert_0']." VALUES(".$_POST['sgbd_insert_1'].");";

			$query = $this->dispatcher->Core->SQL->query($request);
			$response = $query === true ? '<strong>Request : </strong>'.$request.'<br /><strong>Response : </strong>Insertion effectuée avec succès.' : $query;

			$_POST['sgbd_log'] = $response;
		}
		
		parent::handlePost();
	}
}
?>