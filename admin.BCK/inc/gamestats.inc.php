<?php

$tpl->define("content", "gamestats.htm");


  $sql_q[] = "SELECT player, LTRIM(tag) as postava, SUM(cast(val as DEC)) as zabito, MAX(last) as naposledy FROM `boss_kill` WHERE last > CONVERT('\$DTFROM',DATETIME) AND last < CONVERT('\$DTTO',DATETIME) group by boss_kill.player";
  $sql_q[] = "SELECT player, LTRIM(tag) as postava, SUM(cast(val as DEC)) as zabito, MAX(last) as naposledy FROM `boss_kill` WHERE last > CONVERT('\$DTFROM',DATETIME) AND last < CONVERT('\$DTTO',DATETIME)  group by boss_kill.tag";
  $sql_q[] = "SELECT kills.name boss_name, kills.loc as lokace_tag, sum(count) as projito, location_property.name as location_name , last as Naposledy from (SELECT *, cast(val as DEC) as count, SUBSTRING_INDEX( `name` , '|', 1 ) as loc FROM `boss_kill` WHERE last > CONVERT('\$DTFROM',DATETIME) AND last < CONVERT('\$DTTO',DATETIME)) kills, location_property where kills.loc = location_property.tag group by kills.loc ";
  $sql_q[] = " SELECT * FROM location_property WHERE spawn_disable >0 ";
  $sql_q[] = "SELECT IF(object_type = 1,'NPC','Placeably') typ, loc_resref, loc_tag, SUM(1) as pocet_objektu, name, spawn_disable  FROM loc_persist_plc, location_property   where loc_persist_plc.loc_tag = location_property.tag group by loc_tag,object_type";

  $sql_q[] = "SELECT kills.player as hrac, LTRIM(kills.tag) as postava, kills.name boss_name, kills.loc as lokace_tag, count as 'pocet_projiti', location_property.name as location_name, last as naposledy from (SELECT *, cast(val as DEC) as count, SUBSTRING_INDEX( `name` , '|', 1 ) as loc FROM `boss_kill` WHERE last > CONVERT('\$DTFROM',DATETIME) AND last < CONVERT('\$DTTO',DATETIME) ) kills, location_property where kills.loc = location_property.tag ";


//XP a GP hracu
  $sql_q[] = "SELECT xp.player, LTRIM(xp.tag) as postava, cast(xp.val as DEC) xp ,ROUND((1+sqrt(1+4*(xp.val/2500)))/2,1) as level, ROUND(IFNULL(gp.val,0),1) as gp_v_bance, play.last as zalozeno  
FROM pwdata as xp, 
pwchars as play LEFT JOIN pwdata as gp ON play.player = gp.player AND
play.tag = gp.tag AND gp.name = 'GOLD'
where
xp.last > CONVERT('\$DTFROM',DATETIME) AND 
xp.last < CONVERT('\$DTTO',DATETIME) AND 
xp.name = 'XP_BACKUP' AND 
xp.player = play.player and 
xp.tag = play.tag AND 
cast(xp.val as DEC) > '999' AND 
play.name = 'PLAYED'";
//Umrti postav
  $sql_q[]= "SELECT sum.*, deathlog.level, subdual, killer_acc, killer_name, IFNULL(killer_lvl,0) as killer_lvl from (SELECT player, name, SUM(1) as pocet_smrti, max(date) as naposledy 
FROM deathlog group by player,name) as sum, deathlog 
where sum.player= deathlog.player and sum.name = deathlog.name and sum.naposledy = deathlog.date AND
date > CONVERT('\$DTFROM',DATETIME) AND date < CONVERT('\$DTTO',DATETIME) ";
//Umrti z rukou PC
  $sql_q[]= "SELECT * FROM deathlog WHERE killer_acc != 'NPC' AND
 date > CONVERT('\$DTFROM',DATETIME) AND date < CONVERT('\$DTTO',DATETIME) ";
//Zabijaci hracu
  $sql_q[]= "SELECT killer_acc, killer_name, max(killer_lvl), count(1) as pocet_zarezu 
FROM deathlog 
WHERE date > CONVERT('\$DTFROM',DATETIME) AND date < CONVERT('\$DTTO',DATETIME)
group by killer_acc, killer_name"; 
// Pronajmy
  $sql_q[]= "SELECT r.player, r.name, 
 if(r.lessor_id=1,'Ivory - Hostinec u krkovicky',
   if(r.lessor_id=2,'Karatha - U Skopku',
     if(r.lessor_id=3,'Karatha - U modreho racka',
       if(r.lessor_id=4,'Karatha - Najemni byty a domy',
         if(r.lessor_id=5,'Charaxss - guildovni domy',
           if(r.lessor_id=6,'Olath Deis - pokoje','-Neznamy-')))))) Pronajimatel, r.room_id, ROUND((r.hire_expire - r.hire_from)/3600/24,2) 'Celkova delka pronajmu [IC dny]', ROUND((r.hire_expire - CAST(c.val as DECIMAL))/3600/24,2) 'zbyva [IC dni]' FROM room_hire r, pwdata c where c.name = 'CURRENT_TIMESTAMP'"; 
