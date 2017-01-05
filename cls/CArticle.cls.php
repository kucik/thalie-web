<?php

  class CArticle {
    
    var $db;
    var $id;
  
  
    function CArticle(&$db, $id = '') {
		
		  $this->db = $db;
		  $this->load($id);
		  
		}  
  

    
    function load($id = 0) {
    
      $this->id = (int)$id;     
      
      if (!$this->id) { 
        $rs = $this->db->qy("SELECT id, name, url, level, lft, rght FROM category ORDER BY lft");      
        $this->items = $rs['rows'];

      }
      else {
        $rs = $this->db->qy("SELECT id, name, url, level, lft, rght, text FROM category ORDER BY lft");      
        $this->items = $rs['rows'];      
      }
      
    }
    
  function update($param, $id='') {
  		
  		$qy = "UPDATE category SET
  					text = '".addslashes($param['text'])."'
  			   WHERE id = ".$id;
  						
  		$rs = $this->db->qy($qy);
  		//$this->level();
//  		$this->rebuild_tree($id);
  		$this->load($id);
  	}

  function get_name($id) {
    
    $rs = $this->db->qy("SELECT name FROM category WHERE id = '".$id."' ");
    return $rs['rows'][0]['name'];
    
  }

  function get_text($id) {
    
    $rs = $this->db->qy("SELECT text FROM category WHERE id = '".$id."' ");
    return $rs['rows'][0]['text'];
    
  }

  function get_url($id) {
    
    $rs = $this->db->qy("SELECT url FROM category WHERE id = '".$id."' ");
    return $rs['rows'][0]['url'];
    
  }
  	
    function print_tpl(&$tpl, $items, $dynamic_block) {

      if(is_array($items)) {
        foreach ($items as $k=>$v) {

				if ($v['level'] == 3) {
					$rs = $this->db->qy("SELECT id,url FROM category WHERE lft <= '".$v['lft']."' AND rght >= '".$v['rght']."' ORDER BY lft DESC LIMIT 1 ");
					$parrent_url = $rs['rows'][0]['url'].'-'.$rs['rows'][0]['id'].'.htm';            
/*					$parrent_url = 'index.php?page='.$rs['rows'][0]['url'].'&id='.$rs['rows'][0]['id'];           */ 
            }            
            
            $tpl->assign($v);
            $tpl->assign(array( 
                               'CATEGORY_ID'=>$v['id'],
                              // 'CATEGORY_LINK'=>($v['level'] == 3)?'<span class=\"sub'.($v['level']-1).'\">'.$v['name'].'</span>':'<a href="'.{HOME_URL}.$v['url'].'" class="sub'.($v['level']-1).'">'.$v['name'].'</a>',
 		                          'CLASS'=>($v['level'])?'sub'.($v['level']-1):'',
                               'C_CATEGORY_ACTIVE'=>($v['active'])?'active':'',                               
                               'CATEGORY_NAME'=>$v['name'],
                               'ARTICLE_NAME'=>$v['name'],
                               'ARTICLE_TEXT'=>$v['text']                                                                             
                          ));
                          
            $tpl->parse(strtoupper($dynamic_block),'.'.$dynamic_block);  
            
        }        
      }
    }


      
    function parse_tpl(&$tpl, $where_parse='', $tpl_file = '', $dynamic_block='category_list') {

      if ($tpl_file) {$tpl->define('category', $tpl_file); $tpl_name = 'category';}
      else $tpl_name = 'content';

      $tpl->define_dynamic($dynamic_block,  $tpl_name);
      
      $this->print_tpl($tpl, $this->items, $dynamic_block);


      if ($where_parse) $tpl->parse($where_parse,$tpl_name);
  }
  
  
  }

?>
