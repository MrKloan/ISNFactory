<?php
    $diff = $this->dispatcher->model->getDiffGrades();
        
    $array = array(array());
    for($i=0; $i < count($diff); $i++)
        foreach ($this->dispatcher->model->getIdGrades(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))) as $key => $value)
            $array[$i][] = $value->id;
    
    for($i=0 ; $i < count($diff) ; $i++):
?>

    <section class="jumbotron background1">
        <div class="container titre">
            <h1><?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?></h1>
        </div>
    </section>

    <?php
        $projects = $this->dispatcher->model->getProjectsByGrades($array[$i]);
        $projects_for = $this->dispatcher->model->getProjectsFor($array[$i]);
    ?>
    <section class="jumbotron background3 groups">
        <div class="container">
            <div class="panel panel-default">
                <table class="table table-hover">
                    <thead>
                        <th class="col-sm-10">Projets</th>
                        <th class="col-sm-1">Disponible</th>
                        <th class="col-sm-1"><span class="glyphicon glyphicon-plus ajout" data-toggle="modal" data-target="#new_project_modal_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>"></span></th>
                    </thead>
                    <tbody>
                        <?php if(!$projects): ?>
                            <tr><td colspan="3">Aucun projet en cours.</td>
                        <?php
                            else:
                                for($j=0 ; $j < count($projects) ; $j++):
                        ?>
                            <form action="" method="POST">
                                <tr>
                                    <td>Projet <?php echo($projects[$j]->number); ?> : <?php echo($projects[$j]->title); ?></td>
                                    <td>
                                        <?php //Si le projet est activé pour une classe, il l'est pour toutes les autres ?>
                                        <?php if($projects_for[0][$j]->enabled == 1): ?>
                                            <button type="submit" title="Rendre indisponible" class="btn btn-sm btn-success" name="submit_dispo_ok">Oui</button>
                                        <?php else: ?>
                                            <button type="submit" title="Rendre disponible" class="btn btn-sm btn-danger" name="submit_dispo_ko">Non</button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a title="Modifier" class="icon-pencil update-courses" data-toggle="modal" data-target="#edit_project_modal_<?php echo(sha1($projects[$j]->id)); ?>"></a>
                                        <input type="hidden" name="project_id" value="<?php echo(base64_encode(SALT.$projects[$j]->id.PEPPER)); ?>" />
                                        <button type="submit" title="Supprimer" name="submit_del_project" class="btn-link icon-cancel remove-courses"></button>
                                    </td>
                                </tr>
                            </form>
                        <?php endfor; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="modal fade" id="new_project_modal_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>" tabindex="-1" role="dialog" aria-labelledby="project_label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <div class="modal-title" id="project_label">  
                                <h4>Ajout de projet</h4> 
                            </div>                          
                        </div>
                        <form class="form-horizontal" role="form" action="" method="POST">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="new_number" class="col-sm-3 control-label">Numéro</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="new_number" id="new_number" min="1" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new_title" class="col-sm-3 control-label">Projet</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="new_title" name="new_title" placeholder="Nom du Projet" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="<?php echo('new_date_end'.$diff[$i]->id); ?>" class="col-sm-3 control-label">Echéance</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control datepicker" id="<?php echo('new_date_end'.$diff[$i]->id); ?>" name="new_date_end" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new_coeff" class="col-sm-3 control-label">Coefficient</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="any" min="0.5" class="form-control" id="new_coeff" name="new_coeff" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new_group_type_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>" class="col-sm-3 control-label">Type de projet</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="new_group_type_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>" name="new_group_type" required>
                                            <option value="1">Individuel</option>
                                            <option value="2">En groupe</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="new_group_div_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>">
                                    <div class="form-group">
                                        <label for="new_group_size" class="col-sm-3 control-label">Taille du groupe</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="new_group_size" name="new_group_size" min="2">
                                                <span class="input-group-addon">élèves</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="<?php echo('new_date_group'.$diff[$i]->id); ?>" class="col-sm-3 control-label">Échéance de formation</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control datepicker" id="<?php echo('new_date_group'.$diff[$i]->id); ?>" name="new_date_group">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new_codiad" class="col-sm-3 control-label">Codiad</label>
                                    <div class="col-sm-9">
                                        <input type="checkbox" class="switch" id="new_codiad" name="new_codiad" <?php echo($this->dispatcher->Core->Config->codiad == 0 ? 'disabled' : ''); ?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new_todo" class="col-sm-3 control-label">To-do list</label>
                                    <div class="col-sm-9">
                                        <input type="checkbox" class="switch" id="new_todo" name="new_todo">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new_ftp" class="col-sm-3 control-label">Mise en ligne</label>
                                    <div class="col-sm-9">
                                        <input type="checkbox" class="switch" id="new_ftp" name="new_ftp" <?php echo($this->dispatcher->Core->Config->ftp == 0 ? 'disabled' : ''); ?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new_subject_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>" class="control-label">Enoncé</label>
                                    <textarea class="form-control" id="new_subject_<?php echo(substr($diff[$i]->grade,0,strpos($diff[$i]->grade," "))); ?>" name="new_subject" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" class="btn btn-primary" value="Ajouter" name="submit_new_project" />
                                <input type="hidden" name="grades_id" value="<?php echo(base64_encode(serialize($array[$i]))); ?>" />
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php
                if($projects)
                    for($j=0 ; $j < count($projects) ; $j++):
            ?>
            <div class="modal fade" id="edit_project_modal_<?php echo(sha1($projects[$j]->id)); ?>" tabindex="-1" role="dialog" aria-labelledby="edit_label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <div class="modal-title" id="edit_label">  
                                <h4>Modifier le projet</h4> 
                            </div>                          
                        </div>
                        <form class="form-horizontal" role="form" action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="edit_number" class="col-sm-3 control-label">Numéro</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="edit_number" id="edit_number" min="1" value="<?php echo($projects[$j]->number); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="edit_title" class="col-sm-3 control-label">Projet</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="edit_title" name="edit_title" placeholder="Nom du Projet" value="<?php echo($projects[$j]->title); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="<?php echo('edit_date_end'.$projects[$j]->id); ?>" class="col-sm-3 control-label">Echéance</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control datepicker" id="<?php echo('edit_date_end'.$projects[$j]->id); ?>" name="edit_date_end" value="<?php echo($projects[$j]->date_end); ?>">
                                    </div>
                                </div>
                                <?php if($projects[$j]->group_size > 1): ?>
                                    <div class="form-group">
                                        <label for="<?php echo('edit_date_group'.$projects[$j]->id); ?>" class="col-sm-3 control-label">Échéance de formation</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control datepicker" id="<?php echo('edit_date_group'.$projects[$j]->id); ?>" name="edit_date_group" value="<?php echo($projects[$j]->date_group); ?>">
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="edit_codiad" class="col-sm-3 control-label">Codiad</label>
                                    <div class="col-sm-9">
                                        <input type="checkbox" class="switch" id="edit_codiad" name="edit_codiad" <?php echo($projects[$j]->codiad_enable && $this->dispatcher->Core->Config->codiad ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->codiad == 0 ? 'disabled' : ''); ?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="edit_todo" class="col-sm-3 control-label">To-do list</label>
                                    <div class="col-sm-9">
                                        <input type="checkbox" class="switch" id="edit_todo" name="edit_todo" <?php echo($projects[$j]->todo_enable ? 'checked' : ''); ?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="edit_ftp" class="col-sm-3 control-label">Mise en ligne</label>
                                    <div class="col-sm-9">
                                        <input type="checkbox" class="switch" id="edit_ftp" name="edit_ftp" <?php echo($projects[$j]->upload_enable && $this->dispatcher->Core->Config->ftp ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->ftp == 0 ? 'disabled' : ''); ?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="edit_subject_<?php echo($projects[$j]->id); ?>" class="control-label">Enoncé</label>
                                    <textarea class="form-control" id="edit_subject_<?php echo($projects[$j]->id); ?>" name="edit_subject" rows="5"><?php echo($projects[$j]->description); ?></textarea>
                                </div>
                                <input type="file" name="edit_file" />
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fichier</th>
                                            <th>Supprimer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $files = unserialize(base64_decode($projects[$j]->files_url));
                                            if($files)
                                                for($k=0; $k < count($files); $k++)
                                                    echo("<tr><td>".substr($files[$k],strrpos($files[$k], "/")+1,strlen($files[$k]))."</td><td><form action=\"\" method=\"POST\" ><input type=\"hidden\" name=\"file_id\" value=\"".$projects[$j]->id."_".$k."\"/><button type=\"submit\" name=\"submit_del_file\" class=\"btn btn-link icon-cancel remove-courses\"></a></form></td></tr>");
                                            else
                                                echo("<tr><td colspan=\"2\">Aucune entrée.</td></tr>");
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="project_id" value="<?php echo(base64_encode(SALT.$projects[$j]->id.PEPPER)); ?>" />
                                <input type="submit" class="btn btn-primary" value="Modifier" name="submit_edit_project" />
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endfor; ?>

        </div>
    </section>
<?php endfor; ?>