<?php
if($submit){
//conectar no banco
	$rtsimdb = mysql_connect("localhost","rtsim","betterthanraw") or
		die(mysql_error());

	mysql_select_db("rtsim",$rtsimdb) or die(mysql_error());

	$query="insert into bugs values(0,\"$nome\",\"$email\",\"$bug\")";

        $deucerto=mysql_query($query) or die(mysql_error());

        if ($deucerto) include "result.inc";

}
else include "rtbugs.html";
?>
