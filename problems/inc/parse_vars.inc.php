<?php
  //sid
	if (isset($_GET['page'])) {
		$page = $_GET['page'];	
	}elseif (isset($_POST['page'])) {
		$page = $_POST['page'];	
	}else{
		$page = "";	
	}

	if (isset($_GET['id'])) {
		$id = $_GET['id'];	
	}elseif (isset($_POST['id'])) {
		$id = $_POST['id'];	
	}else{
		$id = "";	
	}
	
	if (isset($_GET['pid'])) {
		$pid = $_GET['pid'];	
	}elseif (isset($_POST['pid'])) {
		$pid = $_POST['pid'];	
	}else{
		$pid = "";	
	}	
	
	if (isset($_GET['action'])) {
		$action = $_GET['action'];	
	}elseif (isset($_POST['action'])) {
		$action = $_POST['action'];	
	}else{
		$action = "";	
	}
	
	if (isset($_GET['order'])) {
		$order = $_GET['order'];	
	}elseif (isset($_POST['order'])) {
		$order = $_POST['order'];	
	}else{
		$order = "";	
	}

	if (isset($_GET['show'])) {
		$show = $_GET['show'];	
	}elseif (isset($_POST['show'])) {
		$show = $_POST['show'];	
	}else{
		$show = "";	
	}

	if (isset($_GET['param'])) {
		$param = $_GET['param'];	
	}elseif (isset($_POST['param'])) {
		$param = $_POST['param'];	
	}else{
		$param = "";	
	}
	
?>
