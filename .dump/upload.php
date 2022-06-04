<?php
include_once("login.php");





function build_message($codigo, $name, $email, $tipo, $title_projeto, $friendly_capitulo, $filename)
{
	$tipodest = ($tipo == 'aluno') ? 'professor' : 'aluno';
	
	$site = "https://".$_SERVER['HTTP_HOST'];
	$filename = $site.$filename;
	$site .= true_self($_SERVER['PHP_SELF']);
	
	$message = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\r\n".
			   "\"http://www.w3.org/TR/html4/loose.dtd\">\r\n".
			   "<html>\r\n".
			   "<head>\r\n".
			   "<title>Projeto Final</title>\r\n".
			   "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\r\n".
			   "<style type=\"text/css\">\r\n".
			   "<!--\r\n".
			   "body {\r\n".
			   "	font-family: Verdana, Arial, Helvetica, sans-serif;\r\n".
			   "	font-size: 12px;\r\n".
			   "}\r\n".
			   "-->\r\n".
			   "</style>\r\n".
			   "</head>\r\n".
			   "\r\n".
			   "<body>\r\n".
			   "<p>Aten&ccedil;&atilde;o, $tipodest(a) $name! O arquivo <b>$friendly_capitulo</b> do projeto <b>$title_projeto</b>, j&aacute; est&aacute; dispon&iacute;vel para download.<br>\r\n".
			   "Clique no link abaixo ou acesse a p&aacute;gina do projeto final.</p>\r\n".
			   "<p><a href=\"$site\">Projeto Final</a></p>\r\n".
			   "<p>Aten&ccedil;&atilde;o: esta mensagem foi enviada automaticamente pelo sistema. Favor n&atilde;o responder.</p>\r\n".
			   "</body>\r\n".
			   "</html>\r\n";
	
	return $message;
}//function build_message;







function send_mail($codigo, $tipo, $projeto, $title_projeto, $friendly_capitulo, $filename)
{
	$info_projeto = new Info_projeto($projeto);
	
	if($tipo == 'aluno')
	{
		$emails = $info_projeto->dados_projeto['professores'];
		$tipodest = 'professor';
	}
	else
	{
		$emails = $info_projeto->dados_projeto['alunos'];
		$tipodest = 'aluno';
	}
	
	for($i=0;$i < count($emails);$i++)
	{
		$name = $emails[$i]["nome_".$tipodest];
		$email = $emails[$i]["email_".$tipodest];
		
		$subject = "Entrega de arquivo";
		
		$message = build_message($codigo, $name, $email, $tipo, $title_projeto, $friendly_capitulo, $filename);
		
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "To: $email\r\n";
		$headers .= "From: Projeto Final <no-reply@".$_SERVER['HTTP_HOST'].">\r\n";
		
		// quando no sistema real, esta função deverá enviar o e-mail para o destinatário
		mail($email, $subject, $message, $headers);
	}
}//function send_mail()








