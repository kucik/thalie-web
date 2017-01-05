<?php
  
  if ($sess->isAuthorized() && (($_SESSION['user_id'] == 3) || ($_SESSION['user_id'] == 44)) ) {
    
    $tpl->define('content','logs.htm');
    
    $path="../../../../home/nwn/logs.0";
    
    $handle=opendir($path); 
    while (false!==($file = readdir($handle))) 
    { 
        if ($file != "." && $file != "..") 
        { 
            echo "<a href=\"index.php?page=logs&amp;file=".$file."\">".$file."</a><br>\n"; 
        } 
    }
    closedir($handle); 


    
   function logs($getfile) {
    
        $path="../../../../home/nwn/logs.0";
        
        if($getfile) {
          $file = $path."/".$getfile;      
	  $file = file($file);   
            
        foreach ($file as $v) {
          print $v."<br />";
        }
       }   
      
    }
    
    $tpl->assign(array("LOGS"=>logs($_GET['file']) ));
  }
  else {
    $tpl->define('content','login.htm');
  }
 

 print "<br>-----------------------------<br>";

 $file = "../../../../home/nwn/logs.0/nwserverStatus.txt";
 $file = file($file);
 $loaded=false;
 foreach ($file as $v) {
   if(strrpos($v,"Module loaded")>0)
     $loaded=true;
   if(strrpos($v,"Loading module")>0) 
     $load=strlen(substr($v,strpos($v,'".')+2))/8.87;
 }

 if($loaded)
   print "Server spusten";
 else
   print "Nahravani modulu: ".$load."%";
?>
