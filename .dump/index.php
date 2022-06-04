<?php
include_once("login.php");
include_once("common/classes_info.php");








function list_dates($tipo)
{
	if(($tipo == "aluno") && ($_SESSION['tipo'] == "aluno"))
	{
		$nao_entregue = 0;
		$dados = array();
		
		$capitulos = new Info_capitulos($_SESSION['id_projeto']);
		
		for($i=0; $i < count($capitulos->dados_capitulos['capitulos']); $i++)
		{
			$capitulo = $capitulos->dados_capitulos['capitulos'][$i];
			$tipo_capitulo = $capitulo['tipo'];
			
			$dados['capitulos'][$tipo_capitulo][$i] = $capitulo;
			$dados['capitulos'][$tipo_capitulo][$i]['entregue'] = $capitulos->dados_capitulos['entregas'][$i]['entregue'];
			unset($capitulo);
		}//for
		
		$dados['penalidades'] = $capitulos->dados_capitulos['penalidades'];
		
		for($i=0; $i < count($capitulos->dados_capitulos['entregas']); $i++)
		{
			if($capitulos->dados_capitulos['entregas'][$i]['tipo'] == "aluno" && !$capitulos->dados_capitulos['entregas'][$i]['entregue'])
			{
				if($nao_entregue == 0)
				{
					$nao_entregue = 1;
					break;
				}
			}//if
		}//for
	}//if
	else
	{
		$capitulos = new Info_capitulos();
		$capitulos->Processa();
		
		for($i=0; $i < count($capitulos->dados_capitulos['capitulos']); $i++)
		{
			$capitulo = $capitulos->dados_capitulos['capitulos'][$i];
			$dados['capitulos'][$capitulo['tipo']][] = $capitulo;
			unset($capitulo);
		}
	}//else
	
	?>
	<p>
		<table border="1">
			<tr>
				<th>Nome do cap&iacute;tulo</th>
				<th>Data de entrega</th>
				<?php echo "\n";
				if(($tipo == "aluno") && ($_SESSION['tipo'] == "aluno"))
				{
					?>
					<th>Penalidade</th>
					<?php echo "\n";
				}//if
				?>
			</tr>
			<?php echo "\n";
			for($i=0; $i < count($dados['capitulos'][$tipo]); $i++)
			{
				$capitulo = $dados['capitulos'][$tipo][$i];
				
				?>
				<tr>
					<td><?php echo $capitulo['legenda_capitulo']; ?></td>
					<td><?php echo date("d/m/Y H:i:s", $capitulo['data_entrega']); ?></td>
					<?php echo "\n";
					if(($tipo == "aluno") && ($_SESSION['tipo'] == "aluno"))
					{
						$entregue = $capitulo['entregue'];
						
						if(!is_numeric($dados['penalidades'][$i]))
							$penalidade = "Ok";
						else
						{
							if(!$entregue)
							{
								if($dados['penalidades'][$i] == 0)
									$penalidade = "-*";
								else
									$penalidade = "-".$dados['penalidades'][$i]."*";
							}//if
							else
							{
								if($dados['penalidades'][$i] == 0)
									$penalidade = "Ok";
								else
									$penalidade = "-".$dados['penalidades'][$i];
							}//else
						}//if
						?>
						<td><?php echo $penalidade; ?></td>
						<?php echo "\n";
					}//if
					?>
				</tr>
				<?php echo "\n";
			}//for
			
			if(($tipo == "aluno") && ($_SESSION['tipo'] == "aluno") && ($i > 0))
			{
				?>
				<tr>
					<td colspan="2"><b>Pontos perdidos</b></td>
					<td>-<?php echo (array_sum($dados['penalidades'])) ? array_sum($dados['penalidades']) : ""; ?></td>
				</tr>
				<?php echo "\n";
			}//if
			?>
		</table>
	</p>
	<?php echo "\n";
	if(isset($nao_entregue) && $nao_entregue)
	{
		?>
		<p>(*) N&atilde;o foi entregue at&eacute; o presente momento</p>
		<?php echo "\n";
	}//if
}//function list_dates()








