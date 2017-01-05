<?php

  require_once ("../cls/CCategory.cls.php");
  require_once ("../cls/CPhoto.cls.php");
  require_once ("inc/iso2ascii.fce.php");
  
  $tpl->define("content","category.htm");
    
    $category_list = new CCategory($db, $id);
	$gallery_list = new CGallery($db, $id);    
      
    if ($action == "save") {
      
      if(!$param['url']) {
        $param['url'] = iso2ascii($param['name']);
      }
      
      if($param['edit_id']) {
        $category_list->update($param, $param['edit_id']);
      }
      else {
        $category_list->create($id, $param);
      }
      
    }
    
	//save with gallery    
	
    if ($action == "save_gallery" && $param['edit_id']) {
         $category_list->update_gallery($param, $param['edit_id']);
         $gallery_list->update($param);      
    }	
	
	//end gallery	
    
    if ($action == "delete") {
    
      $category_list->delete($id);
    
    }
    if ($action == "edit") {

      $tpl->assign(array(
              "EDIT_ID"=>$id,
              "CATEGORY_EDIT_NAME"=>$category_list->get_name($id),
              "CATEGORY_EDIT_URL"=>$category_list->get_url($id),
              "CATEGORY_EDIT_SORT"=>$category_list->get_sort($id),
              "CATEGORY_EDIT_HOMEPG"=>""
              )
        );
    	
    }
    if (!$action && $id) {
    
      $tpl->assign(array(
              "CATEGORY_PARRENT"=>$category_list->get_name($id))
        );
      
    }
   
  $category_list->parse_tpl($tpl, 'content', '','category_list');
  
	$gallery_list->parse_tpl($tpl, 'content', '','gallery_list');
?>