function formulario($codigo, $tipo)
{
	?>
	<img src="images/hr.gif" width="90%">
	<p>
	<form name="upload" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
		<table border="0">
			<tr>
				<th align="right">Selecione o tipo de arquivo a ser enviado:</th>
				<td align="left">
					<select name="id_capitulo">
					<?php echo "\n";
					// capítulos que serão selecionados
					$capitulos = new Info_capitulos();
					$capitulos->Processa();
					
					$options = array();
					for($i=0; $i < count($capitulos->dados_capitulos['capitulos']); $i++)
					{
						if($capitulos->dados_capitulos['capitulos'][$i]['tipo'] == $tipo)
							$options[] = $capitulos->dados_capitulos['capitulos'][$i];
					}
					
					for($i=0;$i < count($options);$i++)
					{
						$id = $options[$i]['id_capitulo'];
						$friendly = $options[$i]['legenda_capitulo'];
						?>
						<option value="<?php echo $id; ?>"><?php echo $friendly; ?></option>
						<?php echo "\n";
					}
					?>
					</select>
				</td>
			</tr>
			<?php echo "\n";
			if($tipo == 'professor')
			{
				?>
				<tr>
					<th align="right">Selecione o projeto:</th>
					<td align="left">
					<select name="projeto" onChange="javascript:changeText(this.selectedIndex);">
					<?php echo "\n";
					// projetos dos quais o professor faz parte
					$professor = new Info_professor($codigo);
					
					$projetos = array();
					for($i=0; $i < count($professor->dados_professor['projeto']); $i++)
					{
						if($professor->dados_professor['projeto'][$i]['funcao'] == "avaliador")
							$projetos[] = $professor->dados_professor['projeto'][$i];
					}//for
					
					for($i=0;$i<count($projetos);$i++)
					{
						$projeto = $projetos[$i]['id_projeto'];
						$title = $projetos[$i]['titulo'];
						?>
						<option value="<?php echo $projeto; ?>"><?php echo $title; ?></option>
						<?php echo "\n";
					} //for
					?>
					</select>
				</td>
				</tr>
				<tr>
					<th align="right">Selecione o formato de submiss&atilde;o:</th>
					<td align="left">
						<input type="radio" name="tipo_texto" value="0" onClick="javascript:showInput(this.value)" checked>Arquivo
						<input type="radio" name="tipo_texto" value="1" onClick="javascript:showInput(this.value)">Texto<br>
					</td>
				</tr>
				<?php echo "\n";
			} //if
			
			
			if($tipo == "aluno")
			{
				?>
				<tr>
					<th align="right">Nome do arquivo:</th>
					<td align="left">
							<input type="file" name="file">
					</td>
				</tr>
				<?php echo "\n";
			}
			else
			{
				?>
				<tr id="inputfile">
					<th align="right">Nome do arquivo:</th>
					<td align="left">
							<input type="file" name="file">
					</td>
				</tr>
				<tr id="inputtext" style="display:none">
					<th align="right">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>Texto a ser enviado:</th>
					<td align="left">
							Problemas apresentados no texto (quanto ao<br>
							conteúdo, forma, estrutura, etc), e resultado:<br>
							<br>
							<ul>
								<li>aprovado sem restrições</li>
								<li>aprovado com as restrições citadas abaixo</li>
								<li>reprovado conforme justificativa abaixo</li>
							</ul>
							<textarea name="text" rows="8" cols="50">Parecer Projeto RA</textarea><br>
					</td>
				</tr>
				<?php echo "\n";
			} //else
			?>
			</tr>
			<tr>
				<td></td>
				<td align="left">
					<?php echo "\n";
					if($tipo == "aluno")
					{
						?>
						<input type="submit" name="submit_upload" value="Enviar">
						<?php echo "\n";
					}
					else
					{
						?>
						<input type="hidden" name="submit_upload" value="1">
						<input type="button" value="Enviar" onClick="javascript:validate_form();">
						<?php echo "\n";
					}//else
					?>
				</td>
			</tr>
		</table>
	<img src="images/hr.gif" width="90%">
	</form>
	</p>
	<?php
	if($tipo == 'professor')
	{
		?>
		<script language="javascript">
		<!--
		<?php
		if ($tipo == "professor")
		{
			?>
			function showInput(show)
			{
				file = document.getElementById("inputfile");
				text = document.getElementById("inputtext");
				
				if(show == '1')
				{
					file.style.display = "none";
					text.style.display = "";
					document.forms['upload'].text.focus();
				}
				else
				{
					file.style.display = "";
					text.style.display = "none";
				}
			}

			function validate_form()
			{
				entregue = new Array();
				
				<?php echo "\n";
				$capitulos = new Info_capitulos();
				
				for($i=0; $i < count($projetos); $i++)
				{
					$capitulos->Processa($projetos[$i]['id_projeto']);
					
					echo "\t\t\t\tentregue[$i] = new Array();\n";
					
					for($j=0; $j < count($capitulos->dados_capitulos['capitulos']); $j++)
					{
						if($capitulos->dados_capitulos['capitulos'][$j]['tipo'] == "professor")
						{
							$achou = 0;
							
							$id_capitulo_busca = $capitulos->dados_capitulos['capitulos'][$j]['id_capitulo'];
							for($k=0; $k < count($capitulos->dados_capitulos['entregas']); $k++)
							{
								if($capitulos->dados_capitulos['entregas'][$k]['id_capitulo'] == $id_capitulo_busca && $capitulos->dados_capitulos['entregas'][$k]['codigo'] == $codigo)
								{
									echo "\t\t\t\tentregue[$i][$id_capitulo_busca] = 1;\n";
									$achou = 1;
									break;
								}//if
							}
							
							if(!$achou)
								echo "\t\t\t\tentregue[$i][$id_capitulo_busca] = 0;\n";
						}//if
					}//for
				}//for
				?>
				
				id_capitulo = document.forms['upload'].id_capitulo.selectedIndex;
				projeto = document.forms['upload'].projeto.selectedIndex;
				nome_capitulo = document.forms['upload'].id_capitulo.options[id_capitulo].text;
				id_capitulo = document.forms['upload'].id_capitulo.options[id_capitulo].value;
				
				if(!entregue[projeto][id_capitulo])
					document.forms['upload'].submit();
				else
					alert("Arquivo (" + nome_capitulo + ") já enviado!\nFavor contactar administrador do sistema caso seja necessário o reenvio do arquivo.");

			}//function
			<?php echo "\n";
		}
		?>
		
		function changeText(num)
		{
			values = new Array();
			<?php
			$projeto = new Info_projeto();
			
			for($i=0; $i < count($projetos); $i++)
			{
				$projeto->Processa($projetos[$i]['id_projeto']);
				?>
				values[<?php echo $i; ?>] = "<?php echo $projeto->dados_projeto['alunos'][0]['codigo']; ?>";
				<?php echo "\n";
			}//for
			?>
			document.forms['upload'].text.value = "Parecer Projeto RA " + values[num];
			if(document.forms['upload'].text.style.display == "")
				document.forms['upload'].text.focus;
		}//function
		
		changeText(0);
		-->
		</script>
		<?php echo "\n";
	}//if
} //function upload_form()







