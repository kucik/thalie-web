<?php

  
  $tpl->define("content","article.htm");
  
  require_once("../cls/CArticle.cls.php");
  
  
  $article_list = new CArticle($db, $id);
    
  if ($action == "save") {

       $article_list->update($param, $id);
      
  }
  
  $article_list->parse_tpl($tpl, '', '','article_list');

  $article = new CArticle($db, $id);

  $tpl->assign(array(
        "ID"=>$id,
        "ARTICLE_NAME"=>$article->get_name($id),
        "ARTICLE_TEXT"=>$article->get_text($id)
  ));

  
?>
