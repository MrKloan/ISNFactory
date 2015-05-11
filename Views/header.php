<!DOCTYPE html>
<html lang="fr">
<head>
	<title>ISN Factory</title>

	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="Extranet pour la matière ISN enseignée au lycée" />
    <meta name="keywords" content="isn, extranet,informatique, science du numerique, lycee" />
	<meta name="author" content="Jérémy BLANCHARD, Mathieu BOISNARD, Valentin FRIES" />
    
    <?php if(!$this->dispatcher->Core->User->getRole()) : ?>
        <meta name="robots" content="index, follow, archive" />
    <?php else: ?>
        <meta name="robots" content="noindex, nofollow, noarchive" />
    <?php endif; ?>
	    
	<link rel="shortcut icon" href="<?php echo(STYLES.'img/favicon.png'); ?>" />

    <link rel="stylesheet" href="<?php echo(STYLES.'css/normalize.css'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo(STYLES.'css/bootstrap.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo(STYLES.'css/bootstrap-switch.min.css'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo(STYLES.'css/fontello.css'); ?>" />
    <?php if($this->dispatcher->view == "project" || $this->dispatcher->view == "project_page" || $this->dispatcher->view == "notes" || $this->dispatcher->view == "final") : ?>
        <link rel="stylesheet" type="text/css" href="<?php echo(STYLES.'css/jquery-ui-1.10.4.css'); ?>" />
    <?php endif; ?>
    <?php if($this->dispatcher->view == "final"): ?>
        <link rel="stylesheet" type="text/css" href="<?php echo(STYLES.'css/slider.css'); ?>" />
    <?php endif; ?>
	<?php $this->loadCSS(); ?>

    <!--[if lt IE 9]>
        <script type="text/javascript" src="<?php echo(STYLES.'js/html5shiv-printshiv.js'); ?>"></script>
    <![endif]-->
       
    <?php if(!$this->dispatcher->Core->User->getRole()) : ?>
        <script type="text/javascript">
            var RecaptchaOptions = {
            theme : 'white'
            };
       </script>
    <?php endif; ?>
</head>
<body>

<?php if($this->dispatcher->Core->User->getRole()) : ?>
	<header class="navbar navbar-static-top header">
        <nav class="container">
            <div class="row">
                <div class="col-xs-6 col-md-2 navbar-header">
                    <a href="<?php echo(WEB_ROOT.'/extranet/'.$this->dispatcher->controller.'/home'); ?>">
                        <img src="<?php echo(STYLES.'img/logo_alt.svg'); ?>" alt="logo"/>
                    </a>
                </div>
                <form action="" method="POST">
                    <div class="navbar-collapse navbar-right">
                        <a class="icon-cogs btn btn-link navbar-btn params" href="<?php echo(WEB_ROOT.'/extranet/'.$this->dispatcher->controller.'/parameters'); ?>"></a> 
                        <button type="submit" class="btn btn-link navbar-btn" name="submit_disconnect">
                            <img src="<?php echo(STYLES.'img/deconnexion.svg'); ?>" alt="Déconnexion"/>
                        </button>
                    </div>
                </form>
            </div>
        </nav>
    </header>
<?php else: ?>
    <header class="navbar navbar-static-top header">
        <nav class="container">
            <div class="row">
                <div class="col-xs-6 col-md-2 navbar-header">
                    <img src="<?php echo(STYLES.'img/logo_alt.svg'); ?>" alt="logo"/>
                </div>
                <?php if($this->dispatcher->Core->Config->maintenance < 1 && $this->dispatcher->controller != "forgot"): ?>
                <form>
                    <div class="navbar-collapse navbar-right"> 
                        <button class="btn btn-link navbar-btn" data-toggle="collapse" href="#connect-panel">
                            <img src="<?php echo(STYLES.'img/connexion.svg'); ?>" alt="connexion"/>
                        </button>
                    </div>
                </form>
                <?php elseif($this->dispatcher->Core->Config->maintenance > 0): ?>
                    <div class="navbar-right alert alert-danger maintenance">
                        <p>Le site est actuellement en maintenance.</p>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>
<?php endif; ?>

<?php $this->dispatcher->getError(); ?>
<?php $this->dispatcher->getSuccess(); ?>

