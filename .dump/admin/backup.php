<?php
include("./login.php");





function limpar_dados()
{
	?>
	<p class="errmsg">Excluindo os dados...</p>
	<?php echo "\n";
	$limpar_dados = new Backup();
	$limpar_dados->Limpar_dados();
	?>
	<p class="errmsg">Opera&ccedil;&atilde;o conclu&iacute;da.
					  &Eacute; recomend&aacute;vel iniciar imediatamente o preenchimento dos dados da disciplina.</p>
	<?php echo "\n";
}









function deletar_arquivo($nome_arquivo)
{
	if(file_exists($nome_arquivo))
	{
		if(!unlink($nome_arquivo))
			die("Erro ao excluir o arquivo $nome_arquivo!");
	}//if
}//function deletar_arquivo





function realiza_backup()
{
	$self = true_self($_SERVER['PHP_SELF'], 1);
	$path = $self."/admin/backup";
	$date = date("Y-m-d");
	$dbname = "projeto_final";
	$dbuser = "tiago";
	$dbpass = "tiago";
	
	?>
	<p class="errmsg">Realizando o backup...</p>
	<?php echo "\n";
	
	deletar_arquivo("$path/$date.tar.gz");
	
	echo "<pre>";
	//início do script de backup
	chdir($path);
	
	echo "\nCriando diret&oacute;rio tempor&aacute;rio...\n";
	mkdir($date, 0700);
	
	echo "\nCopiando arquivos de submiss&otilde;es...\n";
	system("cp -a ../../arquivos ./$date/arquivos");
	
	echo "\nCopiando relat&oacute;rios de atestados e notas finais...\n";
	system("cp -a ../arquivos ./$date/admin");
	
	echo "\nRealizando backup do banco de dados...\n";
	system("mysqldump --add-drop-table -n -u $dbuser -p$dbpass $dbname > ./$date/$dbname.sql");
	
	echo "\nCompactando os dados no arquivo \"$date.tar.gz\"...\n";
	system("tar -cz -f $date.tar.gz $date");
	
	echo "\nExcluindo o diret&oacute;rio tempor&aacute;rio...\n";
	system("rm -f -R ./$date");
	
	echo "\nModificando as permiss&otilde;es para o arquivo \"$date.tar.gz\"...\n";
	chmod("$date.tar.gz", 0600);
	
	chdir("../");
	//fim do script de backup
	echo "</pre>";
	
	?>
	<p class="errmsg">Opera&ccedil;&atilde;o conclu&iacute;da.
					  Se nenhuma mensagem de erro foi exibida, o backup foi realizado com &ecirc;xito.
					  &Eacute; recomend&aacute;vel fazer o download do arquivo de backup para maior seguran&ccedil;a.</p>
	<?php echo "\n";
}//function realiza_backup






function restaura_backup($nome_backup)
{
	$self = true_self($_SERVER['PHP_SELF'], 1);
	$path = $self."/admin/backup";
	$date = $nome_backup;
	$dbname = "projeto_final";
	$dbuser = "tiago";
	$dbpass = "tiago";
	
	?>
	<p class="errmsg">Realizando o backup...</p>
	<?php echo "\n";
	
	
	//início do script de backup
	echo "<pre>";
	chdir($path);
	
	echo "\nExtraindo os dados do arquivo \"$date.tar.gz\" para o diret&oacute;rio tempor&aacute;rio...\n";
	system("tar -xz -f $date.tar.gz");
	
	echo "\nExcluindo os arquivos de submiss&otilde;es atuais...\n";
	system("rm -f -R ../../arquivos");
	
	echo "\nCopiando os arquivos de submiss&otilde;es do backup...\n";
	system("cp -a ./$date/arquivos ../../arquivos");
	
	echo "\nExcluindo os arquivos de atestados e notas finais atuais...\n";
	system("rm -f -R ../arquivos");
	
	echo "\nCopiando os arquivos de atestados notas finais do backup...\n";
	system("cp -a ./$date/admin ../arquivos");
	
	echo "\nRestaurando o banco de dados a partir do backup...\n";
	system("mysql -u $dbuser -p$dbpass $dbname < ./$date/$dbname.sql");
	
	echo "\nExcluindo o diret&oacute;rio tempor&aacute;rio...\n";
	system("rm -f -R ./$date");
	
	chdir("../");
	echo "</pre>";
	//fim do script de backup
	
	?>
	<p class="errmsg">Opera&ccedil;&atilde;o conclu&iacute;da.
					  Se nenhuma mensagem de erro foi exibida, o backup foi restaurado com &ecirc;xito.
					  &Eacute; recomend&aacute;vel verificar se todos os dados est&atilde;o corretos e consistentes.</p>
	<?php echo "\n";
}//function restaura_backup






