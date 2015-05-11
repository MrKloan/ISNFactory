<?php
//Configuration
define('USE_CONFIG', 1);
define('DEBUG', false);
define('PROD', false);
//Global
define('WEB_ROOT', dirname($_SERVER['SCRIPT_NAME']) == '/' ? '' : dirname($_SERVER['SCRIPT_NAME']));
define('ROOT', dirname(dirname(__FILE__)));
define('CORE', ROOT.'/Core/');
define('CONTROLLERS', ROOT.'/Controllers/');
define('VIEWS', ROOT.'/Views/');
define('MODELS', ROOT.'/Models/');
define('CODIAD', ROOT.'/Codiad/');
define('FILES', ROOT.'/Files/');
define('STYLES', WEB_ROOT.'/Views/Styles/');
define('SESSION_NAME', md5(ROOT));
//Codiad
define('CODIAD_ROOT', ROOT.'/Codiad');
define('CODIAD_DATA', CODIAD_ROOT.'/data/');
define('CODIAD_WORKSPACE', FILES.'/Projects/');
//Database
if(PROD)
{
	define('DB_HOST', 'mysql51-123.perso');
	define('DB_USER', 'isfactor');
	define('DB_PASSWORD', 'bo83qUWXp6vA');
	define('DB_BASE', 'isfactor');
	define('DB_TYPE', 'mysql');
	define('DB_ENCODING', 'utf8');
	define('DB_PREFIX', '');
}
else
{
	define('DB_HOST', 'localhost');
	define('DB_USER', 'isnfactory');
	define('DB_PASSWORD', 'isnfactory');
	define('DB_BASE', 'isnfactory');
	define('DB_TYPE', 'mysql');
	define('DB_ENCODING', 'utf8');
	define('DB_PREFIX', '');
}
//Security
//Generate random keys at https://api.wordpress.org/secret-key/1.1/salt/
define('SALT', 'tDmL>{QYg,%oN~D~I8<@^gE?Pga2Kk-H7$2cZG<O>)(RhH~PYtu%L=XrS7EAmNw+');
define('PEPPER', 'c|dVaR+-[Rj6/g`_YY$v v>zw~PFjYd#=--TQmczFT2nJ;s^iW|F-BU0Ar;t -02');
//ReCaptcha Keys
define('PRIVATE_KEY', '6LeBcPASAAAAAK3-CKq3Jgf7jq-CtGIc34VLxShA');
define('PUBLIC_KEY', '6LeBcPASAAAAAN2fZ-6s5LfnN2_nPuzveQGkH2ZL');
?>