<?php
include_once("login.php");

/*

function consistencia()
{
	?>
	<ul>
	<?php echo "\n";
	
	function erro($num, $field_1 = '', $field_2 = '')
	{
		$erro[0] = "Aluno de RA '$field_1' aponta para projeto com identifica&ccedil;&atilde;o inexistente: '$field_2'.<br>".
				   "Solu&ccedil;&atilde;o: alterar o cadastro do aluno.";
		
		$erro[1] = "Professor de c&oacute;digo '".sprintf("%02d", $field_1)."' aponta para projeto com identifica&ccedil;&atilde;o inexistente: '$field_2'.<br>".
				   "Solu&ccedil;&atilde;o: alterar o cadastro do professor.";
		?>
		<li><p><?php echo $erro[$num]; ?></p></li>
		<?php echo "\n";
	}//function erro();
	
	$projeto_inexiste = db_query("aluno.codigo, aluno.projeto",
								 "aluno LEFT JOIN projeto ON aluno.projeto = projeto.id",
								 "projeto.id IS NULL");
	if(!empty($projeto_inexiste))
	{
		for($i=0; $i < count($projeto_inexiste); $i++)
			erro(0, $projeto_inexiste[$i]['codigo'], $projeto_inexiste[$i]['projeto']);
	}//if
	
	$projeto_inexiste = db_query("prof_proj.codigo, prof_proj.projeto",
								 "prof_proj LEFT JOIN projeto ON prof_proj.projeto = projeto.id",
								 "projeto.id IS NULL");
	if(!empty($projeto_inexiste))
	{
		for($i=0; $i < count($projeto_inexiste); $i++)
			erro(1, $projeto_inexiste[$i]['codigo'], $projeto_inexiste[$i]['projeto']);
	}//if
	
	?>
	</ul>
	<?php echo "\n";
}//function consistencia;



function main()
{
	html_header("P&aacute;gina principal");
?>
<p>
	<a href="javascript:document.forms['editar_alunos'].submit()"
	   onMouseOver="javascript:window.status='Editar cadastros de alunos'; return true"
	   onMouseOut="javascript:window.status=''; return true">Editar cadastros de alunos</a>
	<form name="editar_alunos" action="editar_cadastro.php" method="post">
		<input type="hidden" name="tipo" value="aluno">
	</form>
</p>
<p>
	<a href="javascript:document.forms['editar_professores'].submit()"
	   onMouseOver="javascript:window.status='Editar cadastros de professores'; return true"
	   onMouseOut="javascript:window.status=''; return true">Editar cadastros de professores</a>
	<form name="editar_professores" action="editar_cadastro.php" method="post">
		<input type="hidden" name="tipo" value="professor">
	</form>
</p>
<p>
<a href="editar_capitulo.php"
   onMouseOver="javascript:window.status='Editar cap&iacute;tulos'; return true"
   onMouseOut="javascript:window.status=''; return true">Editar cap&iacute;tulos</a>
</p>
<p>
<a href="editar_projeto.php">Editar projetos e uploads</a>
</p>
<p>
<a href="">Alterar usu&aacute;rios e senhas</a>
</p>
<?php
	consistencia();
	html_footer();
}//function main();
*/
include_once("editar_projeto.php");
//header("Location: https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/editar_projeto.php");

//main();
?>