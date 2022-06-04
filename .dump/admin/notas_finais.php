<?php
include_once("login.php");
include_once("../common/classes_info.php");
include_once("../FPDF/fpdf.php");




function gera_pdf($dados)
{
	$pdf = new FPDF();
	
	$pdf->AddPage();
	$pdf->SetFont('Times','','14');
	
	$pdf->Cell(190,8,"Unidade: INSTITUTO DE BIOCIÊNCIAS, LETRAS E CIÊNCIAS EXATAS",1,1);
	$pdf->SetFontSize('10');
	
	$pdf->Cell(0,2,"",'LR');
	
	$pdf->Ln();
	
	$pdf->Cell(20,4,"Depto.: CCE",'L');
	$pdf->Cell(65,4,"Curso: ".$dados['disciplina']['cod_cursos']);
	$pdf->Cell(20,4,"Emitido em:");
	$pdf->Cell(35,4,$dados['emitido_em']);
	$pdf->Cell(35,4,"Página:",0,0,'R');
	$pdf->Cell(15,4,$dados['pagina'],'R');
	
	$pdf->Ln();
	
	$pdf->Cell(20,4,"Disciplina:",'L');
	$pdf->Cell(65,4,$dados['disciplina']['cod_disciplina']." - Projeto Final");
	$pdf->Cell(20,4,"Turma:");
	$pdf->Cell(35,4,sprintf("%02d",$dados['disciplina']['turma']));
	$pdf->Cell(35,4,"Ano/Semestre:",0,0,'R');
	$pdf->Cell(15,4,sprintf("%d/%d",$dados['disciplina']['ano_letivo'],$dados['disciplina']['semestre_letivo']),'R');
	
	$pdf->Ln();
	
	$pdf->Cell(85,4,"",'L');
	$pdf->Cell(20,4,"C. Horária:");
	$pdf->Cell(35,4,$dados['disciplina']['carga_disciplina']);
	$pdf->Cell(35,4,"Máximo de Faltas:",0,0,'R');
	$pdf->Cell(15,4,sprintf("0%2.1f", $dados['disciplina']['faltas_max']),'R');
	
	$pdf->Ln();
	
	$pdf->Cell(20,4,"Docente:",'L');
	$pdf->Cell(65,4,$dados['disciplina']['responsaveis'][0]['nome']);
	$pdf->Cell(20,4,"Assinatura:");
	$pdf->Cell(35,4,"__________________");
	$pdf->Cell(35,4,"Data da Entrega:",0,0,'R');
	$pdf->Cell(15,4,"__/__/__",'R');
	
	$pdf->Ln();
	
	$pdf->Cell(20,4,"",'L');
	$pdf->Cell(65,4,$dados['disciplina']['responsaveis'][1]['nome']);
	$pdf->Cell(20,4,"");
	$pdf->Cell(35,4,"__________________");
	$pdf->Cell(50,4,"",'R');
	
	$pdf->Ln();
	
	$pdf->Cell(0,2,"",'LBR');
	
	$pdf->Ln();
	
	$pdf->Cell(10,4,"",'LR');
	$pdf->Cell(60,4,"",'LR');
	$pdf->Cell(15,4,"",'LR');
	$pdf->Cell(50,4,"FINAL",'LRB',0,'C');
	$pdf->Cell(20,4,"Recuperação",'LRB',0,'C');
	$pdf->Cell(35,4,"",'LR');
	
	$pdf->Ln();
	
	$pdf->Cell(10,4,"Linha",'LR');
	$pdf->Cell(60,4,"A  L  U  N  O",'LR');
	$pdf->Cell(15,4,"Código",'LR',0,'C');
	$pdf->Cell(10,4,"Linha",'LR');
	$pdf->Cell(10,4,"Faltas",'LR');
	$pdf->Cell(10,4,"Nota",'LR');
	$pdf->Cell(10,4,"Conc.",'LR');
	$pdf->Cell(10,4,"Modo",'LR');
	$pdf->Cell(10,4,"Nota",'LR');
	$pdf->Cell(10,4,"Conc.",'LR');
	$pdf->Cell(35,4,"Observação",'LR');
	
	$pdf->Ln();

	for($i=0; $i < count($dados['alunos']); $i++)
	{
		$pdf->Cell(10,5,sprintf("%03d",$i+1),1,0,'C');
		$pdf->Cell(60,5,$dados['alunos'][$i]['nome_aluno'],1);
		$pdf->Cell(15,5,$dados['alunos'][$i]['codigo'],1,0,'R');
		$pdf->Cell(10,5,sprintf("%03d",$i+1),1,0,'C');
		
		if($dados['conceito'][$i] == "DT")
		{
			$pdf->Cell(10,5,"*****",1,0,'C');
			$pdf->Cell(10,5,"",1,0,'C');
			$pdf->Cell(10,5,$dados['conceito'][$i],1,0,'C');
			$pdf->Cell(10,5,$dados['modo'],1,0,'C');
			$pdf->Cell(10,5,"*****",1,0,'C');
			$pdf->Cell(10,5,"*****",1,0,'C');
			$pdf->Cell(35,5,"Disciplina trancada",1);
		}//if
		else
		{
			$pdf->Cell(10,5,$dados['faltas'][$i],1,0,'C');
			$pdf->Cell(10,5,$dados['notas'][$i],1,0,'C');
			$pdf->Cell(10,5,$dados['conceito'][$i],1,0,'C');
			$pdf->Cell(10,5,$dados['modo'],1,0,'C');
			$pdf->Cell(10,5,"",1,0,'C');
			$pdf->Cell(10,5,"",1,0,'C');
			$pdf->Cell(35,5,$dados['observacao'][$i],1);
		}//else
		
		$pdf->Ln();
	}//for

	//última página
	$pdf->AddPage();

	$pdf->SetFont('Times','',24);
	$pdf->Cell(0,10,"Instruções",0,1,'C');

	$pdf->Ln(5);

	$pdf->SetFontSize(12);
	
	//item 1
	$pdf->Cell(7,5,"1 - ");
	$pdf->MultiCell(0,5,"Entregar esta folha juntamente com as de freqüência na seção de graduação, ".
				   "dentro do prazo estabelecido pelo calendário escolar.");
	
	$pdf->Ln();
	
	//item 2
	$pdf->Cell(7,5,"2 - ");
	$pdf->MultiCell(0,5,"Não incluir nomes de alunos nesta lista.");
	
	$pdf->Ln();

	//item 3
	$pdf->Cell(7,5,"3 - ");
	$pdf->MultiCell(0,5,"Em caso de rasura, rubricar ao lado.");
	
	$pdf->Ln();
	
	//item 4
	$pdf->Cell(7,5,"4 - ");
	$pdf->MultiCell(0,5,"O docente deverá fazer constar nesta folha:");
	
	$pdf->Ln();
	
	//subtabela
	//item 4.1
	$pdf->Cell(7,5,"");
	$pdf->Cell(3,5,"-");
	$pdf->Cell(0,5,"Assinatura");
	
	$pdf->Ln();
	
	//item 4.2
	$pdf->Cell(7,5,"");
	$pdf->Cell(3,5,"-");
	$pdf->Cell(0,5,"Data de entrega");
	
	$pdf->Ln();
	
	//item 4.3
	$pdf->Cell(7,5,"");
	$pdf->Cell(3,5,"-");
	$pdf->Cell(0,5,"Número total de faltas de cada aluno");
	
	$pdf->Ln();
	
	//item 4.4
	$pdf->Cell(7,5,"");
	$pdf->Cell(3,5,"-");
	$pdf->Cell(0,5,"Nota de aproveitamento");
	
	$pdf->Ln();
	
	//item 4.5
	$pdf->Cell(7,5,"");
	$pdf->Cell(3,5,"-");
	$pdf->Cell(0,5,"Conceito:");
	
	$pdf->Ln();
	
	//subtabela
	//item 4.5.1
	$pdf->Cell(7,5,"");
	$pdf->Cell(3,5,"");
	$pdf->Cell(9,5,"AP - ");
	$pdf->Cell(0,5,"Aprovado");
	
	$pdf->Ln();
	
	//item 4.5.2
	$pdf->Cell(7,5,"");
	$pdf->Cell(3,5,"");
	$pdf->Cell(9,5,"RN - ");
	$pdf->Cell(0,5,"Reprovado por nota");
	
	$pdf->Ln();
	
	//item 4.5.3
	$pdf->Cell(7,5,"");
	$pdf->Cell(3,5,"");
	$pdf->Cell(9,5,"RF - ");
	$pdf->Cell(0,5,"Reprovado por faltas (no lugar de nota colocar hífen)");
	
	$pdf->Ln();
	
	//item 4.5.4
	$pdf->Cell(7,5,"");
	$pdf->Cell(3,5,"");
	$pdf->Cell(9,5,"RC - ");
	$pdf->Cell(0,5,"Em recuperação");
	
	$pdf->Ln();
	
	//item 5
	$pdf->Cell(7,5,"5 - ");
	$pdf->MultiCell(0,5,"O número total de faltas do aluno deverá ser necessariamente ".
						"calculado em função do número de aulas previstas, e não em ".
						"função de aulas efetivamente dadas.");
	
	$pdf->Ln();
	
	//item 6
	$pdf->Cell(7,5,"6 - ");
	$pdf->MultiCell(0,5,"O número de faltas das complementações deve ser equivalente à carga horária destas.");
	
	$pdf->Ln(10);
	
	$pdf->SetFontSize(24);
	$pdf->Cell(0,10,"Preenchimento obrigatório",0,1,'C');
	
	$pdf->Ln(5);
	
	$pdf->SetFontSize(12);

	//item 1
	$pdf->Cell(7,5,"1 - ");
	$pdf->MultiCell(0,5,"Nos casos de junção de outra(s) turma(s) com esta disciplina, especificar:");
	
	$pdf->Ln();
	
	//subtabela
	//item 4.1
	for($i=0; $i < 3; $i++)
	{
		$pdf->Cell(7,5,"");
		$pdf->Cell(10,5,"Disc:");
		$pdf->Cell(80,5,"_____________________________________");
		$pdf->Cell(13,5,"Turma:");
		$pdf->Cell(20,5,"_________");
		$pdf->Cell(12,5,"Curso:");
		$pdf->Cell(48,5,"______________________");
		
		$pdf->Ln();
	}//for
	
	$pdf->Ln();
	
	//item 2
	$pdf->Cell(7,5,"2 - ");
	$pdf->MultiCell(0,5,"Esta disciplina foi ministrada integralmente por vossa senhoria?");
	
	$pdf->Ln();
	
	//subtabela
	$pdf->Cell(7,5,"");
	$pdf->Cell(0,5,"(  ) Sim          (  ) Não",0,1);
	$pdf->Cell(7,5,"");
	$pdf->Cell(0,5,"Caso afirmativo, desconsiderar o item abaixo.");
	
	$pdf->Ln(10);
	
	//item 3
	$pdf->Cell(7,5,"3 - ");
	$pdf->MultiCell(0,5,"Nos casos de disciplinas ministradas por mais de um docente, ".
						"registrar abaixo os nomes dos docentes e suas respectivas cargas horárias, ".
						"com visto da chefia do departamento.");
	
	//subtabela
	for($i=0; $i < 3; $i++)
	{
		$pdf->Cell(7,5,"");
		$pdf->Cell(0,5,"_____________________________________________________________________________________");
		$pdf->Ln();
	}//for
	
	$pdf->Cell(7,5,"");
	$pdf->Cell(0,5,"Responsável:_____________________________");
	
	$self = true_self($_SERVER['PHP_SELF'], 1);
	$pdf->Output("$self/admin/arquivos/notas_finais.pdf","F");
}





