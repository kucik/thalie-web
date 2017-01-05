<?php
	//sid
	if (isset($_GET['sid'])) {
		$sid = $_GET['sid'];	
	}elseif (isset($_POST['sid'])) {
		$sid = $_POST['sid'];	
	}else{
		$sid = "";	
	}	
  //sid
	if (isset($_GET['page'])) {
		$page = $_GET['page'];	
	}elseif (isset($_POST['page'])) {
		$page = $_POST['page'];	
	}else{
		$page = "";	
	}
	if (isset($_GET['layout'])) {
		$layout = $_GET['layout'];	
	}elseif (isset($_POST['layout'])) {
		$layout = $_POST['layout'];	
	}else{
		$layout = "";	
	}
	
	if (isset($_GET['list_param'])) {
		$list_param = $_GET['list_param'];	
	}elseif (isset($_POST['list_param'])) {
		$list_param = $_POST['list_param'];	
	}else{
		$list_param = "";	
	}
	if (isset($_GET['list_type'])) {
		$list_type = $_GET['list_type'];	
	}elseif (isset($_POST['list_type'])) {
		$list_type = $_POST['list_type'];	
	}elseif (isset($HTTP_COOKIE_VARS['list_type'])) {
		$list_type = $HTTP_COOKIE_VARS['list_type'];	
	}else{
		$list_type = "";	
	}
	
	if (isset($_GET['id'])) {
		$id = $_GET['id'];	
	}elseif (isset($_POST['id'])) {
		$id = $_POST['id'];	
	}else{
		$id = "";	
	}
	if (isset($_GET['action'])) {
		$action = $_GET['action'];	
	}elseif (isset($_POST['action'])) {
		$action = $_POST['action'];	
	}else{
		$action = "";	
	}
	
	if (isset($_GET['photo_id'])) {
		$photo_id = $_GET['photo_id'];	
	}elseif (isset($_POST['photo_id'])) {
		$photo_id = $_POST['photo_id'];	
	}else{
		$photo_id = "";	
	}
	
	if (isset($_GET['param'])) {
		$param = $_GET['param'];	
	}elseif (isset($_POST['param'])) {
		$param = $_POST['param'];	
	}else{
		$param = "";	
	}
	
		
	if (isset($_SESSION['category_id']) && $_GET['last']) {
		$category_id = $_SESSION['category_id'];
  }elseif (isset($_GET['category_id'])) {
		$category_id = $_GET['category_id'];	
    $_SESSION['category_id'] = $category_id;		
	}elseif (isset($_POST['category_id'])) {
		$category_id = $_POST['category_id'];
    $_SESSION['category_id'] = $category_id;
	}else {$category_id = ''; 	}
	
	if (isset($_SESSION['cond']) && $_GET['last']) {
		$cond = $_SESSION['cond'];
  }elseif (isset($_GET['cond'])) {
		$cond = $_GET['cond'];	
    $_SESSION['cond'] = $cond;		
	}elseif (isset($_POST['cond'])) {
		$cond = $_POST['cond'];
    $_SESSION['cond'] = $cond;
	}else {$cond = ''; 	}

	if (isset($_SESSION['sort']) && $_GET['last']) {
		$sort = $_SESSION['sort'];
  }elseif (isset($_GET['sort'])) {
		$sort = $_GET['sort'];
    $_SESSION['sort'] = $sort;    	
	}elseif (isset($_POST['sort'])) {
		$sort = $_POST['sort'];	
    $_SESSION['sort'] = $sort;    
	}else $sort = '';
  
	
	
	if (isset($_GET['product_url'])) {
		$product_url = $_GET['product_url'];	
	}elseif (isset($_POST['product_url'])) {
		$product_url = $_POST['product_url'];	
	}else{
		$product_url = "";	
	}
	
	
?>
