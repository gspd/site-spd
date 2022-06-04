<?php
include_once("login.php");

class Tabela_Mod extends Tabela
{
	function Tabela_Mod($dados, $no_display = array(-1))
	{
		$this->Tabela($dados, $no_display);
	}
	
	function append_header()
	{
		echo "\n\t\t"; ?><th colspan="2">A&ccedil;&atilde;o</th><?php
	}
	
	function append_fields($i)
	{
		echo "\n\t\t"; ?><td><?php echo "\n\t\t\t";
						?><a href="javascript:submit_form('alter<?php echo $i; ?>')"<?php echo "\n\t\t\t";
						?>   onMouseOver="javascript:window.status='alterar'; return true"<?php echo "\n\t\t\t";
						?>   onMouseOut="javascript:window.status=''; return true">alterar</a><?php echo "\n\t\t\t";
		echo "\n\t\t"; ?></td><?php
		echo "\n\t\t"; ?><td><?php echo "\n\t\t\t";
						?><a href="javascript:submit_form('excl<?php echo $i; ?>')"<?php echo "\n\t\t\t";
						?>   onMouseOver="javascript:window.status='excluir'; return true"<?php echo "\n\t\t\t";
						?>   onMouseOut="javascript:window.status=''; return true">excluir</a><?php echo "\n\t\t\t";
		echo "\n\t\t"; ?></td><?php
	}
}//class Tabela2





function list_avisos()
{
	$avisos = new Info_avisos();
	$num_avisos = $avisos->num_avisos();
	
	for($i=0; $i < $num_avisos; $i++)
		$avisos->dados_avisos[$i]['conteudo'] = ereg_replace("\n", "<br>\n", $avisos->dados_avisos[$i]['conteudo']);
	
	?>
	<p align="center" class="title">Mensagens do quadro de avisos:</p>
	<?php echo "\n";
	
	if($num_avisos)
	{
		?>
		<table border="0" cellpadding="3" align="center">
			<tr>
				<td align="left">
					<a href="javascript:submit_form('include')"
					   onMouseOver="javascript:window.status='incluir avisos'; return true"
					   onMouseOut="javascript:window.status=''; return true">
						<font class="subtitle">Incluir avisos</font>
					</a>
				</td>
			</tr>
			<tr>
				<td align="center">
					<table border="1" align="center">
						<tr>
							<th>Posi&ccedil;&atilde;o</th>
							<th>Conte&uacute;do</th>
							<th colspan="2">A&ccedil;&atilde;o</th>
						</tr>
						<?php echo "\n";
						for($i=0; $i < $num_avisos; $i++)
						{
							?>
							<tr>
								<td><?php echo $avisos->dados_avisos[$i]['id_aviso']; ?></td>
								<td><?php echo $avisos->dados_avisos[$i]['conteudo']; ?></td>
								<td>
									<a href="javascript:submit_form('alter<?php echo $i; ?>')"
									   onMouseOver="javascript:window.status='alterar'; return true"
									   onMouseOut="javascript:window.status=''; return true">alterar</a>
								</td>
								<td>
									<a href="javascript:submit_form('excl<?php echo $i; ?>')"
									   onMouseOver="javascript:window.status='excluir'; return true"
									   onMouseOut="javascript:window.status=''; return true">excluir</a>
								</td>
							</tr>
							<?php echo "\n";
						}//for
						?>
					</table>
				</td>
			</tr>
			<tr>
				<td align="left">
					<a href="javascript:submit_form('include')"
					   onMouseOver="javascript:window.status='incluir avisos'; return true"
					   onMouseOut="javascript:window.status=''; return true">
						<font class="subtitle">Incluir avisos</font>
					</a>
				</td>
			</tr>
		</table>
		<?php echo "\n";
		
		for($i=0; $i < $num_avisos; $i++)
		{
			?>
			<form name="alter<?php echo $i; ?>" method="post" action="pop_altera_aviso.php" target="altera_senha">
				<input type="hidden" name="action" value="alterar">
				<input type="hidden" name="id" value="<?php echo $avisos->dados_avisos[$i]['id_aviso']; ?>">
			</form>
			<form name="excl<?php echo $i; ?>" method="post" action="pop_altera_aviso.php" target="altera_senha">
				<input type="hidden" name="action" value="excluir">
				<input type="hidden" name="id" value="<?php echo $avisos->dados_avisos[$i]['id_aviso']; ?>">
			</form>
			<?php echo "\n";
		}//for
	}//if
	else
	{
		?>
		<p align="center">
			<table border="0">
				<tr>
					<td align="left">N&atilde;o existem avisos no momento.</td>
				</tr>
				<tr>
					<td align="left">
						<a href="javascript:submit_form('include')"
						   onMouseOver="javascript:window.status='incluir avisos'; return true"
						   onMouseOut="javascript:window.status=''; return true">
						   <font class="subtitle">Incluir avisos</font>
						</a>
					</td>
				</tr>
			</table>
		</p>
		<?php echo "\n";
	}//else
	
	echo "\n"; ?>
	<form name="reload" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	</form>
	
	<form name="include" method="post" action="pop_altera_aviso.php" target="pop_editar">
		<input type="hidden" name="action" value="incluir">
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
	html_header("Quadro de avisos");
	common_header('avisos');
	list_avisos();
	common_footer();
	html_footer();
}//function main();



main();
?>