function gerar_notas_finais()
{
	$disciplina = new Info_disciplina();
	
	$dados = build_notas();
	
	$dados['alunos'] = $dados[0];
	$dados['notas'] = $dados[1];
	$dados['faltas'] = $dados[2];
	$dados['observacao'] = $dados[3];
	$dados['conceito'] = $dados[4];
	
	for($i=0; $i < count($dados['alunos']); $i++)
	{
		$dados['alunos'][$i]['nome_aluno'] = undo_html_entities($dados['alunos'][$i]['nome_aluno']);
		$dados['observacao'][$i] = undo_html_entities($dados['observacao'][$i]);
	}//for
	
	$dados['disciplina'] = $disciplina->dados_disciplina;
	
	$dados['disciplina']['responsaveis'][0]['nome'] = undo_html_entities($dados['disciplina']['responsaveis'][0]['nome']);
	$dados['disciplina']['responsaveis'][1]['nome'] = undo_html_entities($dados['disciplina']['responsaveis'][1]['nome']);
	
	$dados['emitido_em'] = (isset($_POST['emitido_em'])) ? $_POST['emitido_em'] : "";
	$dados['pagina'] = (isset($_POST['pagina'])) ? $_POST['pagina'] : "";
	$dados['modo'] = (isset($_POST['modo'])) ? $_POST['modo'] : "";

	gera_pdf($dados);
}//function