function custom_header()
{
	?>
	<tr><td align="center">
		<a href="upload.php"><font style="font-size: 14px; font-weight: bold;"><img src="images/filefolder.jpg" border="0">Enviar arquivos</font></a>
		<?php echo "\n";
		if($_SESSION['tipo'] == 'professor')
		{
			?>
			| 
			<a href="javascript:toggle_fontSize()"><img src="images/magnify.gif" border="0"><font style="font-size: 14px; font-weight: bold;">Aumentar/Diminuir tamanho do texto</font></a>
			<?php echo "\n";
		}//if
		?>
		| <a href="javascript:session_close()"><img src="images/error.gif" border="0"><font style="font-size: 14px; font-weight: bold;">Encerrar sess&atilde;o</font></a>
	</td>
	</tr>
	<?php echo "\n";
}//function custom_header();









function list_chapters($projeto, $controle)
{
	if($_SESSION['tipo'] == "professor")
		$professor = new Info_professor($_SESSION['codigo'], $projeto);
	
	$capitulos = new Info_capitulos($projeto);
	
	for($i=0; $i < count($capitulos->dados_capitulos['capitulos']); $i++)
	{
		if($capitulos->dados_capitulos['capitulos'][$i]['tipo'] == $controle)
			$id_capitulos[] = $i;
	}
	
	for($i=0; $i < count($capitulos->dados_capitulos['entregas']); $i++)
	{
		if(in_array($capitulos->dados_capitulos['entregas'][$i]['id_capitulo']-1, $id_capitulos))
			$id_entregas[] = $i;
	}
	
	$arquivo_entregue = 0;
	
	foreach($id_entregas as $value)
	{
		if($capitulos->dados_capitulos['entregas'][$value]['entregue'])
		{
			$arquivo_entregue = 1;
			break;
		}//if
	}
	
	if($arquivo_entregue)
	{
		if($controle == "professor" || ($controle == "aluno" && (isset($professor) && $professor->dados_professor['funcao'] == "avaliador")) || $_SESSION['tipo'] == "aluno")
		{
			?>
			<table border="0">
				<tr>
					<td align="left">
						<?php echo "\n";
						foreach($id_entregas as $value)
						{
							$entrega = $capitulos->dados_capitulos['entregas'][$value];
							
							if($entrega['tipo'] == "professor")
							{
								if($entrega['data_avaliacao'] > $capitulos->dados_capitulos['capitulos'][$entrega['id_capitulo']-1]['data_entrega'])
									$atrasado = 1;
								else
									$atrasado = 0;
							}//if
							else if($entrega['tipo'] == "aluno")
							{
								$penalidade = $capitulos->dados_capitulos['penalidades'][$entrega['id_capitulo']-1];
								
								if($penalidade != "No" && $penalidade != "-")
									$atrasado = ($penalidade > 0);
								else
									$atrasado = 0;
							}//else
							
							if($entrega['entregue'])
							{
								$friendly = $capitulos->dados_capitulos['capitulos'][$entrega['id_capitulo']-1]['legenda_capitulo'];
								?>
								<li><a href="<?php echo $entrega['nome_arquivo']['remoto']; ?>" target="_blank">
									<?php echo "\n";
									if (($atrasado) && (($_SESSION['tipo'] == 'professor' && $_SESSION['codigo'] == $entrega['codigo']) || $controle == "aluno"))
									{
										?>
										[<font class="data_erro"><?php echo date("d/m/Y H:i:s", $entrega[($entrega['tipo'] == 'aluno') ? 'data_submissao' : 'data_avaliacao']); ?></font>]
										<?php echo "\n";
									}//if
									else
									{
										?>
										[<b><?php echo date("d/m/Y H:i:s", $entrega[($entrega['tipo'] == 'aluno') ? 'data_submissao' : 'data_avaliacao']); ?></b>]
										<?php echo "\n";
									}//else
									?>
									<b><?php echo $friendly; ?></b>
								</a></li>
								<?php echo "\n";
							}//if
							
							unset($entrega);
						}//foreach
						?>
					</td>
				</tr>
			</table>
			<?php echo "\n";
		}//if
		else
		{
			?>
			<table border="1" align="center">
				<tr>
					<th>Penalidade</th>
					<th>Data de Entrega</th>
					<th>Cap&iacute;tulo</th>
				</tr>
				<?php echo "\n";
				for($i=0; $i < count($capitulos->dados_capitulos['penalidades']); $i++)
				{
					$capitulo = $capitulos->dados_capitulos['capitulos'][$i];
					$penalidade = $capitulos->dados_capitulos['penalidades'][$i];
					$entrega = $capitulos->dados_capitulos['entregas'][$i];
					if($entrega['tipo'] == 'aluno')
						$entrega['data_submissao'] = date("d/m/Y H:i:s", $entrega['data_submissao']);
					
					if($entrega['entregue'])
					{
						?>
						<tr>
							<td><?php echo ($penalidade) ? (($penalidade == "No") ? "Ok" : $penalidade) : "Ok"; ?></td>
							<td>
								<?php echo "\n";
								if($penalidade != "No" && $penalidade != "-")
								{
									?>
									<b><font class="data_erro"><?php echo $entrega['data_submissao']; ?></font></b>
									<?php echo "\n";
								}
								else
									echo $entrega['data_submissao'];
								?>
							</td>
							<td>
								<b><a href="<?php echo $entrega['nome_arquivo']['remoto']; ?>" target="_blank"><?php echo $capitulo['legenda_capitulo']; ?></a></b>
							</td>
						</tr>
						<?php echo "\n";
					}//if
				}//for
				?>
				<tr>
					<td><?php echo array_sum($capitulos->dados_capitulos['penalidades']); ?></td>
					<td colspan="2" align="center"><b>Pontos Perdidos (incluindo cap&iacute;tulos n&atilde;o entregues)</b></td>
				</tr>
			</table>
			<?php echo "\n";
		}//else
	}//if
	else
	{
		?>
		<p>Nenhum arquivo foi enviado at&eacute; o presente momento.</p>
		<?php echo "\n";
	}//else
}











