<?php

class CProblems {

  var $id;
  var $db;
  var $param;
  
  function CProblems(&$db, $id = '') {
    
    $this->db = $db;
    $this->id = $id;
    
    if((int)$this->id)
      $this->load();  
  }
  
  function load() {
    $qy = "SELECT *,DATE_FORMAT(date,'%d.%m.%Y') AS date FROM problems WHERE id = ".$this->id." ";
    $rs = $this->db->qy($qy);
    $this->replace($rs['rows']);
  }

  function update($param) {

    if(!$this->id) {
      $rs = $this->db->qy("INSERT INTO problems(author,date) VALUES('".(int)$_SESSION['user_id']."', NOW()) ");      
      $this->id = $rs['insert_id'];
    }

    $qy = "UPDATE problems SET 
                      name = '".addslashes($param['name'])."',
                      descript = '".addslashes($param['descript'])."',
                      solution = '".addslashes($param['solution'])."',
                      status = '".(int)$param['status']."',
                      flag = '".(int)$param['flag']."'
                  WHERE id = ".$this->id."";
                  
    $rs = $this->db->qy($qy);
    $this->load();               
  }

  function delete($pid) {
    $this->db->qy("DELETE FROM problems WHERE id = '".$pid."' ");    
  }
  
  function repair($pid) {
 
   $qy = "UPDATE problems SET repairer = '".$_SESSION['user_id']."' WHERE id = '".$pid."' ";
   $rs = $this->db->qy($qy);

  }
 
  function graph(&$tpl) {
    
    $qy = "SELECT repairer, COUNT(status) AS count FROM problems WHERE status = 3 GROUP BY repairer;";
    $rs = $this->db->qy($qy);
    $this->replace($rs['rows']);
    
    $total = $this->db->qy("SELECT COUNT(status) AS total_count FROM problems WHERE status = 3");
    
    $tpl->define_dynamic("user_graph","content");
    
    foreach ($this->param as $k=>$v) {
      
      $tpl->assign($v);
      $tpl->assign(array( 'USER_GRAPH_REPAIRER'=>($v['repairer'])?$v['repairer']:'',
                          'USER_COUNT'=>$v['count'],
                          'USER_WIDTH'=>(int)(5*100*($v['count']/$total['rows'][0]['total_count']))
                          ));
      $tpl->parse("USER_GRAPH",".user_graph");
      }
  }

  function replace($problems) {
    
    $status = array(1 => 'neopraveno', 'testuje se', 'opraveno');
    $class = array(1=> 'red', 'orange', 'green');
    
    $rs = $this->db->qy("SELECT user_id, username FROM problems_users");
    
    foreach($rs['rows'] as $v) {
      $users[$v['user_id']] = $v['username'];
    }
    
    $i = 0;

    foreach($problems as $v) {
      
      if ($v['status']) {
        $problems[$i]['status'] = $status[$v['status']];
        $problems[$i]['style'] = $class[$v['status']];
        $problems[$i]['icon'] = $v['status'];
      }
      if ($v['repairer']) {
        $problems[$i]['repairer'] = $users[$v['repairer']];
      }
      if ($v['author']) {
        $problems[$i]['author'] = $users[$v['author']];
      }
      $i++;      
    }
    
    return $this->param = $problems;
  }
        
  function parse_tpl(&$tpl,$dynamic_block='problem_detail') {
    
    $tpl->define_dynamic($dynamic_block,"content");
    
    foreach ($this->param as $k=>$v) {
      
      $tpl->assign($v);
      $tpl->assign(array( 'PROBLEM_ID'=>$v['id'],               
                          'PROBLEM_NAME'=>$v['name'],
                          'PROBLEM_DATE'=>$v['date'],
                          'PROBLEM_AUTHOR'=>($v['author'])?$v['author']:'',
                          'PROBLEM_REPAIRER'=>($v['repairer'])?$v['repairer']:'',
                          'PROBLEM_STATUS'=>$v['status'],
                          'PROBLEM_ICON'=>$v['icon'],
                          'PROBLEM_CHECKED'=>($v['flag']==1)?'checked=\"checked\"':'',
                          'PROBLEM_STYLE'=>$v['style'],                          
                          'PROBLEM_SOLUTION'=>$v['solution'],
                          'PROBLEM_SOLUTION_BR'=>nl2br($v['solution']),
                          'PROBLEM_DESCRIPT'=>$v['descript'],
                          'PROBLEM_DESCRIPT_BR'=>nl2br($v['descript'])
                          ));
      $tpl->parse(strtoupper($dynamic_block),'.'.$dynamic_block);
      }
      
  }
}

class CProblemsList {
  
  var $id;
  var $db;
  var $param;
  
  function CProblemsList(&$db, $cond='', $order='', $limit='') {

    $this->db = $db;
    $qy = "SELECT *,DATE_FORMAT(date,'%d.%m.%Y') AS date FROM problems ".
    ($cond?"WHERE $cond ":"")
    .($order?"ORDER BY $order ASC":"");
    $rs = $this->db->qy($qy);
    $this->replace($rs['rows']);    
  }
  
  function replace($problems) {
    
    $status = array(1 => 'neopraveno', 'testuje se', 'opraveno');
    $class = array(1=> 'red', 'orange', 'green');
    
    $rs = $this->db->qy("SELECT user_id, username FROM problems_users");
    
    foreach($rs['rows'] as $v) {
      $users[$v['user_id']] = $v['username'];
    }

    $i = 0;
        
    foreach($problems as $v) {
      
      if ($v['status']) {
        $problems[$i]['status'] = $status[$v['status']];
        $problems[$i]['style'] = $class[$v['status']];
        $problems[$i]['icon'] = $v['status'];
      }
      if ($v['repairer']) {
        $problems[$i]['repairer'] = $users[$v['repairer']];
      }
      if ($v['author']) {
        $problems[$i]['author'] = $users[$v['author']];
      }
            
      $i++;      
    }
    return $this->param = $problems;
  }
  
  function parse_tpl(&$tpl) {

        $tpl->define_dynamic("problems_list","content");
        
        foreach ($this->param as $k=>$v) {
            
            $v['descript'] = str_replace("\r\n"," ",$v['descript']);

            $tpl->assign($v);
            $tpl->assign(array(
                               'PROBLEM_ID'=>$v['id'],
                               'PROBLEM_NAME'=>$v['name'],
                               'PROBLEM_DESCRIPT'=>$v['descript'],
                               'PROBLEM_STATUS'=>$v['status'],
                               'PROBLEM_FLAG'=>($v['status']!="opraveno")?$v['flag']:'',
                               'PROBLEM_ICON'=>$v['icon'],
                               'PROBLEM_DATE'=>$v['date'],
                               'PROBLEM_STYLE'=>$v['style'],
                               'PROBLEM_REPAIRER'=>($v['repairer'])?$v['repairer']:'',
                               'PROBLEM_AUTHOR'=>($v['author'])?$v['author']:'',
                               'PROBLEM_REPAIRER'=>$v['repairer']
                          ));
            $tpl->parse("PROBLEMS_LIST",".problems_list");    
        }

    }
}

?>