function atualiza_dados($dados)
{
	if(!isset($dados['codigo']))
		die("Dados incorretos!");
	
	for($i=0; $i < count($dados['codigo']); $i++)
	{
		$codigo = $dados['codigo'][$i];
		$old_nota_final = $dados['old_nota_final'][$i];
		$new_nota_final = $dados['new_nota_final'][$i];
		$old_faltas = $dados['old_faltas'][$i];
		$new_faltas = $dados['new_faltas'][$i];
		$old_observacao = $dados['old_observacao'][$i];
		$new_observacao = $dados['new_observacao'][$i];
		$old_disciplina_trancada = $dados['old_disciplina_trancada'][$i];
		$new_disciplina_trancada = $dados['new_disciplina_trancada'][$i];
				
		if(($new_nota_final != $old_nota_final) || ($new_faltas != $old_faltas) || ($new_observacao != $old_observacao) || ($new_disciplina_trancada != $old_disciplina_trancada))
		{
			$dados_validos = 1;
			
			if($new_disciplina_trancada == "1" || $new_nota_final === "" || $new_nota_final === "-")
				$new_nota_final = 0;
			
			if(!is_numeric($new_nota_final) || $new_nota_final < 0 || $new_nota_final > 10)
			{
				$dados_validos = 0;
				echo "<p align=\"center\" class=\"errmsg\">Nota inv&aacute;lida para o(a) aluno(a) com RA $codigo: \"$new_nota_final\". Seus dados n&atilde;o foram atualizados.</p>";
			}//if
			
			if($new_faltas < 0 || $new_faltas > 99)
				$new_faltas = 0;
			
			if($dados_validos)
			{
				$new_observacao = htmlentities($new_observacao, ENT_QUOTES);
				
				$nota_final = new Info_nota_final();
				
				$nota_final->Dados_nota_final($codigo);
				
				$existe = ($nota_final->dados_nota_final['nota_final'] !== 0);
				
				if(!$existe)
					$nota_final->inserir_nota_final($codigo, $new_nota_final, $new_faltas, $new_observacao, $new_disciplina_trancada);
				else
					$nota_final->alterar_nota_final($codigo, $new_nota_final, $new_faltas, $new_observacao, $new_disciplina_trancada);
			}//if
		}//if
	}//for
}







