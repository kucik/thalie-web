<?php

class CKlevety {

  var $id;
  var $db;
  var $param;
  
  function CKlevety(&$db, $id ) {
    
    $this->db = $db;
    $this->id = $id;
    
    if((int)$this->id)
      $this->load();  
  }
  
  function CZDecode($input) {
    $rep['č']="<cc>";
    $rep['ď']="<cd>";
    $rep['ě']="<ce>";
    $rep['ň']="<cn>";
    $rep['ř']="<cr>";
    $rep['ť']="<ct>";
    $rep['ů']="<cu>";
    $rep['Č']="<cC>";
    $rep['Ď']="<cD>";
    $rep['Ě']="<cE>";
    $rep['Ň']="<cN>";
    $rep['Ř']="<cR>";
    $rep['Š']="<cS>";
    $rep['Ť']="<cT>";
    $rep['Ů']="<cU>";

    $text = strtr($input,$rep);

    return $text;
  }

  function load() {
    $qy = "SELECT * FROM Klevety ORDER BY lokace, NPC, id";
    $rs = $this->db->qy($qy);
    $this->replace($rs['rows']);
  }

  function update($id,$text) {
    if($id <= 0) {
      return;
    }
    $replacement="áé?óúÁ?ÉÍÓ?3Úݮ";
    $diakr="áéò»úÁÏÌÒة«Úݾ";
//    $text = strtr($text,$diakr,$replacement);
    $text = mysql_real_escape_string($this->CZDecode($text));
//    print "$id: '$text'\n";
    $qy = "UPDATE Klevety SET text = '".$text."' 
                               WHERE id = ".$id."";
//    print "$qy";
    $rs = $this->db->qy($qy);
    $this->load();               
  }
 
  function insert($prev_id,$text) {
   $text = $this->CZDecode($text);
   $qy = "INSERT INTO Klevety (lokace_tag, lokace, NPC_tag, NPC, text)
          SELECT  lokace_tag, lokace, NPC_tag, NPC, '".$text."' FROM `Klevety`
          WHERE id  = ".$prev_id.""; 
   //print "inserting as id ".$prev_id."!!! ".$text;
   $rs = $this->db->qy($qy);
  }

  function delete($pid) {
    $this->db->qy("DELETE FROM Klevety WHERE id = '".$pid."' ");    
  }
  
  function repair($pid) {
 
   $qy = "UPDATE problems SET repairer = '".$_SESSION['user_id']."' WHERE id = '".$pid."' ";
   $rs = $this->db->qy($qy);

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
      $tpl->assign(array( 'KLEVETY_ID'=>$v['id'],               
                          'KLEVETY_AREA_NAME'=>$v['area'],
                          'KLEVETY_AREA_TAG'=>$v['area_tag'],
                          'KLEVETY_DATE'=>$v['date'],
                          'KLEVETY_AUTHOR'=>($v['author'])?$v['author']:'',
                          'KLEVETY_REPAIRER'=>($v['repairer'])?$v['repairer']:'',
                          'KLEVETY_STATUS'=>$v['status'],
                          'KLEVETY_ICON'=>$v['icon'],
                          'KLEVETY_CHECKED'=>($v['flag']==1)?'checked=\"checked\"':'',
                          'KLEVETY_STYLE'=>$v['style'],                          
                          'KLEVETY_SOLUTION'=>$v['solution'],
                          'KLEVETY_SOLUTION_BR'=>nl2br($v['solution']),
                          'KLEVETY_DESCRIPT'=>$v['descript'],
                          'KLEVETY_DESCRIPT_BR'=>nl2br($v['descript'])
                          ));
      $tpl->parse(strtoupper($dynamic_block),'.'.$dynamic_block);
      }
      
  }
}

class CKlevetyList {
  
  var $id;
  var $db;
  var $param;
  
  function CKlevetyList(&$db, $cond='', $order='', $limit='') {

    $this->db = $db;

    mysql_query("SET CHARACTER SET iso-8859-2");
    $qy = "SELECT * FROM Klevety ORDER BY lokace, NPC, id";
    $rs = $this->db->qy($qy);
    $this->replace($rs['rows']);    
  }

  function CZEncode($input) {
      $rep['<cc>']="č";
      $rep['<cd>']="ď";
      $rep['<ce>']="ě";
      $rep['<cn>']="ň";
      $rep['<cr>']="ř";
      $rep['<ct>']="ť";
      $rep['<cu>']="ů";
      $rep['<cC>']="Č";
      $rep['<cD>']="Ď";
      $rep['<cE>']="Ě";
      $rep['<cN>']="Ň";
      $rep['<cR>']="Ř";
      $rep['<cS>']="Š";
      $rep['<cT>']="Ť";
      $rep['<cU>']="Ů";

      $text = strtr($input,$rep);

      return $text;
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
        $tpl->define_dynamic("klevety_list","content");
        
        foreach ($this->param as $k=>$v) {
	 
      //      $v['descript'] = str_replace("\r\n"," ",$v['descript']);

            $tpl->assign($v);
	    $v['text'] = $this->CZEncode($v['text']);
            $tpl->assign(array(
                               'KLEVETY_ID'=>$v['id'],
		               'KLEVETY_PREV_ID'=>$v['id'],
                               'KLEVETY_AREA_NAME'=>$v['lokace'],
                               'KLEVETY_AREA_TAG'=>$v['lokace_tag'],
                               'KLEVETY_NPC_NAME'=>$v['NPC'],
                               'KLEVETY_NPC_TAG'=>$v['NPC_tag'],
	                       'KLEVETY_TEXT'=>$v['text'],
                               'KLEVETY_ACT'=>"update",
                               'KLEVETY_BUTTON'=>"Změnit",
                               'KLEVETY_NOT_NEW'=>"Změnit",
                               'KLEVETY_ICON'=>"images/icon2.gif"
                          ));
            $tpl->parse("KLEVETY_LIST",".klevety_list");   
	    $act_npc = $this->param[$k][lokace_tag].";".$this->param[$k][NPC_tag];
	    $next_npc = $this->param[$k+1][lokace_tag].";".$this->param[$k+1][NPC_tag];
	    if($act_npc <> $next_npc) {
              $tpl->assign($v);
	      $tpl->assign(array(
	                         'KLEVETY_ID'=>0,
				 'KLEVETY_PREV_ID'=>$v['id'],
                                 'KLEVETY_AREA_NAME'=>$v['lokace'],
                                 'KLEVETY_AREA_TAG'=>$v['lokace_tag'],
				 'KLEVETY_NPC_NAME'=>$v['NPC'],
				 'KLEVETY_NPC_TAG'=>$v['NPC_tag'],
				 'KLEVETY_TEXT'=>"",
                                 'KLEVETY_BUTTON'=>"Přidat",
                                 'KLEVETY_ICON'=>"images/icon3.gif",
				 'KLEVETY_ACT'=>"insert"
				 ));
              $tpl->parse("KLEVETY_LIST",".klevety_list");
	    }
      //      print "<tr>";
//	    print "<td>".$v['lokace']."(".$v['lokace_tag'].")</td>";
//	    print "<td>".$v['NPC']."(".$v['NPC_tag'].")</td>";
//	    print "<td>".$v['text']."(".$act_npc."|".$next_npc.")</td>";
  //          print "</tr>";
        }
	//print "</table>";

    }
}

?>
