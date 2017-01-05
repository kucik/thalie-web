<?php

$tpl->define('content','registrace.htm');

if ($action == "send") {
	if ($param['user'] && $param['pass'] && ($param['pass'] == $param['pass2']) && (strlen($param['pass']) > 5) && $param['email']) {
	
		$user = addslashes($param['user']);
		$rs = $db->qy("SELECT login FROM pwplayers WHERE login = '".$user."' ");
		
		$check = 1;	
		if ($rs['rows'][0]['login']) {
			$check = 0;
			$tpl->assign(array("ERROR"=>"Tento uživatel je již zaregistrovaný!"));			
		}
		if (!eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", $param['email'])) {
			$check = 0;
			$tpl->assign(array("ERROR"=>"Zadejte platný e-mail."));
		}
		
		if ($check == 1) {
			$db->qy("INSERT INTO pwplayers (login, password, email, privilegies) VALUES ('".$user."', PASSWORD('$param[pass]'), '".$param['email']."', '0');");
/*			$db->qy("INSERT INTO pwplayers (login, password, email, privilegies) VALUES ('".$user."', PASSWORD('$param[pass]'), '".$param['email']."', '-1');");*/
			$tpl->assign(array("ERROR"=>"Registrace proběhla úspešně. Teď se musíte přihlásit a vyplnit všechny údaje (IP adresa a Thalie CD-KEY)."));
		}
	}
	else {
		$tpl->assign(array("ERROR"=>"Nevyplnili jste všechny položky nebo se neshodují hesla."));
	}
}


?>
