<?php
	$file = $_GET['file'];
	
	$usertest = trim($user);
	$filetest = trim($file);
	if($usertest[0] == '.' || $usertest[0] == '\\' || $usertest[0] == '\'' || $usertest[0]== '/' ||
		 $filetest[0] == '.' || $filetest[0] == '\\' || $filetest[0] == '\'' || $filetest[0]== '/' ||
		 strpos($usertest, "..") != false || strpos($filetest, "..") != false){
		header("HTTP/1.1 403 Forbidden");
		print("<h1>403 Forbidden</h1>");
		return;
	}
	
	session_start();
	
	$path = "/usr/local/NWNserver/servervault/".$_SESSION['user']."/".$file.".bic";
	
	$contents = file_get_contents($path);
	
	header('Pragma: public');
	header('Content-type: application/zip');
	header('Content-length: '.strlen($contents));
	header('Content-Disposition: attachment; filename='.$file.'.bic'); 
	
	print $contents;
?>