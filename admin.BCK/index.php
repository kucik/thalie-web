<?php
	session_start();
	
  require_once ("../cls/ft.cls.php");
  require_once ("../cls/db.cls.php");
  require_once("../cls/CSession.cls.php");
  require_once ("inc/settings.inc.php");
  require_once ("inc/parse_vars.inc.php");
  
  	$db = new db(DB_HOST, DB_USER , DB_PWD , DB, "true"); //z db.cls.php
	//$db->qy("set names utf8");
	$db->qy("set character set utf8");  
  	$tpl = new FastTemplate("tpl");
	$sess = new CSession($db);

	if ($action == "login") {    
    $sess->login($param['user'],$param['pwd']);
  }
	if ($action == "logout") {
    $sess->logout();
  }

  $tpl->define(array("main"=>"main.htm"));
  $tpl->assign(array("ID"=>$id,
  							"HOME_URL"=>HOME_URL,
  							"USER"=>""));
  							

  if ($sess->isAuthorized() ) {  	$tpl->assign("USER",$sess->get_user_name());
	  switch($page) {
    
   	case 'category':
    		require_once("inc/category.inc.php");
   	 break;
   	 case 'article':
    		require_once("inc/article.inc.php");
	 	break;
   	case 'sitemap':
    		require_once("inc/sitemap.inc.php");
	 	break;
        case 'gamestats':
                require_once("inc/gamestats.inc.php");
                break;
 	default:
	 		require_once("inc/home.inc.php"); 	
	 	break;
	 }
	}  
	else {
		require_once("inc/login.inc.php");	
	}
  
  if ($tpl->is_defined('content')) {
      $tpl->parse('CONTENT', 'content');	
  } 
  else $tpl->assign(array('CONTENT'=>''));

  $tpl->parse("MAIN","main");
  $tpl->FastPrint("MAIN");        
  
?>
