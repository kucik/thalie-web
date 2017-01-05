<?php
  include("inc/settings.inc.php");


  $sql_q[]="SELECT kills.player, kills.tag, sum(count) as 'zabito_bossu', location_property.name from (SELECT *, cast(val as DEC) as count, SUBSTRING_INDEX( `name` , '|', 1 ) as loc FROM `boss_kill`) kills, location_property where kills.loc = location_property.tag group by kills.player";
  $sql_q[]="SELECT kills.player, kills.tag, sum(count) as 'zabito_bossu', location_property.name from (SELECT *, cast(val as DEC) as count, SUBSTRING_INDEX( `name` , '|', 1 ) as loc FROM `boss_kill`) kills, location_property where kills.loc = location_property.tag group by kills.tag ";
  $sql_q[]="SELECT kills.name, kills.loc, sum(count) as 'zabito_bossu', location_property.name from (SELECT *, cast(val as DEC) as count, SUBSTRING_INDEX( `name` , '|', 1 ) as loc FROM `boss_kill`) kills, location_property where kills.loc = location_property.tag group by kills.tag ";

  $hdrs[]="Nej creep hráči";
  $hdrs[]="Nej creep postavy";
  $hdrs[]="Nejnavštěvovanější dungy";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="cache-control" content="no-cache" />
  <meta name="author" content="http://thalie.pilsfree.cz" />
  <meta name="description" content="Správa chyb Thalie" />
  <meta name="keywords" content="sprava chyb, správa chyb, thalie, nwn, neverwinter, nights, rpg, fantasy, online" />
  <meta name="robots" content="index,follow" />
  <meta http-equiv="content-language" content="cs" />

  <link rel="stylesheet" type="text/css" href="css/main.css" />

</head>


<title>Herní statistiky</title>
</head>
<body>

<?php
die(nasrat);
/*  *************** PRINT MENU ***************/

print "<table width=600 border=1><tr>\n";
 
for($i=0;$i<sizeof($sql_q);$i++) {
  print "<td><a href=\"?sel=".$i."\">".$hdrs[$i]."</td>";

}
print "</tr></table>";

/* Change next two lines  if using online*/
$db=DB;
$link = mysql_connect(DB_HOST, DB_USER, DB_PWD);

if (! $link) die(mysql_error());
mysql_select_db($db , $link) or die("Couldn't open $db: ".mysql_error());

$selectid = $_GET['sel'];
$orderby = mysql_real_escape_string($_GET['order']);
$odir = $_GET['odir'];

if($selectid <= 0)
  $selectid = 0;
$order="";
if(strlen($orderby) > 0) {
  if($odir != "a") 
    $dir=" DESC";
  else 
    $dir=" ASC";
  $order=" ORDER BY ".$orderby.$dir;
}


#print $selectid."++".$sql_q[0]."++";
print "<h2>$hdrs[$selectid]</h2>";
$result = mysql_query( $sql_q[$selectid].$order )
          or die("SELECT Error: ".mysql_error());
$num_rows = mysql_num_rows($result);
print "There are $num_rows records.<br>";
$i=0;
print "<table width=600 border=1>\n";
while ($get_info = mysql_fetch_row($result)){
  if($i==0) {
    print "<tr>\n";
    foreach ($get_info as $key => $field) {
      $column_name=mysql_field_name($result,$key);
      $dir='d';
      if($orderby == $column_name && $odir != 'a')
        $dir='a';
      print "\t<th><a href=\"?sel=".$selectid."&order=".$column_name."&odir=".$dir."\">".$column_name."</a></th>\n";
    }
    print "</tr>\n";
  }
  print "<tr>\n";
  foreach ($get_info as $key => $field)
    print "\t<td>$field</td>\n";
  print "</tr>\n";
  $i++;
}
print "</table>\n";
mysql_close($link);
?>
<br>

<form method="POST" action="birthdays_dbase_interface.php">
<input type="submit" value="Dbase Interface">
</form>

</body>
</html>

