<?php
  session_start();

	require_once("cls/db.cls.php");
	require_once("cls/ft.cls.php");
  require_once ("inc/parse_vars.inc.php");
  require_once("cls/CSession.cls.php");
	require_once("cls/CProblems.cls.php");
  require_once ("inc/settings.inc.php");
 	
	$db = new db(DB_HOST, DB_USER , DB_PWD , DB, "true"); //z db.cls.php
	$db->qy("set option character set utf8");

	$tpl = new FastTemplate("tpl"); //nova instance FT
	$sess = new CSession($db);


  if ($action == "login") {        
  $sess->login($param['user'],$param['pwd']);
    $tpl->assign("LOGINPAGE",$sess->get_user_name());

  }
  elseif ($action == "logout") {
    $sess->logout();
  }
  {
	$tpl->assign(array("PAGE"=> $page,
					   "ID"=>$id,
					   "HOME_URL"=>HOME_URL,
					   "USER"=>"",
					   "SHOW"=>"",
			   		));
  }

  if ($sess->isAuthorized() ) {  
    $tpl->assign("USER",$sess->get_user_name());
  }
  
  if ($sess->isAuthorized() && (($_SESSION['user_id'] == 3) || ($_SESSION['user_id'] == 44)) ) {
    $tpl->assign(array("LOGS"=>"1"));  
  } 

	$tpl->define(array("main"=>"main.htm"));


  
  if (!$page) $page = 'klevety';

	switch ($page) {	
		
		case 'home':
		  require_once("inc/home.inc.php");
		break;
		
		case 'login':
      require_once("inc/login.inc.php");
    break;
    
  	case 'edit':
      require_once("inc/edit.inc.php");
    break;

  	case 'logs':
      require_once("inc/logs.inc.php");
    break;
        case 'klevety':
      require_once("inc/klevety.inc.php");
    break;

		default:
		  require_once("inc/klevety.inc.php");      
		break;
		
	}
	  
	  if ($tpl->is_defined("content")) $tpl->parse("CONTENT","content");
	  
		$tpl->parse("MAIN","main");	
		$tpl->FastPrint("MAIN");

?>	