function mount_table($alunos)
{
	?>
<p>
	<table border="1">
		<tr>
			<th>RA</th>
			<th>Nome</th>
			<th>E-mail</th>
		</tr>
	<?php echo "\n";
	for($i=0; $i < count($alunos); $i++)
	{
		$codigo = $alunos[$i]['codigo'];
		$name = $alunos[$i]['nome_aluno'];
		$email = $alunos[$i]['email_aluno'];
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
</p>
	<?php echo "\n";
} //function mount_table()



function list_projetos($codigo, $tipo, $funcao)
{
	// projetos dos quais o docente faz parte
	$professor = new Info_professor($codigo);
	
	$projetos = array();
	
	for($i=0; $i < count($professor->dados_professor['projeto']); $i++)
	{
		if($professor->dados_professor['projeto'][$i]['funcao'] == $funcao)
			$projetos[] = $professor->dados_professor['projeto'][$i];
	}//for
	
	$defesa = new Info_defesa();
	$projeto = new Info_projeto();
	
	for($i=0; $i < count($projetos); $i++)
	{
		$id_projeto = $projetos[$i]['id_projeto'];
		$titulo = $projetos[$i]['titulo'];
		
		$defesa->Processa($id_projeto);
		$projeto->Processa($id_projeto);
		
		// alunos que fazem parte do projeto selecionado
		$alunos = $projeto->dados_projeto['alunos'];
		?>
		<p class="subtitle"><?php echo $titulo; ?></p>
		<p><b>Aluno(s):</b></p>
		<?php echo "\n";
		mount_table($alunos);
		?>
		<p><b>Defesa:</b></p>
		<table align="center" border="1">
			<tr>
				<th>Data</th>
				<th>Local</th>
				<?php echo "\n";
				if($funcao == "orientador")
				{
					?>
					<th>Ata</th>
					<?php echo "\n";
				}//if
				?>
			</tr>
			<tr>
				<?php echo "\n";
				if(!$defesa->dados_defesa)
				{
					?>
					<td colspan="3">Data e local indefinidos</td>
					<?php echo "\n";
				}
				else
				{
					?>
					<td><?php echo date("d/m/Y H:i:s", $defesa->dados_defesa['data_defesa']); ?></td>
					<td><?php echo $defesa->dados_defesa['local']; ?></td>
					<?php echo "\n";
					if($funcao == "orientador")
					{
						?>
						<td>
							<a href="<?php echo true_self($_SERVER['PHP_SELF'])."/arquivos/$id_projeto/relatorio_ata.pdf"; ?>"
							   onMouseOver="javascript:window.status='Imprimir relat&oacute;rio de ata'; return true"
							   onMouseOut="javascript:window.status=''; return true"
							   target="_blank">Visualizar</a>
						</td>
						<?php echo "\n";
					}//if
				}//else
				?>
			</tr>
		</table>
		<p><b>Arquivos entregues pelos avaliadores:</b></p>
		<?php echo "\n"; list_chapters($id_projeto, "professor"); ?>
		<p><b>Arquivos entregues pelo(s) aluno(s):</b></p>
		<?php echo "\n";
		if($funcao == "avaliador" || 1)
			list_chapters($id_projeto, "aluno");
		else
		{
			$capitulos = new Info_capitulos($id_projeto);
			?>
			<table border="1" align="center">
				<tr>
					<th>Penalidade</th>
					<th>Data de Entrega</th>
					<th>Cap&iacute;tulo</th>
				</tr>
				<?php echo "\n";
				for($j=0; $j < count($capitulos->dados_capitulos['penalidades']); $j++)
				{
					$capitulo = $capitulos->dados_capitulos['capitulos'][$j];
					$penalidade = $capitulos->dados_capitulos['penalidades'][$j];
					$entrega = $capitulos->dados_capitulos['entregas'][$j];
					$entrega['data_submissao'] = date("d/m/Y H:i:s", $entrega['data_submissao']);
					
					if($entrega['entregue'])
					{
						?>
						<tr>
							<td><?php echo ($penalidade) ? $penalidade : "Ok"; ?></td>
							<td>
								<?php echo "\n";
								if($penalidade)
								{
									?>
									<b><font class="data_erro"><?php echo $entrega['data_submissao']; ?></font></b>
									<?php echo "\n";
								}
								else
									echo $entrega['data_submissao'];
								?>
							</td>
							<td>
								<b><a href="<?php echo $entrega['nome_arquivo']['remoto']; ?>" target="_blank"><?php echo $capitulo['legenda_capitulo']; ?></a></b>
							</td>
						</tr>
						<?php echo "\n";
					}//if
				}//for
				?>
				<tr>
					<td><?php echo array_sum($capitulos->dados_capitulos['penalidades']); ?></td>
					<td colspan="2" align="center"><b>Pontos Perdidos</b></td>
				</tr>
			</table>
			<?php echo "\n";
		}//else
		?>
		<br>
		<br>
		<br>
		<?php echo "\n";
	} //for;
} //function list_projetos()



function main()
{
	html_header("P&aacute;gina principal");
	
	if(isset($_SESSION))
	{
		$username = $_SESSION['username'];
		$tipo = $_SESSION['tipo'];
		$codigo = $_SESSION['codigo'];
		$nome = $_SESSION['nome'];
		if($tipo == "aluno")
			$id_projeto = $_SESSION['id_projeto'];
	}//if
	
	if(isset($username))
	{
		if($tipo == 'aluno')
		{
			?>
			<center>
			<?php echo "\n";
		}//if
		?>
		<table border="0">
		<tr>
		<?php echo "\n";
		if($tipo == 'professor')
		{
			?>
			<td rowspan="4" width="20%" valign="top">
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<table border="0" cellpadding="5" class="menu">
					<tr><td><a href="#cronograma">Cronograma</a></td></tr>
					<tr><td><a href="#avaliados">Projetos avaliados</a></td></tr>
					<tr><td><a href="#orientados">Projetos orientados</a></td></tr>
				</table>
			</td>
			<?php echo "\n";
		}//if
		?>
		<td height="150" valign="top" align="center">
			<img src="images/logo.gif">
			<p class="title">Bem vindo(a), <?php echo $tipo; ?>(a) <?php echo $nome; ?>!</p>
		</td>
		</tr>
		<?php echo "\n";
		custom_header();
		?>
		<tr>
		<td align="center">
		<img src="images/hr.gif" width="90%">
		<p class="title2"><a name="cronograma">Cronograma</a></p>
		<?php echo "\n";
		if($tipo == 'professor')
		{
			?>
			<p class="subtitle">Avaliador</p>
			<?php list_dates("professor"); ?>
			<p class="subtitle">Aluno</p>
			<?php echo "\n";
			list_dates("aluno");
		}//if
		else
		{
			list_dates("aluno");
			?>
			<p class="title2">Defesa</p>
			<?php echo "\n";
			$defesa = new Info_defesa($id_projeto);
			?>
			<table align="center" border="1">
				<tr>
					<th>Data</th>
					<th>Local</th>
				</tr>
				<tr>
				<?php echo "\n";
				if(!$defesa->dados_defesa)
				{
					?>
					<td colspan="2">Data indefinida</td>
					<?php echo "\n";
				}
				else
				{
					?>
					<td><?php echo date("d/m/Y H:i:s", $defesa->dados_defesa['data_defesa']); ?></td>
					<td><?php echo $defesa->dados_defesa['local']; ?></td>
					<?php echo "\n";
				}//else
				?>
				</tr>
			</table>
			<br>
		<?php echo "\n";
		}//else
		
		if($tipo == 'professor')
		{
			//liste os projetos
			?>
			<p align="right"><a href="#">Voltar ao topo</a></p>
			<img src="images/hr.gif" width="90%">
			<p class="title2"><a name="avaliados">Projetos avaliados por voc&ecirc;:</a></p>
			<?php echo "\n";
			list_projetos($codigo, $tipo, "avaliador");
			?>
			<p align="right"><a href="#">Voltar ao topo</a></p>
			<img src="images/hr2.gif" width="80%">
			<p class="title2"><a name="orientados">Projetos orientados por voc&ecirc;:</a></p>
			<?php echo "\n";
			list_projetos($codigo, $tipo, "orientador");
			?>
			<p align="right"><a href="#">Voltar ao topo</a></p>
			<img src="images/hr.gif" width="90%">
			<?php echo "\n";
		} //if;
		else
		{
			?>
			<img src="images/hr.gif" width="90%">
			<p class="title2">Informa&ccedil;&otilde;es sobre o seu projeto:</p>
			<p><b>Arquivos entregues pelos avaliadores:</b></p>
			<?php list_chapters($id_projeto, "professor"); ?>
			<p><b>Arquivos entregues pelo(s) aluno(s):</b></p>
			<?php list_chapters($id_projeto, "aluno"); ?>
			<img src="images/hr.gif" width="90%">
			<?php echo "\n";
		} //else;
	} //if
	
	?>
	</td>
	</tr>
	<?php echo "\n";
	custom_header();
	?>
	</table>
	<?php echo "\n";
	if($tipo == 'aluno')
	{
		?>
		</center>
		<?php echo "\n";
	}//if
	html_footer();
} //function main()

main();
?>
