<?php
include_once("login.php");




function recarrega_capitulos()
{
	?>
	<script language="javascript">
	<!--
	opener.document.forms['orderby_id'].order.value = "ASC";
	opener.document.forms['orderby_id'].submit();
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
	
	$info_capitulos = new Info_capitulos();
	
	if($action != "excluir")
	{
		$newdate = $newdate[2]."-".$newdate[1]."-".$newdate[0]." ".$newdate[3].":".$newdate[4].":".$newdate[5];
		
		if($action == 'incluir')
		{
			$id = $info_capitulos->num_capitulos()+1;
			$info_capitulos->inserir_capitulo($id, $tipo, $newname, $newfriendly, $newdate, $newpenalidade);
		}//if
		else
		{
			$info_capitulos->alterar_capitulo($id, $id, $newname, $newfriendly, $newdate, $newpenalidade);
		}//else
		
		//trocar as identificações
		$source = $id;
		$destination = $newid;
		
		$info_capitulos->mover_capitulo($id, $newid);
		
		$past = ($action == 'incluir') ? 'inclu&iacute;dos': 'alterados';
		
		?>
		<p class="title" align="center">Dados <?php echo $past; ?> com sucesso!</p>
		<?php echo "\n";
		
		$newdate = $_POST['newdate'];
		$newdate = $newdate[0]."/".$newdate[1]."/".$newdate[2]." ".$newdate[3].":".$newdate[4].":".$newdate[5];
		tabela_dados($tipo, $newid, $newname, $newfriendly, $newdate, $newpenalidade, "Novos dados");
		
		?>
		<p align="center"><a href="javascript:self.close()">Fechar janela</a></p>
		<?php echo "\n";
	}//if
	else
	{
		$info_capitulos->excluir_capitulo($id);
		
		?>
		<p class="errmsg" align="center">Dados exclu&iacute;dos.</p>
		<p align="center"><a href="javascript:self.close()">Fechar janela.</a></p>
		<?php echo "\n";
	}//else
}// function executar()



























function tabela_dados($tipo, $id, $name, $friendly, $date, $penalidade, $caption = '')
{
	if(is_array($date))
		$date = $date[0]."/".$date[1]."/".$date[2]." ".$date[3].":".$date[4].":".$date[5];
	
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
				<th align="right">Tipo:</th>
				<td align="left"><?php echo $tipo; ?></td>
			</tr>
			<tr>
				<th align="right">Identifica&ccedil;&atilde;o:</th>
				<td align="left"><?php echo $id; ?></td>
			</tr>
			<tr>
				<th align="right">Nome de arquivo:</th>
				<td align="left"><?php echo $name; ?></td>
			</tr>
			<tr>
				<th align="right">Nome de exibi&ccedil;&atilde;o:</th>
				<td align="left"><?php echo $friendly; ?></td>
			</tr>
			<tr>
				<th align="right">Data:</th>
				<td align="left"><?php echo $date; ?></td>
			</tr>
			<tr>
				<th align="right">Aplicar penalidade:</th>
				<td align="left"><?php echo ($penalidade) ? "Sim" : "N&atilde;o"; ?></td>
			</tr>
		</table>
	</p>
	<?php
}// function tabela_dados()
























