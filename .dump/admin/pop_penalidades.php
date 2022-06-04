<?php
include_once("login.php");



function reload()
{
	?>
	<script language="javascript">
	<!--
	opener.document.forms['reload'].submit();
	-->
	</script>
	<?php echo "\n";
}//function reload()




function valida_dados()
{
	$nota = $_POST['newnota'];
	$valid = 1;
	
	if($nota == "")
		$valid = 0;
	else
	{
		if(!ereg("^[0-9]{1,2}\.[0-9]{1,2}$", $nota))
			$valid = 0;
		else
		{
			if($nota < 0 || $nota > 10)
				$valid = 0;
		}//else
	}//else
	
	if(!$valid)
	{
		?>
		<p align="center" class="errmsg">Nota inv&aacute;lida!</p>
		<?php echo "\n";
	}//else
	
	return $valid;
}//function valida_dados()








function executar()
{
	foreach($_POST as $key => $value)
		$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	
	$nota = new Info_nota();
	
	if($action == "incluir")
		$nota->inserir_nota($id, $newnota, $newcomentario);
	else if ($action == "alterar")
		$nota->alterar_nota($id, $newnota, $newcomentario);
	
	//atualizar a nota final dos alunos
	$capitulos = new Info_capitulos($id);
	$info_nota_final = new Info_nota_final($id);
	
	$penalidades = array_sum($capitulos->dados_capitulos['penalidades']);
	
	$new_nota_final = $newnota - $penalidades;
	
	if($new_nota_final < 0)
		$new_nota_final = 0;
	
	$num_alunos = count($info_nota_final->dados_nota_final['alunos']);
	$alunos = $info_nota_final->dados_nota_final['alunos'];
	
	for($i=0; $i < $num_alunos; $i++)
	{
		
		$existe = ($alunos[$i]['nota'] !== 0);
		
		if($existe)
			$info_nota_final->alterar_nota_final($alunos[$i]['codigo'], $new_nota_final, "", "", "", 1);
		else
			$info_nota_final->inserir_nota_final($alunos[$i]['codigo'], $new_nota_final);
	}//for
	
	$past = ($action == "incluir") ? "inclu&iacute;dos" : "alterados";
	
	?>
	<p align="center" class="title">Dados <?php echo $past; ?> com sucesso!</p>
	<p align="center" class="subtitle">A nota final tamb&eacute;m foi alterada!</p>
	<p align="center"><b>Novos dados:</b><br>
		<table align="center" border="1">
			<tr>
				<th align="right">Nota:</th>
				<td align="left"><?php echo $newnota; ?></td>
			</tr>
			<tr>
				<th align="right">Nota final:</th>
				<td align="left"><?php echo $new_nota_final; ?></td>
			</tr>
			<tr>
				<th align="right">Coment&aacute;rio:</th>
				<td align="left"><?php echo ereg_replace("\n", "<br>\n", $newcomentario); ?></td>
			</tr>
		</table>
	</p>
	<p align="center"><a href="javascript:self.close();">Fechar janela</a></p>
	<?php echo "\n";
	reload();
}//function executar();




function confirmar()
{
	$id = $_POST['id'];
	$nota = $_POST['nota'];
	$newnota = $_POST['newnota'];
	$comentario = $_POST['comentario'];
	$newcomentario = stripslashes(htmlentities(trim($_POST['newcomentario']), ENT_QUOTES));
	$action = $_POST['action'];
	
	if(valida_dados())
	{
		?>
		<p  align="center" class="title">Confirmar dados:</p>
		<p align="center">
			<b>Novos dados:</b><br>
			<table align="center" border="1">
				<tr>
					<th align="right">Nota:</th>
					<td align="left"><?php echo $newnota; ?></td>
				</tr>
				<tr>
					<th align="right">Coment&aacute;rio:</th>
					<td align="left"><?php echo ereg_replace("\n", "<br>\n", $newcomentario); ?></td>
				</tr>
			</table>
		</p>
		<?php echo "\n";
		if($action == "alterar")
		{
			?>
			<p align="center">
				<b>Dados antigos:</b><br>
				<table align="center" border="1">
					<tr>
						<th align="right">Nota:</th>
						<td align="left"><?php echo $nota; ?></td>
					</tr>
					<tr>
						<th align="right">Coment&aacute;rio:</th>
						<td align="left"><?php echo ereg_replace("\n", "<br>\n", $comentario); ?></td>
					</tr>
				</table>
			</p>
			<?php echo "\n";
		}//if
		?>
		<p align="center">
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="newnota" value="<?php echo $newnota; ?>">
				<input type="hidden" name="newcomentario" value="<?php echo $newcomentario; ?>">
				<input type="hidden" name="action" value="<?php echo $action; ?>">
				<input type="hidden" name="step" value="2">
				<input type="submit" value="Confirmar dados">
				<input type="button" value="Cancelar" onClick="javascript:self.close();">
			</form>
		</p>
		<?php echo "\n";
	}//if
	else
		formulario("corrigir");
}//function confirmar







function formulario($new_action = "")
{
	if($new_action != "corrigir")
	{
		$id = $_POST['id'];
		$info_nota = new Info_nota($id);
		
		if(empty($info_nota->dados_nota))
		{
			$nota = $comentario = "";
			$action = "incluir";
		}
		else
		{
			$nota = $info_nota->dados_nota['nota'];
			$comentario = $info_nota->dados_nota['comentario'];
			$action = "alterar";
		}//else
	}//if
	else
	{
		$id = $_POST['id'];
		$nota = $_POST['nota'];
		$comentario = $_POST['comentario'];
		$action = $_POST['action'];
	}//else
	
	?>
	<p align="center" class="title">Adicionar/alterar informa&ccedil;&otilde;es</p>
	<p align="center">
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<table align="center" border="0">
				<tr>
					<th align="right">Nota (formato NN.NN):</th>
					<td align="left"><input type="text" name="newnota" value="<?php echo $nota; ?>" maxlength="5" size="5"></td>
				</tr>
				<tr>
					<th align="right">Coment&aacute;rio:</th>
					<td align="left"><textarea name="newcomentario" cols="40" rows="5" wrap="hard"><?php echo $comentario; ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" value="Adicionar/Alterar">
						<input type="button" value="Cancelar" onClick="javascript:self.close()">
					</td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<input type="hidden" name="nota" value="<?php echo $nota; ?>">
			<input type="hidden" name="comentario" value="<?php echo $comentario; ?>">
			
			<input type="hidden" name="action" value="<?php echo $action; ?>">
			<input type="hidden" name="step" value="1">
		</form>
	</p>
	<?php echo "\n";
}





function main()
{
	if(!isset($_POST['action']) || !isset($_POST['id']))
	{
		?>
		<script language="javascript">
		<!--
		self.close();
		-->
		</script>
		<?php echo "\n";
	} //if
	else
	{
		$action = $_POST['action'];
		
		if($action == "incluir" || $action == "alterar")
		{
			$step = (isset($_POST['step'])) ? $_POST['step'] : 0;
			
			switch($step)
			{
				case 0:
					html_header("Adicionar/Alterar informa&ccedil;&otilde;es");
					formulario();
					html_footer();
					break;
				case 1:
					html_header("Confirmar");
					confirmar();
					html_footer();
					break;
				case 2:
					html_header("Dados adicionados/alterados");
					executar();
					html_footer();
					break;
			}//switch;
		}//if
	}//else
}


main();
?>