<?php if($this->dispatcher->action == "extranet") : ?>
<?php if($this->dispatcher->Core->User->getRole() == "role_student") : ?> 
    <nav class="jumbotron background2">
        <div class="container">
            <div class="row">
                <div class="col-xs-4 col-md-2 <?php echo(!$this->dispatcher->model->isTerminale() ? 'col-md-offset-1' : ''); ?>">
                    <a href="<?php echo(WEB_ROOT.'/extranet/student/home'); ?>"><img src="<?php echo(STYLES.'img/picto_home.svg'); ?>" alt="Home"/></a>
                </div>
                <?php if($this->dispatcher->Core->Config->courses == 2): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/student/courses'); ?>"><img src="<?php echo(STYLES.'img/picto_cours.svg'); ?>" alt="Cours"/></a>
                    </div>
                <?php endif; ?>
                <?php if($this->dispatcher->Core->Config->projets == 2): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/student/project'); ?>"><img src="<?php echo(STYLES.'img/picto_projets.svg'); ?>" alt="Projets"/></a>
                    </div>
                    <?php if($this->dispatcher->model->isTerminale()): ?>
                        <div class="col-xs-4 col-md-2">
                            <a href="<?php echo(WEB_ROOT.'/extranet/student/final'); ?>"><img src="<?php echo(STYLES.'img/picto_projetfinal.svg'); ?>" alt="Projet Final"/></a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if($this->dispatcher->Core->Config->mails == 2): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/student/mails'); ?>"><img src="<?php echo(STYLES.'img/picto_mail.svg'); ?>" alt="Mails"/></a>
                    </div>
                <?php endif; ?>
                <?php if($this->dispatcher->Core->Config->links == 2): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/student/links'); ?>"><img src="<?php echo(STYLES.'img/picto_liens.svg'); ?>" alt="Liens"/></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
<?php elseif($this->dispatcher->Core->User->getRole() == "role_professor") : ?>
    <nav class="jumbotron background2">
        <div class="container">
            <div class="row">
                <div class="col-xs-4 col-md-2">
                    <a href="<?php echo(WEB_ROOT.'/extranet/professor/home'); ?>"><img src="<?php echo(STYLES.'img/picto_home.svg'); ?>" alt="Home"/></a>
                </div>
                <div class="col-xs-4 col-md-2">
                    <a href="<?php echo(WEB_ROOT.'/extranet/professor/users'); ?>"><img src="<?php echo(STYLES.'img/picto_users.svg'); ?>" alt="Utilisateurs"/></a>
                </div>
                <?php if($this->dispatcher->Core->Config->notes >= 1): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/professor/notes'); ?>"><img src="<?php echo(STYLES.'img/picto_notes.svg'); ?>" style="width:44%;" alt="Notes"/></a>
                    </div>
                <?php endif; ?>
                <?php if($this->dispatcher->Core->Config->courses >= 1): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/professor/courses'); ?>"><img src="<?php echo(STYLES.'img/picto_cours.svg'); ?>" alt="Cours"/></a>
                    </div>
                <?php endif; ?>
                <?php if($this->dispatcher->Core->Config->projets >= 1): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/professor/project'); ?>"><img src="<?php echo(STYLES.'img/picto_projets.svg'); ?>" alt="Projets"/></a>
                    </div>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/professor/final'); ?>"><img src="<?php echo(STYLES.'img/picto_projetfinal.svg'); ?>" alt="Projet Final"/></a>
                    </div>
                <?php endif; ?>
                <?php if($this->dispatcher->Core->Config->ftp >= 1): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/professor/files'); ?>"><img src="<?php echo(STYLES.'img/picto_files.svg'); ?>" alt="Visuel"/></a>
                    </div>
                <?php endif; ?>
                <?php if($this->dispatcher->Core->Config->mails >= 1): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/professor/mails'); ?>"><img src="<?php echo(STYLES.'img/picto_mail.svg'); ?>" alt="Mails"/></a>
                    </div>
                <?php endif; ?>
                <?php if($this->dispatcher->Core->Config->links >= 1): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/extranet/professor/links'); ?>"><img src="<?php echo(STYLES.'img/picto_liens.svg'); ?>" alt="Liens"/></a>
                    </div>
                <?php endif; ?>
                <?php if($this->dispatcher->Core->Config->codiad >= 1): ?>
                    <div class="col-xs-4 col-md-2">
                        <a href="<?php echo(WEB_ROOT.'/Codiad'); ?>" target="_blank"><img src="<?php echo(STYLES.'img/picto_codiad.png'); ?>" alt="Codiad Web IDE" style="width:45%; height:45%;"/></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
<?php elseif($this->dispatcher->Core->User->getRole() == "role_admin") : ?>
    <nav class="jumbotron background2">
        <div class="container">
            <div class="row">
                <div class="col-xs-6 col-md-2 col-md-offset-2">
                    <a href="<?php echo(WEB_ROOT.'/extranet/admin/home'); ?>"><img src="<?php echo(STYLES.'img/picto_home.svg'); ?>" alt="Home"/></a>
                </div>
                <div class="col-xs-6 col-md-2">
                    <a href="<?php echo(WEB_ROOT.'/extranet/admin/users'); ?>"><img src="<?php echo(STYLES.'img/picto_users.svg'); ?>" alt="Utilisateurs"/></a>
                </div>
                <div class="col-xs-6 col-md-2">
                    <a href="<?php echo(WEB_ROOT.'/extranet/admin/sgbd'); ?>"><img src="<?php echo(STYLES.'img/picto_bdd.svg'); ?>" alt="Liens"/></a>
                </div>
                <div class="col-xs-6 col-md-2">
                    <a href="<?php echo(WEB_ROOT.'/Codiad'); ?>" target="_blank"><img src="<?php echo(STYLES.'img/picto_codiad.png'); ?>" alt="Codiad Web IDE" style="width:45%; height:45%;"/></a>
                </div>
            </div>
        </div>
    </nav>
<?php endif; ?>
<?php endif; ?>