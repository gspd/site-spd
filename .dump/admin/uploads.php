<?php

//atualizado em 28/09 21:11

include_once("login.php");
include_once("../common/classes_info.php");



function list_uploads()
{
	$projeto = new Info_projeto();
	
	$num_projetos = $projeto->num_projetos();
	
	for($i=0; $i < $num_projetos; $i++)
		$id_projetos[$i]['id_projeto'] = $i+1;
	
	$uploads = new Info_entregas();
	
	for($i=0; $i < $num_projetos; $i++)
	{
		$projeto->Processa($id_projetos[$i]['id_projeto']);
		$dados[$i] = $projeto->dados_projeto;
		$uploads->Processa($id_projetos[$i]['id_projeto']);
		$dados[$i]['uploads'] = $uploads->dados_entregas;
	}//for
	?>
	<p class="title" align="center"><big>Editar submiss&otilde;es:</big></p>
	<?php echo "\n";
	for($i=0; $i < $num_projetos; $i++)
	{
		?>
		<p class="title" align="center"><?php echo $dados[$i]['titulo']; ?></p>
		<p align="center"><b>Professores:</b></p>
		<table border="1" align="center">
			<tr>
				<th>C&oacute;digo</th>
				<th>Nome</th>
				<th>E-mail</th>
				<th>Fun&ccedil;&atilde;o</th>
			</tr>
			<?php echo "\n";
			for($j=0; $j < count($dados[$i]['professores']); $j++)
			{
				$codigo = $dados[$i]['professores'][$j]['codigo'];
				$name = $dados[$i]['professores'][$j]['nome_professor'];
				$email = $dados[$i]['professores'][$j]['email_professor'];
				$funcao = $dados[$i]['professores'][$j]['funcao'];
				?>
				<tr>
					<td><?php echo $codigo; ?></td>
					<td><?php echo $name; ?></td>
					<td><?php echo $email; ?></td>
					<td><?php echo $funcao; ?></td>
				</tr>
				<?php echo "\n";
			}//for
			?>
		</table>
		<p align="center"><b>Aluno(s):</b></p>
		<table border="1" align="center">
			<tr>
				<th>RA</th>
				<th>Nome</th>
				<th>E-mail</th>
			</tr>
			<?php echo "\n";
			for($j=0; $j < count($dados[$i]['alunos']); $j++)
			{
				$codigo = $dados[$i]['alunos'][$j]['codigo'];
				$name = $dados[$i]['alunos'][$j]['nome_aluno'];
				$email = $dados[$i]['alunos'][$j]['email_aluno'];
				?>
				<tr>
					<td><?php echo $codigo; ?></td>
					<td><?php echo $name; ?></td>
					<td><?php echo $email; ?></td>
				</tr>
				<?php echo "\n";
			}//for
			?>
		</table>
		<p class="subtitle" align="center">Submiss&otilde;es:</p>
		<table border="1" align="center">
			<tr>
				<th>RA/C&oacute;digo</th>
				<th>Cap&iacute;tulo</th>
				<th>Data</th>
				<th>A&ccedil;&atilde;o</th>
			</tr>
			<?php echo "\n";
			$mensagem = 1;
			
			if($dados[$i]['uploads'])
			{
				for($j=0; $j < count($dados[$i]['uploads']); $j++)
				{
					if($mensagem)
						$mensagem = !$dados[$i]['uploads'][$j]['entregue'];
					
					if($dados[$i]['uploads'][$j]['entregue'])
					{
						$codigo = $dados[$i]['uploads'][$j]['codigo'];
						$remote = $dados[$i]['uploads'][$j]['nome_arquivo']['remoto'];
						$filename = basename(urldecode($remote));
						if($dados[$i]['uploads'][$j]['tipo'] == "aluno")
							$date = date("d/m/Y H:i:s", $dados[$i]['uploads'][$j]['data_submissao']);
						else
							$date = date("d/m/Y H:i:s", $dados[$i]['uploads'][$j]['data_avaliacao']);
						?>
						<tr>
							<td><?php echo $codigo; ?></td>
							<td><a href="<?php echo $remote; ?>" target="_blank"><?php echo $filename; ?></a></td>
							<td><?php echo $date; ?></td>
							<td>
								<a href="javascript:submit_form('excl<?php echo "$i$j"; ?>')"
								   onMouseOver="javascript:window.status='excluir arquivo'; return true"
								   onMouseOut="javascript:window.status=''; return true">excluir</a>
								<form name="excl<?php echo "$i$j"; ?>" method="post" action="pop_uploads.php" target="pop_uploads">
									<input type="hidden" name="action" value="excluir">
									<input type="hidden" name="id" value="<?php echo $id_projetos[$i]['id_projeto']; ?>">
									<input type="hidden" name="name" value="<?php echo $filename; ?>">
								</form>
							</td>
						</tr>
						<?php echo "\n";
					}//if
				}//for
			}//if
			
			if($mensagem)
			{
				?>
				<tr>
					<td colspan="4">Nenhum arquivo foi enviado at&eacute; o momento.</td>
				</tr>
				<?php echo "\n";
			}//else
			?>
		</table>
		<?php
		if($i+1 < $num_projetos)
		{
			?>
			<p>&nbsp;</p>
			<p align="center"><img src="images/hr2.gif" width="60%"></p>
			<p>&nbsp;</p>
			<?php echo "\n";
		}//if
	}//for
	?>
	<form name="reload" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	</form>
	
	<script language="javascript">
	<!--
	function submit_form(name)
	{
		document.forms[name].submit();
	}
	-->
	</script>
	<?php echo "\n";
}//function list_uploads();



function main()
{
	html_header("Projetos e uploads");
	common_header('uploads');
	list_uploads();
	common_footer();
	html_footer();
}//function main();



main();
?>