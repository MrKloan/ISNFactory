<?php
    if(file_exists('Core/config.php'))
        require_once('Core/config.php');
    else
        require_once('../Core/config.php');
    $styles = str_replace('Views/Views', 'Views', STYLES);
?>
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
    
    <meta name="robots" content="noindex, nofollow, noarchive" />
	    
	<link rel="shortcut icon" href="<?php echo($styles.'img/favicon.png'); ?>" />

    <link rel="stylesheet" href="<?php echo($styles.'css/normalize.css'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo($styles.'css/bootstrap.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo($styles.'css/404.css'); ?>" />
    <!--[if lt IE 9]>
        <script type="text/javascript" src="<?php echo($styles.'js/html5shiv-printshiv.js'); ?>"></script>
    <![endif]-->
</head>
<body>
    <div class="site-wrapper">
        <div class="site-wrapper-inner">
            <div class="cover-container">
                <div class="masthead clearfix">
                    <div class="inner">
                        <h3 class="masthead-brand"><a href="<?php echo(WEB_ROOT);?>"><img src="<?php echo($styles.'img/logo_av_bandeau_alt.svg'); ?>" alt="logo" /></a></h3>
                    </div>
                </div>

                <div class="cover">
                    <h1 class="cover-heading">Oops 404 !</h1>
                    <p class="lead">You found a <strong>Dead Link</strong></p>
                    <p class="lead">
                        <a href="<?php echo(WEB_ROOT);?>"><img src="<?php echo($styles.'img/404.png'); ?>" alt="dead-link" /></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>