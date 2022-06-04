<?php
include_once("login.php");
include_once("../common/classes_info.php");




function atualiza_dados($dados)
{
	foreach($dados as $key => $value)
		$$key = $value;
	
	$dados_validos = 1;
	
	if(empty($new_nome[0]))
	{
		$dados_validos = 0;
		echo "<p class=\"errmsg\" align=\"center\">Nome do(a) primeiro(a) respons&aacute;vel inv&aacute;lido!</p>";
	}//if
	
	if(empty($new_nome[1]))
	{
		$dados_validos = 0;
		echo "<p class=\"errmsg\" align=\"center\">Nome do(a) segundo(a) respons&aacute;vel inv&aacute;lido!</p>";
	}//if
	
	if(empty($new_cod_disciplina))
	{
		$dados_validos = 0;
		echo "<p class=\"errmsg\" align=\"center\">C&oacute;digo da disciplina inv&aacute;lido!</p>";
	}//if
	else if(!is_numeric($new_cod_disciplina))
	{
		$dados_validos = 0;
		echo "<p class=\"errmsg\" align=\"center\">C&oacute;digo da disciplina inv&aacute;lido!</p>";
	}//if
	
	if(empty($new_cod_cursos))
	{
		$dados_validos = 0;
		echo "<p class=\"errmsg\" align=\"center\">C&oacute;digos dos cursos da disciplina inv&aacute;lido!</p>";
	}//if
	
	if(empty($new_turma))
	{
		$dados_validos = 0;
		echo "<p class=\"errmsg\" align=\"center\">Turma inv&aacute;lida!</p>";
	}//if
	else if(!is_numeric($new_turma))
	{
		$dados_validos = 0;
		echo "<p class=\"errmsg\" align=\"center\">Turma inv&aacute;lida!</p>";
	}//if
	
	if(empty($new_carga_disciplina))
	{
		$dados_validos = 0;
		echo "<p class=\"errmsg\" align=\"center\">Carga hor&aacute;ria da disciplina inv&aacute;lida!</p>";
	}//if
	else if(!is_numeric($new_carga_disciplina))
	{
		$dados_validos = 0;
		echo "<p class=\"errmsg\" align=\"center\">Carga hor&aacute;ria da disciplina inv&aacute;lida!</p>";
	}//else if
	
	if($dados_validos)
	{
		$disciplina = new Info_disciplina();
		$disciplina->alterar_disciplina($new_ano_letivo, $new_nome[0], $new_titulo[0], $new_nome[1], $new_titulo[1], $new_cod_disciplina, $new_cod_cursos, $new_turma, $new_carga_disciplina, $new_semestre_letivo, $_SESSION['username']);
	}//if
	else
	{
		?>
		<p class="errmsg" align="left">Para descartar qualquer altera&ccedil;&atilde;o,
									   basta carregar esta p&aacute;gina novamente, a partir
									   do painel de navega&ccedil;&atilde;o do site.
		</p>
		<?php echo "\n";
	}//else
	
	return $dados_validos;
}//function atualiza_dados

