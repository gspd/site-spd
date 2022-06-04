<?php
include_once("login.php");







function atualiza_dados()
{
	if(isset($_POST['form_name']) && $_POST['form_name'] == "editar_defesa")
	{
		$old_date = $_POST['old_date'];
		$new_date = $_POST['new_date'];
		$old_local = $_POST['old_local'];
		$new_local = $_POST['new_local'];
		$id = $_POST['id'];
		
		$arquivo = new Handle_arquivo();
		$filename = true_self($_SERVER['PHP_SELF'])."/arquivos/$id/relatorio_ata";
		
		$defesa = new Info_defesa($id);
		$existe = (!empty($defesa->dados_defesa));
		
		if(empty($new_date) && empty($new_local))
		{
			$defesa->excluir_defesa($id);
		}
		else if(!ereg("([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $new_date, $new_date))
			echo "<p align=\"center\" class=\"errmsg\">Data inv&aacute;lida!</p>";
		else if(!checkdate($new_date[2], $new_date[1], $new_date[3]) || ($new_date[4] > 23 || $new_date[5] > 59 || $new_date[6] > 59))
			echo "<p align=\"center\" class=\"errmsg\">Data inv&aacute;lida!</p>";
		else
		{
			$new_date = $new_date[3]."-".$new_date[2]."-".$new_date[1]." ".$new_date[4].":".$new_date[5].":".$new_date[6];
			
			if(!$existe)
				$defesa->inserir_defesa($id, $new_local, $new_date);
			else
				$defesa->alterar_defesa($id, $new_local, $new_date);
		}//else
	}//if
}









function build_defesas()
{
	$projeto = new Info_projeto();
	$defesa = new Info_defesa();
	$nota_final = new Info_nota_final();
	$capitulos = new Info_capitulos();
	$nota = new Info_nota();
	
	$num_projetos = $projeto->num_projetos();
	
	for($i=0; $i < $num_projetos; $i++)
	{
		$id_projeto = $i+1;
		$projeto->Processa($id_projeto);
		$defesa->Processa($id_projeto);
		$capitulos->Processa($id_projeto);
		$nota_final->Processa($id_projeto);
		$nota->Processa($id_projeto);
		
		$dados[$i]['projeto'] = $projeto->dados_projeto;
		$dados[$i]['defesa'] = $defesa->dados_defesa;
		$dados[$i]['penalidades'] = $capitulos->dados_capitulos['penalidades'];
		$dados[$i]['nota'] = $nota->dados_nota;
		$dados[$i]['nota_final'] = $nota_final->dados_nota_final['alunos'][0]['nota'];
	}//for
	return (!empty($dados)) ? $dados : NULL;
}





function list_defesas($dados)
{
	?>
	<p align="center" class="title"><big>Datas e notas de defesas</big></p>
	<?php echo "\n";
	for($i=0; $i < count($dados); $i++)
	{
		$title = $dados[$i]['projeto']['titulo'];
		$nota = $dados[$i]['nota']['nota'];
		$comentario = $dados[$i]['nota']['comentario'];
		
		?>
		<p class="title" align="center"><?php echo $title; ?></p>
		<p align="center"><b>Professores:</b></p>
		<table border="1" align="center">
			<tr>
				<th>C&oacute;digo</th>
				<th>Nome</th>
				<th>E-mail</th>
				<th>Fun&ccedil;&atilde;o</th>
			</tr>
			<?php echo "\n";
			for($j=0; $j < count($dados[$i]['projeto']['professores']); $j++)
			{
				$codigo = $dados[$i]['projeto']['professores'][$j]['codigo'];
				$name = $dados[$i]['projeto']['professores'][$j]['nome_professor'];
				$email = $dados[$i]['projeto']['professores'][$j]['email_professor'];
				$funcao = $dados[$i]['projeto']['professores'][$j]['funcao'];
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
			for($j=0; $j < count($dados[$i]['projeto']['alunos']); $j++)
			{
				$codigo = $dados[$i]['projeto']['alunos'][$j]['codigo'];
				$name = $dados[$i]['projeto']['alunos'][$j]['nome_aluno'];
				$email = $dados[$i]['projeto']['alunos'][$j]['email_aluno'];
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
		<p class="subtitle" align="center">Defesa</p>
		<?php echo "\n";
		if(!$dados[$i]['defesa'])
		{
			$dados[$i]['defesa']['data_defesa'] = "00/00/0000 00:00:00";
			$dados[$i]['defesa']['local'] = "";
		}
		else
			$dados[$i]['defesa']['data_defesa'] = date("d/m/Y H:i:s", $dados[$i]['defesa']['data_defesa']);
		
		//quantidade de pontos perdidos. Colocado aqui pelo fato
		//de as versões do PHP 4.1.2 ou menores modificam os dados
		//contidos no array, transformando valores não numéricos
		//em numéricos (resultando na maior parte em 0)
		$perdidos = array_sum($dados[$i]['penalidades']);
		?>
		<p>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<table border="1" align="center">
				<tr>
					<th>
						Data<br>
						(dd/mm/aaaa hh:mm:ss)
					</th>
					<th>Local</th>
					<th>Ata</th>
					<th>Confirmar</th>
				</tr>
				<tr>
					<td align="center">
						<input type="text" name="new_date" value="<?php echo $dados[$i]['defesa']['data_defesa']; ?>" maxlength="19" size="22">
						<input type="hidden" name="old_date" value="<?php echo $dados[$i]['defesa']['data_defesa']; ?>">
					</td>
					<td align="center">
						<input type="text" name="new_local" value="<?php echo $dados[$i]['defesa']['local']; ?>" maxlength="20">
						<input type="hidden" name="old_local" value="<?php echo $dados[$i]['defesa']['local']; ?>">
					</td>
					<td>
						<a href="<?php echo true_self($_SERVER['PHP_SELF'])."/arquivos/".$dados[$i]['projeto']['id_projeto']."/relatorio_ata.pdf"; ?>"
						   onMouseOver="javascript:window.status='Imprimir relat&oacute;rio de ata'; return true"
						   onMouseOut="javascript:window.status=''; return true"
						   target="_blank">Visualizar</a>
					</td>
					<td><input type="submit" value="Gerar/Atualizar"></td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $dados[$i]['projeto']['id_projeto']; ?>">
			<input type="hidden" name="form_name" value="editar_defesa">
			</form>
		</p>
		<p class="subtitle" align="center">Notas</p>
		<p>
			<table border="1" align="center">
			<tr>
				<th align="right">Pontos perdidos</th>
				<td align="left">-<?php echo ($perdidos) ? $perdidos : ""; ?></td>
			</tr>
			<tr>
				<th align="right">Nota</th>
				<td align="left"><?php echo ($nota == "") ? "Em aberto" : $nota; ?></td>
			</tr>
			<tr>
				<th align="right">Nota Final</th>
				<td align="left">
					<?php echo "\n";
					//$notafinal = ($nota != "" && !$mensagem) ? (($nota - $perdidos < 0) ? 0 : $nota - $perdidos) : "N/D";
					$notafinal = $dados[$i]['nota_final'];
					echo $notafinal; ?>
				</td>
			</tr>
			<?php echo "\n";
			if($comentario)
			{
				?>
				<tr>
					<th align="right">Coment&aacute;rio:</th>
					<td align="justify"><?php echo ereg_replace("\n", "<br>\n", $comentario); ?></td>
				</tr>
				<?php echo "\n";
			}//if
			?>
		</table>
		</p>
		<p align="center">
			<a href="javascript:submit_form('alter<?php echo $i; ?>')"
			   onMouseOver="javascript:window.status='excluir arquivo'; return true"
			   onMouseOut="javascript:window.status=''; return true">Alterar/Adicionar informa&ccedil;&otilde;es</a>
			<form name="alter<?php echo $i; ?>" method="post" action="pop_penalidades.php" target="pop_penalidades">
				<input type="hidden" name="action" value="<?php echo ($nota == "") ? "incluir" : "alterar"; ?>">
				<input type="hidden" name="id" value="<?php echo $dados[$i]['projeto']['id_projeto']; ?>">
			</form>
		</p>

		<?php echo "\n";
		if($i+1 < count($dados))
		{
			?>
			<p>&nbsp;</p>
			<p align="center"><img src="images/hr2.gif" width="70%"></p>
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
}






function main()
{
	if(!empty($_POST))
		atualiza_dados();
	
	html_header('Defesas');
	common_header('defesas');
	
	$dados = build_defesas();
	list_defesas($dados);
	
	common_footer();
	html_footer();
}

main();
?>