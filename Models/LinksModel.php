<?php
class LinksModel extends Model
{
	public function __construct($dispatcher)
	{
		parent::__construct($dispatcher);
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_links_create']))
		{
			$title = isset($_POST['title_create']) ? $this->dispatcher->Core->SQL->secure($_POST['title_create']) : NULL;
			$description = isset($_POST['description_create']) ? $this->dispatcher->Core->SQL->secure($_POST['description_create']) : NULL;
			$url = isset($_POST['url_create']) ? $this->dispatcher->Core->SQL->secure($_POST['url_create']) : NULL;
			$grades = $this->getGrades();
			
			if($title!=NULL && $description!=NULL && $url!=NULL && !empty($grades))
			{
				$this->dispatcher->Core->SQL->insert(array('table' => 'links', 'values' => array(NULL, $title, $description, $url)));
				$id = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'links','conditions' => array('url' => $url)));

				$values = array(array());
				for($i=0; $i < count($grades); $i++){
					$values[$i][0] = $id->id;
					$values[$i][1] = $grades[$i]->id;
				}

				$this->dispatcher->Core->SQL->insert(array('table' => 'links_for', 'values' => $values));

				$_POST['success_log'] = 'Votre lien a bien été créé.';
            }
            else
            	$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';

		}
		else if(isset($_POST['submit_links_edit']))
		{
			$id = isset($_POST['links_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['links_id'])))) : NULL;
			$title = isset($_POST['title_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['title_edit']) : NULL;
			$description = isset($_POST['description_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['description_edit']) : NULL;
			$url = isset($_POST['url_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['url_edit']) : NULL;
			
			if($id!=NULL && $title!=NULL && $description!=NULL && $url!=NULL){
				$this->dispatcher->Core->SQL->update(array('table' => 'links', 'columns' => array('title', 'description', 'url'), 'values' => array($title,$description,$url), 'conditions' => array('id' => $id)));
				$_POST['success_log'] = 'Votre lien a bien été modifié.';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
		}
		
        else if(isset($_POST['submit_del_links']))
        {
            $id = isset($_POST['link_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['link_id'])))) : NULL;
            
            if($id!=NULL){
                $query = $this->dispatcher->Core->SQL->delete(array('table' => 'links', 'conditions' => 'id='.$id));
                $_POST['success_log'] = 'Votre lien a bien été supprimé.';
            }
            else
            	$_POST['error_log'] = 'Certaines données de traitements sont manquantes.';
        }
		
		parent::handlePost();
	}

	public function getGrades()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		return $this->dispatcher->Core->SQL->select(array('table' => 'grades', 'conditions' => array('teacher' => $user->id)));	
	}

	public function getProfLinks()
	{
		
		$grades = $this->getGrades();
		if($grades != false)
		{
			$conditions = "WHERE ";
			for($i=0; $i < count($grades); $i++){
				$conditions .= "grade=".$grades[$i]->id;
				if($i < count($grades)-1)
					$conditions .= " OR ";
			}


			$request = "SELECT * FROM links WHERE id IN(SELECT link FROM links_for ".$conditions.");";
			return $this->dispatcher->Core->SQL->query($request);
		}
	}

	public function getLinks()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
        $request = "SELECT * FROM links WHERE id IN (SELECT link FROM links_for WHERE grade=".$user->grade.");";
        
        return $this->dispatcher->Core->SQL->query($request);
	}
}
?>