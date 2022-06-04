<?php
include_once("login.php");
include_once("../common/classes_info.php");
include_once("../common/pdf_html.php");






//$dados deve ser um array com a seguinte estrutura de dados:

//$dados['responsaveis'][1..m] = array com os nomes dos responsáveis pela disciplina

//$dados[1..n] = array com a seguinte estrutura de dados:

//$dados[1..n]['professor'] = nome do professor cujas informações se referem

//$dados[1..n]['projeto'] = nome do projeto do qual o professor faz parte

//$dados[1..n]['alunos'][1..j]['nome_aluno'] = nome dos alunos que fazem parte do projeto

//$dados[1..n]['date'] = data de defesa do projeto

//$dados[1..n]['banca']['orientador'][1..p]['nome_professor'] = nome do orientador da banca

//$dados[1..n]['banca']['avaliador'][1..k]['nome_professor'] = nome dos avaliadores da banca, array contendo a mesma estrutura de dados do orientador da banca

function gera_pdf($dados, $funcao)
{
	$pdf = new PDF_HTML('L');
	
	$instru_caption = "Instruções";
	
	$instructions = "        Este documento contém os atestados dos docentes ${funcao}es ";
	$instructions .= "que estavam presentes na apresentação da monografia de projeto final ";
	$instructions .= "do curso de Bacharelado em Ciência da Computação. A impressão do mesmo ";
	$instructions .= "deverá ser feita na forma \"frente e verso\", uma vez que no verso constam ";
	$instructions .= "instruções relativas à banca examinadora.";
	
	$instructions_page2 = "Esta página foi intencionalmente deixada em branco.";
	
	
	$header = "Universidade Estadual Paulista\n";
	$header .= "Instituto de Biociências, Letras e Ciências Exatas\n";
	$header .= "Campus de São José do Rio Preto\n";
	$header .= "Departamento de Ciências de Computação e Estatística";
	
	$caption = "ATESTADO";
	
	$responsaveis = $dados['responsaveis'];
	
	$texto_final = "Professores da disciplina Projeto Final";
	
	//====================================
	//aqui começa a geração do arquivo PDF
	//====================================
	
	//============================
	//    página de instruções
	//============================
	$pdf->AddPage();
	
	$pdf->SetFont('Times','B','18');
	$pdf->Cell(0,20,$instru_caption,0,1,'C');
	
	$pdf->SetFont('Times','',12);
	$pdf->MultiCell(0,6,$instructions);
	
	//============================
	//       segunda página
	//============================
	$pdf->AddPage();
	$pdf->Cell(0,10,$instructions_page2,0,1);
	
	//======================
	//término das instruções
	//======================
	
	for($i=0; $i < count($dados)-1; $i++)
	{
		$professor = $dados[$i]['professor'];
		
		$projeto = $dados[$i]['projeto'];
		
		$alunos_temp = $dados[$i]['alunos'];
		
		$alunos = "<b>".$alunos_temp[0]."</b>";
		
		for($j=1; $j < count($alunos_temp); $j++)
		{
			if($j+1 < count($alunos_temp))
				$alunos .= ", <b>".$alunos_temp[$j]."</b>";
			else
				$alunos .= " e <b>".$alunos_temp[$j]."</b>";
		}//for
		
		$date = $dados[$i]['defesa'];
		
		if(count($alunos_temp)-1)
		{
			$pelos_alunos = "pelos alunos";
			$os_alunos = "os alunos";
		}//else
		else
		{
			$pelos_alunos = "pelo(a) aluno(a)";
			$os_alunos = "o(a) aluno(a)";
		}//else
		
		if($funcao == "avaliador")
		{
			$texto = "        Atestamos que <b>$professor</b> participou da banca examinadora ";
			$texto .= "da monografia de projeto final do curso de Bacharelado em Ciência da Computação, ";
			$texto .= "sob o título <b>$projeto</b>, apresentada $pelos_alunos $alunos nesta data.";
		}
		else
		{
			$texto = "        Atestamos que <b>$professor</b> orientou $os_alunos $alunos ";
			$texto .= "na realização de sua monografia de projeto final do curso de ";
			$texto .= "Bacharelado em Ciência da Computação, sob o título <b>$projeto</b>, ";
			$texto .= "apresentada para banca examinadora nesta data.";
		}//else
		
		
		//===========================
		//    início dos atestados
		//===========================
		$pdf->AddPage();
		
		$pdf->Image("images/certificado.jpg",10,10);
		
		//Logotipo da UNESP
		//$pdf->Image("images/logo.png",61,17,35);
		
		$pdf->SetY(22.7);
		$pdf->SetFont("Times","B",18);
		$pdf->MultiCell(0,7,$header,0,"C");
		
		$pdf->SetY(67);
		$pdf->SetFontSize(24);
		$pdf->Cell(0,10,$caption,0,1,"C");
		
		$pdf->SetY(89);
		$pdf->SetLeftMargin(54);
		$pdf->SetRightMargin(50);
		$pdf->SetFont('Times','',16);
		$pdf->WriteHTML($texto,7);
		$pdf->Ln();
		$pdf->Cell(0,6,"",0,1);
		
		$pdf->Cell(0,7,$date,0,1);
		
		$pdf->Cell(0,6,"",0,1);
		$pdf->Cell(0,6,"",0,1);
		
		$pdf->SetFontSize(14);
		$pdf->SetX(154);
		$pdf->SetY(149);
		$pdf->Cell(100,7,$responsaveis[0]);
		$pdf->Cell(0,7,$responsaveis[1],0,1,'R');
		$pdf->Cell(0,7,$texto_final,0,1,'C');
		
		$pdf->SetMargins(10,10);
		
		
		//=====================================
		//              página 2
		//=====================================
		
		$pdf->AddPage();
		
		$pdf->SetFont('Times','B','16');
		$pdf->Cell(0,10,'Banca Examinadora:',0,1);
		
		
		$pdf->SetFontSize('14');
		
		//presidente da banca (caso o projeto não seja aprovado, não aparecerá esse nome);
		for($j=0; $j < count($dados[$i]['banca']['orientador']); $j++)
		{
			$pdf->SetFont('Times','B');
			$pdf->Cell(45,10,'Presidente da banca:');
			$pdf->SetFont('');
			$pdf->Cell(90,10,$dados[$i]['banca']['orientador'][0],0,1);
		}//for
		
		//membros da banca
		for($j=0; $j < count($dados[$i]['banca']['avaliador']); $j++)
		{
			//membro da banca
			$pdf->SetFont('Times','B');
			$pdf->Cell(45,10,'Membro da banca:');
			$pdf->SetFont('');
			$pdf->Cell(90,10,$dados[$i]['banca']['avaliador'][$j],0,1);
		}//for
		
		$pdf->Cell(0,20,"",0,1);
		
	}//for
	
	$self = true_self($_SERVER['PHP_SELF'], 1);
	
	$filename = "$self/admin/arquivos/atestado_banca_${funcao}es.pdf";
	
	$pdf->Output($filename, "F");
}//function gera_pdf();






