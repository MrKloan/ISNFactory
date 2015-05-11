<?php
require_once("../../../Core/config.php");

header ("Content-type: image/png");

$group = isset($_GET['group']) ? str_replace("'", "", str_replace("\"", "", str_replace(SALT, "", str_replace(PEPPER, "", base64_decode($_GET['group']))))) : 'NULL';
$todo = imagecreatefrompng("todo.png");
$postit = imagecreatefrompng("postit.png");	
$dest = imagecreatetruecolor(1100, 700);

$l_todo = imagesx($todo);
$h_todo = imagesy($todo);

$l_postit = imagesx($postit);
$h_postit = imagesy($postit);

$l_dest = imagesx($dest);
$h_dest = imagesy($dest);

$cx = @mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD) or die();
@mysqli_select_db($cx, DB_BASE) or die();
$result = @mysqli_query($cx,"SELECT * FROM todo_lists WHERE `group` = '".$group."'") or die();

while(($element = mysqli_fetch_array($result)) != false)
{
	$left=''; $top=''; $zindex='';
    list($left,$top,$zindex) = explode('x',$element[7]);
    if($left > $l_todo)
    	$left = $l_todo-$l_postit;
    imagecopymerge($todo, $postit, $left-50, $top+50, 0, 0, $l_postit, $h_postit, 100);
}

mysqli_close($cx);

imagecopyresampled($dest, $todo, 0, 0, 0, 0, $l_dest, $h_dest, $l_todo, $h_todo);

imagepng($dest);

imagedestroy($todo);
imagedestroy($postit);
imagedestroy($dest);
?>