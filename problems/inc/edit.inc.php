<?php
  
  if ($sess->isAuthorized()) {
    $tpl->define('content','edit.htm');
    
    if (($action == "update") && $param['name']) {
      $update = new CProblems($db,$id);
      $update->update($param);
      
    }
    if ($id) {
      $edit = new CProblems($db,$id);
      $edit->parse_tpl($tpl,'problem_detail');

    }
    
  }
  else {
    $tpl->define('content','login.htm');
  }
  
?>