function gera_atestados($funcao)
{
	$disciplina = new Info_disciplina();
	
	for($i=0; $i < count($disciplina->dados_disciplina['responsaveis']); $i++)
	{
		$responsavel = $disciplina->dados_disciplina['responsaveis'][$i];
		$dados['responsaveis'][$i] = ($responsavel['titulo']) ? "Prof(a). Dr(a). " : "Prof(a). ";
		$dados['responsaveis'][$i] .= undo_html_entities($responsavel['nome']);
		unset($responsavel);
	}
	
	//para gerar a data
	$meses = array(1 => "janeiro", "fevereiro", "março", "abril", "maio", "junho",
				   "julho", "agosto", "setembro", "outubro", "novembro", "dezembro");
	
	
	$projeto = new Info_projeto();
	$defesa = new Info_defesa();
	$nota_final = new Info_nota_final();
	
	$num_projetos = $projeto->num_projetos();
	
	//zerar a variável de contagem
	$k=0;
	
	for($i=0; $i < $num_projetos; $i++)
	{
		$id = $i+1;
		
		$projeto->Processa($id,1);
		$nota_final->Processa($id);
		$defesa->Processa($id);
		
		
		if(($conceito = $nota_final->dados_nota_final['alunos'][0]['conceito']) !== "AP")
		{
			//habilitar o próximo comando faz o orientador do projeto reprovado ser creditado como avaliador
			/*
			if($conceito === "RN")
			{
				while(($temp = array_shift($projeto->dados_projeto['banca']['orientador'])) !== NULL)
					array_unshift($projeto->dados_projeto['banca']['avaliador'], $temp);
			}
			*/
			//o próximo comando controla a geração de atestados para projetos cujos alunos tenham sido reprovados
			if($conceito == "DT")
				continue;
		}//if
		
		$temp = NULL;
		
		for($j=0; $j < count($projeto->dados_projeto['banca'][$funcao]); $j++)
		{
			$temp = $projeto->dados_projeto['banca'][$funcao][$j];
			
			$doutor = $projeto->dados_projeto['professores'][$temp]['doutor'];
			
			$temp = $projeto->dados_projeto['professores'][$temp]['nome_professor'];
			
			$nomes_organizar[] = $temp;
			
			$temp = ($doutor) ? $temp = "Prof(a). Dr(a). ".$temp : "Prof(a). ".$temp;
			
			//nome do professor
			$dados[$k]['professor'] = undo_html_entities($temp);
			
			//título do projeto
			$dados[$k]['projeto'] = undo_html_entities($projeto->dados_projeto['titulo']);
			
			//nomes dos alunos
			for($m=0; $m < count($projeto->dados_projeto['alunos']); $m++)
				$dados[$k]['alunos'][$m] = undo_html_entities($projeto->dados_projeto['alunos'][$m]['nome_aluno']);
			
			//nomes da banca
			for($m=0; $m < count($projeto->dados_projeto['banca']['orientador']); $m++)
			{
				$temp = $projeto->dados_projeto['banca']['orientador'][$m];
				$dados[$k]['banca']['orientador'][$m] = undo_html_entities($projeto->dados_projeto['professores'][$temp]['nome_professor']);
			}
			
			if(!$m)
				$dados[$k]['banca']['orientador'] = array();
			
			for($m=0; $m < count($projeto->dados_projeto['banca']['avaliador']); $m++)
			{
				$temp = $projeto->dados_projeto['banca']['avaliador'][$m];
				$dados[$k]['banca']['avaliador'][$m] = undo_html_entities($projeto->dados_projeto['professores'][$temp]['nome_professor']);
			}
			
			//data da defesa do projeto
			$date = getdate($defesa->dados_defesa['data_defesa']);
			$dados[$k]['defesa'] = $date['mday']." de ".$meses[$date['mon']]." de ".$date['year'];
			
			//incrementar $k
			$k++;
		}//for
	}//for
	
	gera_pdf($dados, $funcao);
}//function







