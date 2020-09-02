<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("/var/www/html/site/bopimo.php");

if($bop->logged_in())
{
	die();
}

$ip = $_SERVER["REMOTE_ADDR"];
$secretKey = "6LcQ-foUAAAAAJyNjljvgvzMLklU_YOwLxoTZD3_";
if (isset($_POST["g-recaptcha-response"])) {
$responseKey = $_POST["g-recaptcha-response"];
$recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$ip";
$recaptchaResult = (json_decode(file_get_contents($recaptchaUrl))->success) ? true : false;
var_dump(file_get_contents($recaptchaUrl));
if (!$recaptchaResult) {
	//die("err7");
}
} else {
	//die("err7");
}


if(empty($_POST['username']) || empty($_POST['pw1']) || empty($_POST['pw2']) || empty($_POST['email']))
{
	//one field wasn't filled out
	die("err1");
}

$username = $_POST['username'];
$pw1 = $_POST['pw1'];
$pw2 = $_POST['pw2'];
$email = $_POST['email'];

if(!is_string($pw1) || !is_string($pw2) || !is_string($email) || !is_string($username))
{
	die("err1");
}

if($pw1 != $pw2) //passwords don't match
{
	die("err2");
}

$length = strlen($username);
if($length < 3 || $length > 20) //if the length doesn't fit
{
	die("err3");
}

if(!ctype_alnum($username)) //if any special characters
{
	die("err4");
}

if($bop->user_exists($username)) //if it does exist
{
	die("err5");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	die("err6");
}


$checked_username = $username;
$hashed = password_hash($pw1, PASSWORD_DEFAULT);
$user = $bop->insert("users", [
	"username" => $checked_username,
	"password" => $hashed,
	"email" => $email
]);
$uniq = uniqid();
$avatar = $bop->insert("avatar", [
	"user_id" => $user->id,
	"head_color" => "edf2f9",
	"torso_color" => "000000",
	"left_arm_color" => "edf2f9",
	"right_arm_color" => "edf2f9",
	"left_leg_color" => "edf2f9",
	"right_leg_color" => "edf2f9",
	"cache" => $uniq
]);

$id = $user->id;



die("succ");
?>
