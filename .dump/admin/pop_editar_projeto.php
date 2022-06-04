<?php
include_once("login.php");



function valida_dados($action)
{
	function erro($num)
	{
		$erro[0] = "T&iacute;tulo de projeto inv&aacute;lido";
		
		?>
		<p class="errmsg" align="center"><?php echo $erro[$num]; ?></p>
		<?php echo "\n";
	}//function erro()
	
	
	foreach($_POST as $key => $value)
	{
		if($key != 'action')
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}//foreach
	
	
	$valid = 1;
	
	if(empty($newtitle))
	{
		erro(0);
		$valid = 0;
	}//if
	else
	{
		$newtitle = trim($newtitle);
		if(empty($newtitle))
		{
			erro(0);
			$valid = 0;
		}//if
	}//else

	return $valid;
}//function valida_dados();



function tabela_dados($id, $title, $caption = '')
{
	if($caption)
	{
		?>
		<p align="center"><b><?php echo $caption; ?>:</b></p>
		<?php echo "\n";
	}
	?>
	<p align="center">
		<table align="center" border="1">
			<tr>
				<th align="right">T&iacute;tulo:</th>
				<td align="left"><?php echo $title; ?></td>
			</tr>
		</table>
	</p>
	<?php
}// function tabela_dados()



function executar($action, $id)
{
	/*function update($source, $destination)
	{
		$query = new Query();
		
		$query_string[0] = "UPDATE projeto
							SET projeto.id_projeto = $destination
							WHERE projeto.id_projeto = $source";
		
		$query_string[1] = "UPDATE submissoes
							SET submissoes.id_projeto = $destination
							WHERE submissoes.id_projeto = $source";
		
		$query_string[2] = "UPDATE prof_proj
							SET prof_proj.id_projeto = $destination
							WHERE prof_proj.id_projeto = $source";
		
		$query_string[3] = "UPDATE aluno
							SET aluno.id_projeto = $destination
							WHERE aluno.id_projeto = $source";
		
		$query_string[4] = "UPDATE defesas
							SET defesas.id_projeto = $destination
							WHERE defesas.id_projeto = $source";
		
		$query_string[5] = "UPDATE notas
							SET notas.id_projeto = $destination
							WHERE notas.id_projeto = $source";
		
		$query_string[6] = "UPDATE penalidades_locais
							SET penalidades_locais.id_projeto = $destination
							WHERE penalidades_locais.id_projeto = $source";
		
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
		$dir = realpath("../arquivos")."/";

		if(file_exists($dir.$destination))
		{
			if(!rename($dir.$source, $dir.$destination))
				die("Erro ao renomear o diret&oacute;rio!");
		}//if
	}//function update();*/

	
	foreach($_POST as $key => $value)
	{
		if($key != 'action')
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}//foreach
	
	
	$projeto = new Info_projeto();
	
	if($action != "excluir" && $action != "deletar")
	{
		if($action == 'incluir')
			$projeto->inserir_projeto($id, $newtitle);
		else
			$projeto->alterar_projeto($id,$id,$newtitle);

		$past = ($action == 'incluir') ? 'inclu&iacute;dos' : 'alterados';

		?>
		<p class="title" align="center">Dados <?php echo $past; ?> com sucesso!</p>
		<?php echo "\n";

		tabela_dados($id, $newtitle, "Novos dados");

		?>
		<p align="center"><a href="javascript:self.close()">Fechar janela</a></p>
		<?php echo "\n";
	}//if
	else
	{
		if($action == 'excluir')
		{
			$projeto->excluir_projeto($id);
			
			?>
			<p class="errmsg" align="center">Dados exclu&iacute;dos.</p>
			<p align="center"><a href="javascript:self.close()">Fechar janela.</a></p>
			<?php echo "\n";
		}//if
		else
		{
			$name = $_POST['name'];
			$local = realpath("../arquivos/$id")."/";
			if(!unlink($local.$name))
				die("Erro ao excluir o arquivo!");
			else
			{
				?>
				<p class="errmsg" align="center">Arquivo exclu&iacute;do.</p>
				<?php echo "\n";
			}//else
			
			?>
			<p align="center"><a href="javascript:self.close()">Fechar janela.</a></p>
			<?php echo "\n";
		}//else
	}//else
}//function executar();




function recarrega_projetos()
{
	?>
	<script language="javascript">
	<!--
	opener.document.forms['orderby_titulo'].order.value = "ASC";
	opener.document.forms['orderby_titulo'].submit();
	-->
	</script>
	<?php
} //function recarrega_cadastros();




