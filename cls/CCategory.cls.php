<?php
  
  class CCategory {
    
    var $db;
    var $id;
    var $values;
    var $items;
    var $name;
    var $active_items;
/********************************************/

    function CCategory(&$db, $id = '') {
		
		  $this->db = $db;
		  $this->load($id);
		  
		}
    
/********************************************/
    
    function load($id = 0) {
    
      $this->id = (int)$id;
      
      if ($this->id == 0) {
        
        $rs = $this->db->qy("SELECT id,id_prev,lft,rght,name,url,level,sort,homepg  FROM category WHERE id_prev = '".$this->id."' ORDER BY sort,name ASC ");
        $items= $rs['rows'];

      }
      else {
        
        $rs = $this->db->qy("SELECT id,id_prev,lft,rght,name,url,level,sort FROM category WHERE id = '".$this->id."' ");
        $rs = $this->db->qy("SELECT id,id_prev,lft,rght,name,url,level,sort,homepg FROM category WHERE lft <= '".$rs['rows'][0]['lft']."' AND rght  >= '".$rs['rows'][0]['rght']."' /* ORDER BY sort,name ASC */ ");
        
        $this->active_items = $rs['rows'];
        array_unshift($this->active_items,array('id'=>0));
                    
        if (is_array($this->active_items)) {

          $levels=0;
          foreach ($this->active_items as $k=>$item) {
            $levels++;
            $rs = $this->db->qy("SELECT * FROM category WHERE id_prev = '".$item['id']."' ");
            
            foreach ($rs['rows'] as $k2=>$row) {
              
              foreach($this->active_items as $k3=>$active_item) {
                if ($active_item['id'] == $row['id']) {
                  $active = 1;
                  break;
                }
                else {
                  $active = 0;
                }
              }
               
              $items[] = array(
                                "id"=>$row['id'],
                                "name"=>$row['name'],
                                "lft"=>$row['lft'],
                                "rght"=>$row['rght'],
                                "sort"=>$row['sort'],
                                "url"=>$row['url'],
                                "active"=>$active,
                                "level"=>$levels,
                                "homepg"=>$row['homepg']
                              );
            }              
          }
          $items = $this->msort($items, 'lft');
        }
        
      } 
      $this->items = $items;
      //print_r($items);
      
    }
    
/********************************************/

  function msort($array, $id="id") {
        $temp_array = array();
        while(count($array)>0) {
            $lowest_id = 0;
            $index=0;
            foreach ($array as $item) {
                if ($item[$id]<$array[$lowest_id][$id]) {
                    $lowest_id = $index;
                }
                $index++;
            }
            $temp_array[] = $array[$lowest_id];
            $array = array_merge(array_slice($array, 0,$lowest_id), array_slice($array, $lowest_id+1));
        }
        return $temp_array;
    }
/*******************************************/

  function validate_data($param) {
    return "";
  }

/*******************************************/
	function create($id_prev = 0, $param) {
	  if ($param['name']) {

      $this->id_prev = (int)$id_prev;
      $this->name = addslashes($param['name']);
      $this->url = $param['url'];
  		
  		$rs = $this->db->qy("SELECT id FROM category WHERE id_prev = '".$this->id_prev."' AND name = '".$this->name."' ");
  		if (!$rs['rows']) {
   		$rs = $this->db->qy("INSERT INTO category (id_prev, name, url) VALUES ('".$this->id_prev."', '".$this->name."', '".$this->url."')");
  			$this->id = $rs['insert_id'];
      	$this->update($param, $this->id);
      }
    }
  }

/********************************************/

  function delete($id='') {
		$this->id = $id;
		$rs = $this->db->qy("SELECT lft, rght FROM category WHERE id = '".$id."' ");
		$this->db->qy("DELETE FROM category WHERE (lft BETWEEN '".$rs['rows'][0]['lft']."' AND '".$rs['rows'][0]['rght']."') OR id = '".$this->id."' ");
		unset($this->param);
		
		$this->rebuild_tree($id);
		$this->load();
	}

/********************************************/
  
	function update($param, $id='') {
  		
  		$qy = "UPDATE category SET
  					name = '".addslashes($param['name'])."',
  					url = '".$param['url']."',
  					sort = '".$param['sort']."'
  			   WHERE id = ".$id;
  						
  		$rs = $this->db->qy($qy);

		if($param['homepg']) {
			$this->db->qy("UPDATE category SET homepg = 0 ");
			$this->db->qy("UPDATE category SET homepg = 1 WHERE id = ".$id." ");		
		}  		
  		
  		$this->rebuild_tree($id);
  		$this->level();
      $this->load($id);
  	}
  	
  	function update_gallery($param, $id) {
		  	$this->db->qy("UPDATE category SET gallery = '".$param['gallery']."' WHERE id = ".$id." ");
  	}
/********************************************/

  function level() {
    //echo "level";
    $rs = $this->db->qy("SELECT id, lft, rght FROM category");
    
    foreach ($rs['rows'] as $k=>$v) {
      $rs = $this->db->qy("SELECT COUNT(id) AS level FROM category WHERE lft < '".$v['lft']."' AND rght > '".$v['rght']."' ");
      $level = $rs['rows'][0]['level']+1;
      $rs = $this->db->qy("UPDATE category SET level = '".$level."' WHERE id = '".$v['id']."' ");
      //print_r($rs['rows']);
      
    }
    return;
  }

  function get_name($id) {    
    $rs = $this->db->qy("SELECT name FROM category WHERE id = '".$id."' ");
    return $rs['rows'][0]['name'];
  }

  function get_url($id) {
    $rs = $this->db->qy("SELECT url FROM category WHERE id = '".$id."' ");
    return $rs['rows'][0]['url'];
  }
  function get_sort($id) {
    $rs = $this->db->qy("SELECT sort FROM category WHERE id = '".$id."' ");
    return $rs['rows'][0]['sort'];
  }  
  function get_homepg($id) {
    $rs = $this->db->qy("SELECT homepg FROM category WHERE id = '".$id."' ");
    return $rs['rows'][0]['homepg'];
  }
	
	function get_active($id) {
		$rs = $this->db->qy("SELECT id,lft,rght,name,url FROM category WHERE id = '".$this->id."' ");
		$rs = $this->db->qy("SELECT id,lft,rght,name,url FROM category WHERE lft <= '".$v['lft']."' AND rght >= '".$v['rght']."' ORDER BY lft DESC ");
		$this->active_items = $rs['rows'];
	}
/********************************************/

  function rebuild_tree($id = 0, $id_prev = 0, $left = 1) {		
		
 		$right = $left+1;
 		$rs = $this->db->qy("SELECT id FROM category 
 							 WHERE id_prev = '".(int)$id_prev."'ORDER BY sort,name ASC "); 		  
      
  		foreach ($rs['rows'] as $k=>$row) {
  			$right = $this->rebuild_tree($id, $row['id'], $right);
  		}
  		
  		$this->db->qy("UPDATE category SET lft = '".$left."', rght='".$right."' WHERE id ='".$id_prev."' ");
  		return $right+1;
  
	}

/*******************************************/

    function print_tpl(&$tpl, $items, $dynamic_block) {

      if(is_array($items)) {
        foreach ($items as $k=>$v) {
            if (($v['active'] == 1) && ($v['level'] == 2)) {
					$rs = $this->db->qy("SELECT id,url FROM category WHERE lft <= '".$v['lft']."' AND rght >= '".$v['rght']."' ORDER BY lft DESC LIMIT 1 ");
/*					$parrent_url = $rs['rows'][0]['url'].'-'.$rs['rows'][0]['id'].'.htm';            */
					$parrent_url = 'index.php?page='.$rs['rows'][0]['url'].'&id='.$rs['rows'][0]['id'];            
            }
            $tpl->assign($v);
            $tpl->assign(array( 
                               'CATEGORY_ID'=>$v['id'],
                               'CLASS'=>($v['level'])?'sub'.($v['level']-1):'',
                               'C_CATEGORY_ACTIVE'=>($v['active'])?' active':'',                               
                               'CATEGORY_NAME'=>$v['name'],
                               'CATEGORY_SORT'=>$v['sort'],
                               //'CATEGORY_URL'=>$v['url']                               
                               //'CATEGORY_EDIT_HOMEPG'=>($v['homepg'])?' checked="checked"':'',
                               'CATEGORY_URL'=>($v['level'] == 3)?$parrent_url.'#'.$v['url']:$v['url'].'-'.$v['id'].'.htm'                                                                             
//                               'CATEGORY_URL'=>($v['level'] == 3)?$parrent_url.'#'.$v['url']:'index.php?page='.$v['url'].'&id='.$v['id']
                          ));
                          
            $tpl->parse(strtoupper($dynamic_block),'.'.$dynamic_block);  
            
        }        
      }
    }
/*******************************************/

      
    function parse_tpl(&$tpl, $tpl_name='', $tpl_file = '', $dynamic_block='category_list') {

      if(!$tpl_name) {
      	$tpl_name = 'main';
      }
		
      $tpl->define_dynamic($dynamic_block,  $tpl_name);
      
      $this->print_tpl($tpl, $this->items, $dynamic_block);


      


/*******************************************/  
  }  

?>