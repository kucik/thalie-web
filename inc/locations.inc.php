<?php

function __getStatusText($status) {
  switch($status) {
    case 0: return "Nenačteno";
    case 1: return "Načtena předchozí verze";
    case 2: return "Načtena dvojmo";
    case 3: return "Načteno";
  }
  return "";
}

date_default_timezone_set("Europe/Prague");
$tpl->define('content','locations.htm');
$tpl->assign(array('ERROR'=>"",
	           'RESTART_SERVERU'=>""
		));	


// Configuration - dest directory
            $target_path='/home/nwn/resman/';
//            $target_path='/tmp/resman/';


if (isset($_SESSION['user'])){
	
	$tpl->assign(array('LOGIN'=>1));		
        // Get input data
          $action= $_POST['action'];
          if(strlen($action) == 0)
            $action= $_GET['action'];


	if ($action == "logout"){
		session_destroy();
		$tpl->assign(array('LOGIN'=>''));		
	}

        // Only if you have privilegies 	
	if ($_SESSION['priv'] & 4) {
		$tpl->assign(array("RESTART_SERVERU"=>1));

          //Locations upload
          if($action == "upload_loc") {
            // Check errors
            $error=0;
            if ($_FILES["are"]["error"] > 0) {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Nemuzu nahrat ARE soubor ".$_FILES["are"]["name"]));
            }
            if ($_FILES["gic"]["error"] > 0) {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Nemuzu nahrat GIC soubor ".$_FILES["gic"]["name"]));
            }
            if ($_FILES["git"]["error"] > 0) {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Nemuzu nahrat GIT soubor ".$_FILES["git"]["name"]));
            }
            
            if($error > 0)
              return;

            //Check file size
            $limit=1024000;
            if($_FILES["are"]["size"] > $limit) {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Soubor ".$_FILES["are"]["name"]." je prilis velky. Max ". ($limit/1024) ."KB"));
            }
            if($_FILES["gic"]["size"] > $limit) {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Soubor ".$_FILES["gic"]["name"]." je prilis velky. Max ".($limit/1024)."KB"));
            }
            if($_FILES["git"]["size"] > $limit) {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Soubor ".$_FILES["git"]["name"]." je prilis velky. Max ".($limit/1024)."KB"));
            }
     
            if($error > 0)
              return;

            //Check names
            $loc_name = str_replace(".are","",$_FILES["are"]["name"]);
            $loc_name = preg_replace('/[^_a-z0-9]/', '', $loc_name);
            if(strlen($loc_name) > 16) {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Lokace nemuze mit tag delsi nez 16 znaku."));
            }
            if($_FILES["are"]["name"] != $loc_name.".are") {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Neplatna pripona souboru ".$_FILES["are"]["name"]));
            }
            if($_FILES["gic"]["name"] != $loc_name.".gic") {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Neplatna pripona souboru ".$_FILES["gic"]["name"]));
            }
            if($_FILES["git"]["name"] != $loc_name.".git") {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Neplatna pripona souboru ".$_FILES["git"]["name"]));
            }
            if($error > 0)
              return;

            //Everything looks ok. Upload it.

            move_uploaded_file($_FILES["are"]["tmp_name"], $target_path."are/".$loc_name.".are");
            move_uploaded_file($_FILES["gic"]["tmp_name"], $target_path."gic/".$loc_name.".gic");
            move_uploaded_file($_FILES["git"]["tmp_name"], $target_path."git/".$loc_name.".git");
            $rs = $db->qy("SELECT * from resman_locations where location = '".$loc_name."'");     
            $timestamp = time();
            if($rs['rows']) {
               $rs = $db->qy("UPDATE resman_locations SET owner='".$_SESSION['id']."', status='1', insert_time = now() WHERE location = '".$loc_name."'");
            }
            else {
               $rs = $db->qy("INSERT INTO resman_locations VALUES ('".$loc_name."','".$_SESSION['id']."','1',now())");
            }

            // Make server load location
//            $rs = $db->qy("INSERT INTO server_commands VALUES (NULL, 'load_location','".$loc_name."',NULL, NULL)");
            

            $tpl->assign(array('ERROR'=>"Uploaded"));
          }
          if($action == "delete") {
            $error = 0;

            //Check name
            $loc_name = str_replace(".are","", $loc_name=$_GET['loc']);
            $loc_name = preg_replace('/[^_a-z0-9]/', '', $loc_name);
            if(strlen($loc_name) > 16 || (strlen($loc_name) == 0) ) {
              $error++;
              $tpl->assign(array('ERROR'=>"Chyba! Lokace nemuze mit tag delsi nez 16 znaku."));
            }
           
            // Check if user can delete this location
            $rs = $db->qy("SELECT * from resman_locations where location = '".$loc_name."' and owner = '".$_SESSION['id']."'");
            if($rs['rows'] <= 0 && ($_SESSION['priv'] & 8) <= 0) {
              $tpl->assign(array('ERROR'=>"Chyba! Lokace patri nekomu jinemu."));
              return;
            }

            //remove files from filesystem
            unlink( $target_path."are/".$loc_name.".are" );
            unlink( $target_path."git/".$loc_name.".git" );
            unlink( $target_path."gic/".$loc_name.".gic" );
            
            // delete from DB
            if( ($_SESSION['priv'] & 8) > 0)
              $rs = $db->qy("DELETE FROM resman_locations where location = '".$loc_name."' ");
            else 
              $rs = $db->qy("DELETE FROM resman_locations where location = '".$loc_name."' and owner = '".$_SESSION['id']."'");
            $tpl->assign(array('ERROR'=>"Lokace ".$loc_name." byla smazana."));
          }

          // List of uploaded locations
          $rs = $db->qy("SELECT resman_locations.*, pwplayers.login, pwplayers.id from resman_locations, pwplayers where pwplayers.id = resman_locations.owner");
          $tpl->define_dynamic("location_list","content");
          foreach ($rs['rows'] as $k=>$loc) {
                if($loc['owner'] == $_SESSION['id'] || ($_SESSION['priv'] & 8) > 0)
                  $delete_me="<a href=\"?action=delete&loc=".$loc['location']."\">Smazat</a>";
                else 
                  $delete_me="";
                $tpl->assign(array(
                        "LOCATION_NAME"=>$loc['location'],
                        "LOCATION_OWNER"=>$loc['login'],
                        "LOCATION_STATUS"=>$loc['status'],
                        "LOCATION_STATUS_TEXT"=>__getStatusText($loc['status']),
                        "LOCATION_TIME"=>$loc['insert_time'],
                        "LOCATION_DELETE"=>$delete_me
                ));
                $tpl->parse("LOCATION_LIST",".location_list");
          }
	}

/*        $rs = $db->qy("SELECT ip, login, cdkey, cdkey2, cdkey3, email, noipcheck FROM pfnwn.pwplayers WHERE id = ".$_SESSION['id']." ");*/
/*        $tpl->assign(array("IPADRESS"=>$rs['rows'][0]['ip'],
                                                        "USER"=>$rs['rows'][0]['login'],
                                                        "CDKEY"=>$cdkey,
                                                        "EMAIL"=>$rs['rows'][0]['email'],
                                                        "CHECKED"=>($rs['rows'][0]['noipcheck'])?' checked="checked"':'',
                                                        "USER_ID"=>$_SESSION['id'],
                                                        "VAR_SYMBOL"=>$_SESSION['id']+1000000000,
                                                        "IP_HLASKA"=>$hlaska
                                                        ));	*/
       $tpl->assign(array("USER_ID"=>$_SESSION['id'],
                          "USER"=>$_SESSION['user']));


}

?>
