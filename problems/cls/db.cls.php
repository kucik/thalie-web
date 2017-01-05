<?php
class db{

	var $conn;	
	var $print; // "superverbose", "verbose", null (other), "silent"
	var	$strip;

	function db($host=null,$user=null,$pwd=null,$db="", $_strip = "false"){
		$this->conn = mysql_connect($host,$user,$pwd);
		$this->strip = $_strip;

		if(!is_resource($this->conn)) return;
		if($db) $ret = mysql_select_db($db,$this->conn);
		return $this->conn;
	}
	
	function first($qy) {
		// preset used variables
		if (($res = $this->exec($qy,'silent'))) {
			$ret = mysql_fetch_array($res, MYSQL_ASSOC);
			if ($this->strip == "true") {
//				print_var($ret);
				if (is_array($ret)) {
					foreach ($ret as $key=>$hodnota) {
						$ret[$key] = stripslashes($hodnota);
//						echo $key.":".$hodnota."<BR>";
					}
				}
			}
		}
		// running the query

		return $ret; 
	
	}

  function exec($qy, $print='unset'){

		// preset used variables
		$ret = array();
    	$query_printed = false;
		if ($print=='unset') $print = $this->print; 
   
		// get out if no connection
		if(!is_resource($this->conn)) return false;

		// running the query
		$ret = mysql_query($qy,$this->conn);
  
		// print query if verbose or not silent and error
		if($print=="verbose" || $print=="superverbose"
    		 || $print!="silent" && mysql_errno($this->conn)){ 
	
      	print "<table border='1' bordercolor='black' style='border:0px dashed black'>";
        print "<tr><th bgcolor='#ccffcc'>query</th><td><pre>$qy</pre></td></tr>";
      	print "<tr><th bgcolor='#ffffcc'>result</th><td>$ret</td></tr>";

				// print error if any
				if($print!="silent" && mysql_errno($this->conn)){
					print "<tr><th bgcolor='#ffcccc'>errno</th><td>".mysql_errno($this->conn)."</td></tr>";
					print "<tr><th bgcolor='#ffcccc'>error</th><td>".mysql_error($this->conn)."</td></tr>";
				}

      	print "</table>";
    }

		return $ret;
  }
  
	function qy($qy, $print='unset'){

		// preset used variables
		$ret = array();
		$row = array();
		if ($print=='unset') $print = $this->print; 
    $c=0;
    
		// running the query
		$ret['result'] = $this->exec($qy,'silent');

    // getting information
		$ret['errno'] = mysql_errno($this->conn);
		$ret['error'] = mysql_error($this->conn);
    $ret['insert_id'] = mysql_insert_id($this->conn);
		$ret['affected_rows'] = mysql_affected_rows($this->conn); 

		// getting rows if present
		if(is_resource($ret['result'])){
			$ret['rows']=array();			
			while($row = mysql_fetch_array($ret['result'],MYSQL_ASSOC)){
				if ($this->strip == "true") {
					foreach ($row as $key=>$hodnota) {
						$row[$key] = stripslashes($hodnota);
					}
				}
				$ret['rows'][]=$row;
			}
		}
 				if($print=="superverbose"){
        	$c=count($ret['rows'][0]);
					print "<table border='1' bordercolor='black' style='border:0px dashed black'>\r\n";
					print "<tr><th bgcolor='#ccffcc'>query</th><td colspan='$c'><pre>".$qy."</pre></td></tr>";
					print "<tr><th bgcolor='#ffffcc'>result</th><td colspan='$c'>".$ret['result']."</td></tr>";
					if($ret['errno']){
						print "<tr><th bgcolor='#ffcccc'>errno</th><td colspan='$c'>".$ret['errno']."</td></tr>";
						print "<tr><th bgcolor='#ffcccc'>error</th><td colspan='$c'>".$ret['error']."</td></tr>";
          }
					print "<tr><th bgcolor='#ccffff'>insert_id</th><td colspan='$c'>".$ret['insert_id']."</td></tr>";
					print "<tr><th bgcolor='#ccccff'>affected rows</th><td colspan='$c'>".$ret['affected_rows']."</td></tr>";
					if(is_array($ret['rows'])){
						print "<tr><th bgcolor='#cccccc' colspan = '".($c+1)."'>data</th></tr>";
						if(is_array($ret['rows'][0])){
	            print "<tr>";
  		        foreach($ret['rows'][0] as $k => $v) print "<th>$k</th>";
	  		      print "</tr>\r\n";
            }
						foreach($ret['rows'] as $row){
							print "<tr>";
  	      	 	foreach($row as $v) print "<td>$v</td>";
    	      	print "</tr>";
	          }
          }
  	      print "</table>";
        }

    
		//returning information
		return $ret;
	}




	//next useful functions...

	function array2chain($array){
		if(!is_array($array)) return false;
  	foreach ($array as $k=>$v){
			$array[$k] = "'$v'";
    }
		$string = implode(",",$array);
		return $string;
  }
	function chain2array($string){
		$je = ereg("^([^,]*(,[^,]+)?)*$", $string);
    echo $je;
		if(!$je) return false;
		$array = explode(",",$string);
  	foreach ($array as $k=>$v){
			$je = ereg("^[[:space:]]*'(.*)'[[:space:]]*$", $v, $regs);
			if(!$je) return false;
			$array[$k] = $regs[1];
    }
		return $array;
  }
  function shackle($expression){
  	return "CONCAT('%\'',$expression,'\'%')";
  }



}

?>
