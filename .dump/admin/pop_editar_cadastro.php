<?php
include_once("login.php");



function tabela_dados($tipo, $name, $codigo, $email,
					  $title = '',
					  $username = '',
					  $password = '',
					  $caption = '',
					  $projeto = '',
					  $funcao = '',
					  $titulo = '')
{
	if($caption)
	{
		?>
		<p align="center"><b><?php echo $caption; ?>:</b></p>
		<?php
	}
	
	if($tipo == 'professor')
	{
		$codigoname = "C&oacute;digo";
	}//if
	else
		$codigoname = "RA";
	?>
	<p align="center">
		<table align="center" border="1">
			<?php echo "\n";
			if($tipo == "professor")
			{
				?>
				<tr>
					<th align="right">T&iacute;tulo:</th>
					<td align="left"><?php echo ($titulo) ? "Doutor(a)" : "-"; ?></td>
				</tr>
				<?php echo "\n";
			}//if
			?>
			<tr>
				<th align="right">Nome:</th>
				<td align="left"><?php echo $name; ?></td>
			</tr>
			<tr>
				<th align="right"><?php echo ucfirst($codigoname); ?>:</th>
				<td align="left"><?php echo $codigo; ?></td>
			</tr>
			<tr>
				<th align="right">E-mail:</th>
				<td align="left"><?php echo $email; ?></td>
			</tr>
			<?php
			if ($title)
			{
				if($tipo == 'aluno')
				{
					?>
					<tr>
						<th align="right">Projeto:</th>
						<td align="left"><?php echo $title; ?></td>
					</tr>
					<?php
				}
				else
				{
					?>
					<tr>
						<th align="right">Projeto(s):</th>
						<td align="left">
							<?php
							for($i=0; $i < count($title); $i++)
							{
								?>
								<div>
									 <span style="display: <?php echo ($projeto[$i]) ? "" : "none"; ?>"><?php echo $title[$i]; ?>: </span><span style="display: <?php echo ($projeto[$i] && $funcao[$i] == 1) ? "" : "none"; ?>">Avaliador</span><span style="display: <?php echo ($projeto[$i] && $funcao[$i] == 2) ? "" : "none"; ?>">Orientador</span>
								</div>
								<?php echo "\n";
							}//for
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>
			<?php if ($username) { ?>
			<tr>
				<th align="right">Nome de usu&aacute;rio:</th>
				<td align="left"><?php echo $username; ?></td>
			</tr>
			<?php } ?>
			<?php if ($password) { ?>
			<tr>
				<th align="right">Senha:</th>
				<td align="left"><?php echo $password; ?></td>
			</tr>
			<?php } ?>
		</table>
	</p>
	<?php
}// function tabela_dados()



function recarrega_cadastros($tipo = '')
{
	?>
	<script language="javascript">
	<!--
	opener.document.forms['orderby_nome_<?php echo $tipo; ?>'].order.value = "ASC";
	opener.document.forms['orderby_nome_<?php echo $tipo; ?>'].submit();
	-->
	</script>
	<?php
} //function recarrega_cadastros();



function valida_dados($action)
{
	function erro($num, $codigoname = '')
	{
		$erro[0] = "Nome inv&aacute;lido!";
		$erro[1] = ucfirst($codigoname)." inv&aacute;lido!";
		$erro[2] = ucfirst($codigoname)." j&aacute; existente!";
		$erro[3] = "E-mail inv&aacute;lido!";
		$erro[4] = "Nenhum projeto foi selecionado!";
		$erro[5] = "Nome de usu&aacute;rio inv&aacute;lido!";
		$erro[6] = "Nome de usu&aacute;rio j&aacute; existente!";
		$erro[7] = "Senha inv&aacute;lida!";
		$erro[8] = "Senha n&atilde;o coincidente!";
		
		?>
		<p align="center" class="errmsg"><?php echo $erro[$num]; ?></p>
		<?php echo "\n";
	}//function erro()
	
	
	foreach($_POST as $key => $value)
	{
		if($key != 'action')
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}
	
	// variável inicializada como válida
	$valid = 1;
	
	if($tipo == 'professor')
	{
		if(empty($newprojeto))
			$newprojeto = array();
		if(!array_sum($newprojeto))
		{
			erro(4);
			$valid = 0;
		}//if
		$codigoname = "C&oacute;digo";
	}
	else
		$codigoname = "RA";
	
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
	}//else
	
	if(empty($newcodigo))
	{
		erro(1, $codigoname);
		$valid = 0;
	}//if
	else
	{
		$newcodigo = trim($newcodigo);
		
		if(empty($newcodigo))
		{
			erro(1, $codigoname);
			$valid = 0;
		}//if
		else
		{
			$newcodigo++;
			$newcodigo--;
			
			if(is_string($newcodigo) || !$newcodigo || ($tipo == "aluno" && $newcodigo < 100))
			{
				erro(1, $codigoname);
				$valid = 0;
			}//if
			else if(isset($codigo) && ($codigo != $newcodigo))
			{
				if($tipo == "aluno")
				{
					$aluno = new Info_aluno($newcodigo,1);
					$existe = !empty($aluno->dados_aluno['projeto']);
				}//if
				else
				{
					$professor = new Info_professor($newcodigo,1);
					$existe = !empty($professor->dados_professor['funcao']);
				}//else
				
				if($existe)
				{
					erro(2, $codigoname);
					$valid = 0;
				}//if
			}//if
		}//else
	}//else (verificação de RA);
	
	if(empty($newemail))
	{
		erro(3);
		$valid = 0;
	}//if
	else
	{
		$newemail = trim($newemail);
		if(empty($newemail))
		{
			erro(3);
			$valid = 0;
		}//if
		else
		{
			$regexp = "^[^@ ]+@[^@ ]+\.[^@ \.]+$";
			if(!ereg($regexp, $newemail))
			{
				erro(3);
				$valid = 0;
			}//if
		}//else
	}//else (verificação de e-mail);
	
	if($action == 'incluir')
	{
		if(empty($newusername))
		{
			erro(5);
			$valid = 0;
		}//if
		else
		{
			$newusername = trim($newusername);
			
			if(empty($newusername))
			{
				erro(5);
				$valid = 0;
			}//if
			else
			{
				$login = new Info_login($newusername, "", "user");
				if($login->dados_login['username_existe'])
				{
					erro(6);
					$valid = 0;
				}//if
			}//else
		}//else
		
		if(empty($newpassword))
		{
			erro(7);
			$valid = 0;
		}//if
		else
		{
			$newpassword = trim($newpassword);
			
			if(empty($newpassword))
			{
				erro(7);
				$valid = 0;
			}//if
			else
			{
				if($newpassword != $confirmpassword)
				{
					erro(8);
					$valid = 0;
				}//if
			}//else
		}//else (verifica senhas)
	}// if
	
	return $valid;
}// function valida_dados();












function executar($action, $tipo, $codigo = '')
{
	foreach($_POST as $key => $value)
	{
		if($key != 'action')
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}
	
	if($action != "excluir")
	{
		$action = substr($_POST['action'], 0, strpos($_POST['action'], "_"));
		
		if($action == 'alterar')
		{
			if($tipo == 'aluno')
			{
				$aluno = new Info_aluno();
				$aluno->alterar_aluno($codigo, $newcodigo, $newname, $newemail, $newprojeto);
			}
			else if($tipo == 'professor')
			{
				for($i=0; $i < count($newprojeto); $i++)
				{
					$projetos[$i]['id_projeto'] = $i+1;
					$projetos[$i]['funcao'] = $newfuncao[$i];
					$projetos[$i]['selecionar'] = $newprojeto[$i];
				}//for
				
				$professor = new Info_professor();
				$professor->alterar_professor($codigo, $newcodigo, $newtitulo, $newname, $newemail, $projetos);
			}//else if
		}//if
		else if ($action == 'incluir')
		{
			// atualizar informações da tabela do tipo (aluno ou professor) correspontente
			if($tipo == "aluno")
			{
				$aluno = new Info_aluno();
				$aluno->inserir_aluno($newcodigo, $newname, $newemail, $newprojeto, $newusername, $newpassword);
			}//if
			else if($tipo == "professor")
			{
				$j=0;
				
				$projetos = array();
				for($i=0; $i < count($newprojeto); $i++)
				{
					if($newprojeto[$i])
					{
						$projetos[$j]['id_projeto'] = $i+1;
						$projetos[$j++]['funcao'] = $newfuncao[$i];
					}
				}
				
				$professor = new Info_professor();
				$professor->inserir_professor($newcodigo, $newtitulo, $newname, $newemail, $projetos, $newusername, $newpassword);
			}//else if
		}//elseif
		
		$past = ($action == 'alterar') ? "alterados" : "inclu&iacute;dos";
		?>
		<p align="center" class="title">Dados <?php echo $past; ?> com sucesso!</p>
		<?php echo "\n";
		
		if($action == 'alterar')
		{
			$newusername = "";
			$newpassword = "";
		}
		
		$info_projeto = new Info_projeto();
		
		if($tipo == 'aluno')
		{
			$newtitle = $aluno->dados_aluno['projeto']['titulo'];
		}//if
		else
		{
			for($i=0; $i < count($professor->dados_professor['projeto']); $i++)
			{
				$newtitle[$i] = $professor->dados_professor['projeto'][$i]['titulo'];
				$newfuncao[$i] = ($professor->dados_professor['projeto'][$i]['funcao'] == "avaliador") ? 1 : 2;
			}//for
		}//else
					
		tabela_dados($tipo, $newname, $newcodigo, $newemail,
					 $newtitle, $newusername, $newpassword,
					 "Novos dados", $newprojeto, ($tipo == 'aluno') ? "" : $newfuncao, ($tipo == 'aluno') ? "" : $newtitulo);
		?>
		<p align="center"><a href="javascript: self.close()">Fechar janela</a>
		<?php
	}//if
	else
	{
		if($tipo == "aluno")
		{
			$aluno = new Info_aluno();
			$aluno->excluir_aluno($codigo);
		}
		else if($tipo == "professor")
		{
			$professor = new Info_professor();
			$professor->excluir_professor($codigo);
		}
		?>
		
		<p align="center" class="errmsg">Os dados foram exclu&iacute;dos.</p>
		<p align="center"><a href="javascript: self.close()">Fechar janela</a></p>
		
		<?php
		recarrega_cadastros($tipo);
		
	}//else
}// function executar()



function confirmar($action, $tipo, $codigo = '')
{
	foreach($_POST as $key => $value)
	{
		if($key != "action")
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}//foreach
	
	if($action != "excluir")
	{
		if(valida_dados($action))
		{
			if($action == 'incluir')
			{
				?>
				<p align="center" class="title">Confirmar os dados digitados!</p>
				<?php echo "\n";
			}//if
			else
			{
				?>
				<p align="center" class="title">Confirmar as altera&ccedil;&otilde;es!</p>
				<?php echo "\n";
			}//else
			
			if(empty($newusername))
				$newusername = "";
			
			if(empty($newpassword))	
				$newpassword = "";
			
			$info_projeto = new Info_projeto();
			
			if($tipo == 'aluno')
			{
				$info_projeto->processa($newprojeto,1);
				$newtitle = $info_projeto->dados_projeto['titulo'];
				
				if($action == 'alterar')
				{
					$info_projeto->Processa($projeto,1);
					$title = $info_projeto->dados_projeto['titulo'];
				}//if
			}//if
			else if($tipo == "professor")
			{
				if(!empty($newprojeto))
				{
					for($i=0; $i < count($newprojeto); $i++)
					{
						$info_projeto->Processa($i+1,1);
						$newtitle[$i] = $info_projeto->dados_projeto['titulo'];
					}//for
				}//if
				
				if($action == 'alterar')
				{
					if(!empty($projeto))
					{
					for($i=0; $i < count($projeto); $i++)
						{
							$info_projeto->Processa($i+1,1);
							$title[$i] = $info_projeto->dados_projeto['titulo'];
						}//for
					}//if
				}//if
			}//else
			
			if(empty($title))
				$title = "";
			
			tabela_dados($tipo, $newname, $newcodigo, $newemail,
						 $newtitle, $newusername, $newpassword,
						 "Novos dados", $newprojeto, ($tipo == 'aluno') ? "" : $newfuncao, ($tipo == "aluno") ? "" : $newtitulo);
			
			if($action == 'alterar')
				tabela_dados($tipo, $name, $codigo, $email, $title,
							 "", "", "Dados antigos", $projeto, ($tipo == 'aluno') ? "" : $funcao, $title);
			
			?>
			<p align="center">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
					<input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
					<input type="hidden" name="newname" value="<?php echo $newname; ?>">
					<input type="hidden" name="newcodigo" value="<?php echo $newcodigo; ?>">
					<input type="hidden" name="newemail" value="<?php echo $newemail; ?>">
					<?php
					if($tipo == 'aluno')
					{
						?>
						<input type="hidden" name="newprojeto" value="<?php echo $newprojeto; ?>">
						<?php
					}
					else
					{
						?>
						<input type="hidden" name="newtitulo" value="<?php echo $newtitulo; ?>">
						<?php echo "\n";
						if(!empty($newprojeto))
						{
							for($i=0; $i < count($newprojeto); $i++)
							{
								?>
								<input type="hidden" name="newprojeto[]" value="<?php echo $newprojeto[$i]; ?>">
								<input type="hidden" name="newfuncao[]" value="<?php echo $newfuncao[$i]; ?>">
								<?php echo "\n";
							}//for
						}//if
					}//else
					
					if($action == 'incluir')
					{
						?>
						<input type="hidden" name="newusername" value="<?php echo $newusername; ?>">
						<input type="hidden" name="newpassword" value="<?php echo $newpassword; ?>">
						<input type="hidden" name="confirmpassword" value="<?php echo $confirmpassword; ?>">
						<?php
					}//if
					?>				
					<input type="hidden" name="action" value="<?php echo $action; ?>_executar">
					<input type="submit" value="Confirmar dados">
					<input type="button" value="Cancelar" onClick="javascript:self.close()">
				</form>
			</p>
			<?php
			
		}//if
		else
			formulario("corrigir", $tipo, $codigo);
	}//if
	else
	{
		?>
		<p align="center" class="title">Tem certeza de que deseja excluir os dados abaixo?</p>
		<?php echo "\n";
		
		if($tipo == 'aluno')
		{
			$aluno = new Info_aluno($codigo,1);
			$cadastro = $aluno->dados_aluno;
		}
		else
		{
			$professor = new Info_professor($codigo);
			$cadastro = $professor->dados_professor;
		}
		
		$codigo = $cadastro['codigo'];
		$name = $cadastro["nome_{$tipo}"];
		$email = $cadastro["email_{$tipo}"];
		
		if($tipo == 'aluno')
		{
			$projeto = $cadastro['projeto']['id_projeto'];
			$title = $cadastro['projeto']['titulo'];
		}
		else
		{
			$titulo = $cadastro['doutor'];
			
			$projeto = $cadastro['projeto'];
			
			for($i=0; $i < count($projeto); $i++)
			{
				$title[$i] = $projeto[$i]['titulo'];
				$funcao[$i] = ($projeto[$i]['funcao'] == "orientador") ? 2 : 1;
			}//for
			if(!$i)
				$title[] = "Nenhum";
		}//else
		
		tabela_dados($tipo, $codigo, $name, $email,
					 $title, "", "",
					 "Dados a serem exclu&iacute;dos", $projeto, ($tipo == 'aluno') ? "" : $funcao, $title);		
		
		?>
			<p align="center">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
					<input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
					<input type="hidden" name="action" value="<?php echo $action; ?>_executar">
					<input type="submit" value="Excluir dados">
					<input type="button" value="Cancelar" onClick="javascript:self.close()">
				</form>
			</p>
		<?php
	}//else
}// function confirma_cadastro();



function formulario($action, $tipo, $codigo = '')
{
	if($action == 'alterar')
	{
		if($tipo == 'aluno')
		{
			$aluno = new Info_aluno($codigo,1);
			$cadastro = $aluno->dados_aluno;
			$codigoname = "RA";
		}//if
		else
		{
			$professor = new Info_professor($codigo);
			$cadastro = $professor->dados_professor;
			$codigoname = "c&oacute;digo";
		}//else
		
		$name = $cadastro["nome_{$tipo}"];
		$codigo = $cadastro['codigo'];
		$email = $cadastro["email_{$tipo}"];
		
		if($tipo == 'aluno')
			$projeto = $cadastro['projeto']['id_projeto'];
		else
		{
			$titulo = $cadastro['doutor'];
		}//else
	}//if
	else if ($action == 'corrigir')
	{
		$name = (empty($_POST['newname'])) ? "" : $_POST['newname'];
		
		$codigo = (empty($_POST['newcodigo'])) ? "" : $_POST['newcodigo'];
		
		$email = (empty($_POST['newemail'])) ? "" : $_POST['newemail'];
		
		
		if($tipo == 'aluno')
		{
			$codigoname = "RA";
			if(isset($_POST['newprojeto']))
				$projeto = $_POST['newprojeto'];
		}//if
		else
		{
			$codigoname = "c&oacute;digo";
			$titulo = (empty($_POST['newtitulo'])) ? "1" : $_POST['newtitulo'];
		}
		
		$formeraction = "corrigir";
		$action = substr($_POST['action'], 0, strpos($_POST['action'], "_"));
		
		if($action == 'incluir')
		{
			if(!empty($_POST['newusername']))
				$newusername = $_POST['newusername'];
		}//if
		else
		{
			if(empty($_POST['newname']))
				$name = $_POST['name'];
			if(empty($_POST['newcodigo']))
				$codigo = $_POST['codigo'];
			if(empty($_POST['newemail']))
				$email = $_POST['email'];
		}//if
	}//else
	else
	{
		$codigoname = ($tipo == 'professor') ? "c&oacute;digo" : "RA";
		
		$titulo = $name = $codigo = $email = $projeto = "";
	}//else
	
	?>
	<p align="center" class="title"><?php echo ucfirst($action)." cadastro: ".ucfirst($tipo)." $name"; ?></p>
	<p align="center">
		<form name="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<table align="center" border="0">
				<?php echo "\n";
				if($tipo == "professor")
				{
					?>
					<tr>
						<th align="right">T&iacute;tulo:</th>
						<td align="left">
							<select name="newtitulo">
								<option value="1"<?php echo ($titulo) ? " selected" : ""; ?>>Doutor(a)</option>
								<option value="0"<?php echo (!$titulo) ? " selected" : ""; ?>>-</option>
							</select>
						</td>
					</tr>
					</tr>
					<?php echo "\n";
				}//if
				?>
				<tr>
					<th align="right">Nome:</th>
					<td align="left"><input type="text" name="newname" value="<?php echo $name; ?>" maxlength="50" size="20"></td>
				</tr>
				<tr>
					<th align="right"><?php echo ucfirst($codigoname); ?>:</th>
					<td align="left"><input type="text" name="newcodigo" value="<?php echo $codigo; ?>" maxlength="<?php echo ($tipo == 'aluno') ? "6" : "2"; ?>" size="20"></td>
				</tr>
				<tr>
					<th align="right">E-mail:</th>
					<td align="left"><input type="text" name="newemail" value="<?php echo $email; ?>" maxlength="50" size="20"></td>
				</tr>
				<tr>
					<?php
					if ($tipo == 'aluno')
					{
						?>
						<th align="right">Projeto:</th>
						<td align="left">
							<select name="newprojeto">
							<?php
							$lista = new Info_lista("projetos");
							$projetos = $lista->dados_lista;
							for($i=0;$i < count($projetos);$i++)
							{
								$id = $projetos[$i]['id_projeto'];
								$title = $projetos[$i]['titulo'];
								if($id == $projeto)
									$selected = " selected";
								else
									$selected = "";
								
								?>
								<option value=<?php echo "$id$selected"; ?>><?php echo $title; ?></option>
								<?php echo "\n";
							}
							?>
							</select>
						</td>
						<?php
					}//if
					else
					{
						$lista = new Info_lista("prof_proj", "", $codigo);
						$projetos = $lista->dados_lista;
						
						$numprojetos = count($projetos);
						
						if(!empty($formeraction) && $formeraction == "corrigir")
						{
							if(!empty($_POST['newprojeto']) && !empty($_POST['newfuncao']))
							{
								for($i=0; $i < $numprojetos; $i++)
								{
									$projetos[$i]['cadastrado'] = $_POST['newprojeto'][$i];
									$projetos[$i]['funcao'] = $_POST['newfuncao'][$i];
								}//for
							}//if
						}//if
						
						?>
						<th align="right">Projetos:</th>
						<td align="left">
							<?php echo "\n";
							
							for($i=0;$i < $numprojetos; $i++)
							{
								$title = $projetos[$i]['titulo'];
								$select = $projetos[$i]['cadastrado'];
								$funcao = (empty($projetos[$i]['funcao'])) ? 1 : $projetos[$i]['funcao'];
								
								$checked = ($select) ? " checked" : "";
								?>
								<div>
								<span id="projeto<?php echo $i; ?>" style="display: <?php echo ($select) ? "" : "none"; ?>"><?php echo $title; ?>: </span><span id="avaliador<?php echo $i; ?>" style="display: <?php echo ($select && $funcao == 1) ? "" : "none"; ?>">Avaliador</span><span id="orientador<?php echo $i; ?>" style="display: <?php echo ($select && $funcao == 2) ? "" : "none"; ?>">Orientador</span>
								</div>
								<input type="hidden" name="newprojeto[]" value=<?php echo $select; ?>>
								<input type="hidden" name="newfuncao[]" value="<?php echo $funcao; ?>">
								<?php echo "\n";
							}//for
							?>
							<a href="pop_selecionar_projetos.php" target="_blank">selecionar projetos</a>
						</td>
						<?php
					}//else
					?>
				</tr>
				<?php
				if($action == 'alterar')
				{
					?>
					<tr>
						<td colspan="2">
							<a href="javascript:document.forms['alterar_senha'].submit()">Alterar nome de usu&aacute;rio e senha</a>
						</td>
					</tr>
					<?php
				}
				else
				{
					if(empty($_POST['newusername']))
						$newusername = "";
					?>
					<tr>
						<th align="right">Nome de usu&aacute;rio:</th>
						<td align="left"><input type="text" name="newusername" value="<?php echo $newusername; ?>" maxlength="20" size="20"></td>
					</tr>
					<tr>
						<th align="right">Senha:</th>
						<td align="left"><input type="password" name="newpassword" size="20"></td>
					</tr>
					<tr>
						<th align="right">Confirmar senha:</th>
						<td align="left"><input type="password" name="confirmpassword" size="20"></td>
					</tr>
					<?php
				} //else
				?>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" value="<?php echo ucfirst($action); ?>">
						<input type="button" value="Cancelar" onClick="javascript: self.close();">
					</td>
				</tr>
			</table>
			<?php
			if($action == 'alterar')
			{
				?>
				<input type="hidden" name="name" value="<?php echo $name; ?>">
				<input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
				<input type="hidden" name="email" value="<?php echo $email; ?>">
				<?php
				if($tipo == 'aluno')
				{
				?>
				<input type="hidden" name="projeto" value="<?php echo $projeto; ?>">
				<?php
				}//if
				else
				{
					?>
					<input type="hidden" name="titulo" value="<?php echo $titulo; ?>">
					<?php echo "\n";
					for($i=0; $i < $numprojetos; $i++)
					{
						$select = $projetos[$i]['cadastrado'];
						$funcao = (empty($projetos[$i]['funcao'])) ? 1 : $projetos[$i]['funcao'];
						?>
						<input type="hidden" name="projeto[]" value="<?php echo $select; ?>">
						<input type="hidden" name="funcao[]" value="<?php echo $funcao; ?>">
						<?php
					}//for
				}//else
			}//if
			?>
			<input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
			<input type="hidden" name="action" value="<?php echo $action; ?>_confirmar">
		</form>
		
		<form name="alterar_senha" method="post" action="pop_altera_senha.php" target="altera_senha">
			<input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
		</form>

	</p>
	<?php
}//function formulario();



function main()
{
	if(isset($_POST['action']) && isset($_POST['tipo']))
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
	
	if((strpos($action, "incluir") !== false) || (strpos($action, "alterar") !== false))
	{
		$step = substr($action, strpos($action, "_")+1);
		if(strpos($action, "_"))
			$action = substr($action, 0, strpos($action, "_"));
		
		if(!isset($_POST['codigo']))
			$_POST['codigo'] = "";
		
		switch($step)
		{
			// funções para alterar um cadastro
			case "confirmar":
				html_header("Confirmar dados cadastrais");
				confirmar($action, $_POST['tipo'], $_POST['codigo']);
				html_footer();
				break;
			case "executar":
				html_header("Dados cadastrais alterados");
				executar($action, $_POST['tipo'], $_POST['codigo']);
				recarrega_cadastros($_POST['tipo']);
				html_footer();
				break;
			default:
				html_header("Alterar dados cadastrais");
				formulario($action, $_POST['tipo'], $_POST['codigo']);
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
				executar($action, $_POST['tipo'], $_POST['codigo']);
				recarrega_cadastros($_POST['tipo']);
				html_footer();
				break;
			default:
				html_header("Confirmar exclus&atilde;o de dados");
				confirmar($action, $_POST['tipo'], $_POST['codigo']);
				html_footer();
				break;
		}//switch
	}//else
}//function main();



main();
?>