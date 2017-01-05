<?php
  session_start();

  require_once ("cls/ft.cls.php");
  require_once ("cls/db.cls.php");
  require_once ("inc/settings.inc.php");
  require_once ("inc/parse_vars.inc.php");


  $db = new db(DB_HOST, DB_USER , DB_PWD , DB, "true"); //z db.cls.php
        $db->qy("SET CHARACTER SET utf8");

//*** Datum a cas
        $rs = $db->qy("SELECT name,val FROM pwdata WHERE tag = 'Thalie' AND name LIKE 'JA_TIME%' ");

  foreach($rs['rows'] as $k=>$v) {
                $date[$v['name']] = $v['val'];
  print $v['name']." -> ".$v['val']."<br>";
        }
?>
