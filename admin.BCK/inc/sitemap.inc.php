<?php
  
$file = fopen("../sitemap.xml", "w");
/* <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> */
$data = '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="gss.xsl"?><urlset xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">';
fputs($file, $data);

$data = "<url><loc>http://thalie.pilsfree.cz/</loc></url>";
fputs($file, $data);

$rs = $db->qy("SELECT id, url FROM category WHERE level < 3 ORDER BY lft");


foreach ($rs['rows'] as $k=>$v) {
	$data = "<url><loc>http://thalie.pilsfree.cz/".$v['url']."-".$v['id'].".htm</loc></url>";

fputs($file, $data);

}

$data = "</urlset>";

fputs($file, $data); 
fclose($file);

$google_ping = fopen("http://www.google.com/webmasters/sitemaps/ping?sitemap=http://thalie.pilsfree.cz/sitemap.xml", "r");
fclose($google_ping);
  
?>