function disciplina($dados, $correcao_dados = 0)
{
	if($correcao_dados)
	{
		foreach($dados as $key => $value)
			$dados[str_replace("new_", "", $key)] = $value;
		
		for($i=0; $i < count($dados['new_nome']); $i++)
		{
			$dados['responsaveis'][$i]['nome'] = $dados['new_nome'][$i];
			$dados['responsaveis'][$i]['titulo'] = $dados['new_titulo'][$i];
		}//for
	}//if
	
	if(empty($dados['ano_letivo']))
		$dados['ano_letivo'] = date("Y");
	/*
	foreach($keys as $value)
	{
		if(!isset($dados[$value]))
			$dados[$value] = "";
	}//foreach
	*/
	?>
	<p class="title" align="center">Dados da disciplina:</p>
	<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
	<table align="center" border="0">
	<?php echo "\n";
	foreach($dados['responsaveis'] as $responsavel)
	{
		?>
		<tr>
			<th align="right">Respons&aacute;vel</th>
			<td align="left"><input type="text" name="new_nome[]" value="<?php echo $responsavel['nome']; ?>" size="40" maxlength="255"></td>
		</tr>
		<tr>
			<th align="right">T&iacute;tulo</th>
			<td align="left">
				<select name="new_titulo[]">
					<option value="1"<?php echo ($responsavel['titulo']) ? " selected" : ""; ?>>Doutor(a)</option>
					<option value="0"<?php echo (!$responsavel['titulo']) ? " selected" : ""; ?>>-</option>
				</select>
			</td>
		</tr>
		<?php echo "\n";
	}//foreach
	?>
		<tr>
			<th align="right">C&oacute;digo da disciplina</th>
			<td align="left"><input type="text" name="new_cod_disciplina" value="<?php echo $dados['cod_disciplina']; ?>" size="5" maxlength="4"></td>
		</tr>
		<tr>
			<th align="right">C&oacute;digos dos cursos da disciplina</th>
			<td align="left"><input type="text" name="new_cod_cursos" value="<?php echo $dados['cod_cursos']; ?>" size="30" maxlength="255"></td>
		</tr>
		<tr>
			<th align="right">Turma</th>
			<td align="left"><input type="text" name="new_turma" value="<?php echo $dados['turma']; ?>" size="10" maxlength="2"></td>
		</tr>
		<tr>
			<th align="right">Carga hor&aacute;ria</th>
			<td align="left"><input type="text" name="new_carga_disciplina" value="<?php echo $dados['carga_disciplina']; ?>" size="10" maxlength="3"></td>
		</tr>
		<tr>
			<th align="right">M&aacute;ximo de faltas (30% da carga hor&aacute;ria)</th>
			<td align="left"><?php echo (isset($dados['faltas_max'])) ? $dados['faltas_max'] : "0"; ?></td>
		</tr>
		<tr>
			<th align="right">Ano letivo</th>
			<td align="left">
				<select name="new_ano_letivo">
				<?php echo "\n";
				$year = date("Y");
				for($i=$year - 2; $i <= $year + 2; $i++)
				{
					?>
					<option value="<?php echo $i; ?>"<?php echo ($dados['ano_letivo'] == $i) ? " selected" : ""; ?>><?php echo $i; ?></option>
					<?php echo "\n";
				}//for
				?>
				</select>
			</td>
		</tr>
		<tr>
			<th align="right">Semestre letivo</th>
			<td align="left">
				<select name="new_semestre_letivo">
					<option value="1"<?php echo ($dados['semestre_letivo'] == 1) ? " selected" : ""; ?>>1&ordm; semestre</option>
					<option value="2"<?php echo ($dados['semestre_letivo'] == 2) ? " selected" : ""; ?>>2&ordm; semestre</option>
				</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td align="left"><input type="submit" value="Atualizar dados"></td>
		</tr>
	</table>
	<?php echo "\n";
	/*
	foreach($dados as $key => $value)
	{
		if(!is_array($value))
		{
			?>
			<input type="hidden" name="<?php echo "old_".$key; ?>" value="<?php echo $value; ?>">
			<?php echo "\n";
		}//if
		else
		{
			foreach($value as $value2)
			{
				foreach($value2 as $key3 => $value3)
				{
					?>
					<input type="hidden" name="<?php echo "old_".$key3; ?>[]" value="<?php echo $value3; ?>">
					<?php echo "\n";
				}//foreach
			}//foreach
		}//else
	}//foreach
	*/
	?>
	</form>
	<?php echo "\n";
}




function main()
{
	
	html_header("Editar dados da disciplina");
	common_header('disciplina');
	
	$dados_validos = 1;
	
	if(!empty($_POST))
		$dados_validos = atualiza_dados($_POST);
	
	$disciplina = new Info_disciplina();
	
	if($dados_validos)
		disciplina($disciplina->dados_disciplina);
	else
		disciplina($_POST,1);
	
	common_footer();
	html_footer();
}	


main();
?>