function valida_dados($action)
{
	function erro($num)
	{
		$erro[0] = "Nome do cap&iacute;tulo inv&aacute;lido";
		$erro[1] = "Nome de exibi&ccedil;&atilde;o inv&aacute;lido";
		$erro[2] = "Dia do m&ecirc;s n&atilde;o foi selecionado";
		$erro[3] = "M&ecirc;s n&atilde;o foi selecionado";
		$erro[4] = "Nome do cap&iacute;tulo j&aacute; existe";
		
		?>
		<p class="errmsg" align="center"><?php echo $erro[$num]; ?></p>
		<?php echo "\n";
	}//function erro()
	
	
	foreach($_POST as $key => $value)
	{
		if($key != 'action')
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}//foreach
	
	$name = (!empty($name)) ? $name : "";
	
	$valid = 1;
	
	if(empty($newname))
	{
		erro(0);
		$valid = 0;
	}//if
	else
	{
		$newname = trim($newname);
		if(empty($newname))
		{
			erro(0);
			$valid = 0;
		}//if
		else if($newname != $name)
		{
			$info_capitulos = new Info_capitulos();
			$info_capitulos->Processa();
			$capitulos = $info_capitulos->dados_capitulos['capitulos'];
			
			$existe = 0;
			for($i=0; $i < count($capitulos); $i++)
			{
				if($capitulos[$i]['nome_capitulo'] == $newname)
				{
					$existe = 1;
					break;
				}//if
			}//for
			
			if($existe)
			{
				erro(4);
				$valid = 0;
			}//if
		}//else
	}//else
		
	if(empty($newfriendly))
	{
		erro(1);
		$valid = 0;
	}//if
	else
	{
		$newfriendly = trim($newfriendly);
		if(empty($newfriendly))
		{
			erro(1);
			$valid = 0;
		}//if
	}//else
	
	if($newdate[0]+0 == 0)
	{
		erro(2);
		$valid = 0;
	}//if
	
	if($newdate[1]+0 == 0)
	{
		erro(3);
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
				<p class="title" align="center">Confirmar os dados digitados</p>
				<?php echo "\n";
			}
			else
			{
				?>
				<p class="title" align="center">Confirmar as altera&ccedil;&otilde;es</p>
				<?php echo "\n";
			}
			
			tabela_dados($tipo, $newid, $newname, $newfriendly, $newdate, $newpenalidade, "Novos dados");
			
			if($action == 'alterar')
				tabela_dados($tipo, $id, $name, $friendly, $date, $penalidade, "Dados antigos");
			
			?>
			<p align="center">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<input type="hidden" name="newid" value="<?php echo $newid; ?>">
					<input type="hidden" name="newname" value="<?php echo $newname; ?>">
					<input type="hidden" name="newfriendly" value="<?php echo $newfriendly; ?>">
					<?php echo "\n";
					for($i=0; $i < 6; $i++)
					{
						?>
						<input type="hidden" name="newdate[]" value="<?php echo $newdate[$i]; ?>">
						<?php echo "\n";
					}
					?>
					<input type="hidden" name="newpenalidade" value="<?php echo $newpenalidade; ?>">
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
			form = document.forms['editar_capitulo'];
			tipo = search_field("tipo", 0);
			seleciona_id(tipo.selectedIndex);
			-->
			</script>
			<?php echo "\n";
		} //else
	}//if
	else
	{
		?>
		<p class="title" align="center">Tem certeza de que deseja excluir os dados abaixo?</p>
		<?php echo "\n";
		$capitulos = new Info_capitulos();
		$capitulos->Dados_capitulo($id);
		
		$tipo = $capitulos->dados_capitulos['tipo'];
		$name = $capitulos->dados_capitulos['nome_capitulo'];
		$friendly = $capitulos->dados_capitulos['legenda_capitulo'];
		$date = date("d/m/Y H:i:s", $capitulos->dados_capitulos['data_entrega']);
		
		tabela_dados($tipo, $id, $name, $friendly, $date, "Dados a serem exclu&iacute;dos");		
		
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
	if($action == "alterar")
	{
		$capitulos = new Info_capitulos();
		$capitulos->Dados_capitulo($id);
		
		$tipo = $capitulos->dados_capitulos['tipo'];
		$name = $capitulos->dados_capitulos['nome_capitulo'];
		$friendly = $capitulos->dados_capitulos['legenda_capitulo'];
		$date = getdate($capitulos->dados_capitulos['data_entrega']);
		$penalidade = $capitulos->dados_capitulos['aplicar_penalidade'];
	}//if
	else
	{
		$tipo = "aluno";
		$name = $friendly = $date['mday'] = $date['mon'] = $date['year'] = $date['hours'] = $date['minutes'] = $date['seconds'] = $penalidade = "";
		
		if(isset($_POST['tipo']))
			$tipo = $_POST['tipo'];
		
		if(isset($_POST['newid']))
			$id = $_POST['newid'];
		
		if(isset($_POST['newname']))
			$name = $_POST['newname'];
		
		if(isset($_POST['newfriendly']))
			$friendly = $_POST['newfriendly'];
		
		if(isset($_POST['newdate'][0]))
			$date['mday'] = $_POST['newdate'][0];
		
		if(isset($_POST['newdate'][1]))
			$date['mon'] = $_POST['newdate'][1];
	
		if(isset($_POST['newdate'][2]))
			$date['year'] = $_POST['newdate'][2];
	
		if(isset($_POST['newdate'][3]))
			$date['hours'] = $_POST['newdate'][3];
	
		if(isset($_POST['newdate'][4]))
			$date['minutes'] = $_POST['newdate'][4];
	
		if(isset($_POST['newdate'][5]))
			$date['seconds'] = $_POST['newdate'][5];
		
		if(isset($_POST['penalidade']))
			$penalidate = $_POST['penalidade'];
	
	}//else
	?>
	<p align="center" class="title"><?php echo ucfirst($action)." cap&iacute;tulo: $friendly"; ?></p>
	<p align="center">
		<form name="editar_capitulo" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<table align="center" border="0">
				<tr>
					<th align="right">Vis&iacute;vel para:</th>
					<td align="left">
						<select name="tipo" onChange="javascript:seleciona_id(this.selectedIndex)"<?php if($action == "alterar" || strstr($_POST['action'], "alterar_confirmar") == "alterar_confirmar") echo " disabled"; ?>>
							<option value="aluno"<?php if($tipo == "aluno") echo " selected" ?>>alunos</option>
							<option value="professor"<?php if($tipo == "professor") echo " selected" ?>>professores</option>
						</select>
					</td>
				</tr>
				<tr>
					<th align="right">Identifica&ccedil;&atilde;o:</th>
					<td align="left">
						<select name="newid">
						</select>
					</td>
				</tr>
				<tr>
					<th align="right">Nome de arquivo:</th>
					<td align="left"><input type="text" name="newname" value="<?php echo $name; ?>"></td>
				</tr>
				<tr>
					<th align="right">Nome de exibi&ccedil;&atilde;o:</th>
					<td align="left"><input type="text" name="newfriendly" value="<?php echo $friendly; ?>"></td>
				</tr>
				<tr>
					<th align="right">Data limite de entrega:</th>
					<td>
						<select name="newdate[]">
						<?php echo "\n";
						for($i=0; $i <= 31; $i++)
						{
							$selected = ($i == $date['mday']) ? " selected" : "";
							$zero = ($i < 10) ? "0" : "";
							
							if($i == '0')
							{
								?>
								<option value=<?php echo "\"$zero$i\"$selected"; ?>>Dia</option>
								<?php echo "\n";
							}
							else
							{
								?>
								<option value=<?php echo "\"$zero$i\"$selected"; ?>><?php echo "$zero$i"; ?></option>
								<?php echo "\n";
							}//else
						} //for
						?>
						</select> /
						<select name="newdate[]" onChange="javascript:validade_date(this.selectedIndex)">
						<?php
						$mes[0] = "M&ecirc;s";
						$mes[1] = "Janeiro";
						$mes[2] = "Fevereiro";
						$mes[3] = "Mar&ccedil;o";
						$mes[4] = "Abril";
						$mes[5] = "Maio";
						$mes[6] = "Junho";
						$mes[7] = "Julho";
						$mes[8] = "Agosto";
						$mes[9] = "Setembro";
						$mes[10] = "Outubro";
						$mes[11] = "Novembro";
						$mes[12] = "Dezembro";
						
						for($i=0; $i <= 12; $i++)
						{
							$selected = ($i == $date['mon']) ? " selected" : "";
							$zero = ($i < 10) ? "0" : "";
							
							?>
							<option value=<?php echo "\"$zero$i\"$selected"; ?>><?php echo $mes[$i]; ?></option>
							<?php echo "\n";
						}
						?>
						</select> / <?php echo date("Y"); ?>
						<input type="hidden" name="newdate[]" value="<?php echo date("Y"); ?>">
						<select name="newdate[]">
						<?php
						for($i=0; $i < 24; $i++)
						{
							$selected = ($i == $date['hours']) ? " selected" : "";
							$zero = ($i < 10) ? "0" : "";
							
							?>
							<option value=<?php echo "\"$zero$i\"$selected"; ?>><?php echo "$zero$i"; ?></option>
							<?php echo "\n";
						}
						?>
						</select> :
						<select name="newdate[]">
						<?php
						for($i=0; $i < 60; $i++)
						{
							$selected = ($i == $date['minutes']) ? " selected" : "";
							$zero = ($i < 10) ? "0" : "";
							
							?>
							<option value=<?php echo "\"$zero$i\"$selected"; ?>><?php echo "$zero$i"; ?></option>
							<?php echo "\n";
						}
						?>
						</select> :
						<select name="newdate[]">
						<?php
						for($i=0; $i < 60; $i++)
						{
							$selected = ($i == $date['seconds']) ? " selected" : "";
							$zero = ($i < 10) ? "0" : "";
							
							?>
							<option value=<?php echo "\"$zero$i\"$selected"; ?>><?php echo "$zero$i"; ?></option>
							<?php echo "\n";
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<th align="right">Aplicar penalidade:</th>
					<td align="left">
						<select name="newpenalidade">
							<option value="0"<?php if(!$penalidade) echo " selected"; ?>>N&atilde;o</option>
							<option value="1"<?php if($penalidade) echo " selected"; ?>>Sim</option>
						</select>
					</td>
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
			<input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<input type="hidden" name="name" value="<?php echo $name; ?>">
			<input type="hidden" name="friendly" value="<?php echo $friendly ?>">
			<input type="hidden" name="date[]" value="<?php echo $date['mday']; ?>">
			<input type="hidden" name="date[]" value="<?php echo $date['mon']; ?>">
			<input type="hidden" name="date[]" value="<?php echo $date['year']; ?>">
			<input type="hidden" name="date[]" value="<?php echo sprintf("%02d", $date['hours']); ?>">
			<input type="hidden" name="date[]" value="<?php echo sprintf("%02d", $date['minutes']); ?>">
			<input type="hidden" name="date[]" value="<?php echo sprintf("%02d", $date['seconds']); ?>">
			<input type="hidden" name="penalidade" value="<?php echo $penalidade; ?>">
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
		
		
		function search_field(fieldname, num)
		{
			form = document.forms['editar_capitulo'];
			i = 0;
			for(j=i; form.elements[j]; j++)
			{
				if(form.elements[j].name == fieldname)
					if (i == num)
						return form.elements[j];
					else
						i++;
			}//for
		}//function

		
		
		function reset_id()
		{
			form = document.forms['editar_capitulo'];
			tipo = search_field("tipo", 0);
			seleciona_id(tipo.selectedIndex);
		}
		
		
		function seleciona_id(num)
		{
			form = document.forms['editar_capitulo'];
			
			aluno = new Array();
			professor = new Array();
			<?php echo "\n";
			$capitulos = new Info_capitulos();
			
			$m = $capitulos->num_capitulos('aluno');
			
			$n = $capitulos->num_capitulos('professor');
			
			if($action == 'incluir')
				$include = 1;
			else
				$include = 0;
			
			for($i=0; $i < $m + $include; $i++)
			{
				?>
				aluno[<?php echo $i; ?>] = <?php echo $i+1; ?>;
				<?php echo "\n";
			}//for
			
			for($i=$m; $i < $m + $n + $include;$i++)
			{
				?>
				professor[<?php echo $i - $m; ?>] = <?php echo $i+1; ?>;
				<?php echo "\n";
			}//for
			
			?>
			tipo = (num) ? "professor" : "aluno";
			
			clear_select(form.newid);
			
			for(i=0; i < eval(tipo+".length"); i++)
			{
				value = eval(tipo + "[i]");
				add_option(form.newid, value, value);
				<?php echo "\n";
				if(!empty($id))
				{
					?>
					if(eval(tipo + "[i]") == <?php echo $id; ?>)
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
		
		function validade_date(num)
		{
			form = document.forms['editar_capitulo'];
			
			mes = new Array();
			ano = new Date("yyyy");
			
			mes[0] = mes[1] = mes[3] = mes[5] = mes[7] = mes[8] = mes[10] = mes[12] = 31;
			mes[4] = mes[6] = mes[9] = mes[11] = 30;
			mes[2] = 28;
			
			if(ano % 4 == 0)
				mes[2]++;
			
			newdate = new Array();
			for(i=0; i < 6; i++)
				newdate[i] = search_field("newdate[]", i);
			
			i = newdate[0].length - 1;
			
			if(i > mes[num])
			{
				while(newdate[0].length - 1 > mes[num])
					newdate[0].remove(i--);
			}//if
			else
			{
				if(i < mes[num])
				{
					while(newdate[0].length - 1 < mes[num])
						add_option(newdate[0], ++i, i);
				}//if
			}//else
		}//function validate_date;
	-->
	</script>
<?php
}//function formulario



function main()
{
	$initialize = "javascript:reset_id();";	
	
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