function erro($num)
{
	// erros de upload - servidor
	$maxsize = ini_get('upload_max_filesize');
	$erro[-3] = sprintf("O arquivo excedeu o tamanho m&aacute;ximo de %.1f MB.", $maxsize/pow(2,20));
	$erro[-2] = $erro[-3];
	$erro[-1] = "O arquivo foi enviado apenas parcialmente. Tente novamente.";
	$erro[0] = "Nenhum arquivo foi enviado!";
	
	// erros de upload - sistema
	$erro[1] = "S&oacute; &eacute; poss&iacute;vel enviar arquivos .doc, .pdf, .zip ou .rar!";
	$erro[2] = "Arquivo j&aacute; enviado!<br>\nFavor contactar administrador do sistema caso seja necess&aacute;rio o reenvio do arquivo.";
	$erro[3] = "Nenhum texto foi enviado!";
	
	// erros ao salvar os arquivos
	$erro[4] = "Erro fatal ao criar o diret&oacute;rio!";
	$erro[5] = "Erro fatal ao salvar o arquivo enviado!";
	$erro[6] = "Erro fatal ao salvar o PDF com o texto enviado!";
	$erro[7] = "Erro fatal ao fazer a consulta no banco de dados!";
	$erro[8] = "Nenhum projeto foi selecionado!";
	
	?>
	<tr>
		<td align="left">
			<img src="images/hr.gif" width="90%">
			<p>&nbsp;</p>
			<p><font class="errmsg">Erro: <?php echo $erro[$num]; ?></font></p>
			<p align="left">
			<a href="upload.php"
			   onMouseOver="javascript:window.status='Tentar novamente'; return true"
			   onMouseOut="javascript:window.status=''; return true"><img src="images/filefolder.jpg" border="0"><font style="font-size: 14px; font-weight: bold;">Tentar novamente</font></a>
			</p>
			<img src="images/hr.gif" width="90%">
		</td>
	</tr>
	<?php echo "\n";
	custom_header();
	?>
	</table>
	</td></tr></table>
	</center>
	<?php echo "\n";
	html_footer();
	exit();
}//function erro()








function custom_header()
{
	?>
	<tr><td align="center">
	<a href="./"
	   onMouseOver="javascript:window.status='Voltar &agrave; p&aacute;gina inicial'; return true"
	   onMouseOut="javascript:window.status=''; return true"><img src="images/home.gif" border="0"><font style="font-size: 14px; font-weight: bold;">Voltar &agrave; p&aacute;gina inicial</font></a>
	<?php echo "\n";
	if($_SESSION['tipo'] == 'professor')
	{
		?>
		| <a href="javascript:toggle_fontSize()"><img src="images/magnify.gif" border="0"><font style="font-size: 14px; font-weight: bold;">Aumentar/Diminuir tamanho do texto</font></a>
		<?php echo "\n";
	}//if
	?>
	| <a href="javascript:session_close()"><img src="images/error.gif" border="0"><font style="font-size: 14px; font-weight: bold;">Encerrar sess&atilde;o</font></a>
	</td></tr>
	<?php echo "\n";
}//function custom_header