function atestados()
{
	?>
	<p class="title" align="center">Atestados:</p>
	<p>
	<table align="center" border="1">
		<tr>
			<th>Atestados</th>
			<th colspan="2">A&ccedil;&atilde;o</th>
		</tr>
		<tr>
			<td>Orientadores</td>
			<td>
				<a href="<?php echo true_self($_SERVER['PHP_SELF'])."/admin/arquivos/atestado_banca_orientadores.pdf"; ?>"
				   onMouseOver="javascript:window.status='Visualizar atestados de orientadores'; return true"
				   onMouseOut="javascript:window.status=''; return true"
				   target="_blank">Visualizar</a>
			</td>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<td><input type="submit" value="Gerar/Atualizar"></td>
			<input type="hidden" name="funcao" value="orientador">
			</form>
		</tr>
		<tr>
			<td>Avaliadores</td>
			<td>
				<a href="<?php echo true_self($_SERVER['PHP_SELF'])."/admin/arquivos/atestado_banca_avaliadores.pdf"; ?>"
				   onMouseOver="javascript:window.status='Visualizar atestados de avaliadores'; return true"
				   onMouseOut="javascript:window.status=''; return true"
				   target="_blank">Visualizar</a>
			</td>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<td><input type="submit" value="Gerar/Atualizar"></td>
			<input type="hidden" name="funcao" value="avaliador">
			</form>
		</tr>
	</table>
	<?php echo "\n";
}



function main()
{
	if(isset($_POST) && isset($_POST['funcao']))
		gera_atestados($_POST['funcao']);
	
	html_header("Relat&oacute;rio de atestados");
	common_header('atestados');
	atestados();
	common_footer();
	html_footer();
}



main();
?>