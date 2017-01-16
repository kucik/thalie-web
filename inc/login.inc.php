<?php

$tpl->define('content','login.htm');
$tpl->assign(array('ERROR'=>"",
						'RESTART_SERVERU'=>""
					));	

if($action == 'login') {
	$login = addslashes($param['login']);
	$pass  = addslashes($param['pass']);
		
	$rs = $db->qy("SELECT id, login, privilegies FROM pfnwn.pwplayers WHERE login = '".$login."' AND password = PASSWORD('$pass') ");
	if($rs['rows']){
		$_SESSION['user'] = $rs['rows'][0]['login'];
		$_SESSION['id']   = $rs['rows'][0]['id'];
		$_SESSION['priv'] = $rs['rows'][0]['privilegies'];
	} 
	else {
		$tpl->assign(array('ERROR'=>"Špatné jméno nebo heslo."));
	}
}

if (isset($_SESSION['user'])){
	
	$tpl->assign(array('LOGIN'=>1));		
	
	if ($action == "logout"){
		session_destroy();
		$tpl->assign(array('LOGIN'=>''));		
	}
	if (($_SESSION['priv'] & 2) > 0) {
		$tpl->assign(array("RESTART_SERVERU"=>1));	
	}
        if (($_SESSION['priv'] & 4) > 0) {
                $tpl->assign(array("LOKACE"=>1));
        }
	if ($_SESSION['user'] == "igor") {
		$tpl->assign(array("RESET_PASSWORD"=>1));	
	}	
	
	
	if(($param['restart'] == "restart")  && (($_SESSION['priv'] & 2) )) {
		$tpl->assign(array('ERROR'=>"Server se restartuje."));
/*		exec("/home/nwn/stop.pl");		*/
                $rs = $db->qy("INSERT INTO server_commands VALUES('','killserver','',NULL,'') ");
	}	
		
	if($param['update'] == 'Aktualizovat' && isset($param['ipaddress']) && isset($param['cdkey']) && isset($param['email'])) {	
		
		$ipaddress = addslashes($param['ipaddress']);
		$cdkey = addslashes($param['cdkey']);
		$email = addslashes($param['email']);
                $prevkey = $_POST['prev_key'];
		$noipcheck = $param['noipcheck']=="yes"?1:0;
		
		if(!$noipcheck){
  			if($ipaddress == ""){
  				$ipaddress = $_SERVER['HTTP_X_REAL_IP'];
  			}
  		
     		if($ipaddress == $_SERVER['HTTP_X_REAL_IP']){
        		$tpl->assign(array('ERROR'=>"IP adresu neměňte pokud hrajete na tomto počítači."));	   
  	  		}
  	 		else{
  	    		$tpl->assign(array('ERROR'=>"Zjištěná IP adresa ".$_SERVER['HTTP_X_REAL_IP']." se neshoduje se zadanou! Pokud hrajete na tomto počítači, máte ji spatně zadanou a nebudete vpuštěni do hry!"));
  	  		}
	  	}
	  	else{
	    		$tpl->assign(array('ERROR'=>"IP adresa nebude kontrolována."));
		}
		
      function getCDkey($input,$s){
      	if((strlen($input))!=24) {
      		return;
      	}
      	for($i=0;$i<8;$i++){
				$output=$output.substr($input,3*$i+$s,1);
     	
      	}
      	return $output;
      }
 		     	
		$cdkey1 = getCDkey($cdkey,0);
		$cdkey2 = getCDkey($cdkey,1);
		$cdkey3 = getCDkey($cdkey,2);		
              
                if(strlen($ipaddress)<7) {
                       $tpl->assign(array('ERROR'=>"Musíte vyplnit IP adresu."));
                }
		else if($cdkey1 && $cdkey2 && $cdkey3) {
	
                        $q = "SELECT login FROM pfnwn.pwplayers WHERE
                             ((cdkey = '".$cdkey1."' OR cdkey2 = '".$cdkey2."' OR cdkey3 = '".$cdkey3."')
                             /* 
                             AND id!=".$_SESSION['id']."^M
                             AND password!=(SELECT password FROM pfnwn.pwplayers WHERE id=".$_SESSION['id'].")^M
                            */
                             AND ip!='".$ipaddress."' ";
                       $q = "SELECT login FROM pfnwn.pwplayers WHERE
                             ( cdkey = '".$cdkey1."' OR cdkey2 = '".$cdkey2."' OR cdkey3 = '".$cdkey3."')
                             AND ip!='".$ipaddress."' 
                             AND id!='".$_SESSION['id']."' order by id";
			/*
                                   $rs = $db->qy("SELECT login FROM pfnwn.pwplayers WHERE
					( cdkey = '".$cdkey1."' OR cdkey2 = '".$cdkey2."' OR cdkey3 = '".$cdkey3."')
					AND id!=".$_SESSION['id']."
					AND password!=(SELECT password FROM pfnwn.pwplayers WHERE id=".$_SESSION['id'].")
					AND ip!='".$ipaddress."' "					
					);
                       */ 
                        $rs = $db->qy($q);
                           
                        #$tpl->assign(array('ERROR'=>"Keys compare ".$cdkey." ? ".$prevkey." = ".(strcmp($cdkey==$prevkey))." sdf sdf "));
			
                        // Pokud klic neni obsazeny, nebo pokud nemenime klic
			if((!$rs['rows'][0]['login']) || ($cdkey ==  $prevkey) ) {							
				$db->qy("UPDATE pfnwn.pwplayers SET ip='".$ipaddress."', cdkey='".$cdkey1."', cdkey2='".$cdkey2."', cdkey3='".$cdkey3."', email='".$email."', noipcheck=".$noipcheck." WHERE id=".$_SESSION['id']." ");
			}
			else {
                             $q = "SELECT login FROM pfnwn.pwplayers WHERE
                             ( cdkey = '".$cdkey1."' )
                             AND ip!='".$ipaddress."'
                             AND id!='".$_SESSION['id']."' order by id";
                             $rs = $db->qy($q);
                             if($rs['rows'][0]['login']) {
                               $tpl->assign(array('ERROR'=>"Váš 1. klíč (NWN) již používá někdo jiný: ".$rs['rows'][0]['login']));
                             } 
                             else {
                               $q = "SELECT login FROM pfnwn.pwplayers WHERE
                               ( cdkey2 = '".$cdkey2."' )
                               AND ip!='".$ipaddress."'
                               AND id!='".$_SESSION['id']."' order by id";
                               $rs = $db->qy($q);
                               if($rs['rows'][0]['login']) {
                                 $tpl->assign(array('ERROR'=>"Váš 2. klíč (NWN - SoU) již používá někdo jiný: ".$rs['rows'][0]['login']));
                               }
                               else {
                                 $q = "SELECT login FROM pfnwn.pwplayers WHERE
                                 ( cdkey3 = '".$cdkey3."' )
                                 AND ip!='".$ipaddress."'
                                 AND id!='".$_SESSION['id']."' order by id";
                                 $rs = $db->qy($q);
                                 if($rs['rows'][0]['login']) {
                                   $tpl->assign(array('ERROR'=>"Váš 3. klíč (NWN - HoU) již používá někdo jiný: ".$rs['rows'][0]['login']));
                                 }
                                 else {
                                   $tpl->assign(array('ERROR'=>"Vámi zadaný Thalie CD-key již používá uživatel s účtem: <strong>".$rs['rows'][0]['login']."</strong>."));
                                 }
                               } 
                             }         
			}
		}
		else {
			$tpl->assign(array('ERROR'=>"Byl zadán nesprávný Thalie CD-KEY."));
		}
		
	}
	//konec update	
	$cdkey = "";
	$rs = $db->qy("SELECT ip, login, cdkey, cdkey2, cdkey3, email, noipcheck FROM pfnwn.pwplayers WHERE id = ".$_SESSION['id']." ");

	$cdkey1     = $rs['rows'][0]['cdkey'];
	$cdkey2     = $rs['rows'][0]['cdkey2'];
	$cdkey3     = $rs['rows'][0]['cdkey3'];

	if($cdkey1 && $cdkey2 && $cdkey3){
		for($i=0;$i<8;$i++){
			$cdkey = $cdkey.substr($cdkey1,$i,1);
			$cdkey = $cdkey.substr($cdkey2,$i,1);
			$cdkey = $cdkey.substr($cdkey3,$i,1);			
		}
		
	}
	
	$tpl->assign(array("IPADRESS"=>$rs['rows'][0]['ip'],
//                           "REMOTEIP"=>$_SERVER['REMOTE_ADDR'],
//                           "REMOTEIP"=>$_SERVER['HTTP_X_REAL_IP'],
							"USER"=>$rs['rows'][0]['login'],
							"CDKEY"=>$cdkey,
							"EMAIL"=>$rs['rows'][0]['email'],
							"CHECKED"=>($rs['rows'][0]['noipcheck'])?' checked="checked"':'',
							"USER_ID"=>$_SESSION['id'],
							"VAR_SYMBOL"=>$_SESSION['id']+1000000000,
							"IP_HLASKA"=>$hlaska
							));
							
	if (($param['reset_pwd'] == 1) && ($_SESSION['user'] == "igor") && $param['reset_pwd_user']) {
		$rs = $db->qy("UPDATE pfnwn.pwplayers SET password = PASSWORD('$param[reset_pwd_user]') WHERE login = '".$param['reset_pwd_user']."' ");
	}
	
	if (($param['change_pwd'] == 1) && $param['new_pwd'] && $param['new_pwd2']) {
		if (($param['new_pwd'] == $param['new_pwd2']) && (strlen($param['new_pwd']) > 5)) {
			$pwd = addslashes($param['new_pwd']);
			$rs = $db->qy("UPDATE pfnwn.pwplayers SET password = PASSWORD('$pwd') WHERE id = '".$_SESSION['id']."' ");
			$tpl->assign(array('ERROR'=>"Heslo bylo změněno."));	
		}
		else {
			$tpl->assign(array('ERROR'=>"Hesla se neshodují nebo nemá aspoň 6 znaků."));		
		}
	}

        if ($action == "delete") {
          $del_id = preg_replace('/[^0-9]/', '', $_GET['chid']);
          // Mark delete flag
          $rs = $db->qy("UPDATE  pwchars
                         set delete_flag = '1'
                         WHERE id = '".$del_id."' AND player = '".$_SESSION['user']."'");
        }
        if ($action == "storno") {
          $del_id = preg_replace('/[^0-9]/', '', $_GET['chid']);
          // Mark delete flag
          $rs = $db->qy("UPDATE  pwchars
                         set delete_flag = NULL
                         WHERE id = '".$del_id."' AND player = '".$_SESSION['user']."'");
        }

        // List of Characters
          $rs = $db->qy("SELECT c.id id, c.tag name, p.val portrait, f.val filename, c.delete_flag flag, p.last last 
                         FROM 
                           pwchars c,
                           pwdata p,
                           pwdata f
                         WHERE c.player = '".$_SESSION['user']."' AND
                               c.player = f.player AND 
                               c.tag = f.tag AND 
                               f.name = 'FILENAME' AND
                               c.player = p.player AND
                               c.tag = p.tag AND
                               c.tag = f.tag AND
			       p.name = 'PORTRAIT'");
          $tpl->define_dynamic("chars_list","content");
          foreach ($rs['rows'] as $k=>$chars) {
                if(strlen($chars['portrait']) > 0 )
                  $delete_me="<a href=\"?action=delete&chid=".$chars['id']."\">Smazat postavu</a>";        
                else 
                  $delete_me="";
                if($chars['flag'] == 1) {
//                  $flag = "(Čeká na smazání)";
                  $dt=date("H:i:s", round((time() -7200 ) / 43200,0) * 43200 + 46800);
                  $dt=date("H:i:s", round((time() + 14400) / 43200,0) * 43200 );
                  $dt=date("H:i:s", round((time() - 7200 ) ,0)  );
                  $dt=date("H:i:s", round((time() - 7200 ) / 43200 ,0) * 43200 );
//                  $delete_me="(Bude smazáno v $dt) <a href=\"?action=storno&chid=".$chars['id']."\">Storno</a>";
                  $delete_me="(Bude smazáno po následujícím restartu!) <a href=\"?action=storno&chid=".$chars['id']."\">Storno</a>";
                }
                $tpl->assign(array(
                        "CHARS_NAME"=>str_replace(' ','_',$chars['name']),
                        "CHARS_PORTRAIT"=>$chars['portrait'],
                        "CHARS_LAST"=>$chars['last'],
//                        "CHARS_STATUS"=>$flag,
                        "CHARS_DELETE"=>$delete_me
                ));
                $tpl->parse("CHARS_LIST",".chars_list");
          }
							
}	

?>
