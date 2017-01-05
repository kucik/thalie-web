<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cz" lang="cz">
<head>
  <title>Přihlášení | Thalie - fantasy online RP svět hry Neverwinter Nights (NWN)</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta http-equiv="cache-control" content="no-cache" />
  <meta name="verify-v1" content="LihvmLYnMtHTb/xrZ1mMYDV97lQ5K6Qp1Erdb+kJSDo=" />
  <meta name="description" content="Thalie je persistentní fantasy online svět hry Neverwinter Nights (NWN), zaměřen především na RP hru." />
  <meta name="keywords" content="Thalie, Neverwinter Nights, nwn, fantasy, online, RP, role playing, svět, hry, Dungeons &amp; Dragons, rpg, multiplayer" />
  <meta name="robots" content="index,follow" />
  <meta http-equiv="content-language" content="cz" />

  <link rel="stylesheet" type="text/css" href="http://thalie.pilsfree.cz/css/main.css" />
  <link rel="stylesheet" type="text/css" href="http://thalie.pilsfree.cz/css/tinymce.css" />

</head>
 <body>

<div class="changelog">
<p>
<?php

/*
<iframe src="http://thalie.pilsfree.cz/changelog.php" style="border-width:0" width="100%" height="200" frameborder="0" scrolling="yes"></iframe>
*/

  $file = fopen("data/moduleversion.dat", "r");
  $moduleversion = fread($file, 16);

  $file=file("data/changelog");
   

    for($i=0;$i<sizeof($file);$i++) {
      $dt=substr($file[$i],0,19);
      $msg=substr($file[$i],28);
      $spec=substr($file[$i],26,1);
//      print "strncmp(".$moduleversion.",".$dt.",16) = ".strncmp($moduleversion,$dt,16)."\n<br>";
      if(strncmp($moduleversion,$dt,16) <= 0 && $spec == "s" )
        echo "Nenahráno ";
      print "$dt - $msg<br>";

    }
?>
</p>
 </div>

 
 </body>
</html>
