<?php
class FinalModel extends Model
{
	public $param;

	public function __construct($dispatcher)
	{
		$role = $dispatcher->Core->User->getRole();
		$config = $dispatcher->Core->Config->projets;
		if(($role == "role_student" && $config != 2) || ($role == "role_professor" && $config < 1))
			$dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/home');

		parent::__construct($dispatcher);

        $this->param = $this->getParams();
	}

	public function handlePost()
	{
		if(isset($_POST['submit_create_final']))
		{
			$grades = isset($_POST['grades_final']) ? unserialize(base64_decode($_POST['grades_final'])) : NULL;
			$date_end = isset($_POST['date_end_final']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['date_end_final']))) : NULL;
			$group_size = isset($_POST['group_size']) ? $this->dispatcher->Core->SQL->secure($_POST['group_size']) : NULL;
			$codiad = isset($_POST['codiad_enable']) && $this->dispatcher->Core->Config->codiad ? '1' : '0';
			$todo = isset($_POST['todo_enable']) ? '1' : '0';
			$ftp = isset($_POST['ftp_enable']) && $this->dispatcher->Core->Config->ftp ? '1' : '0';

			if($grades != NULL && $date_end != NULL && $group_size != NULL)
			{
				$this->dispatcher->Core->SQL->insert(array('table' => 'projects', 'values' => array(NULL, '0', 'Projet Final', NULL, NULL, $group_size, $codiad, $todo, $ftp, '1', '1', NULL, NULL, $date_end)));
				$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => array('group_size' => $group_size, 'final_project' => '1', 'date_end' => $date_end)));

				$values = array(array());
				for($i=0; $i < count($grades); $i++)
				{
					$values[$i][0] = $project->id;
					$values[$i][1] = $grades[$i]->id;
					$values[$i][2] = 0;
				}
				$this->dispatcher->Core->SQL->insert(array('table' => 'projects_for', 'values' => $values));
				if(mkdir(FILES.'Projects/Final-'.$project->id, 0777, true))
					$_POST['success_log'] = 'Le projet a bien été créé.';
				else
					$_POST['error_log'] = 'Erreur lors de la céation du dossier';
				
			}
			else
				$_POST['error_log'] = 'Certaines données de traitement sont manquantes.';
		}
		else if(isset($_POST['submit_config_final']))
		{
			$id = isset($_POST['final_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['final_id'])))) : NULL;
			$grades = isset($_POST['grades_final']) ? unserialize(base64_decode($_POST['grades_final'])) : NULL;
			$date_end = isset($_POST['date_end_final']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['date_end_final']))) : NULL;
			$group_size = isset($_POST['group_size']) ? $this->dispatcher->Core->SQL->secure($_POST['group_size']) : NULL;
			$codiad = isset($_POST['codiad_enable']) && $this->dispatcher->Core->Config->codiad ? '1' : '0';
			$todo = isset($_POST['todo_enable']) ? '1' : '0';
			$ftp = isset($_POST['ftp_enable']) && $this->dispatcher->Core->Config->ftp ? '1' : '0';
			$dispo = isset($_POST['dispo_final']) ? '1' : '0';

			if($id != NULL && $date_end != NULL && $group_size != NULL)
			{
				$this->dispatcher->Core->SQL->update(array('table' => 'projects', 'columns' => array('group_size', 'codiad_enable', 'todo_enable', 'upload_enable', 'date_end'), 'values' => array($group_size, $codiad, $todo, $ftp, $date_end), 'conditions' => 'id='.$id));

				for($i=0; $i < count($grades); $i++)
					$this->dispatcher->Core->SQL->update(array('table' => 'projects_for', 'columns' => 'enabled', 'values' => $dispo, 'conditions' => 'grade='.$grades[$i]->id));
				$_POST['success_log'] = 'Mise à jour effectuée';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitement sont manquantes.';
		}
		else if(isset($_POST['submit_edit_desc']))
		{
			$id = isset($_POST['final_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['final_id'])))) : NULL;
			$description = isset($_POST['desc_final']) ? $this->dispatcher->Core->SQL->secure($_POST['desc_final']) : NULL;

			if($id != NULL && $description != NULL){
				$this->dispatcher->Core->SQL->update(array('table' => 'projects', 'columns' => 'description', 'values' => $description, 'conditions' => 'id='.$id));
				$_POST['success_log'] = 'Mise à jour effectuée';
			}
			else
				$_POST['error_log'] = 'Certaines données de traitement sont manquantes.';
		}
		else if(isset($_POST['submit_create_group']))
		{
			$id = isset($_POST['project_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['project_id'])))) : NULL;

			if($id != NULL)
			{
				$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
				
				$group = $this->getGroupByUserIdAndProject($user->id, $id);
				if($group == false)
				{
					$this->dispatcher->Core->SQL->insert(array('table' => 'groups', 'values' => array(NULL, $id, $user->id, '0')));
				}
				else
					$_POST['error_log'] = "Vous faites déjà parti d'un groupe pour ce projet.";
			}
		}
		else if(isset($_POST['submit_send_invitation']))
		{
			$group = isset($_POST['group_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['group_id'])))) : NULL;
			$user = isset($_POST['invited_user']) && !empty($_POST['invited_user']) ? $this->dispatcher->Core->SQL->secure($_POST['invited_user']) : NULL;

			if($group != NULL && $user != NULL)
			{
				$invitation = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'group_invitations', 'conditions' => array('`group`' => $group, '`to`' => $user)));

				if($invitation == false)
					$this->dispatcher->Core->SQL->insert(array('table' => 'group_invitations', 'values' => array(NULL, $group, $user)));
				else
					$_POST['error_log'] = "Une invitation a déjà été envoyée à cet utilisateur.";
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_cancel_invitation']))
		{
			$group = isset($_POST['group_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['group_id'])))) : NULL;
			$user = isset($_POST['user_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['user_id'])))) : NULL;

			if($group != NULL && $user != NULL)
			{
				$invitation = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'group_invitations', 'conditions' => array('`group`' => $group, '`to`' => $user)));

				if($invitation != false)
					$this->dispatcher->Core->SQL->delete(array('table' => 'group_invitations', 'conditions' => array('`group`' => $group, '`to`' => $user)));
				else
					$_POST['error_log'] = "Aucune invitation à ce nom n'a été trouvée.";
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_quit_group']))
		{
			$group = isset($_POST['group_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['group_id'])))) : NULL;
		
			if($group != NULL)
			{
				$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
				$membership = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'groups_members', 'conditions' => array('`group`' => $group, 'user' => $user->id)));
				if($membership != false)
					$this->dispatcher->Core->SQL->delete(array('table' => 'groups_members', 'conditions' => array('`group`' => $group, 'user' => $user->id)));
				else
					$_POST['error_log'] = "Vous n'êtes pas membre de ce groupe.";
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_fire_member']))
		{
			$group = isset($_POST['group_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['group_id'])))) : NULL;
			$user = isset($_POST['user_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['user_id'])))) : NULL;

			if($group != NULL && $user != NULL)
			{
				$membership = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'groups_members', 'conditions' => array('`group`' => $group, 'user' => $user)));
				if($membership != false)
					$this->dispatcher->Core->SQL->delete(array('table' => 'groups_members', 'conditions' => array('`group`' => $group, 'user' => $user)));
				else
					$_POST['error_log'] = "Cet utilisateur n'est pas membre de votre groupe.";
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_del_group']))
		{
			$group = isset($_POST['group_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['group_id'])))) : NULL;
		
			if($group != NULL)
			{
				$value = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'groups', 'conditions' => array('id' => $group)));
				if($value != false)
					$this->dispatcher->Core->SQL->delete(array('table' => 'groups', 'conditions' => array('id' => $group)));
				else
					$_POST['error_log'] = "Ce groupe n'existe pas.";
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_lock_group']))
		{
			$group = isset($_POST['group_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['group_id'])))) : NULL;
			$project = isset($_POST['project_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['project_id'])))) : NULL;
		
			if($group != NULL && $project != NULL)
			{
				$value = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'groups', 'conditions' => array('id' => $group)));
				if($value != false)
				{
					$this->dispatcher->Core->SQL->update(array('table' => 'groups', 'columns' => 'locked', 'values' => '1', 'conditions' => array('id' => $group)));
					
					//Initialisation du projet
					$prj = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => array('id' => $project)));
					$this->initProjectForGroup($value, $prj);
				}
				else
					$_POST['error_log'] = "Ce groupe n'existe pas.";
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_deny_invitation']))
		{
			$invitation = isset($_POST['invitation_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['invitation_id'])))) : NULL;

			if($invitation != NULL)
			{
				$value = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'group_invitations', 'conditions' => array('id' => $invitation)));
				if($value != false)
					$this->dispatcher->Core->SQL->delete(array('table' => 'group_invitations', 'conditions' => array('id' => $invitation)));
				else
					$_POST['error_log'] = "Cette invitation n'existe pas.";
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_accept_invitation']))
		{
			$invitation = isset($_POST['invitation_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['invitation_id'])))) : NULL;
			$project = isset($_POST['project_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['project_id'])))) : NULL;

			if($invitation != NULL && $project != NULL)
			{
				$value = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'group_invitations', 'conditions' => array('id' => $invitation)));
				if($value != false)
				{
					$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
					$group = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'groups', 'conditions' => array('id' => $value->group)));
					
					$this->dispatcher->Core->SQL->insert(array('table' => 'groups_members', 'values' => array($group->id, $user->id)));
					$this->dispatcher->Core->SQL->delete(array('table' => 'group_invitations', 'conditions' => array('id' => $invitation)));

					//Suppression de toutes les autres invitations pour ce projet
					$request = "SELECT * FROM group_invitations WHERE `to`=".$user->id." AND `group` IN(SELECT id FROM groups WHERE project=".$project.");";
					$invitations = $this->dispatcher->Core->SQL->query($request);

					if($invitations != false)
					{
						for($i=0 ; $i < count($invitations) ; $i++)
							$this->dispatcher->Core->SQL->delete(array('table' => 'group_invitations', 'conditions' => array('id' => $invitations[$i]->id)));
					}
				}
				else
					$_POST['error_log'] = "Cette invitation n'existe pas.";
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_edit_yourproject']))
		{
			$group = isset($_POST['yourproject']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['yourproject'])))) : NULL;
			$title = isset($_POST['title_final_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['title_final_edit']) : NULL;
			$description = isset($_POST['desc_final_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['desc_final_edit']) : NULL;
			$progress = isset($_POST['progress_final_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['progress_final_edit']) : NULL;
			$changelog = isset($_POST['changelog_final_edit']) ? $this->dispatcher->Core->SQL->secure($_POST['changelog_final_edit']) : NULL;

			if($group != NULL && $title != NULL && $description != NULL && $progress != NULL && $changelog != NULL)
			{
				if($_FILES['logo_final_edit'] && $_FILES['logo_final_edit']['error'] != 4)
				{
					$project = $this->getFinalProjectByGrade();
					if(file_exists(FILES."Projects/Final-".$project->id."/Groupe-".$group."/upload/"))
					{
						$directory = FILES."Projects/Final-".$project->id."/Groupe-".$group."/upload/";
						$taille = 2097152; //2Mo en octets
						$extension = array(".png",".jpg");
						$file = str_replace(substr($_FILES['logo_final_edit']['name'], 0, strpos($_FILES['logo_final_edit']['name'], ".")), "logo", $_FILES['logo_final_edit']['name']);

						if(filesize($_FILES['logo_final_edit']['tmp_name']) > $taille)
							return false;
						else if(!in_array(strrchr($_FILES['logo_final_edit']['name'], '.'), $extension))
							return false;
						else{
							if(move_uploaded_file($_FILES['logo_final_edit']['tmp_name'], $directory.$file))
							{
								$logo = base64_encode(str_replace(FILES,WEB_ROOT."/Files/", $directory).$file);
								$this->dispatcher->Core->SQL->update(array('table' => 'final_projects', 'columns' => array('title', 'logo', 'description', 'progress', 'changelog'), 'values' => array($title, $logo, $description, $progress, $changelog), 'conditions' => '`group`='.$group));
							}
							else
								$_POST['error_log'] = 'Erreur de copie du fichier image';
						}
					}
				}
				else
					$this->dispatcher->Core->SQL->update(array('table' => 'final_projects', 'columns' => array('title', 'description', 'progress', 'changelog'), 'values' => array($title, $description, $progress, $changelog), 'conditions' => '`group`='.$group));
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}
		else if(isset($_POST['submit_todo']))
		{
			$todo = isset($_POST['id_postit']) ? $_POST['id_postit'] : NULL;
			if($todo!=NULL)
			{
				for($i=0; $i < count($todo); $i++)
				{
					if(!isset($_POST['position_'.$todo[$i]]))
					{
						$_POST['error_log'] = "Post-It inexistant";
						break;
					}
					$position = explode('x', $_POST['position_'.$todo[$i]]);
					if($position[0] < 195 && $position[1] > 600)
						$this->dispatcher->Core->SQL->delete(array('table' => 'todo_lists', 'conditions' => array('id' => $todo[$i])));
					else
						$this->dispatcher->Core->SQL->update(array('table' => 'todo_lists', 'columns' => 'position', 'values' => $_POST['position_'.$todo[$i]], 'conditions' => 'id='.$todo[$i]));
				}
			}
			else
				$_POST['error_log'] = "Erreur de récupération de Post-It";
		}
		else if(isset($_POST['submit_new_todo']))
		{
			//$title = isset($_POST['title_new']) ? $this->dispatcher->Core->SQL->secure($_POST['title_new']) : NULL;
			$description = isset($_POST['desc_new']) ? $this->dispatcher->Core->SQL->secure($_POST['desc_new']) : NULL;
			$name = isset($_POST['student_new']) ? $this->dispatcher->Core->SQL->secure($_POST['student_new']) : NULL;
			$deadline = isset($_POST['date_new']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['date_new']))) : NULL;
			$color = isset($_POST['color_new']) ? $this->dispatcher->Core->SQL->secure($_POST['color_new']) : NULL;
			$group = isset($_POST['id_group']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['id_group'])))) : NULL;

			if(/*$title!=NULL &&*/ $description!=NULL && $name!=NULL && $deadline!=NULL && $color!=NULL && $group!=NULL)
			{
				$this->dispatcher->Core->SQL->insert(array('table' => 'todo_lists', 'values' => array(NULL, $group, NULL, $description, $name, $deadline, $color, "100x100x1")));
				$_POST['show_todo'] = '1';
			}
			else
				$_POST['error_log'] = "Veuillez remplir correctement le formulaire";
		}
		else if(isset($_POST['submit_del_postit']))
		{
			$id = isset($_POST['del_id_postit']) ? $this->dispatcher->Core->SQL->secure($_POST['del_id_postit']) : NULL;
			if($id!=NULL)
			{
				$this->dispatcher->Core->SQL->delete(array('table' => 'todo_lists', 'conditions' => 'id='.$id));
				$_POST['show_todo'] = '1';
			}
			else
				$_POST['error_log'] = "Impossible de supprimer le Post-It";

		}
		else if(isset($_POST['submit_upload_project_file']))
		{
			$ids = isset($_POST['upload_id']) ? explode("_", str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['upload_id']))))) : NULL;

			if($ids != NULL && !empty($ids[0]) && !empty($ids[1]))
			{
				if(isset($_FILES['file_project']) && $_FILES['file_project']['error'] != 4)
				{
					$path = FILES.'Projects/Final-'.$ids[0].'/Groupe-'.$ids[1].'/upload/';
				
					if(false !== ($ressource = opendir($path)))
					{
						while (false !== ($file = readdir($ressource)))
						{
							if($file != "." && $file != ".." && $file != "logo.png" && $file != "logo.jpg")
								unlink($path."/".$file);
						}
						closedir($ressource);
					}
					

					$max_size = 20971520; //20Mo en octets
					$ext = array(".zip",".rar");
					$file = basename($this->accents($_FILES['file_project']['name']));

					if(filesize($_FILES['file_project']['tmp_name']) > $max_size)
					{
						$_POST['error_log'] = "Le fichier est trop volumineux.";
						return false;
					}
					else if(!in_array(strrchr($_FILES['file_project']['name'], '.'), $ext))
					{
						$_POST['error_log'] = "Extension de fichier non autorisée.";
						return false;
					}
					else
					{
						if(move_uploaded_file($_FILES['file_project']['tmp_name'], $path.$file))
							return true;
						else
						{
							$_POST['error_log'] = "Échec de la mise en ligne.";
							return false;
						}
					}
				}
			}
		}
		parent::handlePost();
	}

	public function getTermGrades()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));

		$request = "SELECT * FROM grades WHERE teacher=".$user->id." AND SUBSTRING(grade, 1, INSTR(grade,' ')-1) = 'Terminale';";
		return $this->dispatcher->Core->SQL->query($request);
	}

	public function getFinalProject()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		$project = $this->dispatcher->Core->SQL->select(array('table' => 'projects', 'conditions' => 'final_project=1'));

		if(!$project)
			return false;
		else
		{
			for($i=0; $i<count($project); $i++){
				$request = "SELECT * FROM projects_for WHERE project=".$project[$i]->id." AND grade IN(SELECT id FROM grades WHERE teacher=".$user->id." AND SUBSTRING(grade, 1, INSTR(grade,' ')-1) = 'Terminale')";
				$query = $this->dispatcher->Core->SQL->query($request);
				if($query)
					return $project[$i];
			}
		}
	}

	public function getFinalProjectByGrade()
	{
		$request = "SELECT * FROM projects WHERE final_project='1' AND id IN(SELECT project FROM projects_for WHERE grade=".$this->dispatcher->Core->User->getGrade()." AND enabled='1');";
		$query = $this->dispatcher->Core->SQL->query($request);
		if($query)
			return $query[0];
		else
			return false;
	}

	public function getGroups($id)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'groups', 'conditions' => array('project' => $id, 'locked' => '1')));
	}

	public function getGroup($project)
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		$request = "SELECT * FROM groups WHERE project=".$project." AND (chief=".$user->id." OR id IN(SELECT `group` FROM groups_members WHERE user=".$user->id."));";
		
		$group = $this->dispatcher->Core->SQL->query($request);
		if($group == false)
			return false;
		else
			return $group[0];
	}

	public function getGroupChief($group_id)
	{
		$group = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'groups', 'conditions' => array('id' => $group_id)));
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('id' => $group->chief)));
	}

	public function getFinalProjectByGroup($id)
	{
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'final_projects', 'conditions' => '`group`='.$id));
	}

	public function getFinalProjectById($id)
	{
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'final_projects', 'conditions' => 'id='.$id));
	}

	public function getProjectsfor($grades,$id)
	{
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects_for', 'conditions' => array('project' => $id, 'grade' => $grades[0]->id)));
	}

	public function getUsersByGroup($group)
	{
		$request = "SELECT * FROM users WHERE id IN(SELECT chief FROM groups WHERE id=".$group.") OR id IN(SELECT user FROM groups_members WHERE `group`=".$group.");";
		return $this->dispatcher->Core->SQL->query($request);
	}

	public function getInvitationsByGroup($group)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'group_invitations', 'conditions' => array('`group`' => $group)));
	}

	public function getFreeUsersByGradeAndProject($grade, $project)
	{
		$request = "SELECT * FROM users WHERE grade=".$grade." AND (id NOT IN(SELECT chief FROM groups WHERE project=".$project.") AND id NOT IN(SELECT user FROM groups_members WHERE `group` IN(SELECT id FROM groups WHERE project=".$project.")));";
		return $this->dispatcher->Core->SQL->query($request);
	}

	public function getUserById($id)
	{
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('id' => $id)));
	}

	public function getInvitationsByUser($project)
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		$request = "SELECT * FROM group_invitations WHERE `to`=".$user->id." AND `group` IN(SELECT id FROM groups WHERE project=".$project.");";
		return $this->dispatcher->Core->SQL->query($request);
	}

	public function getGroupByUserIdAndProject($user, $project)
	{
		$request = "SELECT * FROM groups WHERE project=".$project." AND chief=".$user." OR id IN(SELECT `group` FROM groups_members WHERE user=".$user.");";
		
		$group = $this->dispatcher->Core->SQL->query($request);
		if($group == false)
			return false;
		else
			return $group[0];
	}

	private function initProjectForGroup($group, $project)
	{
		$path = FILES.'Projects/Final-'.$project->id.'/Groupe-'.$group->id.'/';
		mkdir($path, 0777, true);

		//Si l'upload est activé, on crée le dossier de destination
		if($project->upload_enable)
			mkdir($path.'upload/', 0777, true);

		//Si Codiad est activé, on crée le projet et on assigne les autorisations aux membres du groupe
		if($project->codiad_enable)
		{
			$name = 'Projet Final - Groupe '.$group->id;
			$codiad_path =  str_replace(FILES.'Projects/', '', $path).'Codiad/';
			$this->dispatcher->Core->Codiad->createProject($name, $codiad_path);

			$users = $this->getUsersByGroup($group->id);
			for($i=0 ; $i < count($users) ; $i++)
				$this->dispatcher->Core->Codiad->addProject($users[$i]->email, $codiad_path);

			//Activation du projet Codiad pour le prof
			if($this->dispatcher->Core->User->getRole() == "role_student")
				$request = "SELECT * FROM users WHERE id IN(SELECT teacher FROM grades WHERE id=".$this->dispatcher->Core->User->getGrade().");";
			else
				$request = "SELECT * FROM users WHERE email='".$this->dispatcher->Core->User->getEmail()."';";
			$prof = $this->dispatcher->Core->SQL->query($request);
			$this->dispatcher->Core->Codiad->addProject($prof->email, $codiad_path);
		}
		$this->dispatcher->Core->SQL->insert(array('table' => 'final_projects', 'values' => array(NULL, $group->id, NULL, NULL, NULL, '0', NULL)));
		$_POST['success_log'] = 'Vous pouvez commencer votre projet';
	}

	public function getParams()
	{
		$params = $this->dispatcher->params;

		if($params)
		{
			$param = $this->dispatcher->Core->SQL->secure($params[0]);
			$role = $this->dispatcher->Core->User->getRole();
			if(preg_match("(^[0-9]*$)", $param))
			{
				$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'final_projects', 'conditions' => 'id='.$param));
				if(!$project)
					$this->dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/final');
				else
				{
					$request = "SELECT grade FROM users WHERE id IN(SELECT chief FROM groups WHERE id IN(SELECT `group` FROM final_projects WHERE id=".$param."));";
					$temp = $this->dispatcher->Core->SQL->query($request);

					if($temp)
					{
						$array = array();
						for($i=0; $i<count($temp); $i++)
							array_push($array, $temp[$i]->grade);
						if($role == "role_professor")
						{
							$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
							$grades = $this->dispatcher->Core->SQL->select(array('table' => 'grades', 'conditions' => array('teacher' => $user->id)));
							if($grades != NULL && count($grades) > 0)
							{
								for($i=0 ; $i < count($grades) ; $i++)
									if(in_array($grades[$i]->id, $array))
										return $param;
							}
						}
						else
							if(in_array($this->dispatcher->Core->User->getGrade(), $array))
								return $param;
					}
					$this->dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/final');
				}
			}	
			else
				$this->dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/final'); 
		}
	}

	/**
     * Calcul d'une valeur numérique identifiant les modules présents sur la page.
     * @param $project
     * @return $value
     */
	public function getModulesValue($project)
	{
		$value = 0;

		if($project->todo_enable)
			$value += 4;
		if($project->codiad_enable)
			$value += 1;
		if($project->upload_enable)
			$value += 1;
		
		return $value;
	}

	/**
	 * Renvoie la todo list en fonction du groupe.
	 * @param $group
	 */
	public function getTodoList($group)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'todo_lists', 'conditions' => array('`group`' => $group)));
	}

	public function getFiles($project, $group)
	{				
		$path = FILES.'Projects/Final-'.$project.'/Groupe-'.$group.'/upload/';

		$files = array();
		if(false !== ($ressource = opendir($path)))
		{
			while(false !== ($data = readdir($ressource)))
			{
				if($data != "." && $data != "..")
					array_push($files, str_replace(FILES,WEB_ROOT."/Files/", $path)."/".$data);
			}
			closedir($ressource);
		}
		return $files;
	}

	/**
	 * Gestion des accents dans les noms de fichier.
	 * @param $str
	 * @param $charset='utf-8'
	 * @return $str
	 */
	private function accents($str, $charset='utf-8')
	{
	    $str = htmlentities($str, ENT_NOQUOTES, $charset);
	    
	    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	    
	    return $str;
	}
}
?>