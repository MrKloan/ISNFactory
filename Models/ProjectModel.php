<?php
class ProjectModel extends Model
{
	public function __construct($dispatcher)
	{
		$role = $dispatcher->Core->User->getRole();
		$config = $dispatcher->Core->Config->projets;
		if(($role == "role_student" && $config != 2) || ($role == "role_professor" && $config < 1))
			$dispatcher->redirect(WEB_ROOT.'/extranet/'.substr($role, strpos($role, '_')+1, strlen($role)-strpos($role, '_')+1).'/home');

		parent::__construct($dispatcher);	
	}

	protected function handlePost()
	{
		if(isset($_POST['submit_new_project']))
		{
			//Variables du projets
			$number = isset($_POST['new_number']) ? $this->dispatcher->Core->SQL->secure($_POST['new_number']) : NULL;
			$title = isset($_POST['new_title']) ? $this->dispatcher->Core->SQL->secure($_POST['new_title']) : NULL;
			$date_end = isset($_POST['new_date_end']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['new_date_end']))) : NULL;
			$coeff = isset($_POST['new_coeff']) ? $this->dispatcher->Core->SQL->secure($_POST['new_coeff']) : NULL;
			$group_type = isset($_POST['new_group_type']) ? $this->dispatcher->Core->SQL->secure($_POST['new_group_type']) : NULL;
			if($group_type == 1)
			{
				$group_size = 1;
				$date_group = NULL;
			}
			else
			{
				$group_size = isset($_POST['new_group_size']) ? $this->dispatcher->Core->SQL->secure($_POST['new_group_size']) : NULL;
				$date_group = isset($_POST['new_date_group']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['new_date_group']))) : NULL;
			}
			$codiad_enable = isset($_POST['new_codiad']) && $this->dispatcher->Core->Config->codiad ? '1' : '0';
			$todo_enable = isset($_POST['new_todo']) ? '1' : '0';
			$upload_enable = isset($_POST['new_ftp']) && $this->dispatcher->Core->Config->ftp ? '1' : '0';
			$description = isset($_POST['new_subject']) ? $this->dispatcher->Core->SQL->secure($_POST['new_subject']) : NULL;
			$grades = isset($_POST['grades_id']) ? unserialize(base64_decode($_POST['grades_id'])) : NULL;

			//Traitement
			if($number != NULL && $title != NULL && $date_end != NULL && $group_type != NULL && $description != NULL && is_array($grades))
			{

				$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => array('number' => $number, 'title' => $title)));
				if($project != false)
				{
					$_POST['error_log'] = "Le projet ".$number." : ".$title." existe déjà.";
					return;
				}
				else
				{
					//Insertions BDD
					$this->dispatcher->Core->SQL->insert(array('table' => 'projects', 'values' => array(NULL, $number, $title, $description, NULL, $group_size, $codiad_enable, $todo_enable, $upload_enable, '0', '0', 'NOW()', $date_group, $date_end)));
					$this->dispatcher->Core->SQL->insert(array('table' => 'works', 'values' => array(NULL, $title, 'projet', $date_end, $coeff)));
					$work = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'works', 'conditions' => array('title' => $title, 'type' => 'projet', 'date_end' => $date_end, 'coeff' => $coeff)));
					$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => array('number' => $number, 'title' => $title)));

					$values = array(array());
					$values2 = array(array());

					for($i=0; $i < count($grades); $i++)
					{
						$values[$i][0] = $project->id;
						$values[$i][1] = $grades[$i];
						$values[$i][2] = 0;

						$values2[$i][0] = $work->id;
						$values2[$i][1] = $grades[$i];
					}

					$this->dispatcher->Core->SQL->insert(array('table' => 'projects_for', 'values' => $values));
					$this->dispatcher->Core->SQL->insert(array('table' => 'works_for' , 'values' => $values2));

					//Création des dossiers
					$path = FILES.'Projects/'.$project->id.'-'.$title.'/';
					try
					{
						mkdir($path, 0777, true);
						mkdir($path.'files/', 0777, true);

					}
					catch(Error $e)
					{
						if(DEBUG)
							die($e);
						else
							$_POST['error_log'] = "Erreur lors de la création du répertoire de projet ".$path.".";
					}

					//Gestion des groupes
					if($group_size == 1)
					{
						$users_grades = $this->getUsersByGradesArray($grades);

						if($users_grades != false)
						{
							//Pour chaque utilisateur concerné, on crée un groupe et un projet Codiad si nécessaire.
							//Parcours des différentes classes
							for($i=0 ; $i < count($users_grades) ; $i++)
							{
								$users = $users_grades[$i];
								//Parcours des différents utilisateurs
								for($j=0 ; $j < count($users) ; $j++)
								{
									$this->dispatcher->Core->SQL->insert(array('table' => 'groups', 'values' => array(NULL, $project->id, $users[$j]->id, '1')));
									$group = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'groups', 'conditions' => array('project' => $project->id, 'chief' => $users[$j]->id)));

									//Initialisation du projet
									$this->initProjectForGroup($group, $project);
								}
							}
						}
					}
				}
			}
			else
				$_POST['error_log'] = "Veuillez compléter correctement le formulaire.";
		}
		else if(isset($_POST['submit_edit_project']))
		{
			//Variables du projets
			$id = isset($_POST['project_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['project_id'])))) : NULL;

			$number = isset($_POST['edit_number']) ? $this->dispatcher->Core->SQL->secure($_POST['edit_number']) : NULL;
			$title = isset($_POST['edit_title']) ? $this->dispatcher->Core->SQL->secure($_POST['edit_title']) : NULL;
			$date_end = isset($_POST['edit_date_end']) ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['edit_date_end']))) : NULL;
			$group_size = isset($_POST['edit_group_size']) ? $this->dispatcher->Core->SQL->secure($_POST['edit_group_size']) : NULL;
			$date_group = isset($_POST['edit_date_group']) && $group_size > 1 ? date('Y-m-d', strtotime($this->dispatcher->Core->SQL->secure($_POST['edit_date_group']))) : NULL;
			$codiad_enable = isset($_POST['edit_codiad']) && $this->dispatcher->Core->Config->codiad ? '1' : '0';
			$todo_enable = isset($_POST['edit_todo']) ? '1' : '0';
			$upload_enable = isset($_POST['edit_ftp']) && $this->dispatcher->Core->Config->ftp ? '1' : '0';
			$description = isset($_POST['edit_subject']) ? $this->dispatcher->Core->SQL->secure($_POST['edit_subject']) : NULL;

			//Traitement
			if($id != NULL && $number != NULL && $title != NULL && $date_end != NULL  && $description != NULL)
			{
				//Mise à jour BDD
				$projet = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => 'id='.$id));
				$this->dispatcher->Core->SQL->update(array('table' => 'projects', 'columns' => array('number', 'title', 'date_end', 'date_group', 'codiad_enable', 'todo_enable', 'upload_enable', 'description'), 'values' => array($number, $title, $date_end, $date_group, $codiad_enable, $todo_enable, $upload_enable, $description), 'conditions' => 'id='.$id));
				$this->dispatcher->Core->SQL->update(array('table' => 'works', 'columns' => 'date_end', 'values' => $date_end, 'conditions' => array('title' => $projet->title, 'type' => 'projet', 'date_end' => $projet->date_end)));
				
				//Traitement des fichiers
				if(isset($_FILES['edit_file']) && $_FILES['edit_file']['error'] != 4)
				{
					$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => array('id', $id)));
					$path = FILES."Projects/".$project->id.'-'.$project->title.'/files/';


					if(!file_exists($path))
						mkdir($path, 0777, true);

					$max_size = 20971520; //20Mo en octets
					$ext = array(".pdf",".zip",".rar");
					$file = basename($this->accents($_FILES['edit_file']['name']));

					if(filesize($_FILES['edit_file']['tmp_name']) > $max_size)
					{
						$_POST['error_log'] = "Le fichier est trop volumineux.";
						return false;
					}
					else if(!in_array(strrchr($_FILES['edit_file']['name'], '.'), $ext))
					{
						$_POST['error_log'] = "Extension de fichier non autorisée.";
						return false;
					}
					else
					{
						if(move_uploaded_file($_FILES['edit_file']['tmp_name'], $path.$file))
						{
							$file_url = str_replace(FILES, WEB_ROOT.'/Files/', $path).$file;
							if($project->files_url != NULL)
							{
								$files = unserialize(base64_decode($project->files_url));
								array_push($files, $file_url);
								$this->dispatcher->Core->SQL->update(array('table' => 'projects', 'columns' => 'files_url', 'values' => base64_encode(serialize($files)), 'conditions' => 'id='.$project->id));
							}
							else
								$this->dispatcher->Core->SQL->update(array('table' => 'projects', 'columns' => 'files_url', 'values' => base64_encode(serialize(array($file_url))), 'conditions' => 'id='.$project->id));
							return true;
						}
						else
						{
							$_POST['error_log'] = "Échec de la mise en ligne.";
							return false;
						}
					}
				}
			}
		}
		else if(isset($_POST['submit_del_project']))
		{
			$id = isset($_POST['project_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['project_id'])))) : NULL;
			if($id != NULL)
			{
				$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => 'id='.$id));
				$path = FILES.'Projects/'.$project->id.'-'.$project->title;

				//On supprime le projet de la BDD
				$this->dispatcher->Core->SQL->delete(array('table' => 'projects', 'conditions' => 'id='.$id));
				$this->dispatcher->Core->SQL->delete(array('table' => 'works', 'conditions' => array('title' => $project->title, 'type' => 'projet', 'date_end' => $project->date_end)));

				//On supprime les autorisations et projets Codiad si nécessaire
				if($project->codiad_enable)
				{
					//Suppression des autorisations
					$users = $this->getUsersByGradesArray($this->getGradesByProject($id));
					for($i=0 ; $i < count($users) ; $i++)
					{
						$group = $this->getGroupByUserIdAndProject($users[$i]->id, $id);
						$name = $project->id.'-'.$project->title.'/Groupe '.$group->id.'/Codiad/';
						$this->dispatcher->Core->Codiad->removeProject($users[$i], $name);
						//Suppression du projet pour le prof
						$this->dispatcher->Core->Codiad->removeProject($this->dispatcher->Core->User->getEmail(), $name);
					}

					//Suppression des projets
					$groups = $this->getGroupsByProject($id);
					for($i=0 ; $i < count($groups) ; $i++)
						$this->dispatcher->Core->Codiad->deleteProject(str_replace(FILES.'Projects/', '', $path).'/Groupe '.$groups[$i]->id.'/Codiad/');
				}

				//On supprime tous les fichiers locaux
				$this->recursiveRmdir($path);

			}
		}
		else if(isset($_POST['submit_dispo_ok']))
		{
			$id = isset($_POST['project_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['project_id'])))) : NULL;
			if($id != NULL)
				$this->dispatcher->Core->SQL->update(array('table' => 'projects_for', 'columns' => 'enabled', 'values' => '0', 'conditions' => 'project='.$id));
		}
		else if(isset($_POST['submit_dispo_ko']))
		{
			$id = isset($_POST['project_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['project_id'])))) : NULL;
			if($id != NULL)
				$this->dispatcher->Core->SQL->update(array('table' => 'projects_for', 'columns' => 'enabled', 'values' => '1', 'conditions' => 'project='.$id));
		}
		else if(isset($_POST['submit_del_file']))
		{
			$id = isset($_POST['file_id']) ? explode("_", $this->dispatcher->Core->SQL->secure($_POST['file_id'])): NULL;
			if($id != NULL)
			{
				$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => 'id='.$id[0]));
				$files = unserialize(base64_decode($project->files_url));
				if(unlink(str_replace(WEB_ROOT."/Files/",FILES,$files[$id[1]])))
				{
					unset($files[$id[1]]);
					if(count($files) == 0)
						$files = NULL;
					else
						$files = base64_encode(serialize(array_values($files)));
					$this->dispatcher->Core->SQL->update(array('table' => 'projects', 'columns' => 'files_url', 'values' => $files, 'conditions' => 'id='.$id[0]));
				}
			}
		}
		else if(isset($_POST['submit_todo']))
		{
			$todo = isset($_POST['id_postit']) ? $_POST['id_postit'] : NULL;

			if($todo != NULL)
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
					$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => 'id='.$ids[0]));
					$path = FILES."Projects/".$project->id.'-'.$project->title."/Groupe ".$ids[1]."/upload/";

					
					if(false !== ($ressource = opendir($path)))
					{
						while (false !== ($file = readdir($ressource)))
						{
							if($file != "." && $file != "..")
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
		else if(isset($_POST['submit_init_project']))
		{
			$id = isset($_POST['project_id']) ? str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($this->dispatcher->Core->SQL->secure($_POST['project_id'])))) : NULL;

			if($id != NULL)
			{
				$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => array('id' => $id)));
				if($project != false)
					$this->initAllProjects($project);
				else
					$_POST['error_log'] = "Ce projet n'existe pas.";	
			}
			else
				$_POST['error_log'] = "Certaines valeurs de traitement sont manquantes.";
		}

		parent::handlePost();
	}

	/**
	 * Renvoie un utilisateur en fonction de son id.
	 * @param $id
	 * @return array
	 */
	public function getUserById($id)
	{
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('id' => $id)));
	}

	/**
	 * Renvoie la liste des élèves de la classe qui ne sont pas déjà dans un groupe.
	 * @param $grade
	 * @return array
	 */
	public function getFreeUsersByGradeAndProject($grade, $project)
	{
		$request = "SELECT * FROM users WHERE grade=".$grade." AND (id NOT IN(SELECT chief FROM groups WHERE project=".$project.") AND id NOT IN(SELECT user FROM groups_members WHERE `group` IN(SELECT id FROM groups WHERE project=".$project.")));";
		return $this->dispatcher->Core->SQL->query($request);
	}

	/**
	 * Renvoie tous les utilisateurs d'un ensemble de classe.
	 * @param $grades
	 * @return array
	 */
	public function getUsersByGradesArray($grades)
	{
		$array = array();

		for($i=0 ; $i < count($grades) ; $i++)
		{
			$users = $this->dispatcher->Core->SQL->select(array('table' => 'users', 'conditions' => array('grade' => $grades[$i], 'validated' => '1')));
			array_push($array, $users);
		}

		return $array;
	}

	/**
	 * Renvoie un tableau contenant l'ensemble des utilisateurs membre d'un groupe.
	 * @param $group
	 * @return array
	 */
	public function getUsersByGroup($group)
	{
		$request = "SELECT * FROM users WHERE id IN(SELECT chief FROM groups WHERE id=".$group.") OR id IN(SELECT user FROM groups_members WHERE `group`=".$group.");";
		return $this->dispatcher->Core->SQL->query($request);
	}

	/**
	 * Récupère toutes les classes de niveau différent (Première, Terminale...).
	 * @return array
	 */
	public function getDiffGrades()
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));

		$request = "SELECT * FROM grades WHERE teacher=".$user->id." GROUP BY SUBSTRING(grade, 1, INSTR(grade,' ')-1);";
		return $this->dispatcher->Core->SQL->query($request);
	}

	/**
	 * Renvoie toutes les classes participant à un projet donné.
	 * @param $project
	 * @return array
	 */
	public function getGradesByProject($project)
	{
		$request = "SELECT * FROM grades WHERE id IN(SELECT grade FROM projects_for WHERE project=".$project.");";
		return $this->dispatcher->Core->SQL->query($request);
	}

	/**
	 * Récupère l'id de toutes les classes dont le nom commence par $value (Première, Terminale...).
	 * @param $value
	 * @return array
	 */
	public function getIdGrades($value)
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users','conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));

		$request = "SELECT id FROM grades WHERE teacher=".$user->id." AND SUBSTRING(grade, 1, INSTR(grade,' ')-1)='".$value."';";
		return $this->dispatcher->Core->SQL->query($request);
	}

	/**
	* Renvoie les projets créés par le professeur.
	* @param $grades
	* @return array | boolean
	*/
	public function getProjectsByGrades($grades)
	{
		$conditions = "WHERE ";
		for($i=0; $i < count($grades); $i++)
		{
			$conditions .= "grade=".$grades[$i];
			if($i < count($grades)-1)
				$conditions .= " OR ";
		}

		$request = "SELECT project FROM projects_for ".$conditions.";";
		$query = $this->dispatcher->Core->SQL->query($request);
		
		if($query)
		{
			$conditions = "";
			for($i=0; $i < count($query); $i++)
			{
				$conditions .= "id=".$query[$i]->project;
				if($i < count($query)-1)
					$conditions .= " OR ";
			}

			$request = "SELECT * FROM projects WHERE final_project='0' AND (".$conditions.");";
			return $this->dispatcher->Core->SQL->query($request);
		}
		else
			return false;
	}

	/**
	 * Renvoie un tableau contenant les projets des classes d'un même niveau (Première, Terminale...).
	 * @param $grades
	 * @return array
	 */
	public function getProjectsFor($grades)
	{
		$array = array();
		for($i=0; $i < count($grades); $i++)
			array_push($array,$this->dispatcher->Core->SQL->select(array('table' => 'projects_for', 'conditions' => 'grade='.$grades[$i])));
		return $array;
	}

	/**
	 * Renvoie les projets attribués à la classe de l'élève.
	 * @param $grade
	 */
	public function getProjectsByGrade($grade)
	{
		$request = "SELECT * FROM projects WHERE final_project='0' AND id IN(SELECT project FROM projects_for WHERE grade=".$grade." AND enabled=1);";
		return $this->dispatcher->Core->SQL->query($request);
	}

	/**
	 * Renvoie un groupe à partir d'un utilisateur et d'un projet.
	 * @param $project
	 * @return array
	 */
	public function getGroupByUserAndProject($project)
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		$request = "SELECT * FROM groups WHERE project=".$project." AND (chief=".$user->id." OR id IN(SELECT `group` FROM groups_members WHERE user=".$user->id."));";
		
		$group = $this->dispatcher->Core->SQL->query($request);
		if($group == false)
			return false;
		else
			return $group[0];
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

	/** 
	 * Renvoie l'utilisateur chef du projet donné.
	 * @param $group_id
	 * @return array
	 */
	public function getGroupChief($group_id)
	{
		$group = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'groups', 'conditions' => array('id' => $group_id)));
		return $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('id' => $group->chief)));
	}

	/**
	 * Renvoie un tableau contenant l'intégralité des groupes travaillant sur un projet donné.
	 * @param $project
	 * @return array
	 */
	public function getGroupsByProject($project)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'groups', 'conditions' => array('project' => $project)));
	}

	/**
	 * Renvoie un tableau contenant les utilisateurs ne faisant pas partie d'un groupe pour le projet donné.
	 * @param $project
	 * @return array
	 */
	public function getGrouplessUsersByProject($project)
	{
		$request = "SELECT * FROM users WHERE id NOT IN(SELECT chief FROM groups WHERE project=".$project.") OR id NOT IN(SELECT user FROM groups_members AS gm, groups AS grp WHERE gm.`group`=grp.id AND grp.project=".$project.");";
		return $this->dispatcher->Core->SQL->query($request);
	}

	/** 
	 * Renvoie les invitations de groupe à partir de l'id de l'utilisateur.
	 * @return array
	 */
	public function getInvitationsByUser($project)
	{
		$user = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'users', 'conditions' => array('email' => $this->dispatcher->Core->User->getEmail())));
		$request = "SELECT * FROM group_invitations WHERE `to`=".$user->id." AND `group` IN(SELECT id FROM groups WHERE project=".$project.");";
		return $this->dispatcher->Core->SQL->query($request);
	}

	/**
	 * Renvoie toutes les invitations envoyées par un groupe donné.
	 * @param $group
	 * @return array
	 */
	public function getInvitationsByGroup($group)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'group_invitations', 'conditions' => array('`group`' => $group)));
	}

	/**
	 * Renvoie la todo list en fonction du groupe.
	 * @param $group
	 */
	public function getTodoList($group)
	{
		return $this->dispatcher->Core->SQL->select(array('table' => 'todo_lists', 'conditions' => array('`group`' => $group)));
		// Ajouter la condition pour le groupe de l'élève
	}

	/**
	 * Renvoie la todo list en fonction du groupe.
	 * @param $group, $project
	 * @return $entrees
	 */
	public function getFiles($project, $group)
	{
		$project = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'projects', 'conditions' => 'id='.$project));
					
		$path = FILES."Projects/".$project->id.'-'.$project->title."/Groupe ".$group."/upload";

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
	 * Suppression récursive d'un répertoire et de son contenu.
	 * @return boolean
	 */
	private function recursiveRmdir($dir)
	{
		if(is_dir($dir))
		{
			$files = array_diff(scandir($dir), array('.','..'));
		    foreach($files as $file)
		    	is_dir($dir.'/'.$file) ? $this->recursiveRmdir($dir.'/'.$file) : unlink($dir.'/'.$file);
		    
		    return rmdir($dir);
		}
		else
			return false;
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

	/**
	 * Initialise le projet d'un groupe donné.
	 * @param $group
	 * @param $project
	 */
	private function initProjectForGroup($group, $project)
	{
		$path = FILES.'Projects/'.$project->id.'-'.$project->title.'/Groupe '.$group->id.'/';
		mkdir($path, 0777, true);

		//Si l'upload est activé, on crée le dossier de destination
		if($project->upload_enable)
			mkdir($path.'upload/', 0777, true);

		//Si Codiad est activé, on crée le projet et on assigne les autorisations aux membres du groupe
		if($project->codiad_enable)
		{
			$name = $project->title.' - Groupe '.$group->id;
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
			$prof = $this->dispatcher->Core->SQL->query($request)[0];
			$this->dispatcher->Core->Codiad->addProject($prof->email, $codiad_path);
		}
	}

	/**
	 * Initialise les projets de tous les groupes d'un même projet.
	 * @param $project
	 */
	private function initAllProjects($project)
	{
		//Gestion des groupes formés
		$groups = $this->getGroupsByProject($project->id);
		for($i=0 ; $i < count($groups) ; $i++)
			$this->initProjectForGroup($groups[$i], $project);

		//Gestion des utilisateurs sans groupe
		$forever_alone = $this->getGrouplessUsersByProject($project->id);
		if($forever_alone != false && count($forever_alone) > 0)
		{
			//On calcule le nombre de groupes aléatoires à générer
			$nb_random_groups = count($forever_alone) / $project->group_size;
			$nb_plus = 0;

			if(($nb_plus = count($forever_alone) % $project->group_size) != 0)
				$nb_random_groups++;

			for($i=0 ; $i < $nb_random_groups ; $i++)
			{
				$group = NULL;
				$temp = ($i == $nb_random_groups-1 && $nb_plus != 0) ? $nb_plus : $project->group_size;

				//Pour chaque groupe, on choisit $j élèves seuls au hasard.
				for($j=0 ; $j < $temp ; $j++)
				{
					$index = rand(0, count($forever_alone)-1);

					//Premier utilisateur == chef de groupe.
					if($j == 0)
					{
						$this->dispatcher->Core->SQL->insert(array('table' => 'groups', 'values' => array(NULL, $project->id, $forever_alone[$index]->id, '1')));
						$group = $this->dispatcher->Core->SQL->selectFirst(array('table' => 'groups', 'conditions' => array('chief' => $forever_alone[$index]->id, 'project' => $project->id)));
					}
					else
						$this->dispatcher->Core->SQL->insert(array('table' => 'groups_members', 'values' => array($group->id, $forever_alone[$index]->id)));
					
					array_splice($forever_alone, $index, 1);
				}

				$this->initProjectForGroup($group, $project);
			}
		}
	}

	public function deleteTodoAtEnd($id)
	{
		if($this->dispatcher->Core->SQL->query("SELECT * FROM todo_lists WHERE `group` IN (SELECT id FROM `groups` WHERE project='".$id."');"))
		{
			$request = "DELETE FROM todo_lists WHERE `group` IN (SELECT id FROM `groups` WHERE project='".$id."');";
			$this->dispatcher->Core->SQL->query($request);
		}
	}
}
?>