<?php if($this->dispatcher->Core->Config->maintenance < 1): ?>
	<section id="connect-panel" class="collapse slide">
        <div class="carousel-inner">
            <?php $grades = $this->dispatcher->model->getGrades(); ?>

            <div class="item active">
                <div class="container">
                    <form action="" method="POST" class="form-signin" role="form">
                        <h2 class="form-signin-heading">Connexion</h2>
                        <input type="email" name="email" class="form-control" placeholder="Adresse mail" required autofocus>
                        <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                        
                        <a data-toggle="modal" data-target="#forgot_pwd" class="btn btn-link">Mot de passe oublié</a>

                        <div class="btn-group btn-group-justified">
                            <div class="btn-group">
                                <button class="btn" id="btn-connexion" type="submit" name="submit_login">Connexion</button>
                            </div>
                            <?php if($this->dispatcher->Core->Config->allow_register && $grades != false): ?>
                                <div class="btn-group">
                                    <button class="btn" id="btn-register" data-target="#connect-panel" data-slide-to="1">Enregistrement</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <?php if($this->dispatcher->Core->Config->allow_register && $grades != false): ?>
                <div class="item">
                    <div class="container">
                        <form action="" method="POST" class="form-signin" role="form">
                            <h2 class="form-signin-heading">Enregistrement</h2>
                            <input type="text" name="firstname" class="form-control" placeholder="Prénom" required autofocus>
                            <input type="text" name="lastname" class="form-control" placeholder="Nom" required>
                            <input type="email" name="email" class="form-control" placeholder="Adresse mail" required>
                            <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                            <input type="password" name="passconf" class="form-control" placeholder="Confirmation" required>
                            <select name="grade" class="form-control" required>
                                <?php for($j=0 ; $j < count($grades) ; $j++): ?>
                                    <option value="<?php echo($grades[$j]->id); ?>"><?php echo($grades[$j]->grade); ?></option>
                                <?php endfor; ?>
                            </select>
                            <?php echo recaptcha_get_html(PUBLIC_KEY); ?>
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button class="btn" id="btn-connexion" type="submit" name="submit_signin">Inscription</button>
                                </div>
                                <div class="btn-group">
                                    <button class="btn" id="btn-register" data-target="#connect-panel" data-slide-to="0">Retour</button>
                                </div>   
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="modal fade" id="forgot_pwd" tabindex="-1" role="dialog" aria-labelledby="forgot_pwd_label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="forgot_pwd_label">Me renvoyer un mot de passe</h4>
                        </div>
                        <form action="" method="POST">
                            <div class="modal-body form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="forgot_email">E-mail</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="email" name="forgot_email" id="forgot_email" class="form-control" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                               <input type="submit" name="submit_forgot_pwd" class="btn btn-primary" value="Envoyer"/>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
	</section>
<?php endif; ?>

	<section class="jumbotron background1">
		<div class="container">
			<!--<span class="icon-down-dir"></span>-->
            <img src="<?php echo(STYLES.'img/logo_av_bandeau_alt.svg'); ?>" alt="logo"/>
            <h1>Informatique et Sciences du Numérique</h1>
            <p>
            	L'ISN est une option proposée aux élèves de première et terminale S<br/>reposant sur une pédagogie par projets.<br/>Cet enseignement de 2 heures par semaine se compose<br/>de séances magistrales suivies d'heures de pratique sur ordinateur.<br/>Chacun des TP effectué introduit un nouveau niveau<br/>en programmation mais aussi en compréhension<br/>du fonctionnement des dispositifs informatiques.
            </p>
        </div>
	</section>

	<section class="jumbotron background2">
		<div class="container">
			<!--<span class="icon-down-dir"></span>-->
			<div class="row">
				<div class="col-xs-6 col-md-3">
					<figure>
						<img src="<?php echo(STYLES.'img/point1.svg'); ?>" alt="Point1"/>
						<figcaption>Maîtriser les outils et systèmes<br/>numériques</figcaption>
					</figure>
				</div>
				<div class="col-xs-6 col-md-3">
					<figure>
						<img src="<?php echo(STYLES.'img/point2.svg'); ?>" alt="Point2"/>
						<figcaption>Développer ses compétences<br/>en informatique</figcaption>
					</figure>
				</div>
				<div class="col-xs-6 col-md-3">
					<figure>
						<img src="<?php echo(STYLES.'img/point3.svg'); ?>" alt="Point3"/>
						<figcaption>Conduire un projet en équipe</figcaption>
					</figure>
				</div>
				<div class="col-xs-6 col-md-3">
					<figure>
						<img src="<?php echo(STYLES.'img/point4.svg'); ?>" alt="Point4"/>
						<figcaption>Présenter sa démarche<br/>face à un jury</figcaption>
					</figure>
				</div>
			</div>  		
        </div>
	</section>

	<section id="myCarousel" class="carousel slide" data-ride="carousel">
        <!--<span class="icon-down-dir"></span>-->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>

        <div class="carousel-inner">

            <div class="item active">
                <img src="<?php echo(STYLES.'img/slide1.jpg'); ?>" alt="First slide">
                <div class="container">
                    <div class="carousel-caption">
                	   <div class="carousel-disposition">
                  		    <h1 class="carousel-title">Introduction<br/>à la science informatique :</h1>
                  		    <p class="carousel-txt">
                                Information numérique,<br/>architectures, algorithmique,<br/>langages de programmation.<br/><br/>
                                > Des notions permettant<br/>de <span class="highlight">comprendre</span> les usages,<br/>les créations,les applications<br/>et les enjeux de l'informatique.<br/><br/>
                                > De <span class="highlight">nombreux domaines</span><br/>d'application abordés, découverte<br/>des métiers et des entreprises<br/>du secteur du numérique :<br/>graphisme, sécurité, robotique,<br/>communication, etc.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item">
            	<img src="<?php echo(STYLES.'img/slide2.jpg'); ?>" alt="Second slide">
                <div class="container">
                    <div class="carousel-caption">
                        <div class="carousel-disposition">
                  		    <h1 class="carousel-title">Enseignement :</h1>
                  		    <p class="carousel-txt">
                                Un enseignement de <span class="highlight">2 heures</span><br/>par semaine.<br/><br/>
                                > Mise en lumière des <span class="highlight">nouvelles<br/>problématiques</span> du numérique :<br/>questions éthiques et juridiques<br/>de la nouvelle société numérique.<br/><br/>
                                > Des <span class="highlight">projets</span> menés en équipe favorisant la découverte et la créativité.<br/><br/>
                                > Au baccalauréat, une <span class="highlight">épreuve orale</span> (coefficient 2) portant sur un projet<br/>mené durant la moitié de l'année scolaire.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item">
            	<img src="<?php echo(STYLES.'img/slide3.jpg'); ?>" alt="Third slide">
                <div class="container">
                    <div class="carousel-caption">
                	   <div class="carousel-disposition">
                  		    <h1 class="carousel-title">Poursuivre ses études :</h1>
                  		    <p class="carousel-txt">
                                Pour les élèves qui souhaitent<br/>poursuivre des études dans<br/>le domaine de l'informatique<br/>et des sciences du numérique :<br/><br/>
                                > <span class="highlight">IUT</span> d'informatique et de sciences<br/><br/>
                                > <span class="highlight">Licences</span> d'informatique, mathématiques et informatique<br/><br/>
                                > <span class="highlight">Classes Préparatoires</span> aux Grandes Écoles<br/><br/>
                                > <span class="highlight">Écoles d'ingénieurs</span> sur concours<br/>ou après préparation intégrée

                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <a class="left carousel-control" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
	</section>