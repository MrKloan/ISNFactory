	<section class="jumbotron background1">
		<div class="container titre">
            <h1>Utilisateurs</h1>
        </div>
	</section>

    <section class="jumbotron background3">
        <div class="container">
            <div class="panel-group list-group" id="grades">
              <div class="panel panel-default">
                <div class="list-group-item">
                    <a data-toggle="modal" data-target="#grade_modal" href="#validation" class="btn btn-primary">Créer une classe</a>
                    <a data-toggle="collapse" data-parent="#grades" href="#validation" class="btn btn-warning">En attente de validation</a>
                </div>
                <div id="validation" class="panel-collapse collapse">
                    <div class="panel-body">
                            <table class="table table-striped table-hover table-responsive">
                                <thead>
                                    <tr><th>Prénom</th><th>Nom</th><th>E-mail</th><th>Classe</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $grades = $this->dispatcher->model->getGradesByTeacher();
                                        $nb = 0;
                                        for($i=0; $i < count($grades); $i++){
                                            $students = $this->dispatcher->model->getUsersByValidatedGrade($grades[$i]->id, 0);
                                            if(count($students) != 0)
                                            {
                                                for($j=0; $j < count($students); $j++)
                                                    echo("<tr><td>".$students[$j]->firstname."</td><td>".$students[$j]->lastname."</td><td><a href=mailto:".$students[$j]->email.">".$students[$j]->email."</a></td><td>".$grades[$i]->grade."</td><td><form action=\"\" method=\"POST\"><input type=\"hidden\" name=\"valid_id\" value=\"".base64_encode(SALT.$students[$j]->id.PEPPER)."\" /><button type=\"submit\" name=\"submit_prof_valid\" class=\"btn btn-success\">Valider</button><button type=\"submit\" name=\"submit_prof_refuse\" class=\"btn btn-danger\">Refuser</button></form></td></tr>");
                                                $nb++;
                                            }
                                        }
                                        if($nb == 0)
                                            echo("<tr><td colspan=\"5\">Aucune entrée</td></tr>");
                                    ?>
                                </tbody>
                            </table>
                    </div>
                </div>
                
                <?php 
                    for($i=0; $i < count($grades); $i++):
                ?>
                <a class="list-group-item" data-toggle="collapse" data-parent="#grades" href="#<?php echo(str_replace(" ","_", $grades[$i]->grade)); ?>"><?php echo($grades[$i]->grade) ?></a>
                <div id="<?php echo(str_replace(" ","_", $grades[$i]->grade)); ?>" class="panel-collapse collapse">
                    <div class="panel-body">
                        <form action="" method="POST">
                            <table class="table table-striped table-hover table-responsive">
                                <thead>
                                    <tr><th>Prénom</th><th>Nom</th><th>E-mail</th><th>Dernière connexion</th><th>Connexions totales</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $students = $this->dispatcher->model->getUsersByValidatedGrade($grades[$i]->id,1);
                                        if(count($students) == 0)
                                            echo("<tr><td colspan=\"6\">Aucune entrée</td></tr>");
                                        else
                                            for($j=0; $j < count($students); $j++)
                                                echo("<tr><td>".$students[$j]->firstname."</td><td>".$students[$j]->lastname."</td><td><a href=mailto:".$students[$j]->email.">".$students[$j]->email."</a></td><td>".$students[$j]->last_login."</td><td>".$students[$j]->nb_connection."</td><td><form action=\"\" method=\"POST\"><a title=\"Modifier\" class=\"icon-user edit-user\" data-toggle=\"modal\" data-target=\"#edit_modal_".sha1($students[$j]->id)."\"></a><input type=\"hidden\" name=\"user_id\" value=\"".base64_encode(SALT.$students[$j]->id.PEPPER)."\" /><button type=\"submit\" name=\"submit_del_user\" class=\"btn-link icon-cancel remove-user\"></button></a></form></td></tr>");
                                    ?>
                                </tbody>
                            </table>
                            <div class="list-group-item">
                                <input type="hidden" name="grade_id" value="<?php echo(base64_encode(SALT.$grades[$i]->id.PEPPER)); ?>" />
                                <button name="submit_prof_del_class" class="btn btn-danger">Supprimer cette classe</button>
                                <a href="#" data-toggle="modal" data-target="#create_modal_<?php echo(sha1($grades[$i]->id)); ?>" class="btn btn-primary">Ajouter un élève</a>
                                <button name="<?php echo(($grades[$i]->allow_register) ? "submit_register_ko" : "submit_register_ok"); ?>" class="btn <?php echo(($grades[$i]->allow_register) ? "btn-success" : "btn-danger"); ?>"><?php echo(($grades[$i]->allow_register) ? "Désactiver" : "Activer"); ?> l'enregistrement d'élèves</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endfor; ?>

            <!-- Ajouter une classe -->
            <div class="modal fade" id="grade_modal" tabindex="-1" role="dialog" aria-labelledby="grade_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="grade_label">Ajouter une classe</h4>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="grade_create">Nom de la classe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="grade_create" id="grade_create" class="form-control" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" name="submit_prof_class_create" class="btn btn-primary" value="Créer"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

              <!-- Créer un utilisateur -->
              <?php 
                for($i=0; $i < count($grades); $i++):
              ?>
              <div class="modal fade" id="create_modal_<?php echo(sha1($grades[$i]->id)); ?>" tabindex="-1" role="dialog" aria-labelledby="create_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="create_label">Ajouter un utilisateur</h4>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="user_lastname_create">Nom</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="user_lastname_create" id="user_lastname_create" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="user_firstname_create">Prénom</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="user_firstname_create" id="user_firstname_create" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="user_mail_create">Adresse e-mail</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="email" name="user_mail_create" id="user_mail_create" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="user_password_create">Mot de passe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="password" name="user_password_create" id="user_password_create" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="user_passconf_create">Confirmer le mot de passe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="password" name="user_passconf_create" id="user_passconf_create" class="form-control" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="user_grade_create" value="<?php echo(base64_encode(SALT.$grades[$i]->id.PEPPER)); ?>" />
                                    <input type="submit" name="submit_prof_create" class="btn btn-primary" value="Ajouter"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>

                <!-- Mettre à jour un utilisateur -->
                <?php 
                    for($i=0; $i < count($grades); $i++):
                        $students = $this->dispatcher->model->getUsersByGrade($grades[$i]->id);
                        for($j=0; $j < count($students); $j++):
                ?>
                <div class="modal fade" id="edit_modal_<?php echo(sha1($students[$j]->id)); ?>" tabindex="-1" role="dialog" aria-labelledby="edit_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="edit_label">Modifier l'utilisateur</h4>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="user_grade_edit">Classe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select name="user_grade_edit" id="user_grade_edit" class="form-control">
                                                <?php for($k=0 ; $k < count($grades) ; $k++): ?>
                                                    <option value="<?php echo($grades[$k]->id); ?>" <?php echo(($grades[$k]->id == $students[$j]->grade) ? 'selected' : ''); ?>><?php echo($grades[$k]->grade); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="user_mail_edit">Nouvel e-mail</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="email" name="user_mail_edit" id="user_mail_edit" class="form-control" placeHolder="<?php echo($students[$j]->email); ?>" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="user_password_edit">Nouveau mot de passe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="password" name="user_password_edit" id="user_password_edit" class="form-control" placeHolder="********" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="user_passconf_edit">Confirmer le mot de passe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="password" name="user_passconf_edit" id="user_passconf_edit" class="form-control" placeHolder="********" />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="user_id" value="<?php echo(base64_encode(SALT.$students[$j]->id.PEPPER)); ?>">
                                    <input type="submit" name="submit_prof_edit" class="btn btn-primary" value="Mettre à jour"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endfor; endfor; ?>
                </div>
            </div>
        </div>
    </section>