function build_notas()
{
	$nota_final = new Info_nota_final();
	
	$lista = new Info_lista("alunos", "nome_aluno");
	
	$aluno = $lista->dados_lista;

	for($i=0; $i < count($aluno); $i++)
	{
		$projeto = $aluno[$i]['projeto']['id_projeto'];
		$codigo = $aluno[$i]['codigo'];
		
		$nota_final->Processa($projeto);
		
		$k=0;
		
		while($nota_final->dados_nota_final['alunos'][$k]['codigo'] != $codigo)
			$k++;
		
		$dados = NULL;
		$dados = $nota_final->dados_nota_final['alunos'][$k];
		
		$nota[$i] = $dados['nota'];
		$faltas[$i] = $dados['faltas']+0;
		$observacao[$i] = $dados['observacao'];
		$conceito[$i] = $dados['conceito'];
	}//for
	
	//caso ainda não hajam cadastros
	if(!$i)
		$nota = $faltas = $observacao = $conceito = array();
		
	return array($aluno, $nota, $faltas, $observacao, $conceito);
}//function build_notas();



function list_notas($aluno, $nota, $faltas, $observacao, $conceito)
{
	?>
	<p align="center" class="title">Notas Finais:</p>
	<p>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table border="0" align="center">
		<tr>
			<td>
				<input type="submit" value="Gerar relat&oacute;rio/Atualizar dados">
			</td>
			<td align="right">
				<a href="<?php echo true_self($_SERVER['PHP_SELF'])."/admin/arquivos/notas_finais.pdf"; ?>"
				   onMouseOver="javascript:window.status='Visualizar relat&oacute;rio de notas finais'; return true"
				   onMouseOut="javascript:window.status=''; return true"
				   target="_blank"><font class="subtitle">Visualizar</font></a>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<table border="1" align="center">
				<tr>
					<th colspan="8">Cabe&ccedil;alho</th>
				</tr>
				<tr>
					<td colspan="8">
						<table border="0" width="100%">
						<tr>
							<th align="left">Emitido em:<input type="text" name="emitido_em"></th>
							<th align="left">Modo:<input type="text" name="modo" maxlength="2" size="3">
							<th align="right">P&aacute;gina:<input type="text" name="pagina"></th>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<th>Disc.<br />Trancada</th>
					<th>Linha</th>
					<th>Aluno</th>
					<th>C&oacute;digo</th>
					<th>Faltas</th>
					<th>Nota</th>
					<th>Conceito</th>
					<th>Observa&ccedil;&atilde;o</th>
					<!--<th>A&ccedil;&atilde;o</th>-->
				</tr>
				<?php echo "\n";
				for($i=0; $i < count($conceito); $i++)
				{
					?>
					<tr>
						<td>
							<select name="new_disciplina_trancada[]">
								<option value="0"<?php echo ($conceito[$i] != "DT") ? " selected" : ""; ?>>N&atilde;o</option>
								<option value="1"<?php echo ($conceito[$i] == "DT") ? " selected" : ""; ?>>Sim</option>
							</select>
							<input type="hidden" name="old_disciplina_trancada[]" value="<?php echo ($conceito[$i] == "DT"); ?>">
						</td>
						<td><?php echo $i+1; ?></td>
						<td><?php echo $aluno[$i]['nome_aluno']; ?></td>
						<td>
							<?php echo $aluno[$i]['codigo']; ?>
							<input type="hidden" name="codigo[]" value="<?php echo $aluno[$i]['codigo']; ?>">
						</td>
						<?php echo "\n";
						if($conceito[$i] == "DT")
						{
							?>
							<td>*****
								<input type="hidden" name="new_faltas[]" value="<?php echo $faltas[$i]; ?>">
								<input type="hidden" name="old_faltas[]" value="<?php echo $faltas[$i]; ?>">
							</td>
							<td>*****
								<input type="hidden" name="new_nota_final[]" value="0">
								<input type="hidden" name="old_nota_final[]" value="0">
							</td>
							<td><?php echo $conceito[$i]; ?></td>
							<td>Disciplina Trancada
								<input type="hidden" name="new_observacao[]" value="<?php echo $observacao[$i]; ?>">
								<input type="hidden" name="old_observacao[]" value="<?php echo $observacao[$i]; ?>">
							</td>
							<?php echo "\n";
						}//if
						else
						{
							?>
							<td>
								<input type="text" name="new_faltas[]" value="<?php echo $faltas[$i]; ?>" maxlength="3" size="3">
								<input type="hidden" name="old_faltas[]" value="<?php echo $faltas[$i]; ?>">
							</td>
							<td>
								<input type="text" name="new_nota_final[]" value="<?php echo $nota[$i]; ?>" maxlength="5" size="4">
								<input type="hidden" name="old_nota_final[]" value="<?php echo $nota[$i]; ?>">
							</td>
							<td><?php echo $conceito[$i]; ?></td>
							<td>
								<input type="text" name="new_observacao[]" value="<?php echo $observacao[$i]; ?>" size="20">
								<input type="hidden" name="old_observacao[]" value="<?php echo $observacao[$i]; ?>">
							</td>
							<?php echo "\n";
						}//else
						?>
						<!--
						<?php echo "\n";
						if(!$i)
						{
							?>
							<td rowspan="<?php echo count($conceito)+1; ?>" valign="top">
								<br>
								<input type="submit" value="Gerar/Atualizar">
							</td>
							<?php echo "\n";
						}//if
						?>
						-->
					</tr>
					<?php echo "\n";
				}//for
				?>
			</table>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value="Gerar relat&oacute;rio/Atualizar dados">
			</td>
			<td align="right">
				<a href="<?php echo true_self($_SERVER['PHP_SELF'])."/admin/arquivos/notas_finais.pdf"; ?>"
				   onMouseOver="javascript:window.status='Visualizar relat&oacute;rio de notas finais'; return true"
				   onMouseOut="javascript:window.status=''; return true"
				   target="_blank"><font class="subtitle">Visualizar</font></a>
			</td>
		</tr>
		</table>
		</form>
	</p>
	<?php echo "\n";
}//function list_projetos();



function main()
{
	html_header("Notas Finais");
	common_header('notas_finais');
	
	if(!empty($_POST))
	{
		atualiza_dados($_POST);
		gerar_notas_finais();
	}
	
	$dados = build_notas();
	list_notas($dados[0], $dados[1], $dados[2], $dados[3], $dados[4]);
	common_footer();
	html_footer();
}

main();
?>
