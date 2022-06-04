<?php
include_once("login.php");
include_once("../common/classes_info.php");










function list_penalidades()
{
	?>
	<p class="title" align="center"><big>Editar penalidades:</big></p>
	<?php echo "\n";
	
	if(isset($_POST['form_name']) && $_POST['form_name'] == "editar_penalidade") //fazer as alterações para ignorar a cobrança de penalidades
	{
		$projeto = $_POST['projeto'];
		
		$penalidades_locais = new Info_penalidades_locais($projeto);
		
		for($i=0; $i < count($_POST['old_ignore']); $i++)
		{
			
			if(isset($_POST['new_ignore'][$i]))
			{
				$old = $_POST['old_ignore'][$i];
				$new = $_POST['new_ignore'][$i];
				$old_date = $_POST['old_date'][$i];
				$new_date = $_POST['new_date'][$i];
				
				$info_capitulos = new Info_capitulos();
				$info_capitulos->Dados_capitulo($i+1);
				
				if(empty($new_date))
					$new_date = date("d/m/Y H:i:s", $info_capitulos->dados_capitulos['data_entrega']);
				
				if($old && !$new) //caso a alteração tenha sido de 1 para 0
				{
					$original = date("Y-m-d H:i:s", $info_capitulos->dados_capitulos['data_entrega']);
					
					if(!ereg("([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $new_date, $new_date))
						echo "<p align=\"center\" class=\"errmsg\">Data inv&aacute;lida!</p>";
					else
					{
						if(!checkdate($new_date[2], $new_date[1], $new_date[3]) || ($new_date[4] > 23 || $new_date[5] > 59 || $new_date[6] > 59))
							echo "<p align=\"center\" class=\"errmsg\">Data inv&aacute;lida!</p>";
						else
						{
							$new_date = $new_date[3]."-".$new_date[2]."-".$new_date[1]." ".$new_date[4].":".$new_date[5].":".$new_date[6];
							
							if($new_date == $original)
								$penalidades_locais->excluir_penalidade_local($projeto, $i+1);
							else
								$penalidades_locais->alterar_penalidade_local($projeto, $i+1, 0, $new_date);
							
							$penalidades_locais->Processa($projeto);
						}//else
					}//else
				}//if
				else if(!$old && $new) //caso a alteração tenha sido de 0 para 1
				{
					$existe = $penalidades_locais->dados_penalidades_locais[$i]['existe'];
					
					if(ereg("([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $new_date, $new_date))
					{
						if(checkdate($new_date[2], $new_date[1], $new_date[3]) || ($new_date[4] <= 23 && $new_date[5] <= 59 && $new_date[6] <= 59))
							$new_date = $new_date[3]."-".$new_date[2]."-".$new_date[1]." ".$new_date[4].":".$new_date[5].":".$new_date[6];
						else
							$new_date = $old_date[3]."-".$old_date[2]."-".$old_date[1]." ".$old_date[4].":".$old_date[5].":".$old_date[6];
					}
					else
						$new_date = $old_date[3]."-".$old_date[2]."-".$old_date[1]." ".$old_date[4].":".$old_date[5].":".$old_date[6];
					
					if($existe)
						$penalidades_locais->alterar_penalidade_local($projeto, $i+1, 1, $new_date);
					else
						$penalidades_locais->inserir_penalidade_local($projeto, $i+1, 1, $new_date);
					
					$penalidades_locais->Processa($projeto);
				}//else
				else if($new_date != $old_date)
				{
					if(!ereg("([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $new_date, $new_date))
						echo "<p align=\"center\" class=\"errmsg\">Data inv&aacute;lida!</p>";
					else
					{
						if(!checkdate($new_date[2], $new_date[1], $new_date[3]) || ($new_date[4] > 23 || $new_date[5] > 59 || $new_date[6] > 59))
							echo "<p align=\"center\" class=\"errmsg\">Data inv&aacute;lida!</p>";
						else
						{
							$new_date = $new_date[3]."-".$new_date[2]."-".$new_date[1]." ".$new_date[4].":".$new_date[5].":".$new_date[6];
							$original = $info_capitulos->dados_capitulo['data_entrega'];
							$existe = $penalidades_locais->dados_penalidades_locais[$i]['existe'];
							
							if($new_date == $original)
								$penalidades_locais->excluir_penalidade_local($projeto, $i+1);
							else
							{
								if($existe)
									$penalidades_locais->alterar_penalidade_local($projeto, $i+1, $penalidades_locais->dados_penalidades_locais[$i]['aplicar_penalidade_local'], $new_date);
								else
									$penalidades_locais->inserir_penalidade_local($projeto, $i+1, 0, $new_date);
								
							}//else
							
							$penalidades_locais->Processa($projeto);
						}//else
					}//else
				}//else if
			}//if
		}//for
	}//if
	
	
	//novo
	$projeto = new Info_projeto();
	$capitulos = new Info_capitulos();
	$nota_final = new Info_nota_final();
	
	$num_projetos = $projeto->num_projetos();
	
	for($i=0; $i < $num_projetos; $i++)
		$id_projetos[$i]['id_projeto'] = $i+1;
	
	for($i=0; $i < $num_projetos; $i++)
	{
		$id_projeto = $id_projetos[$i]['id_projeto'];
		
		$projeto->Processa($id_projetos[$i]['id_projeto']);
		$capitulos->Processa($id_projetos[$i]['id_projeto']);
		$nota_final->Processa($id_projetos[$i]['id_projeto']);
		
		$dados[$i] = $projeto->dados_projeto;
		$dados[$i]['nota_final'] = $nota_final->dados_nota_final['alunos'][0]['nota'];
		
		$dados[$i]['capitulos'] = $capitulos->dados_capitulos['capitulos'];
		$dados[$i]['penalidades'] = $capitulos->dados_capitulos['penalidades'];
		$dados[$i]['entregas'] = $capitulos->dados_capitulos['entregas'];
	}//for
	
	for($i=0; $i < $num_projetos; $i++)
	{
		$title = $dados[$i]['titulo'];
		$projeto = $dados[$i]['id_projeto'];
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
		<p class="subtitle" align="center">Penalidades</p>
		<p>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="hidden" name="projeto" value="<?php echo $projeto; ?>">
			<?php echo "\n";
			for($j=0; $j < count($dados[$i]['penalidades']); $j++)
			{
				?>
				<input type="hidden" name="old_ignore[]" value="<?php echo ($dados[$i]['penalidades'][$j] === "No") ? "1" : "0"; ?>">
				<?php echo "\n";
			}//for
			?>
			<table border="1" align="center">
				<tr>
					<th>Nome do cap&iacute;tulo</th>
					<!--<th>Data de entrega</th>-->
					<th>Penalidade</th>
					<th>Ignorar<br>Penalidade</th>
					<th>
						Redefinir data de entrega<br>
						(dd/mm/aaaa hh:mm:ss)
					</th>
					<th>Confirmar</th>
				</tr>
				<?php echo "\n";
				$mensagem = 0;
				
				for($j=0; $j < count($dados[$i]['penalidades']); $j++)
				{
					$friendly = $dados[$i]['capitulos'][$j]['legenda_capitulo'];
					$date = $dados[$i]['capitulos'][$j]['data_entrega'];
					$controle = ($dados[$i]['penalidades'][$j] === "No");
					
					?>
					<tr>
						<td><?php echo $friendly; ?></td>
						<!--<td><?php echo date("d/m/Y H:i:s", $date); ?></td>-->
						<td>
							<?php echo "\n";
							if(!is_numeric($dados[$i]['penalidades'][$j]))
								$penalidade = "Ok";
							else
							{
								$entregue = $dados[$i]['entregas'][$j]['entregue'];
								
								if(!$entregue)
								{
									if($dados[$i]['penalidades'][$j] == 0)
										$penalidade = "-*";
									else
										$penalidade = "-".$dados[$i]['penalidades'][$j]."*";
									
									if(!$mensagem)
										$mensagem = 1;
								}//if
								else
								{
									if($dados[$i]['penalidades'][$j] == 0)
										$penalidade = "Ok";
									else
										$penalidade = "-".$dados[$i]['penalidades'][$j];
								}//else
							}//else
							echo $penalidade;
							?>
						</td>
						<td align="center">
							<select name="new_ignore[]">
								<option value="0"<?php echo (!$controle) ? " selected" : ""; ?>>N&atilde;o</option>
								<option value="1"<?php echo ($controle) ? " selected" : ""; ?>>Sim</option>
							</select>
						</td>
						<td align="center">
							<input type="text" name="new_date[]" value="<?php echo date("d/m/Y H:i:s", $date); ?>" maxlength="19" size="22">
							<input type="hidden" name="old_date[]" value="<?php echo date("d/m/Y H:i:s", $date); ?>">
						</td>
						<?php echo "\n";
						if(!$j)
						{
							?>
							<td rowspan="<?php echo count($dados[$i]['capitulos']); ?>"><input type="submit" value="Confirmar"></td>
							<?php echo "\n";
						}//if
						?>
					</tr>
					<?php echo "\n";
				}//for
				
				//quantidade de pontos perdidos. Colocado aqui pelo fato
				//de as versões do PHP 4.1.2 ou menores modificam os dados
				//contidos no array, transformando valores não numéricos
				//em numéricos (resultando na maior parte em 0)
				$perdidos = array_sum($dados[$i]['penalidades']);

				
				?>
				<tr>
					<th>Pontos Perdidos</th>
					<td colspan="3">-<?php echo (!$perdidos) ? "" : $perdidos; ?></td>
				</tr>
			</table>
		</p>
		<input type="hidden" name="form_name" value="editar_penalidade">
		</form>
		<?php echo "\n";
		if($mensagem)
		{
			?>
			<p align="center">(*) N&atilde;o foi entregue at&eacute; o presente momento</p>
			<?php echo "\n";
		}//if
		
		
		if($i+1 < $num_projetos)
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
}//function list_uploads();




function main()
{
	html_header("Editar penalidades");
	common_header('penalidades');
	list_penalidades();
	common_footer();
	html_footer();
}//function main();



main();
?>