<?php
if($id){

	$rtsimdb = mysql_connect("localhost","rtsim","betterthanraw") or
		die(mysql_error());

	mysql_select_db("rtsim",$rtsimdb) or die(mysql_error());

	$query="delete from bugs where id=\"$id\"";

        $deucerto=mysql_query($query) or die(mysql_error());
	if ($deucerto)
		include "showBugs.php"; 
}

?>
