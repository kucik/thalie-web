<?php
  
  $tpl->define('content','home.htm');

  if ($sess->isAuthorized()) {
  
    if ($action == "repair") {
      $repair = new CProblems($db);
      $repair->repair($pid);
    } 
    if (($action == "delete") && $pid) {
      $delete = new CProblems($db);
      $delete->delete($pid);
      }
   }
  

  if ($id) {
    $problem = new CProblems($db,$id);
    $problem->parse_tpl($tpl);
  }
  else {
    if (!$order) {
      $order = "name";
    }
    if ($show == "no-repaired") {
      //$tpl->assign("SHOW","1");
      $show = "status != '3'";
    }
    
    $problems_list = new CProblemsList($db,$show,$order.", name",'');
    $problems_list->parse_tpl($tpl);
    
    $user_graph = new CProblems($db);
    $user_graph->graph($tpl);
    
  }
  
?>