function file_valid($filename)
{
	$filename = strtolower($filename);
	$extension = substr($filename, -4);
	
	//tipos de arquivos aceitos
	$accept = array(".doc", ".pdf", ".zip", ".rar");
	
	if(in_array($extension, $accept))
		return $extension;
	else
		return 0;
}//function file_valid()






//verifica se existe arquivo antigo e se é para deletar o arquivo antigo
function verify_oldfile($id_capitulo, $delete = 0)
{
	$arquivo = new Handle_arquivo();
	$capitulos = new Info_capitulos();
	
	$codigo = $_SESSION['codigo'];
	$tipo = $_SESSION['tipo'];
	$projeto = ($tipo == 'professor') ? $_POST['projeto'] : $_SESSION['id_projeto'];
	
	$entregas = new Info_entregas($projeto);
	
	$existe = 0;
	
	for($i=0; $i < count($entregas->dados_entregas); $i++)
	{
		if($tipo == "aluno")
		{
			if($entregas->dados_entregas[$i]['id_capitulo'] == $id_capitulo)
			{
				$existe = $entregas->dados_entregas[$i]['entregue'];
				break;
			}//if
		}//if
		else if ($tipo == "professor")
		{
			if($entregas->dados_entregas[$i]['id_capitulo'] == $id_capitulo && $entregas->dados_entregas[$i]['codigo'] == $codigo)
			{
				$existe = $entregas->dados_entregas[$i]['entregue'];
				break;
			}//if
		}//if
	}//for
	
	return $existe;
}//function verify_oldfile








function create_pdf($id_capitulo, $text)
{
	$self = true_self($_SERVER['PHP_SELF']);
	
	$title = "Parecer";
	
	$location = $_SERVER['DOCUMENT_ROOT'].$self."/arquivos";
	$filename = $location."/temp.pdf";
	
	require("FPDF/fpdf.php");
	
	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Helvetica','B','16');
	$pdf->Cell(0,5,$title,0,2,"C",0);
	$pdf->Ln();
	$pdf->SetFont('Helvetica','',12);
	$pdf->MultiCell(0,5,$text,0,"J",0);
	$output = $pdf->Output($filename,"F");
	
	return $filename;
}//function create_pdf();







function save_file($oldfilename, $extension, $filetype, $id_capitulo)
{
	$extension = substr($extension,1);
	$codigo = $_SESSION['codigo'];
	$tipo = $_SESSION['tipo'];
	
	$self = true_self($_SERVER['PHP_SELF']);
	$doc_root = $_SERVER['DOCUMENT_ROOT'];
	
	if($tipo == "professor")
	{
		$projeto = $_POST['projeto'];
		$codigo = sprintf("%02d", $codigo);
	}
	else
		$projeto = $_SESSION['id_projeto'];
	
	$capitulos = new Info_capitulos();
	$capitulos->Dados_capitulo($id_capitulo);
	
	$name_capitulo = $capitulos->dados_capitulos['nome_capitulo'];
	$friendly_capitulo = $capitulos->dados_capitulos['legenda_capitulo'];
	
	$info_projeto = new Info_projeto($projeto,1);
	$title_projeto = $info_projeto->dados_projeto['titulo'];
	
	$dirname = $self."/arquivos/$projeto";
	$filename['remote'] = $dirname."/$name_capitulo-$codigo.$extension";
	$filename['local'] = $doc_root.$filename['remote'];
	$dirname = $doc_root.$dirname;
	$filename['remote'] = "http://".$_SERVER['HTTP_HOST'].$filename['remote'];
	
	if(!file_exists($dirname))
	{
		if(!mkdir($dirname, 0700))
			erro(4);
	}//if
	
	verify_oldfile($id_capitulo, 1);
	
	$success = 0;
	
	if($filetype == 'upload')
	{
		if(!move_uploaded_file($oldfilename, $filename['local']))
			erro(5);
		else
			$success = 1;
	}//
	else
	{
		if(!rename($oldfilename, $filename['local']))
			erro(6);
		else
			$success = 1;
	}//else
	
	if($success)
	{
		//definir as permissões de arquivo
		chmod($filename['local'], 0600);
		//colocar no banco de dados informações sobre o arquivo
		$entregas = new Info_entregas();
		$entregas->inserir_entrega($projeto, $codigo, $id_capitulo, $_SESSION['tipo']);
		?>
		<td><table border="0"><tr><td align="left">
		<img src="images/hr.gif" width="90%">
		<p class="title">Arquivo enviado com sucesso!</p>
		<p><b>Nome do arquivo: <a href="<?php echo $filename['remote']; ?>" target="_blank"><?php echo basename($filename['remote']); ?></a></b></p>
		<p><b>Nome do cap&iacute;tulo</b>: <?php echo $friendly_capitulo; ?></p>
		<?php
		if($tipo == 'professor')
		{
			?>
			<p><b>T&iacute;tulo do projeto</b>: <?php echo $title_projeto; ?></p>
			<?php echo "\n";
		}//if
		?>
		<p>
		<a href="upload.php"
		   onMouseOver="javascript:window.status='Enviar outro arquivo'; return true"
		   onMouseOut="javascript:window.status=''; return true"><font style="font-size: 14px; font-weight: bold;"><img src="images/filefolder.jpg" border="0">Enviar outro arquivo</font></a>
		</p>
		<img src="images/hr.gif" width="90%">
		</td></tr></table></td>
		<?php echo "\n";
		
		send_mail($codigo, $tipo, $projeto, $title_projeto, $friendly_capitulo, $filename['remote']);
	}//if
	
}//function save_file