function confirmar($action, $id)
{
	foreach($_POST as $key => $value)
	{
		if($key != "action")
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}//foreach
	
	if($action != 'excluir' && $action != 'deletar')
	{
		if(valida_dados($action))
		{
			if($action == 'incluir')
			{
				?>
				<p class="title" align="center">Confirmar os dados digitados</p>
				<?php echo "\n";
			}
			else
			{
				?>
				<p class="title" align="center">Confirmar as altera&ccedil;&otilde;es</p>
				<?php echo "\n";
			}
			
			tabela_dados($id, $newtitle, "Novos dados");
			
			if($action == 'alterar')
				tabela_dados($id, $title, "Dados antigos");
			
			?>
			<p align="center">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<input type="hidden" name="newtitle" value="<?php echo $newtitle; ?>">
					<input type="hidden" name="action" value="<?php echo $action; ?>_executar">
					<input type="submit" value="Confirmar dados">
					<input type="button" value="Cancelar" onClick="javascript:self.close()">
				</form>
			</p>
			<?php echo "\n";
		}//if
		else
			formulario("corrigir", $id, $action);
	}//if
	else
	{
		if($action == 'excluir')
		{
			$info_projeto = new Info_projeto($id);
			$title = $info_projeto->dados_projeto['titulo'];
			?>
			<p class="title" align="center">
			Tem certeza de que deseja excluir os dados abaixo?<br>
			Todos os alunos e uploads referentes a este projeto ser&atilde;o exclu&iacute;dos.
			</p>
			<?php echo "\n";
			tabela_dados($id, $title, "Dados a serem exclu&iacute;dos");
			?>
			<p align="center">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<input type="hidden" name="action" value="<?php echo $action; ?>_executar">
					<input type="submit" value="Excluir dados">
					<input type="button" value="Cancelar" onClick="javascript:self.close()">
				</form>
			</p>
			<?php echo "\n";
		}//if
		else
		{
			$name = $_POST['name'];
			?>
			<p class="errmsg" align="center">Tem certeza de que deseja excluir o arquivo "<?php echo $name; ?>"?</p>
			<p align="center">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<input type="hidden" name="name" value="<?php echo $name; ?>">
					<input type="hidden" name="action" value="<?php echo $action; ?>_executar">
					<input type="submit" value="Excluir dados">
					<input type="button" value="Cancelar" onClick="javascript:self.close()">
				</form>
			</p>
			<?php echo "\n";
		}//else
	}//else
}//function confirmar();



function formulario($action, $id, $formeraction = '')
{
	if($action == 'alterar')
	{
		$info_projeto = new Info_projeto($id);
		$title = $info_projeto->dados_projeto['titulo'];
	}//if
	else if ($action == 'corrigir')
	{
		if($formeraction == "alterar")
			$title = $_POST['title'];
		else
			$title = "";
		
		$action = $formeraction;
	}//elseif
	else		
		$title = "";
	
	?>
	<p align="center" class="title"><?php echo ucfirst($action)." t&iacute;tulo de projeto: $title"; ?></p>
	<p align="center">
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<table align="center" border="0">
				<tr>
					<th>T&iacute;tulo</th>
					<td><input name="newtitle" value="<?php echo $title; ?>" size="60"></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" value="<?php echo ucfirst($action); ?>">
						<input type="button" value="Cancelar" onClick="javascript: self.close();">
					</td>
				</tr>
			</table>
		</p>
		<?php echo "\n";
		if($action == 'alterar' || $action == 'corrigir')
		{
			?>
			<input type="hidden" name="title" value="<?php echo $title; ?>">
			<?php echo "\n";
		}//if
		
		?>
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="action" value="<?php echo $action; ?>_confirmar">
	</form>
	<?php echo "\n";
}//function formulario();


function main()
{
	if(isset($_POST['action']))
		$action = $_POST['action'];
	else
	{
		html_header();
		?>
		<script language="javascript">
		<!--
		self.close();
		-->
		</script>
		<?php
		html_footer();
		exit();
	}//else
	
	$id = "";
	if(isset($_POST['id']))
		$id = $_POST['id'];

	if((strpos($action, "incluir") !== false) || (strpos($action, "alterar") !== false))
	{
		$step = substr($action, strpos($action, "_")+1);
		if(strpos($action, "_"))
			$action = substr($action, 0, strpos($action, "_"));
		
		switch($step)
		{
			// funções para alterar um cadastro
			case "confirmar":
				html_header("Confirmar dados cadastrais");
				confirmar($action, $id);
				html_footer();
				break;
			case "executar":
				html_header("Dados cadastrais alterados");
				executar($action, $id);
				recarrega_projetos();
				html_footer();
				break;
			default:
				html_header("Alterar dados cadastrais");
				formulario($action, $id);
				html_footer();
				break;
		}//switch
	}//if
	else if((strpos($action, "excluir") !== false) || (strpos($action, "deletar") !== false))
	{
		$step = substr($action, strpos($action, "_")+1);
		if(strpos($action, "_"))
			$action = substr($action, 0, strpos($action, "_"));
		
		switch($step)
		{
			// funções para alterar um cadastro
			case "executar":
				html_header("Dados exclu&iacute;dos");
				executar($action, $id);
				recarrega_projetos();
				html_footer();
				break;
			default:
				html_header("Confirmar exclus&atilde;o de dados");
				confirmar($action, $id);
				html_footer();
				break;
		}//switch
	}//else if
}//function main();



main();
?>
