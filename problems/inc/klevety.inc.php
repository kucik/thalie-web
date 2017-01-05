<?php
require_once("cls/CKlevety.cls.php");

  $tpl->define('content','klevety.htm');

  if ($sess->isAuthorized()) {
 
    if ($action == "update") {
      $text = trim($_POST['text']);
//      print "update $id ".$_POST['text']."";
      if(strlen($text) > 1) {
      $update = new CKlevety($db);
      $update->update($id,$text);
//      print "update $id ".$_POST['text']."";
      }
    } 
    if (($action == "delete") && $pid > 0) {
      $delete = new CKlevety($db);
      $delete->delete($pid);
    }
    $prev_id = $_POST['prev_id'];
    //print $prev_id."|".$action;
    if (($action == "insert") && $prev_id) {
      $text = trim($_POST['text']);
      if(strlen($text) > 1) {
        $insert = new CKlevety($db);
        $insert->insert($prev_id,$text);
      }
    }
  

/*  if ($id) {
    $problem = new CProblems($db,$id);
    $problem->parse_tpl($tpl);
  }
  else*/ {


    if (!$order) {
      $order = "name";
    }
     
    $problems_list = new CKlevetyList($db,$show,$order.", name",'');
    $problems_list->parse_tpl($tpl);
    
//    $user_graph = new CProblems($db);
//    $user_graph->graph($tpl);
    
  }
  } 
?>