function main()
{
	$codigo = $_SESSION['codigo'];
	$tipo = $_SESSION['tipo'];
	html_header("Enviar arquivos");
	?>
	<center>
	<table border="0" height="300">
	<tr>
		<td height="150" valign="top">
			<img src="images/logo.gif">
			<p class="title">Enviar Arquivos</p>
		</td>
	</tr>
	<tr><td valign="middle">
	<table border="0">
	<?php echo "\n";
	custom_header();
	?>
	<?php echo "\n";
	
	if(!isset($_POST['submit_upload']))
	{
		?>
		<tr>
		<td align="center">
		<?php echo "\n";
		formulario($codigo, $tipo);
		?>
		</td>
		</tr>
		<?php echo "\n";
	}//if
	else
	{
		if($tipo == "professor")
		{
			if(!isset($_POST['projeto']))
				erro(8);
		}//if
		
		$id_capitulo = $_POST['id_capitulo'];
		
		if($tipo == 'aluno')
		{
			if($error = (isset($_FILES['file']['error'])) ? $_FILES['file']['error'] : 0)
				erro($error - 4);
			else
			{
				if(!($extension = file_valid($_FILES['file']['name'])))
					erro(1);
				else
				{
					if(verify_oldfile($id_capitulo, 0))
						erro(2);
					
					$newfile = $_FILES['file']['tmp_name'];
					$filetype = "upload";
				}//else
			}//else
		}//if
		else
		{
			$tipo_texto = $_POST['tipo_texto'];
			
			if($tipo_texto)
			{
				if(isset($_POST['text']))
				{
					$text = $_POST['text'];
					if(!ereg("^[ ]{8}", $text))
						$text = trim($text);
				}
				else
					$text = "";
				
				if(empty($text))
					erro(3);
				else
				{
					if(verify_oldfile($id_capitulo, 0))
						erro(2);
					
					$newfile = create_pdf($id_capitulo, $text);
					$filetype = "text";
				}//else
			}//if
			else
			{
				if($error = $_FILES['file']['error'])
					erro($error - 4);
				else
				{
					if(!($extension = file_valid($_FILES['file']['name'])))
						erro(1);
					else
					{
						if(verify_oldfile($id_capitulo, 0))
							erro(2);
						
						$newfile = $_FILES['file']['tmp_name'];
						$filetype = "upload";
					}//else
				}//else
			}//else
		}//else
	}//else
	
	if(!isset($extension))
		$extension = ".pdf";
	
	if(isset($newfile) && isset($filetype))
		save_file($newfile, $extension, $filetype, $id_capitulo);
	
	//finalizar a página
	custom_header();
	?>
	</table>
	</td></tr></table>
	</center>
	<?php echo "\n";
	html_footer();
}//function main()



main();
?>
