<?php
    $projects = $this->dispatcher->model->getProjectsByGrade($this->dispatcher->Core->User->getGrade());
    $outdated = array();

    //Si la date de rendu d'un projet est déjà passé, on le dépace dans le tableau $outdated.
    for($i=0 ; $i < count($projects) ; $i++)
    {
        if(strtotime(date('Y-m-d')) > strtotime($projects[$i]->date_end))
        {
            array_push($outdated, $projects[$i]);
            array_splice($projects, $i, 1);
            $i--;
        }
    }
?>

<?php if(count($projects) == 0): ?>
    <section class="jumbotron background1">
        <div class="container titre">
            <h1>Projets</h1>
        </div>
    </section>

    <section class="jumbotron background3 enonce">
        <div class="container titre">
            <div class="panel panel-default no-project">
                <table class="table">
                    <tr><td>Aucun projet en cours.</td></tr>
                </table>
            </div>
        </div>
    </section>
<?php else: ?>
    <?php for($i=0 ; $i < count($projects) ; $i++): ?>
        <?php $group = $this->dispatcher->model->getGroupByUserAndProject($projects[$i]->id); ?>

        <section class="jumbotron background1">
            <div class="container titre">
                <h1><?php echo('Projet '.$projects[$i]->number.' : '.$projects[$i]->title); ?></h1>
            </div>
        </section>

        <section class="jumbotron background3 enonce">
            <div class="container">
                <h2>Enoncé</h2>
                <?php echo(htmlspecialchars_decode($projects[$i]->description)); ?>
                <hr />

                <?php //S'il s'agit d'un projet individuel ou si le groupe est déjà formé et verrouillé, on affiche la page ?>
                <?php if($group != false && $group->locked): ?>
                    <?php $modules = $this->dispatcher->model->getModulesValue($projects[$i]); ?>

                    <div class="row">
                        <?php if($projects[$i]->todo_enable): ?>
                            <div class="<?php echo($modules > 4 ? 'col-xs-6 col-sm-6 col-md-6' : 'col-xs-6 col-sm-6 col-md-6 col-md-offset-3'); ?>">
                                <div class="thumbnail" data-toggle="modal" data-target="#to-do_modal">
                                    <div class="thumbnail-img todo">
                                        <img src="<?php echo(STYLES.'img/todo.php?group='.base64_encode(SALT.$group->id.PEPPER)); ?>" alt="todolist"/>
                                    </div>
                                </div>
                            </div>

                            <?php $todo = $this->dispatcher->model->getTodoList($group->id); ?>
                            <div class="modal fade" id="to-do_modal" tabindex="-1" role="dialog" aria-labelledby="to-do_label" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#new_postit_modal">Ajouter</button>
                                                </div>
                                                <div class="col-md-2 col-md-offset-3">
                                                     <h4 class="modal-title" id="to-do_label">To-Do List</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>
                                                    <th class="col-md-4 col-sm-4 col-xs-4">To do</th>
                                                    <th class="col-md-4 col-sm-4 col-xs-4">In progress</th>
                                                    <th class="col-md-4 col-sm-4 col-xs-4">Completed<th>
                                                </thead>
                                                <tbody>
                                                    <tr class="columns">
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr id="todo_trash">
                                                        <td colspan="3"><div class="icon-trash"></div></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php for($j=0; $j < count($todo); $j++): ?>
                                                <?php
                                                    $left=''; $top=''; $zindex='';
                                                    list($left,$top,$zindex) = explode('x',$todo[$j]->position);
                                                ?>
                                                <div class="note <?php echo($todo[$j]->color); ?>" style="<?php echo("left:".$left."px;top:".$top."px;z-index:".$zindex); ?>">
                                                    <?php echo($todo[$j]->description); ?>
                                                    <div class="deadline"><?php echo(date("d/m/y",strtotime($todo[$j]->deadline))); ?></div>
                                                    <div class="author"><?php echo($todo[$j]->name); ?></div> 
                                                    <form action="" method="POST">
                                                    <input class="data" type="hidden" name="id_postit[]" value="<?php echo($todo[$j]->id); ?>"/>
                                                    <input type="hidden" id="position_<?php echo($todo[$j]->id); ?>" name="position_<?php echo($todo[$j]->id); ?>" value="<?php echo($todo[$j]->position); ?>" />
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                            <div class="modal-footer">
                                                <input type="submit" name="submit_todo" class="btn btn-primary" value="Enregistrer"/>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php //Modal d'ajout de Post-It ?>
                            <div class="modal fade" id="new_postit_modal" tabindex="-1" role="dialog" aria-labelledby="new_postit_label" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="new_postit_label">Ajouter un Post-It</h4>
                                        </div>
                                        <form class="form-horizontal" action="" method="POST">
                                            <div class="modal-body">
                                                <div class="row">
                                                    
                                                    <!--<div class="form-group">
                                                        <label for="title_new" class="col-sm-3 control-label">Titre</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" name="title_new" id="title_new" class="form-control" required/>
                                                        </div>
                                                    </div>-->
                                                    
                                                    <div class="form-group">
                                                        <label for="desc_new" class="col-sm-3 control-label">Description</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" name="desc_new" id="desc_new" class="form-control" required/>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="student_new" class="col-sm-3 control-label">Elève</label>
                                                        <div class="col-sm-9">
                                                            <select id="student_new" class="form-control" name="student_new">
                                                                <?php 
                                                                    $group_members = $this->dispatcher->model->getUsersByGroup($group->id);
                                                                    for($j=0 ; $j < count($group_members) ; $j++)
                                                                        echo('<option value="'.$group_members[$j]->firstname.'">'.$group_members[$j]->firstname.' '.$group_members[$j]->lastname.'</option>');
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="date_new" class="col-sm-3 control-label">Echéance</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" name="date_new" id="date_new" class="form-control datepicker" required/>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="color_new" class="col-sm-3 control-label">Couleur</label>
                                                        <div class="col-sm-9">
                                                            <select id="color_new" class="form-control" name="color_new">
                                                                <option value="yellow">Jaune</option>
                                                                <option value="blue">Bleu</option>
                                                                <option value="green">Vert</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="hidden" name="id_group" value="<?php echo(base64_encode(SALT.$group->id.PEPPER)); ?>" />
                                                <input type="submit" name="submit_new_todo" class="btn btn-primary" value="Ajouter"/>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($projects[$i]->codiad_enable && $this->dispatcher->Core->Config->codiad == 2): ?>
                            <div class="<?php echo($modules == 1 ? 'col-xs-6 col-sm-6 col-md-6 col-md-offset-3' : 'col-xs-6 col-sm-6 col-md-6'); ?>">
                                <div class="thumbnail">
                                    <div class="thumbnail-img" <?php echo($modules == 5 || $modules == 1 ? 'style="margin: 82px;"' : ''); ?>>
                                        <img src="<?php echo(STYLES.'img/codiad.png'); ?>" alt="codiad"/>
                                    </div>
                                    <div class="thumbnail-txt">
                                        <a target="_blank" href="<?php echo(WEB_ROOT.'/Codiad'); ?>" class="btn btn-lg btn-default btn-block">Codiad</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($projects[$i]->upload_enable && $this->dispatcher->Core->Config->ftp == 2): ?>
                            <div class="<?php echo($modules == 1 ? 'col-xs-6 col-sm-6 col-md-6 col-md-offset-3' : 'col-xs-6 col-sm-6 col-md-6'); ?>">
                                <div class="thumbnail">
                                    <div class="thumbnail-img" <?php echo($modules == 5 || $modules == 1 ? 'style="margin: 82px;"' : ''); ?>>
                                        <img src="<?php echo(STYLES.'img/ftp.png'); ?>" alt="ftp"/>
                                    </div>
                                    <div class="thumbnail-txt">
                                        <button type="button" class="btn btn-lg btn-default btn-block" data-toggle="modal" data-target="#ftp_modal">Accès FTP</button>
                                    </div>
                                </div>
                            </div>               
                            
                            <div class="modal fade" id="ftp_modal" tabindex="-1" role="dialog" aria-labelledby="ftp_label" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="ftp_label">Upload</h4>
                                        </div>
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            <div class="modal-body form-group">
                                                <input type="file" name="file_project"/>

                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Fichier</th>
                                                            <th>Télécharger</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                            $files = $this->dispatcher->model->getFiles($projects[$i]->id, $group->id);
                                                            if($files)
                                                                for($j=0; $j<count($files); $j++)
                                                                    echo("<tr><td>".substr($files[$j], strrpos($files[$j], "/")+1, strlen($files[$j]))."</td><td><a href=\"".$files[$j]."\"><span class=\"glyphicon glyphicon-save\"></span></a></td></tr>");
                                                            else
                                                                echo("<tr><td colspan=\"2\">Aucun fichier</td></tr>");
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="hidden" name="upload_id" value="<?php echo(base64_encode(SALT.$projects[$i]->id."_".$group->id.PEPPER)); ?>" />
                                                <input type="submit" name="submit_upload_project_file" class="btn btn-primary" value="Upload"/>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <hr />
                    <h2>Supports de projet</h2>
                    <div class="panel panel-default">
                    <table class="table table-striped table-hover">
                        <tr>
                            <th>#</th>
                            <th>Document</th>
                            <th>Télécharger</th>
                        </tr>
                        <?php 
                            if($projects[$i]->files_url == NULL)
                                echo("<tr><td colspan=\"3\">Aucune entrée.</td></tr>");
                            else
                            {
                                $files = unserialize(base64_decode($projects[$i]->files_url));
                                for($i=0; $i < count($files); $i++)
                                    echo("<tr><td>".($i+1)."</td><td>".substr($files[$i],strrpos($files[$i], "/")+1,strlen($files[$i]))."</td><td><a href=\"".$files[$i]."\"><span class=\"glyphicon glyphicon-save\"></span></a></td></tr>");
                            }
                        ?>
                      </table>
                    </div>

                <?php //S'il s'agit d'un projet en groupe et que la date d'échéance est atteinte. ?>
                <?php elseif(($group == false || $group != false && !$group->locked) && strtotime(date("Y-m-d")) > strtotime($projects[$i]->date_end)): ?>
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="panel panel-default group-panel">
                                <div class="panel-heading">Début du projet</div>
                                <div class="panel-body">
                                    La date d'échéance de formation des groupes a été atteinte.<br />
                                    
                                    <form action="" method="POST">
                                        <input type="hidden" name="project_id" value="<?php echo(base64_encode(SALT.$projects[$i]->id.PEPPER)); ?>" />
                                        <input type="submit" name="submit_init_project" value="Commencer le projet" class="btn btn-lg btn-success" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php //S'il s'agit d'un projet en groupe et que le groupe n'est pas encore formé. ?>
                <?php else: ?>
                    <div class="row">
                        <?php //Si l'utilisateur n'a pas de groupe ?>
                        <?php if($group == false): ?>
                            <?php $invitations = $this->dispatcher->model->getInvitationsByUser($projects[$i]->id); ?>
                            <div class="col-md-6">
                                <div class="panel panel-default group-panel">
                                    <div class="panel-heading">Répondre à vos invitations...</div>
                                    <table class="table table-hover">
                                        <thead>
                                            <th class="col-sm-5">Chef de groupe</th>
                                            <th class="col-sm-7">Réponse</th>
                                        </thead>
                                        <tbody>
                                        <?php if(count($invitations) == 0): ?>
                                            <tr><td colspan="2">Vous n'avez reçu aucune invitation.</td></tr>
                                        <?php else: ?>
                                            <?php for($j=0 ; $j < count($invitations) ; $j++): ?>
                                                <?php $chief = $this->dispatcher->model->getGroupChief($invitations[$j]->group); ?>
                                                <tr>
                                                    <td><?php echo($chief->firstname.' '.$chief->lastname); ?></td>
                                                    <td>
                                                        <form action="" method="POST">
                                                            <input type="hidden" name="invitation_id" value="<?php echo(base64_encode(SALT.$invitations[$j]->id.PEPPER)); ?>" />
                                                            <input type="hidden" name="project_id" value="<?php echo(base64_encode(SALT.$projects[$i]->id.PEPPER)); ?>" />
                                                            <button type="submit" name="submit_accept_invitation" class="btn btn-success">Accepter</button>
                                                            <button type="submit" name="submit_deny_invitation" class="btn btn-danger">Refuser</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default group-panel">
                                    <div class="panel-heading">... ou créer votre propre groupe</div>
                                    <form action="" method="POST">
                                        <input type="hidden" name="project_id" value="<?php echo(base64_encode(SALT.$projects[$i]->id.PEPPER)); ?>" />
                                        <input type="submit" name="submit_create_group" value="Créer mon groupe" class="btn btn-lg btn-success" />
                                    </form>
                                </div>
                            </div>

                        <?php //Si l'utilisateur est le chef de son groupe ?>
                        <?php elseif($this->dispatcher->model->getGroupChief($group->id)->email == $this->dispatcher->Core->User->getEmail()): ?>
                            <?php
                                $members = $this->dispatcher->model->getUsersByGroup($group->id);
                                $invitations = $this->dispatcher->model->getInvitationsByGroup($group->id);
                                $invitation_counter = 0;
                                $free_users = $this->dispatcher->model->getFreeUsersByGradeAndProject($this->dispatcher->Core->User->getGrade(), $projects[$i]->id);
                            ?>
                            <div class="col-md-12">
                                <div class="panel panel-default group-panel">
                                    <div class="panel-heading">Gérer votre groupe</div>
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Élève</th>
                                                    <th>État</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php for($j=0 ; $j < $projects[$i]->group_size ; $j++): ?>
                                                <form action="" method="POST" class="no-margin">
                                                    <tr>
                                                        <td>
                                                            <label for="<?php echo('invit_select_'.$j); ?>"><?php echo($j+1); ?></label>
                                                        </td>

                                                        <?php //Affiche en premier les membres du groupe ?>
                                                        <?php if(count($members) >= $j+1): ?>
                                                            <td>
                                                                <select class="form-control" disabled>
                                                                    <option><?php echo($members[$j]->firstname.' '.$members[$j]->lastname); ?></option>
                                                                </select>
                                                            </td>
                                                            <td class="col-md-4">
                                                                <?php if($members[$j]->email == $this->dispatcher->Core->User->getEmail()): ?>
                                                                    <input type="hidden" name="group_id" value="<?php echo(base64_encode(SALT.$group->id.PEPPER)); ?>" />
                                                                    <input type="hidden" name="project_id" value="<?php echo(base64_encode(SALT.$projects[$i]->id.PEPPER)); ?>" />
                                                                    <button type="submit" name="submit_lock_group" class="btn btn-success" <?php echo(count($members) == $projects[$i]->group_size ? '' : 'disabled'); ?>>Verrouiller</button>
                                                                    <button type="submit" name="submit_del_group" class="btn btn-danger">Supprimer</button>
                                                                <?php else: ?>
                                                                    <input type="hidden" name="group_id" value="<?php echo(base64_encode(SALT.$group->id.PEPPER)); ?>" />
                                                                    <input type="hidden" name="user_id" value="<?php echo(base64_encode(SALT.$members[$j]->id.PEPPER)); ?>" />
                                                                    <button type="submit" name="submit_fire_member" class="btn btn-danger">Retirer du groupe</button>
                                                                <?php endif; ?>
                                                            </td>

                                                        <?php //Si il y a des invitations déjà envoyées à afficher ?>
                                                        <?php elseif(count($invitations) > $invitation_counter): ?>
                                                            <?php $invited = $this->dispatcher->model->getUserById($invitations[$invitation_counter]->to); $invitation_counter++; ?>
                                                            <td>
                                                                <select id="<?php echo('invit_select_'.$j); ?>" class="form-control" disabled>
                                                                    <option><?php echo($invited->firstname.' '.$invited->lastname); ?></option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="group_id" value="<?php echo(base64_encode(SALT.$group->id.PEPPER)); ?>" />
                                                                <input type="hidden" name="user_id" value="<?php echo(base64_encode(SALT.$invited->id.PEPPER)); ?>" />
                                                                <button type="submit" name="submit_cancel_invitation" class="btn btn-danger">Retirer l'invitation</button>
                                                            </td>

                                                        <?php //S'il n'y a plus d'invitation déjà envoyée à afficher ?>
                                                        <?php else: ?>
                                                            <td>
                                                                <select id="<?php echo('invit_select_'.$j); ?>" name="invited_user" class="form-control" <?php echo(count($free_users) == 0 ? 'disabled' : ''); ?>>
                                                                    <?php if(count($free_users) > 0): ?>
                                                                        <?php for($k=0 ; $k < count($free_users) ; $k++): ?>
                                                                            <option value="<?php echo($free_users[$k]->id); ?>"><?php echo($free_users[$k]->firstname.' '.$free_users[$k]->lastname); ?></option>
                                                                        <?php endfor; ?>
                                                                    <?php else: ?>
                                                                        <option value="">Aucun élève disponible</option>
                                                                    <?php endif; ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="group_id" value="<?php echo(base64_encode(SALT.$group->id.PEPPER)); ?>" />
                                                                <button type="submit" name="submit_send_invitation" class="btn btn-default" <?php echo(count($free_users) > 0 ? '' : 'disabled'); ?>>Demander</button>
                                                            </td>

                                                        <?php endif; ?>
                                                    </tr>
                                                </form>
                                                <?php endfor; ?>
                                            </tbody>
                                        </table>

                        <?php //Si l'utilisateur est juste membre d'un groupe ?>
                        <?php else: ?>
                            <div class="col-md-12">
                                <div class="panel panel-default group-panel">
                                    <div class="panel-heading">Membre d'un groupe</div>
                                    <div class="panel-body">
                                        Votre êtes actuellement membre d'un groupe, seul votre chef de projet peut en gérer les membres.<br />
                                        L'espace de travail du projet deviendra accessible lorsque votre groupe sera verouillé, ou que l'échéance de formation des groupes sera atteinte. 
                                        
                                        <form action="" method="POST">
                                            <input type="hidden" name="group_id" value="<?php echo(base64_encode(SALT.$group->id.PEPPER)); ?>" />
                                            <input type="submit" name="submit_quit_group" value="Quitter le groupe" class="btn btn-danger" />
                                        </form>
                                    </div>

                        <?php endif; ?>
                                </div>
                            </div>

                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endfor; ?>
<?php endif; ?>

<?php if(count($outdated) > 0): ?>
    <section class="jumbotron background1">
        <div class="container">
            <h2>Projets de l'année</h2>

            <div class="panel-group list-group" id="outdated_annual_projects">
                <div class="panel panel-default">
                    <?php for($i=0 ; $i < count($outdated) ; $i++): ?>
                        <?php $this->dispatcher->model->deleteTodoAtEnd($outdated[$i]->id); ?>
                        <a class="list-group-item" data-toggle="collapse" data-parent="#outdated_annual_projects" href="#outdated_project_<?php echo(sha1(SALT.$outdated[$i]->id.PEPPER)); ?>"><?php echo('Projet '.$outdated[$i]->number.' : '.$outdated[$i]->title); ?></a>
                        <div id="outdated_project_<?php echo(sha1(SALT.$outdated[$i]->id.PEPPER)); ?>" class="panel-collapse collapse">
                            <div class="panel-body">
                                <?php echo(htmlspecialchars_decode($outdated[$i]->description)); ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>