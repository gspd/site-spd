<?php
include_once("login.php");




function recarrega_capitulos()
{
	?>
	<script language="javascript">
	<!--
	opener.document.forms['reload'].submit();
	-->
	</script>
	<?php
} //function recarrega_cadastros();






function executar($action, $id)
{
	foreach($_POST as $key => $value)
	{
		if($key != 'action')
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}//foreach
	$avisos = new Info_avisos();
	
	if($action != "excluir")
	{
		if($action == 'incluir')
			$avisos->inserir_aviso($newid, $newconteudo, $_SESSION['username']);
		else
		{
			$avisos->alterar_aviso($id, $id, $newconteudo, $_SESSION['username']);
			$avisos->mover_aviso($id, $newid);
		}//else
		
		$past = ($action == 'incluir') ? 'inclu&iacute;dos': 'alterados';
		
		?>
		<p class="title" align="center">Dados <?php echo $past; ?> com sucesso!</p>
		<?php echo "\n";
		
		tabela_dados($newid, $newconteudo, "Novo aviso");
		
		?>
		<p align="center"><a href="javascript:self.close()">Fechar janela</a></p>
		<?php echo "\n";
	}//if
	else
	{
		$avisos->excluir_aviso($id);
		?>
		<p class="errmsg" align="center">Aviso exclu&iacute;do.</p>
		<p align="center"><a href="javascript:self.close()">Fechar janela.</a></p>
		<?php echo "\n";
	}//else
}// function executar()



























function tabela_dados($id, $conteudo, $caption = '')
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
				<th align="right">Posi&ccedil;&atilde;o:</th>
				<td align="left"><?php echo $id; ?></td>
			</tr>
			<tr>
				<th align="right">Conte&uacute;do:</th>
				<td align="left"><?php echo ereg_replace("\n", "<br>\n", $conteudo); ?></td>
			</tr>
		</table>
	</p>
	<?php
}// function tabela_dados()
























