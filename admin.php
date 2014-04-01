<?php 
session_start();
require_once('parameters.php');
require_once('class/class.php');

$adatkapcsolat = new data_connect;
$adatkapcsolat->connect();

$user = new user;
$user->login();

$admin_htmluj = new html_blokk;

if ($_SESSION["sessfelhasznalojog"] == "1") {
	//belép
	
	$admin_htmluj->load_template_file("template/admin_menu.tpl",$array);
	$admin_menu = $admin_htmluj->html_code;
	
	if ($_REQUEST[tartalom]){
		include('admin/'.$_REQUEST[tartalom].'.php');
	} else {
		include('admin/admin_cimlap.php');
	}
	$array = array('admin_torzs' => $admin_torzs,
					'admin_menu' => $admin_menu);
	$admin_htmluj->load_template_file("template/admin.tpl",$array);
}
else {
	//nem lép be
	if ($_REQUEST[submit]){
		$array = array('belephiba' => "Rossz felhasználónév, vagy jelszó!");
	}
	$admin_htmluj->load_template_file("template/login.tpl",$array);
	$admin_html = $admin_htmluj->html_code;
	
	
	
	$array = array('admin_torzs' => $admin_html);		
	
	$admin_htmluj->load_template_file("template/admin.tpl",$array);
}

echo $admin_htmluj->html_code;
?>