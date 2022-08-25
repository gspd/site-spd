<?php

	//conectar no banco
	$rtsimdb = mysql_connect("localhost","rtsim","betterthanraw") or
		die(mysql_error());

	mysql_select_db("rtsim",$rtsimdb) or die(mysql_error());
	
	//fazer pesquisa
	$query="select * from bugs";
	$answer=mysql_query($query) or die(mysql_error());
	$numRows = mysql_num_rows($answer);

	if($numRows!=0){
		$nf = mysql_num_fields($answer) or die(mysql_error());
		$campos=array(1=>"Nome",2=>"Email",3=>"Bug");
		include "bugsreport1.inc";

                //escrever

                while ($valores = mysql_fetch_array($answer)) {
                   echo "<table border=0 align=center><center>";
                   echo "<table border=1 width=700>";
		   for ($i=1; $i<$nf; $i++){
			echo "<tr><td align=left bgcolor=#ddeebb width=100><b>$campos[$i]</b></td>";
                        if($i==2)
                           echo "<td bgcolor=#ccccff>
                                        <a href=mailto:$valores[$i]>$valores[$i]</a>
                                 </td></tr>";
                        else
			   echo "<td bgcolor=#ccccff>$valores[$i]</td></tr>";
		   }
                   echo "</table>";
                   echo "</td><td>
                             <a href=JavaScript:deletar($valores[0])>Excluir</a>
                         </td></tr></center></table><br><br>";
		}//end while
                echo "<hr> </body></html>";
	}
	else include "emptybugs.inc";
?>
