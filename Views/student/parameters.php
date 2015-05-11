    <section class="jumbotron background1">
        <div class="container titre">
            <h1>Paramètres</h1>
        </div>
    </section>

    <section class="jumbotron background3 groups">
        <div class="container">
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <div class="panel panel-default parameters">
                        <div class="panel-heading head"><?php echo($this->dispatcher->Core->User->getFirstname(). " " .$this->dispatcher->Core->User->getLastname()); ?></div>
                        <div class="jumbotron">
                            <p>
                                E-mail : <a href="mailto:<?php echo($this->dispatcher->Core->User->getEmail()); ?>"><?php echo($this->dispatcher->Core->User->getEmail()); ?></a><br />
                                Classe de : <?php echo ($this->dispatcher->model->getGradeById($this->dispatcher->Core->User->getGrade())->grade) ?>
                            </p>
                            <hr />
                            <p class="foot">
                                <?php if($this->dispatcher->Core->User->getLastLogin()!= NULL): ?>
                                    Dernière connexion le <?php echo(date('d/m/Y à H:i:s', strtotime($this->dispatcher->Core->User->getLastLogin()))); ?><br />
                                    Dernière IP de connexion : <?php echo($this->dispatcher->Core->User->getLastIP()); ?>
                                <?php else: ?>
                                    Première connexion ! 
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-md-6">
                    <div class="panel panel-default parameters">
                        <div class="panel-heading head">Modifier le mot de passe</div>
                        <div class="jumbotron">
                            <form action="" method="POST">
                                    <input type="password" name="oldpass" placeholder="Ancien mot de passe" class="form-control" /><br />
                                    <input type="password" name="password" placeHolder="Nouveau mot de passe" class="form-control" /><br />
                                    <input type="password" name="passconf" placeHolder="Confirmer le mot de passe" class="form-control" /><br />
                                    <input type="submit" name="submit_edit_pwd" class="btn btn-primary" value="Modifier" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>