function exclui_backup($nome_backup)
{
	$path = true_self($_SERVER['PHP_SELF'], 1)."/admin/backup";
	$date = $nome_backup;
	
	?>
	<p class="errmsg">Excluindo o arquivo de backup <?php echo "$nome_backup.tar.gz"; ?>...</p>
	<?php echo "\n";
	
	deletar_arquivo("$path/$date.tar.gz");
	
	?>
	<p class="errmsg">Opera&ccedil;&atilde;o conclu&iacute;da.</p>
	<?php echo "\n";
}//function





function list_backups()
{
	if(!($dp = opendir(true_self($_SERVER['PHP_SELF'], 1)."/admin/backup")))
		die("Erro ao abrir o diret&oacute;rio dos backups!");
	
	$k = 0;
	
	while(($entry = readdir($dp)) !== false)
	{
		if($entry != "." && $entry != "..")
		{
			if(substr($entry, -7) == ".tar.gz")
				$backups[$k++] = $entry;
		}//if
	}//while
	
	if(!$k)
		return NULL;
	else
		return $backups;
}//function



function mostra_backups()
{
	$backups = list_backups();
	$date_hoje = date("Y-m-d");
	
	$backup_hoje = 0;
	
	for($i=0; $i < count($backups); $i++)
	{
		if("$date_hoje.tar.gz" == $backups[$i])
		{
			$backup_hoje = 1;
			break;
		}//if
	}//for
	
	?>
	<p align="center" class="title">Backup do Sistema</p>
	<table align="center" border="0">
		<tr>
			<td>
				<input type="button" value="Gerar backup" onClick="javascript:realiza_backup()">
			</td>
		</tr>
		<tr>
			<td width="100%">
				<table align="center" border="1" width="100%">
					<tr>
						<th>Nome do backup</th>
						<th colspan="2">A&ccedil;&atilde;o</th>
					</tr>
					<?php echo "\n";
					for($i=0; $i < count($backups); $i++)
					{
						?>
						<tr>
							<td>
								<a href="<?php echo true_self($_SERVER['PHP_SELF'])."/admin/backup/".$backups[$i]; ?>"
								   onMouseOver="javascript:window.status='<?php echo $backups[$i]; ?>'; return true"
								   onMouseOut="javascript:window.status=''; return true"><?php echo $backups[$i]; ?></a>
							</td>
							<td>
								<a href="javascript:restaura_backup('<?php echo substr($backups[$i], 0, 10); ?>');"
								   onMouseOver="javascript:window.status='Restaurar backup de <?php echo substr($backups[$i], 0, 10); ?>'; return true"
								   onMouseOut="javascript:window.status=''; return true">Restaurar</a>
							</td>
							<td>
								<a href="javascript:exclui_backup('<?php echo substr($backups[$i], 0, 10); ?>');"
								   onMouseOver="javascript:window.status='Excluir backup de <?php echo substr($backups[$i], 0, 10); ?>'; return true"
								   onMouseOut="javascript:window.status=''; return true">Excluir</a>
							</td>
						</tr>
						<?php echo "\n";
					}//for
					
					if(!$i)
					{
						?>
						<tr>
							<td colspan="3">N&atilde;o existem backups do sistema.</td>
						</tr>
						<?php echo "\n";
					}//if
					?>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" value="Gerar backup" onClick="javascript:realiza_backup();">
			</td>
		</tr>
	</table>
	<p align="center" class="title">Limpar os dados do sistema</p>
	<p>Para limpar todos os dados relativos &agrave; disciplina este semestre, clique no bot&atilde;o abaixo. Aten&ccedil;&atilde;o: ser&atilde;o exclu&iacute;dos os seguintes dados:</p>
	<p>
		<ul>
			<li>Todos os cadastros de projetos, professores, alunos, nomes de capítulos e datas e notas de defesas</li>
			<li>Todos os relatórios de atestados e notas finais</li>
			<li>Todas as submissões feitas durante este semestre, juntamente com suas penalidades</li>
			<li>Todo o quadro de avisos</li>
			<li>Todas as informações da disciplina (respons&aacute;veis, turma, carga hor&aacute;ria, etc.) <b>exceto os usu&aacute;rios e senhas dos administradores</b></li>
		</ul>
	</p>
	<p>Os backups do sistema n&atilde;o ser&atilde;o excl&iacute;dos. Caso ainda deseje realizar esta a&ccedil;&atilde;o, clique no bot&atilde;o abaixo.</p>
	<p align="center"><input type="button" value="Limpar dados" onclick="javascript:limpar_dados();"></p>
	<form name="form_realizar" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="acao" value="relizar">
	</form>
	
	<form name="form_restaurar" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="nome_backup" value="">
		<input type="hidden" name="acao" value="restaurar">
	</form>
	
	<form name="form_excluir" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="nome_backup" value="">
		<input type="hidden" name="acao" value="excluir">
	</form>
	
	<form name="form_limpar_dados" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="acao" value="limpar_dados">
	</form>
	<script language="javascript">
		<!--
		function realiza_backup()
		{
			var backup_hoje = <?php echo $backup_hoje; ?>;
			
			if(backup_hoje)
			{
				alert('Backup já realizado hoje. Para realizá-lo novamente, exclua o backup existente.');
			}//if
			else
			{
				document.forms['form_realizar'].submit();
				return false;
			}//else
		}//function
		
		
		
		
		function restaura_backup(nome_backup)
		{
			var confirmar = confirm('Ao restaurar este backup, todas as informações atuais, ' +
									'incluindo configurações e submissões de arquivo serão excluídas ' +
									'para dar lugar às informações contidas no arquivo de backup. ' +
									'Deseja realmente continuar a restaurar o backup de ' + nome_backup + '?');
			
			if(confirmar)
			{
				document.forms['form_restaurar'].nome_backup.value = nome_backup;
				document.forms['form_restaurar'].submit();
				return true;
			}
		}//function
		
		
		
		
		function exclui_backup(nome_backup)
		{
			var confirmar = confirm('Tem certeza de que deseja excluir o backup ' + nome_backup + '?');
			
			if(confirmar)
			{
				document.forms['form_excluir'].nome_backup.value = nome_backup;
				document.forms['form_excluir'].submit();
				return true;
			}
		}//function
		
		
		
		function limpar_dados()
		{
			var confirmar = confirm('Tem certeza de que deseja realizar esta ação? ' +
									'Lembre-se de ter salvo os dados antes de exluí-los! ' +
									'Uma vez excluídos, só será possível recuperá-los se foi feito backup dos mesmos!');
			
			if(confirmar)
				document.forms['form_limpar_dados'].submit();
		}
		-->
				</script>
	<?php echo "\n";
}






function main()
{
	html_header("Backup do sistema");
	common_header('backup');
	
	if(isset($_POST['acao']))
	{
		if($_POST['acao'] == "relizar")
			realiza_backup();
		else if($_POST['acao'] == "restaurar" && isset($_POST['nome_backup']))
			restaura_backup($_POST['nome_backup']);
		else if($_POST['acao'] == "excluir" && isset($_POST['nome_backup']))
			exclui_backup($_POST['nome_backup']);
		else if($_POST['acao'] == "limpar_dados")
			limpar_dados();
	}//if
	
	mostra_backups();
	common_footer();
	html_footer();
}//function main();

main();
?>
