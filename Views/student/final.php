	<?php 
        $project = $this->dispatcher->model->getFinalProjectByGrade();
        if($project != false)
        {
            $group = $this->dispatcher->model->getGroup($project->id);
        }
    ?>
    <?php if($this->dispatcher->model->param): ?>
        <?php $final_project = $this->dispatcher->model->getFinalProjectById($this->dispatcher->model->param); ?>
        <section class="jumbotron background1">
            <div class="container titre">
                <h1><?php echo(($final_project->title) ? $final_project->title : "Ajoutez un nom"); ?></h1>
            </div>
        </section>

        <section class="jumbotron background3 enonce">
            <div class="container">
                <h2>Présentation<?php if($group)if($final_project->group == $group->id): ?><button type="button" class="btn btn-default" data-toggle="modal" data-target="#edit_modal" style="position:absolute;left:75%;">Editer</button></h2><?php endif; ?>
                <?php echo(($final_project->description) ? htmlspecialchars_decode($final_project->description) : "<p>Ajoutez une description</p>"); ?>
                <h2>Avancement</h2>
                <div class="progress progress-striped active">
                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo($final_project->progress); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo($final_project->progress); ?>%">
                        <?php echo($final_project->progress."%"); ?>
                    </div>
                </div>
                <h2>Changelog</h2>
                <?php echo(($final_project->changelog) ? htmlspecialchars_decode($final_project->changelog) : "<p>Ajoutez des modifications effectuées</p>"); ?>
                <hr />

                <?php if($group)if($final_project->group == $group->id): ?>
                <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="edit_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="edit_label">Edition</h4>
                            </div>
                            <form class="form-horizontal" role="form" action="" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="title" class="col-sm-3 control-label">Nom du projet</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="title_final_edit" class="form-control" id="title" value="<?php echo($final_project->title); ?>" placeholder="Nom du projet">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="img" class="col-sm-3 control-label">Logo</label>
                                        <div class="col-sm-9">
                                            <input type="file" name="logo_final_edit" class="form-control" style="border:none;box-shadow:none;" id="img">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="description" class="col-sm-3 control-label">Présentation</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="desc_final_edit" id="description" rows="10">
                                                <?php echo($final_project->description); ?>
                                            </textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="avancement" class="col-sm-3 control-label">Avancement</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="slider" value="" data-slider-min="0" data-slider-max="100" data-slider-step="5" data-slider-value="<?php echo($final_project->progress); ?>" data-slider-selection="after">
                                            <input type="hidden" id="slide" name="progress_final_edit" value="<?php echo($final_project->progress); ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="change" class="col-sm-3 control-label">Changelog</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="changelog_final_edit" id="change" rows="10">
                                                <?php echo($final_project->changelog); ?>
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            <div class="modal-footer">
                                <input type="hidden" name="yourproject" value="<?php echo(base64_encode(SALT.$final_project->group.PEPPER)); ?>" />
                                <input type="submit" name="submit_edit_yourproject" class="btn btn-primary" value="Modifier"/>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php $modules = $this->dispatcher->model->getModulesValue($project); ?>
                <div class="row">
                    <?php if($project->todo_enable): ?>
                            <div class="<?php echo($modules > 4 ? 'col-xs-6 col-sm-6 col-md-6' : 'col-xs-6 col-sm-6 col-md-6 col-md-offset-3'); ?>">
                                <div class="thumbnail" data-toggle="modal" data-target="#to-do_modal">
                                    <div class="thumbnail-img todo">
                                        <img src="<?php echo(STYLES.'img/todo.php?group='.base64_encode(SALT.$final_project->group.PEPPER)); ?>" alt="todolist"/>
                                    </div>
                                </div>
                            </div>

                            <?php $todo = $this->dispatcher->model->getTodoList($final_project->group); ?>
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
                                                    <tr id="todo_trash"><?php //TOP > 600 && LEFT > 195 ?>
                                                        <td colspan="3"><div class="icon-trash"></div></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php for($i=0; $i < count($todo); $i++): ?>
                                                <?php
                                                    $left=''; $top=''; $zindex='';
                                                    list($left,$top,$zindex) = explode('x',$todo[$i]->position);
                                                ?>
                                                <div class="note <?php echo($todo[$i]->color); ?>" style="<?php echo("left:".$left."px;top:".$top."px;z-index:".$zindex); ?>">
                                                    <?php echo($todo[$i]->description); ?>
                                                    <div class="deadline"><?php echo(date("d/m/y",strtotime($todo[$i]->deadline))); ?></div>
                                                    <div class="author"><?php echo($todo[$i]->name); ?></div> 
                                                    <form action="" method="POST">        
                                                        <input class="data" type="hidden" name="id_postit[]" value="<?php echo($todo[$i]->id); ?>"/>
                                                        <input type="hidden" id="position_<?php echo($todo[$i]->id); ?>" name="position_<?php echo($todo[$i]->id); ?>" value="<?php echo($todo[$i]->position); ?>" />
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
                                                                    $group_members = $this->dispatcher->model->getUsersByGroup($final_project->group);
                                                                    for($i=0 ; $i < count($group_members) ; $i++)
                                                                        echo('<option value="'.$group_members[$i]->firstname.'">'.$group_members[$i]->firstname.' '.$group_members[$i]->lastname.'</option>');
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
                                                <input type="hidden" name="id_group" value="<?php echo(base64_encode(SALT.$final_project->group.PEPPER)); ?>" />
                                                <input type="submit" name="submit_new_todo" class="btn btn-primary" value="Ajouter"/>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php if($project->codiad_enable && $this->dispatcher->Core->Config->codiad == 2): ?>
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

                        <?php if($project->upload_enable && $this->dispatcher->Core->Config->ftp == 2): ?>
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
                                                            $files = $this->dispatcher->model->getFiles($project->id, $final_project->group);
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
                                                <input type="hidden" name="upload_id" value="<?php echo(base64_encode(SALT.$project->id."_".$final_project->group.PEPPER)); ?>" />
                                                <input type="submit" name="submit_upload_project_file" class="btn btn-primary" value="Upload"/>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; endif; ?>
                </div> 
            </div>
        </section>

    <?php else: ?>
        <section class="jumbotron background1">
    		<div class="container titre">
                <h1>Projet Final</h1>
            </div>
    	</section>

        <?php if($project): ?>
        <section class="jumbotron background3 enonce">
            <div class="container">
                <h2>Enoncé</h2>
                <?php echo(htmlspecialchars_decode($project->description)); ?>
            </div>
        </section>

        <section class="jumbotron background1 groups">
            <div class="container">
                    <?php if($group && $group->locked): ?>
                        <?php
                            $members = $this->dispatcher->model->getUsersByGroup($group->id);
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">Votre groupe</div>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Elève</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for($i=0; $i<count($members); $i++): ?>
                                        <tr>
                                            <td><?php echo($i+1); ?></td>
                                            <td>
                                                <div class="col-md-4 col-md-offset-4">
                                                    <select class="form-control" disabled>
                                                        <option><?php echo($members[$i]->firstname." ".$members[$i]->lastname); ?></option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif($group): ?>
                        <?php if($this->dispatcher->model->getGroupChief($group->id)->email == $this->dispatcher->Core->User->getEmail()): ?>
                            <?php
                                $members = $this->dispatcher->model->getUsersByGroup($group->id);
                                $invitations = $this->dispatcher->model->getInvitationsByGroup($group->id);
                                $invitation_counter = 0;
                                $free_users = $this->dispatcher->model->getFreeUsersByGradeAndProject($this->dispatcher->Core->User->getGrade(), $project->id);
                            ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">Gérer votre groupe</div>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Elève</th>
                                                <th>Etat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php for($i=0; $i<$project->group_size; $i++): ?>
                                            <form action="" method="POST">
                                            <tr>
                                                <td><?php echo($i+1); ?></td>
                                                <?php if(count($members) >= $i+1): ?>
                                                    <td>
                                                        <select class="form-control" disabled>
                                                            <option><?php echo($members[$i]->firstname." ".$members[$i]->lastname); ?></option>
                                                        </select>
                                                    </td>
                                                    <td class="col-md-4">
                                                        <?php if($members[$i]->email == $this->dispatcher->Core->User->getEmail()): ?>
                                                            <input type="hidden" name="group_id" value="<?php echo(base64_encode(SALT.$group->id.PEPPER)); ?>" />
                                                            <input type="hidden" name="project_id" value="<?php echo(base64_encode(SALT.$project->id.PEPPER)); ?>" />
                                                            <button type="submit" name="submit_lock_group" class="btn btn-success">Verrouiller</button>
                                                            <button type="submit" name="submit_del_group" class="btn btn-danger">Supprimer</button>
                                                        <?php else: ?>
                                                            <input type="hidden" name="group_id" value="<?php echo(base64_encode(SALT.$group->id.PEPPER)); ?>" />
                                                            <input type="hidden" name="user_id" value="<?php echo(base64_encode(SALT.$members[$i]->id.PEPPER)); ?>" />
                                                            <button type="submit" name="submit_fire_member" class="btn btn-danger">Retirer du groupe</button>
                                                        <?php endif; ?>
                                                    </td>
                                                    <?php elseif(count($invitations) > $invitation_counter): ?>
                                                        <?php $invited = $this->dispatcher->model->getUserById($invitations[$invitation_counter]->to); $invitation_counter++; ?>
                                                        <td>
                                                            <select class="form-control" disabled>
                                                                <option><?php echo($invited->firstname.' '.$invited->lastname); ?></option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="group_id" value="<?php echo(base64_encode(SALT.$group->id.PEPPER)); ?>" />
                                                            <input type="hidden" name="user_id" value="<?php echo(base64_encode(SALT.$invited->id.PEPPER)); ?>" />
                                                            <button type="submit" name="submit_cancel_invitation" class="btn btn-danger">Retirer l'invitation</button>
                                                        </td>
                                                    <?php else: ?>
                                                        <td>
                                                            <select name="invited_user" class="form-control" <?php echo(count($free_users) == 0 ? 'disabled' : ''); ?>>
                                                                <?php if(count($free_users) > 0): ?>
                                                                    <?php for($j=0 ; $j < count($free_users) ; $j++): ?>
                                                                        <option value="<?php echo($free_users[$j]->id); ?>"><?php echo($free_users[$j]->firstname.' '.$free_users[$j]->lastname); ?></option>
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
                                            <?php endfor;?>
                                        </tbody>
                                    </table>
                            </div>
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
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php $invitations = $this->dispatcher->model->getInvitationsByUser($project->id); ?>
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
                                        <?php for($i=0 ; $i < count($invitations) ; $i++): ?>
                                            <?php $chief = $this->dispatcher->model->getGroupChief($invitations[$i]->group); ?>
                                            <tr>
                                                <td><?php echo($chief->firstname.' '.$chief->lastname); ?></td>
                                                <td>
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="invitation_id" value="<?php echo(base64_encode(SALT.$invitations[$i]->id.PEPPER)); ?>" />
                                                        <input type="hidden" name="project_id" value="<?php echo(base64_encode(SALT.$project->id.PEPPER)); ?>" />
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
                                    <input type="hidden" name="project_id" value="<?php echo(base64_encode(SALT.$project->id.PEPPER)); ?>" />
                                    <input type="submit" name="submit_create_group" value="Créer mon groupe" class="btn btn-lg btn-success" />
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>       
            </div>
        </section>

        <?php $groups = $this->dispatcher->model->getGroups($project->id); ?>
        <section class="jumbotron background3">
            <div class="container">
                <div class="row">
                    <h2>Projets</h2>

                    <?php if($group): ?>
                        <?php $yourproject = $this->dispatcher->model->getFinalProjectByGroup($group->id); ?>
                        <?php if($yourproject): ?>
                        <div class="col-xs-6 col-sm-3 col-md-3">
                            <div class="thumbnail">
                                <div class="thumbnail-img">
                                    <img src="<?php echo(($yourproject && $yourproject->logo != NULL) ? base64_decode($yourproject->logo) : STYLES.'img/logo_alt.svg'); ?>" alt="logo"/>
                                </div>
                                <div class="thumbnail-txt">
                                    <a href="<?php echo(WEB_ROOT.'/extranet/student/final/'.$yourproject->id); ?>" class="btn btn-default btn-lg" role="button"><em><strong>Votre Projet</strong></em></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; endif; ?>
                    <?php if($groups): ?>
                        <?php for($i=0; $i<count($groups); $i++): ?>
                            <?php if($group)if($groups[$i]->id == $group->id) continue; ?>
                            <?php $group_project = $this->dispatcher->model->getFinalProjectByGroup($groups[$i]->id); ?>
                            <?php if($group_project): ?>
                            <div class="col-xs-6 col-sm-3 col-md-3">
                                <div class="thumbnail">
                                    <div class="thumbnail-img">
                                        <img src="<?php echo(($group_project->logo !=NULL) ? base64_decode($group_project->logo) : STYLES.'img/logo_alt.svg'); ?>" alt="logo"/>
                                    </div>
                                    <div class="thumbnail-txt">
                                        <a href="<?php echo(WEB_ROOT.'/extranet/student/final/'.$group_project->id); ?>" class="btn btn-default btn-lg" role="button"><?php echo(($group_project->title != NULL) ? $group_project->title : "<em>Aucun nom</em>"); ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endfor;?>
                    <?php 
                        else :
                            echo("<div><h2>Aucun projet pour le moment.</h2></div>");
                    ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php else: ?>
            <section class="jumbotron background3 enonce">
                <div class="container">
                    <h2>Le projet final n'a pas encore commencé</h2>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
