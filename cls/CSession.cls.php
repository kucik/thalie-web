<?php

class CSession {

  var $db;
  
  function CSession(&$db) {

    $this->db = $db;

  }
  
  function login($username,$password) {

    if ($username && $password) {

      define('IN_PHPBB', true);
      $phpbb_root_path = '../forum/';
      
      $phpEx = substr(strrchr(__FILE__, '.'), 1);
      //include($phpbb_root_path . 'common.php');
      include($phpbb_root_path . 'includes/functions.php');
      //include($phpbb_root_path . 'includes/functions_user.php');
      //include($phpbb_root_path . 'includes/ucp/ucp_register.php');

      //$this->db->qy("USE phpbb3;");
      $rs = $this->db->qy("SELECT user_id, username, user_password FROM phpbb3.phpbb_users WHERE username = '".addslashes($username)."' ");
     
      $hash = $rs['rows'][0]['user_password'];
      $username = $rs['rows'][0]['username'];
            
      $rs2 = $this->db->qy("SELECT group_id FROM phpbb3.phpbb_user_group WHERE user_id = '".$rs['rows'][0]['user_id']."' ");
      
      foreach($rs2['rows'] as $v) {
      
        if ($v['group_id'] == 206) {
        
      $this->db->qy("USE pfnwn;");
      
          if (phpbb_check_hash($password, $hash)){

	         $_SESSION['login'] = 1;
            $_SESSION['user_id'] = $rs['rows'][0]['user_id'];
          }        
        
        }
      
      }

      
      
    }
  
  }
  
  function get_user_name() {
    //$this->db->qy("USE phpbb3;");
    $rs = $this->db->qy("SELECT username FROM phpbb3.phpbb_users WHERE user_id = ".$_SESSION['user_id']." ");
    //$this->db->qy("USE pfnwn;");
    return $rs['rows'][0]['username'];
  }
  
  function isAuthorized() {
		return ($_SESSION['login'])?true:false;
	}
  
  function logout() {
    $_SESSION['login'] = '';
    session_destroy();
  }

}





?>

