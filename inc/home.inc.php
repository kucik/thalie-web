<?php
  
  $tpl->define("content","home.htm");

  require_once("cls/CCategory.cls.php");
  require_once("cls/CArticle.cls.php");
  
  $category_list = new CCategory($db, $id);
  $category_list->parse_tpl($tpl, '', '','category');


  $article = new CArticle($db, $id);

  $tpl->assign(array(
        "ID"=>$id,
        "ARTICLE_NAME"=>$article->get_name($id),
        "ARTICLE_TEXT"=>$article->get_text($id)
  ));

  
?>
