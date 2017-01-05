<?php
  session_start();

  require_once ("cls/ft.cls.php");
  require_once ("cls/db.cls.php");
  require_once ("inc/settings.inc.php");
  require_once ("inc/parse_vars.inc.php");

	$db = new db(DB_HOST, DB_USER , DB_PWD , DB, "true"); //z db.cls.php
	$db->qy("SET CHARACTER SET utf8");
	$tpl = new FastTemplate("tpl");
 	
	$file = fopen("data/restart.dat", "r");
	$restart = fread($file, 10); 	
 	
	$tpl->define(array("main"=>"main.htm"));
	$tpl->assign(array("HOME_URL"=>HOME_URL,
							"RESTART"=>$restart							
							));
		
	//*** MENU *** 
	require_once("cls/CCategory.cls.php");
  	$category_list = new CCategory($db, $id);
  	$category_list->parse_tpl($tpl, '', '','category');	  

	if (!$page && !$id) {
		$rs =	$db->qy("SELECT id FROM category WHERE homepg = 1 ");
		$id = $rs['rows'][0]['id'];	
	}

  if ($id) {

  		$tpl->define(array("content"=>"article.htm"));
 
  		// Pokud existuje id clanku, vypiseme clanek
		require_once("cls/CArticle.cls.php");		
		$article = new CArticle($db, $id);
		$article_name = $article->get_name($id);
  		$tpl->assign(array(
        "ID"=>$id,
        "ARTICLE_NAME"=>$article_name,
        "TITLE"=>$article_name,
        "ARTICLE_TEXT"=>$article->get_text($id)
  		));


  		$rs = $db->qy("SELECT gallery FROM category WHERE id = ".$id." ");
  		if ($rs['rows']) {
			require_once("cls/CPhoto.cls.php");
			$photo_list = new CPhoto($db,$id);
			$photo_list->parse_tpl($tpl, 'content', '','photo_list');
  		}

  		
  		//*** navigace	
		$rs = $db->qy("SELECT id,lft,rght,level FROM category WHERE id = ".$id." ");
		if ($rs['rows'][0]['level'] > 1) {
		$tpl->assign(array("NAV_ACTUAL_NAME"=>$article_name)); 		
			$rs = $db->qy("SELECT id,name,url FROM category WHERE lft < ".$rs['rows'][0]['lft']." AND rght > ".$rs['rows'][0]['rght']." ORDER BY lft");
			$tpl->define_dynamic("navigation","main");
			foreach ($rs['rows'] as $k=>$v) {
				$tpl->assign(array(
										"NAV_ITEM_ID"=>$v['id'],
										"NAV_ITEM_NAME"=>$v['name'],
										"NAV_ITEM_URL"=>$v['url']													
										));
				$tpl->parse("NAVIGATION",".navigation");	
	}			
		}
	}
	else if ($page && !$id) {
		
		//*** Pokud neni id clanku ale je $page, zkusime vlozit
		switch($page) {
			case 'login':
				$tpl->assign(array("TITLE"=>"Přihlášení"));
				require_once('inc/login.inc.php');
			break;
			case 'registrace':
				$tpl->assign(array("TITLE"=>"Registrace"));
				require_once('inc/registrace.inc.php');
			break;
			case 'mapa-stranek':
				$tpl->assign(array("TITLE"=>"Mapa stránek"));
				require_once('inc/mapa-stranek.inc.php');
			break;
                        case 'locations':
				$tpl->assign(array("TITLE"=>"Sprava lokaci"));
				require_once('inc/locations.inc.php');
			break;

			default:
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: http://thalie.pilsfree.cz");
				header("Connection: close");
			break;			
		}	
	
	
	}		

        $file = "/home/nwn/logs.0/nwserverStatus.txt";
        $file = file($file);
        $loaded=false;
        foreach ($file as $v) {
          if(strrpos($v,"Module loaded")>0)
             $loaded=true;
          if(strrpos($v,"Loading module")>0)
	     //$db->qy("DELETE FROM dump");
             $load=strlen(substr($v,strpos($v,'".')+2))/8.87;
	     $load=round($load);
        }
	$tpl->assign(array("LOADED"=>$loaded, "MODULE_LOADING"=>$load));
				    
	//*** Vypis hracu
	$rs = $db->qy("SELECT COUNT(id) AS count FROM dump;");
	$tpl->assign(array("PLAYER_COUNT"=>$rs['rows'][0]['count']));
	$rs = $db->qy("SELECT name, portrait FROM dump ORDER BY id ASC;");
	$tpl->define_dynamic("player_list","main");
	foreach ($rs['rows'] as $k=>$player) {
		$tpl->assign(array("PLAYER_NAME"=>$player['name']));
		$tpl->assign(array("PLAYER_PORTRAIT"=>$player['portrait']));
		$tpl->parse("PLAYER_LIST",".player_list");	
	}
	
	//*** Konec vypisu
	
	
	
	//*** Datum a cas
	$rs = $db->qy("SELECT name,val FROM pwdata WHERE tag = 'Thalie' ");

	foreach($rs['rows'] as $k=>$v) {
		$date[$v['name']] = $v['val'];	
	}	
        $moduledate = filemtime("/home/nwn/modules/Thalie.mod");
	
	$tpl->assign(array(
				"THALIE_YEAR"=>$date['JA_TIME_YEAR'],
				"THALIE_MONTH"=>$date['JA_TIME_MONTH'],					
				"THALIE_DAY"=>$date['JA_TIME_DAY'],
				"MODULE_VERSION"=>date("y.m.d",$moduledate)
	));

         


	if(!$tpl->is_defined("content") && file_exists(HOME_URL."tpl/page/".$page.".htm")) {
		$tpl->define('content',"page/$page.htm");
	}	
	
	if ($tpl->is_defined('content')) {
		$tpl->parse('CONTENT', 'content');	
	}


	$tpl->parse("MAIN","main");
	$tpl->FastPrint("MAIN");        
  
?>
