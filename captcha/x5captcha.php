<?php
include("../res/x5engine.php");
$nameList = array("my8","gsz","flh","kzt","dcz","fsh","y6v","t4l","m4r","hca");
$charList = array("X","H","3","A","D","2","L","Z","R","F");
$cpt = new X5Captcha($nameList, $charList);
//Check Captcha
if ($_GET["action"] == "check")
	echo $cpt->check($_GET["code"], $_GET["ans"]);
//Show Captcha chars
else if ($_GET["action"] == "show")
	echo $cpt->show($_GET['code']);
// End of file x5captcha.php
