	<section class="jumbotron background1">
		<div class="container titre">
            <h1>Utilisateurs</h1>
        </div>
	</section>

    <section class="jumbotron background3 groups">
        <div class="container">
            <div class="panel-group list-group" id="users">
              <div class="panel panel-default">
                <div class="list-group-item">
                    <a data-toggle="modal" data-target="#create_modal" class="btn btn-primary">Ajouter un utilisateur</a>
                </div>

                <a class="list-group-item" data-toggle="collapse" data-parent="#users" href="#admins">Administrateurs</a>
                <div id="admins" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table class="table table-striped table-hover table-responsive">
                            <thead>
                                <tr><th>Prénom</th><th>Nom</th><th>E-mail</th><th>Dernière connexion</th><th>Dernière IP</th><th>Connexions totales</th><th></th></tr>
                            </thead>
                            <tbody>
                                <?php
                                    $admins = $this->dispatcher->model->getUsersByRole("role_admin");
                                    for($i=0 ; $i < count($admins) ; $i++)
                                        echo("<tr><td>".$admins[$i]->firstname."</td><td>".$admins[$i]->lastname."</td><td><a href=\"mailto:".$admins[$i]->email."\">".$admins[$i]->email."</a></td><td>".$admins[$i]->last_login."</td><td>".$admins[$i]->last_ip."</td><td>".$admins[$i]->nb_connection."</td><td><form action=\"\" method=\"POST\"><a href=\"#\" title=\"Modifier\" class=\"icon-user edit-user\" data-toggle=\"modal\" data-target=\"#edit_modal_".sha1($admins[$i]->id)."\"></a><input type=\"hidden\" name=\"user_id\" value=\"".base64_encode(SALT.$admins[$i]->id.PEPPER)."\"/><button type=\"submit\" name=\"submit_del_user\" class=\"btn btn-link icon-cancel remove-user\"></button></form></td></tr>");
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <a class="list-group-item" data-toggle="collapse" data-parent="#users" href="#professors">Professeurs</a>
                <div id="professors" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table class="table table-striped table-hover table-responsive">
                            <thead>
                                <tr><th>Prénom</th><th>Nom</th><th>E-mail</th><th>Dernière connexion</th><th>Dernière IP</th><th>Connexions totales</th><th></th></tr>
                            </thead>
                            <tbody>
                                <?php
                                    $profs = $this->dispatcher->model->getUsersByRole("role_professor");
                                    for($i=0 ; $i < count($profs) ; $i++)
                                        echo("<tr><td>".$profs[$i]->firstname."</td><td>".$profs[$i]->lastname."</td><td><a href=\"mailto:".$profs[$i]->email."\">".$profs[$i]->email."</a></td><td>".$profs[$i]->last_login."</td><td>".$profs[$i]->last_ip."</td><td>".$profs[$i]->nb_connection."</td><td><form action=\"\" method=\"POST\"><a href=\"#\" title=\"Modifier\" class=\"icon-user edit-user\" data-toggle=\"modal\" data-target=\"#edit_modal_".sha1($profs[$i]->id)."\"></a><input type=\"hidden\" name=\"user_id\" value=\"".base64_encode(SALT.$profs[$i]->id.PEPPER)."\"/><button type=\"submit\" name=\"submit_del_user\" class=\"btn btn-link icon-cancel remove-user\"></button></form></td></tr>");
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <a class="list-group-item" data-toggle="collapse" data-parent="#users" href="#students">Élèves</a>
                <div id="students" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table class="table table-striped table-hover table-responsive">
                            <thead>
                                <tr><th>Prénom</th><th>Nom</th><th>E-mail</th><th>Dernière connexion</th><th>Dernière IP</th><th>Connexions totales</th><th></th></tr>
                            </thead>
                            <tbody>
                                <?php
                                    $students = $this->dispatcher->model->getUsersByRole("role_student");
                                    for($i=0 ; $i < count($students) ; $i++)
                                        echo("<tr><td>".$students[$i]->firstname."</td><td>".$students[$i]->lastname."</td><td><a href=\"mailto:".$students[$i]->email."\">".$students[$i]->email."</a></td><td>".$students[$i]->last_login."</td><td>".$students[$i]->last_ip."</td><td>".$students[$i]->nb_connection."</td><td><form action=\"\" method=\"POST\"><a href=\"#\" title=\"Modifier\" class=\"icon-user edit-user\" data-toggle=\"modal\" data-target=\"#edit_modal_".sha1($students[$i]->id)."\"></a><input type=\"hidden\" name=\"user_id\" value=\"".base64_encode(SALT.$students[$i]->id.PEPPER)."\"/><button type=\"submit\" name=\"submit_del_user\" class=\"btn btn-link icon-cancel remove-user\"></button></form></td></tr>");
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>

              <?php // Créer un utilisateur ?>
              <?php $grades = $this->dispatcher->model->getGrades(); ?>
              <div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-labelledby="create_label" aria-hidden="true">
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
                                            <label for="new_user_lastname">Nom</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="new_user_lastname" id="new_user_lastname" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="new_user_firstname">Prénom</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="new_user_firstname" id="new_user_firstname" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="new_user_email">Adresse e-mail</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="email" name="new_user_email" id="new_user_email" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="new_user_password">Mot de passe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="password" name="new_user_password" id="new_user_password" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="new_user_passconf">Confirmer le mot de passe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="password" name="new_user_passconf" id="new_user_passconf" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="new_user_grade">Classe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select name="new_user_grade" id="new_user_grade" class="form-control" required>
                                                <option value="NULL">Aucune</option>
                                                <?php for($j=0 ; $j < count($grades) ; $j++): ?>
                                                    <option value="<?php echo($grades[$j]->id); ?>"><?php echo($grades[$j]->grade); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="new_user_role">Rôle</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select name="new_user_role" id="new_user_role" class="form-control" required>
                                                <option value="role_student">Élève</option>
                                                <option value="role_professor">Professeur</option>
                                                <option value="role_admin">Administrateur</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" name="submit_admin_create" class="btn btn-primary" value="Ajouter"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php //Mettre à jour un utilisateur ?>
                <?php
                    $users = $this->dispatcher->model->getUsers();
                    for($i=0 ; $i < count($users) ; $i++):
                ?>
                <div class="modal fade" id="<?php echo('edit_modal_'.sha1($users[$i]->id)); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo('edit_label_'.$users[$i]->id); ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="<?php echo('edit_label_'.$users[$i]->id); ?>">Modifier l'utilisateur</h4>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="edit_user_email">Nouvel e-mail</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="emai" name="edit_user_email" id="edit_user_email" class="form-control" placeHolder="<?php echo($users[$i]->email); ?>"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="edit_user_password">Nouveau mot de passe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="password" name="edit_user_password" id="edit_user_password" class="form-control" placeHolder="********"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="edit_user_passconf">Confirmer le mot de passe</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="password" name="edit_user_passconf" id="edit_user_passconf" class="form-control" placeHolder="********"/>
                                        </div>
                                    </div>
                                    <?php if($users[$i]->role == "role_student"): ?>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="edit_user_grade">Modifier la classe</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="edit_user_grade" id="edit_user_grade" class="form-control">
                                                    <?php //<option value="NULL">Aucune</option> ?>
                                                    <?php for($j=0 ; $j < count($grades) ; $j++): ?>
                                                        <option value="<?php echo($grades[$j]->id); ?>" <?php echo($users[$i]->grade == $grades[$j]->id ? 'selected' : ''); ?>><?php echo($grades[$j]->grade); ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="edit_user_role">Modifier le rôle</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select name="edit_user_role" id="edit_user_role" class="form-control">
                                                <option value="role_student" <?php echo($users[$i]->role == "role_student" ? 'selected' : ''); ?>>Élève</option>
                                                <option value="role_professor" <?php echo($users[$i]->role == "role_professor" ? 'selected' : ''); ?>>Professeur</option>
                                                <option value="role_admin" <?php echo($users[$i]->role == "role_admin" ? 'selected' : ''); ?>>Administrateur</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="edit_user_validated">Validé</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="checkbox" name="edit_user_validated" id="edit_user_validated" class="switch" value="1" <?php echo($users[$i]->validated ? 'checked' : ''); ?>/>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="user_id" value="<?php echo(base64_encode(SALT.$users[$i]->id.PEPPER)); ?>"/>
                                    <input type="submit" name="submit_admin_edit" class="btn btn-primary" value="Mettre à jour"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>