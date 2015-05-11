	<?php 
        $grades = $this->dispatcher->model->getTermGrades(); 
        $project = $this->dispatcher->model->getFinalProject();
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
                    <h2>Présentation<button type="button" class="btn btn-default" data-toggle="modal" data-target="#edit_modal" style="position:absolute;left:75%;">Editer</button></h2>
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
                                                        <tr id="todo_trash">
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

                        <?php if($project->codiad_enable && $this->dispatcher->Core->Config->codiad >= 1): ?>
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

                            <?php if($project->upload_enable && $this->dispatcher->Core->Config->ftp >= 1): ?>
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
                            <?php endif; ?>
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
                    <h2>Enoncé   <input type="submit" class="modif btn btn-primary" data-toggle="modal" data-target="#modal_modif" value="Modifier"/></h2>
                    <?php echo(($project->description != NULL) ? htmlspecialchars_decode($project->description) : "<div><h2>Aucun énoncé créé</h2></div>"); ?>
                </div>
                <div class="modal fade" id="modal_modif" tabindex="-1" role="dialog" aria-labelledby="modif_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
                                <h4 class="modal-title" id="modif_label">Enoncé</h4>                     
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body form-group">
                                    <textarea class="form-control" name="desc_final" rows="5">
                                        <?php echo(($project->description != NULL) ? $project->description : ""); ?>
                                    </textarea>                           
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="final_id" value="<?php echo(base64_encode(SALT.$project->id.PEPPER)); ?>" />
                                    <input type="submit" name="submit_edit_desc" class="btn btn-primary" value="Modifier"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <section class="jumbotron <?php echo(($project) ? 'background1' : 'background3'); ?> groups">
            <div class="container">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Configuration</h4>
                    </div> 
                    <form action="" method="POST">
                        <table class="table table-striped table-hover">
                            <tbody>
                                <tr>
                                    <td>Échéance</td>
                                    <td>
                                        <div class="col-md-4 col-md-offset-4">
                                            <input type="text" name="date_end_final" class="form-control datepicker" value="<?php echo(($project && $project->date_end) ? $project->date_end  : "");?>">
                                        </div>
                                    </td> 
                                </tr>
                                <tr>
                                    <td>Taille des groupes</td>
                                    <td>
                                        <div class="col-md-4 col-md-offset-4">
                                            <input type="number" name="group_size" class="form-control" min="1" value="<?php echo(($project && $project->group_size) ? $project->group_size : ""); ?>">
                                        </div>
                                    </td> 
                                </tr>
                                <tr>
                                    <td>Codiad</td>
                                    <td>
                                        <input type="checkbox" name="codiad_enable" class="switch" data-size="small" <?php echo(($project && $project->codiad_enable && $this->dispatcher->Core->Config->codiad) ? "checked"  : "");?> <?php echo($this->dispatcher->Core->Config->codiad == 0 ? 'disabled' : ''); ?>>
                                    </td> 
                                </tr>
                                <tr>
                                    <td>To-do list</td>
                                    <td>
                                        <input type="checkbox" name="todo_enable" class="switch" data-size="small" <?php echo(($project && $project->todo_enable) ? "checked"  : "");?>>
                                    </td> 
                                </tr>
                                <tr>
                                    <td>Mise en ligne</td>
                                    <td>
                                        <input type="checkbox" name="ftp_enable" class="switch" data-size="small" <?php echo(($project && $project->upload_enable && $this->dispatcher->Core->Config->ftp) ? "checked"  : "");?> <?php echo($this->dispatcher->Core->Config->ftp == 0 ? 'disabled' : ''); ?>>
                                    </td> 
                                </tr>
                                <?php if($project): ?>
                                <tr>
                                    <td>Disponible</td>
                                    <td>
                                        <input type="checkbox" name="dispo_final" class="switch" data-size="small" <?php echo(($this->dispatcher->model->getProjectsfor($grades,$project->id)->enabled) ? "checked"  : "");?>>
                                    </td> 
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="list-group-item">
                            <input type="hidden" name="grades_final" value="<?php echo(base64_encode(serialize($grades))); ?>" />
                            <?php if($project): ?>
                                <input type="hidden" name="final_id" value="<?php echo(base64_encode(SALT.$project->id.PEPPER)); ?>" />
                                <input type="submit" name="submit_config_final" class="btn btn-primary" value="Envoyer" />
                            <?php else: ?>
                                <input type="submit" name="submit_create_final" class="btn btn-success" value="Créer le projet" />
                            <?php endif; ?>
                        </div>
                    </form> 
                </div>
            </div>
        </section>

        <?php if($project): ?>
            <?php $groups = $this->dispatcher->model->getGroups($project->id); ?>
        	<section class="jumbotron background3">
        		<div class="container">
                    <div class="row">
                        <h2>Projets</h2>

                        <?php if($groups): ?>
                            <?php for($i=0; $i<count($groups); $i++): ?>
                                <?php $group_project = $this->dispatcher->model->getFinalProjectByGroup($groups[$i]->id); ?>
                                <?php if($group_project): ?>
                                    <div class="col-xs-6 col-sm-3 col-md-3">
                                        <div class="thumbnail">
                                            <div class="thumbnail-img">
                                                <img src="<?php echo(($group_project->logo !=NULL) ? base64_decode($group_project->logo) : STYLES.'img/logo_alt.svg'); ?>" alt="logo"/>
                                            </div>
                                            <div class="thumbnail-txt">
                                                <a href="<?php echo(WEB_ROOT.'/extranet/professor/final/'.$group_project->id); ?>" class="btn btn-default btn-lg" role="button"><?php echo(($group_project->title != NULL) ? $group_project->title : "<em>Aucun nom</em>"); ?></a>
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
        <?php endif; ?>
    <?php endif; ?>