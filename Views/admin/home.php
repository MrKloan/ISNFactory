	<section class="jumbotron background1">
		<div class="container titre">
            <h1>Configurations</h1>
        </div>
	</section>

	<!-- Quand on (un)check un switch Professeur, modifier automatiquement en JS le switch correspondant de l'élève -->
    <section class="jumbotron background3 groups">
        <div class="container">
        	<div class="row">
        		<div class="col-xs-6 col-sm-6 col-md-6">
		            <form action="" method="POST">
		            	<div class="panel panel-default">
			                <table class="table table-hover">
			                    <thead>
			                        <th class="col-sm-8">Configurations</th>
			                        <th class="col-sm-2">Élèves</th>
			                        <th class="col-sm-2">Professeurs</th>
			                    </thead>
			                    <tbody>
			                        <tr>
			                            <td>Site en maintenance</td>
			                            <td colspan="2">
			                                <input class="switch" type="checkbox" name="maintenance" <?php echo($this->dispatcher->Core->Config->maintenance == 1 ? 'checked' : ''); ?>>
			                            </td>
			                        </tr>
			                        <tr>
			                            <td>Permettre l'enregistrement</td>
			                            <td colspan="2">
			                                <input class="switch" type="checkbox" name="allow_register" <?php echo($this->dispatcher->Core->Config->allow_register == 1 ? 'checked' : ''); ?>>
			                            </td>
			                        </tr>
			                        <tr>
			                        	<td>Essais avant blocage à la connexion</td>
			                        	<td colspan="2">
			                        		<input class="form-control" type="number" min="2" max="50" name="shield_attempts" <?php echo('value="'.$this->dispatcher->Core->Config->shield_attempts.'"'); ?>/>
			                        	</td>
			                        </tr>
			                        <tr>
			                        	<td>Durée de blocage</td>
			                        	<td colspan="2">
			                        		<div class="input-group">
			                        			<input class="form-control" type="number" min="2" max="25" name="shield_duracy" <?php echo('value="'.$this->dispatcher->Core->Config->shield_duracy.'"'); ?>/>
			                        			<span class="input-group-addon">minutes</span>
			                        		</div>
			                        	</td>
			                        </tr>
			                        <tr>
			                            <td>Calendrier</td>
			                            <td><input class="switch switch-student-calendar" type="checkbox" name="student-calendar" <?php echo($this->dispatcher->Core->Config->calendar == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->calendar >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-calendar" type="checkbox" name="prof-calendar" <?php echo($this->dispatcher->Core->Config->calendar >= 1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>Devoirs</td>
			                            <td><input class="switch switch-student-homeworks" type="checkbox" name="student-homeworks" <?php echo($this->dispatcher->Core->Config->homeworks == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->homeworks >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-homeworks" type="checkbox" name="prof-homeworks" <?php echo($this->dispatcher->Core->Config->homeworks >= 1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>Notes</td>
			                            <td><input class="switch switch-student-notes" type="checkbox" name="student-notes" <?php echo($this->dispatcher->Core->Config->notes == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->notes >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-notes" type="checkbox" name="prof-notes" <?php echo($this->dispatcher->Core->Config->notes >=1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>Informations</td>
			                            <td><input class="switch switch-student-infos" type="checkbox" name="student-infos" <?php echo($this->dispatcher->Core->Config->informations == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->informations >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-infos" type="checkbox" name="prof-infos" <?php echo($this->dispatcher->Core->Config->informations >= 1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>Liens</td>
			                            <td><input class="switch switch-student-links" type="checkbox" name="student-links" <?php echo($this->dispatcher->Core->Config->links == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->links >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-links" type="checkbox" name="prof-links" <?php echo($this->dispatcher->Core->Config->links >= 1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>Cours</td>
			                            <td><input class="switch switch-student-courses" type="checkbox" name="student-courses" <?php echo($this->dispatcher->Core->Config->courses == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->courses >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-courses" type="checkbox" name="prof-courses" <?php echo($this->dispatcher->Core->Config->courses >= 1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>Codiad</td>
			                            <td><input class="switch switch-student-codiad" type="checkbox" name="student-codiad" <?php echo($this->dispatcher->Core->Config->codiad == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->codiad >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-codiad" type="checkbox" name="prof-codiad" <?php echo($this->dispatcher->Core->Config->codiad >= 1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>Accès FTP</td>
			                            <td><input class="switch switch-student-ftp" type="checkbox" name="student-ftp" <?php echo($this->dispatcher->Core->Config->ftp == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->ftp >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-ftp" type="checkbox" name="prof-ftp" <?php echo($this->dispatcher->Core->Config->ftp >= 1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>Projets</td>
			                            <td><input class="switch switch-student-projects" type="checkbox" name="student-projects" <?php echo($this->dispatcher->Core->Config->projets == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->projets >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-projects" type="checkbox" name="prof-projects" <?php echo($this->dispatcher->Core->Config->projets >=1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>FAQ</td>
			                            <td><input class="switch switch-student-faq" type="checkbox" name="student-faq" <?php echo($this->dispatcher->Core->Config->faq == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->faq >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-faq" type="checkbox" name="prof-faq" <?php echo($this->dispatcher->Core->Config->faq >= 1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                            <td>Messagerie</td>
			                            <td><input class="switch switch-student-mails" type="checkbox" name="student-mails" <?php echo($this->dispatcher->Core->Config->mails == 2 ? 'checked' : ''); ?> <?php echo($this->dispatcher->Core->Config->mails >= 1 ? '' : 'disabled'); ?>></td>
			                            <td><input class="switch switch-prof-mails" type="checkbox" name="prof-mails" <?php echo($this->dispatcher->Core->Config->mails >= 1 ? 'checked' : ''); ?>></td>
			                        </tr>
			                        <tr>
			                        	<td colspan="3">
			                        		<input type="submit" name="submit_admin_config" class="btn btn-primary" value="Mettre à jour"/>
			                        	</td>
			                        </tr>
			                    </tbody>
			                </table>
		            	</div>
			        </form>
		        </div>

		        <div class="col-xs-6 col-sm-6 col-md-6">
                  <div class="thumbnail">
                        <div class="thumbnail-img">
                            <img src="<?php echo(STYLES.'img/picto_users.svg'); ?>" alt="Gestion des utilisateurs"/>
                        </div>
                        <div class="thumbnail-txt">
                            <a class="btn btn-lg btn-default btn-block" href="<?php echo(WEB_ROOT.'/extranet/admin/users'); ?>">Gestion des utilisateurs</a>
                        </div>
                    </div>
                </div> 

                <div class="col-xs-6 col-sm-6 col-md-6">
                  <div class="thumbnail">
                        <div class="thumbnail-img">
                            <img src="<?php echo(STYLES.'img/picto_bdd.svg'); ?>" alt="Gestion de la base de données"/>
                        </div>
                        <div class="thumbnail-txt">
                            <a class="btn btn-lg btn-default btn-block" href="<?php echo(WEB_ROOT.'/extranet/admin/sgbd'); ?>">Gestion de la base de données</a>
                        </div>
                    </div>
                </div> 

                <div class="col-xs-6 col-sm-6 col-md-6">
                 	<div class="thumbnail">
                        <div class="thumbnail-img">
                            <img src="<?php echo(STYLES.'img/picto_suppression.svg'); ?>" alt="Gestion de la base de données"/>
                        </div>
                        <div class="thumbnail-txt">
                            <a class="btn btn-lg btn-default btn-block" data-toggle="modal" data-target="#del_all_modal">Tout supprimer</a>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="del_all_modal" tabindex="-1" role="dialog" aria-labelledby="del_all_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="del_all_label">Suppression de toutes les données</h4>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body">
                                    Souhaitez-vous réellement supprimer toutes les données présentes sur le serveur ?<br/>(Utilisateurs Profs et Elèves, Cours, Projets, Notes, Fichiers, Mails, Liens, FAQ)
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" name="submit_del_all" class="btn btn-danger" value="Supprimer"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button> 
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

	        </div>
        </div>
    </section>