function valida_dados($action)
{
	function erro($num)
	{
		$erro[0] = "Erro: Aviso em branco!";
		
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
	
	if(empty($newconteudo))
	{
		erro(0);
		$valid = 0;
	}//if
	
	return $valid;
}// function valida_dados();



function confirmar($action, $id)
{
	foreach($_POST as $key => $value)
	{
		if($key != "action")
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}
	
	if($action != "excluir")
	{
		if(valida_dados($action))
		{
			if($action == 'incluir')
			{
				?>
				<p class="title" align="center">Confirmar o aviso digitado</p>
				<?php echo "\n";
			}
			else
			{
				?>
				<p class="title" align="center">Confirmar as altera&ccedil;&otilde;es</p>
				<?php echo "\n";
			}
			
			tabela_dados($newid, $newconteudo, "Novo aviso");
			
			if($action == 'alterar')
				tabela_dados($id, $conteudo, "Aviso antigo");
			
			?>
			<p align="center">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<input type="hidden" name="newid" value="<?php echo $newid; ?>">
					<input type="hidden" name="newconteudo" value="<?php echo $newconteudo; ?>">
					<input type="hidden" name="action" value="<?php echo $action; ?>_executar">
					<input type="submit" value="Confirmar dados">
					<input type="button" value="Cancelar" onClick="javascript:self.close()">
				</form>
			</p>
			<?php echo "\n";
		}//if
		else
		{
			formulario("corrigir", $id, $action);
			?>
			<script language="javascript">
			<!--
			seleciona_id();
			-->
			</script>
			<?php echo "\n";
		} //else
	}//if
	else
	{
		?>
		<p class="title" align="center">Tem certeza de que deseja excluir o aviso abaixo?</p>
		<?php echo "\n";
		
		$avisos = new Info_avisos();
		$avisos->Dados_aviso($id);
		
		tabela_dados($id, $avisos->dados_avisos['conteudo'], "Aviso a ser exclu&iacute;do");
		
		?>
			<p align="center">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<input type="hidden" name="action" value="<?php echo $action; ?>_executar">
					<input type="submit" value="Excluir dados">
					<input type="button" value="Cancelar" onClick="javascript:self.close()">
				</form>
			</p>
		<?php
	}//else
}// function confirma_cadastro();



















function formulario($action, $id, $formeraction = '')
{
	$avisos = new Info_avisos();
	
	if($action == "alterar")
	{
		$avisos->Dados_aviso($id);
		$conteudo = $avisos->dados_avisos['conteudo'];
	}//if
	else
	{
		$conteudo = "";
		
		if(isset($_POST['conteudo']))
			$conteudo = $_POST['conteudo'];
		
		if(isset($_POST['newid']))
			$id = $_POST['newid'];
	}//else
	?>
	<p align="center" class="title"><?php echo ucfirst($action)." aviso:"; ?></p>
	<p align="center">
		<form name="editar_aviso" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<table align="center" border="0">
				<tr>
					<th align="right">Posi&ccedil;&atilde;o:</th>
					<td align="left">
						<select name="newid">
						</select>
					</td>
				</tr>
				<tr>
					<th align="right">Conte&uacute;do:</th>
					<td align="left"><textarea name="newconteudo" cols="40" rows="5" wrap="soft"><?php echo $conteudo; ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" value="<?php echo ucfirst($action); ?>">
						<input type="button" value="Cancelar" onClick="javascript: self.close();">
					</td>
				</tr>
			</table>
		<?php echo "\n";
		if($action == 'alterar' || $action == 'corrigir')
		{
			?>
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<input type="hidden" name="conteudo" value="<?php echo $conteudo; ?>">
			<?php echo "\n";
		}
		
		if($action == 'corrigir')
			$action = $formeraction;
		?>
		<input type="hidden" name="action" value="<?php echo $action; ?>_confirmar">
		</form>
	</p>
	<script language="javascript">
	<!--
		function add_option(selectname, value, text)
		{
			option = document.createElement("option");
			
			try
			{
				selectname.add(option, null); //compatibilidade com outros browsers
			}
			catch(ex)
			{
				selectname.add(option); //compatibilidade com o IE
			}
			
			optnum = selectname.length - 1;
			selectname.options[optnum].text = text;
			selectname.options[optnum].value = value;
		}// function add_option()
		
		
		function clear_select(selectname)
		{
			for(i=selectname.length; selectname.length; i--)
				selectname.remove(i);
		}// function clear_select();
		
		
		function seleciona_id()
		{
			form = document.forms['editar_aviso'];
			
			aviso = new Array();
			<?php echo "\n";
			
			$m = $avisos->num_avisos();
			
			$include = ($action == 'incluir')+0;
			
			for($i=0; $i < $m + $include; $i++)
			{
				?>
				aviso[<?php echo $i; ?>] = <?php echo $i+1; ?>;
				<?php echo "\n";
			}//for
			
			?>
			
			clear_select(form.newid);
			
			for(i=0; i < aviso.length; i++)
			{
				value = aviso[i];
				add_option(form.newid, value, value);
				<?php echo "\n";
				if(!empty($id))
				{
					?>
					if(aviso[i] == <?php echo $id; ?>)
						form.newid.selectedIndex = i;
					<?php echo "\n";
				}//if
				else
				{
					?>
					form.newid.selectedIndex = form.newid.length - 1;
					<?php echo "\n";
				}//else
				?>
			}//for
		}// function seleciona_id();
	-->
	</script>
<?php
}//function formulario



function main()
{
	$initialize = "javascript:seleciona_id();";	
	
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
				html_header(ucfirst($action).": confirmar");
				confirmar($action, $id);
				html_footer();
				break;
			case "executar":
				html_header(ucfirst($action).": execu&ccedil;&atilde;o completada");
				executar($action, $id);
				recarrega_capitulos();
				html_footer();
				break;
			default:
				html_header(ucfirst($action), $initialize);
				formulario($action, $id);
				html_footer();
				break;
		}//switch
	}//if
	else if(strpos($action, "excluir") !== false)
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
				recarrega_capitulos();
				html_footer();
				break;
			default:
				html_header("Confirmar exclus&atilde;o de dados");
				confirmar($action, $id);
				html_footer();
				break;
		}//switch
	}//else
}//function main();

main();
?>