///Boss respawn time
  $sql_q[]= "SELECT lp.resref, lp.tag, lp.name, ROUND((lp.boss_spawn_time - CAST(c.val as DECIMAL))/3600/6,2) 'respawn za (h)' FROM location_property lp, pwdata c WHERE c.name = 'CURRENT_TIMESTAMP' AND lp.boss_spawn_time > 0";


  $hdrs[] = "Creep hráčů";
  $hdrs[] = "Creep postav";
  $hdrs[] = "Návštěvnost dungů";
  $hdrs[] = "Lokace s vypnutým spawnem";
  $hdrs[] = "Lokace upravene DM";
  $hdrs[] = "Jednotlive postavy a dungy";
  $hdrs[] = "XP a GP hráčů";
  $hdrs[] = "Úmrtí postav";
  $hdrs[] = "Úmrtí z rukou PC";
  $hdrs[] = "Zabijáci hráčů";
  $hdrs[] = "Pronajmy";
  $hdrs[] = "Boss respawn";

if($_GET['sel'] > 0) {
  $selectid = $_GET['sel'];
}
else {
  $selectid= $param['selectID'];
}
$param['selectID'] = $selectid;
#$selectid= $param['selectID'];

$datefrom = $_POST['datefrom'];
$dateto = $_POST['dateto'];
if(!strlen($datefrom)) 
  $datefrom = "2010-05-27";
if(!strlen($dateto)) 
  $dateto = date("Y-m-d H:i:s");
#$datefrom .= " 00:00:00";
#$dateto .= " 00:00:00";

#print "selectid=".$selectid."***";
$tpl->define_dynamic('option_list', 'content');

foreach ($hdrs as $k => $v) {
    $tpl->assign(array(
        'OPTION' => $v,
        'O_VALUE' => $k
    ));
    $tpl->parse('OPTION_LIST', '.option_list');
}

if ($selectid <= 0) {
    $selectid = 0;
}
$order = "";

if (strlen($orderby) > 0) {
    if ($odir != "a") {
        $dir = " DESC";
    } else {
        $dir = " ASC";
    }
    $order = " ORDER BY " . $orderby . $dir;
}

?>

<?php
/* *************** PRINT MENU ************** */

$printout="";

$db = DB;
$link = mysql_connect(DB_HOST, DB_USER, DB_PWD);

if (!$link)
    die(mysql_error());
mysql_select_db($db, $link) or die("Couldn't open $db: " . mysql_error());


#$selectid = $_GET['sel'];
#if($_POST['sel'] >= 0) {
#  $selectid = $_POST['sel'];
#}
$orderby = mysql_real_escape_string($_GET['order']);
$odir = $_GET['odir'];

if ($selectid <= 0)
    $selectid = 0;
$order = "";
if (strlen($orderby) > 0) {
    if ($odir != "a")
        $dir = " DESC";
    else
        $dir=" ASC";
    $order = " ORDER BY " . $orderby . $dir;
}

$sql = $sql_q[$selectid];
$sql = str_replace('$DTFROM', $datefrom, $sql);
$sql = str_replace('$DTTO', $dateto, $sql);

$header .= "<h2>$hdrs[$selectid]</h2>";
$info .= "(statistiky od 27. 05. 2010)<br>";
#print "selectid=".$selectid."***";
$result = mysql_query($sql . $order)
        or die("SELECT Error: " . mysql_error());
$num_rows = mysql_num_rows($result);
$info .= "Nalezeno $num_rows záznamů.<br>";
$info .= "Od: $datefrom Do: $dateto";
$i = 0;
$printout .= "<table id=\"dataTable\" class=\"tablesorter\" cellspacing=\"1\" cellpadding=\"0\">\n";
while ($get_info = mysql_fetch_row($result)) {
    if ($i==0) {
        $printout .= "<thead>\n<tr>\n";
        foreach ($get_info as $key => $field) {
            $column_name = mysql_field_name($result, $key);
            $dir = 'd';
            if ($orderby == $column_name && $odir != 'a')
                $dir = 'a';
            //$printout .="\t<th><a href=\"?page=gamestats&sel=" . $selectid . "&order=" . $column_name . "&odir=" . $dir . "\">" . $column_name . "</a></th>\n";
            $printout .="\t<th>" . $column_name . "</th>\n";
        }
        $printout .= "</tr>\n</thead>\n<tbody>\n";
    }
    $printout .= "<tr>\n";
    foreach ($get_info as $key => $field)
        $printout .= "\t<td>$field</td>\n";
    $printout .= "</tr>\n";
    $i++;
}
$printout .= "</tbody></table>\n";
mysql_close($link);

$tpl->assign('HEADER',$header);
$tpl->assign('DATA_INFO',$info);
$tpl->assign('TABULKA',$printout);
#print "$printout";
?>



