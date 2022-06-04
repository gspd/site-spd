<?php
include_once("login.php");



function list_capitulos()
{
	?>
	<p align="center" class="title">Cap&iacute;tulos cadastrados:</p>
	<?php echo "\n";

	$order = (!isset($_POST['order'])) ? "ASC" : $_POST['order'];
	
	$orderby = (!empty($_POST['orderby'])) ? $_POST['orderby']." ".$order : "id_capitulo $order";
	
	//listar os capítulos
	$capitulos = new Info_capitulos();
	$capitulos->Processa();
	
	$order = ($order == "ASC") ?  "DESC" : "ASC";
	$orderby = (!empty($_POST['orderby'])) ? $_POST['orderby'] : "id_capitulo";
	
	if(empty($capitulos->dados_capitulos['capitulos']))
	{
		?>
		<table align="center" border="0">
			<tr>
				<td align="left">N&atilde;o existem cap&iacute;tulos cadastrados no momento.</td>
			</tr>
			<tr>
				<td align="left">
					<a href="javascript:submit_form('include')"
						onMouseOver="javascript:window.status='incluir cap&iacute;tulos'; return true"
						onMouseOut="javascript:window.status=''; return true">
						<font class="subtitle">Incluir cap&iacute;tulos</font>
					</a>
				</td>
			</tr>
		</table>
		<?php
	}
	else
	{
?>
<table align="center" border="0" cellpadding="3">
	<tr>
		<td align="left">
			<a href="javascript:submit_form('include')"
			   onMouseOver="javascript:window.status='incluir cap&iacute;tulos'; return true"
			   onMouseOut="javascript:window.status=''; return true">
				<font class="subtitle">Incluir cap&iacute;tulos</font>
			</a>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table border="1">
				<tr>
					<th>
						<a href="javascript:submit_form('orderby_id')"
						   onMouseOver="javascript:window.status='Listar por identifica&ccedil;&atilde;o'; return true"
						   onMouseOut="javascript:window.status=''; return true">Identifica&ccedil;&atilde;o</a>
					</th>
					<th>
						<a href="javascript:submit_form('orderby_tipo')"
						   onMouseOver="javascript:window.status='Listar por tipo'; return true"
						   onMouseOut="javascript:window.status=''; return true">Vis&iacute;vel para</a>
					</th>
					<th>
						<a href="javascript:submit_form('orderby_name')"
						   onMouseOver="javascript:window.status='Listar por nome de arquivo'; return true"
						   onMouseOut="javascript:window.status=''; return true">Nome de arquivo</a>
					</th>
					<th>
						<a href="javascript:submit_form('orderby_friendly')"
						   onMouseOver="javascript:window.status='Listar por Nome de exibi&ccedil;&atilde;o'; return true"
						   onMouseOut="javascript:window.status=''; return true">Nome de exibi&ccedil;&atilde;o</a>
					</th>
					<th>
						<a href="javascript:submit_form('orderby_date')"
						   onMouseOver="javascript:window.status='Listar por data'; return true"
						   onMouseOut="javascript:window.status=''; return true">Data</a>
					</th>
					<th>Aplicar Penalidade</th>
					<th colspan="2">A&ccedil;&atilde;o</th>
				</tr>
				<?php echo "\n";
				for($i=0;$i < count($capitulos->dados_capitulos['capitulos']);$i++)
				{
					$id = $capitulos->dados_capitulos['capitulos'][$i]['id_capitulo'];
					$tipo = $capitulos->dados_capitulos['capitulos'][$i]['tipo'];
					$name = $capitulos->dados_capitulos['capitulos'][$i]['nome_capitulo'];
					$friendly = $capitulos->dados_capitulos['capitulos'][$i]['legenda_capitulo'];
					$date = date("d/m/Y H:i:s", $capitulos->dados_capitulos['capitulos'][$i]['data_entrega']);
					$penalidade = ($capitulos->dados_capitulos['capitulos'][$i]['aplicar_penalidade']) ? "Sim" : "N&atilde;o";
				?>
				<tr>
					<td><?php echo $id; ?></td>
					<td><?php echo $tipo; ?></td>
					<td><?php echo $name; ?></td>
					<td><?php echo $friendly; ?></td>
					<td><?php echo $date; ?></td>
					<td><?php echo $penalidade; ?></td>
					<td>
						<a href="javascript:submit_form('alter<?php echo $i; ?>')"
						   onMouseOver="javascript:window.status='alterar cap&iacute;tulo: <?php echo $name; ?>'; return true"
						   onMouseOut="javascript:window.status=''; return true">alterar</a>
						<form name="alter<?php echo $i; ?>" method="post" action="pop_editar_capitulo.php" target="pop_editar">
							<input type="hidden" name="action" value="alterar">
							<input type="hidden" name="id" value="<?php echo $id; ?>">
						</form>
					</td>
					<td>
						<a href="javascript:submit_form('excl<?php echo $i; ?>')"
						   onMouseOver="javascript:window.status='excluir cap&iacute;tulo: <?php echo $name; ?>'; return true"
						   onMouseOut="javascript:window.status=''; return true">excluir</a>
						<form name="excl<?php echo $i; ?>" method="post" action="pop_editar_capitulo.php" target="pop_editar">
							<input type="hidden" name="action" value="excluir">
							<input type="hidden" name="id" value="<?php echo $id; ?>">
						</form>
					</td>
				</tr>
		<?php
				}
		?>
			</table>
		</td>
	</tr>
	<tr>
		<td align="left">
			<a href="javascript:submit_form('include')"
			   onMouseOver="javascript:window.status='incluir cap&iacute;tulo'; return true"
			   onMouseOut="javascript:window.status=''; return true">
				<font class="subtitle">Incluir cap&iacute;tulos</font>
			</a>
		</td>
	</tr>
</table>
<p align="center" class="subtitle">Visualiza&ccedil;&atilde;o dos cap&iacute;tulos no modo aluno</p>
<p align="center">
	<select>
		<?php
		for($i=0; $i < count($capitulos->dados_capitulos['capitulos']); $i++)
		{
			if($capitulos->dados_capitulos['capitulos'][$i]['tipo'] == "aluno")
			{
				$name = $capitulos->dados_capitulos['capitulos'][$i]['nome_capitulo'];
				$friendly = $capitulos->dados_capitulos['capitulos'][$i]['legenda_capitulo'];
				echo "\t<option value=\"$name\">$friendly</option>\n";
			}//if
		}
		?>
	</select>
</p>
<p align="center" class="subtitle">Visualiza&ccedil;&atilde;o dos cap&iacute;tulos no modo professor</p>
<p align="center">
	<select>
		<?php
		for($i=0; $i < count($capitulos->dados_capitulos['capitulos']); $i++)
		{
			if($capitulos->dados_capitulos['capitulos'][$i]['tipo'] == "professor")
			{
				$name = $capitulos->dados_capitulos['capitulos'][$i]['nome_capitulo'];
				$friendly = $capitulos->dados_capitulos['capitulos'][$i]['legenda_capitulo'];
				echo "\t<option value=\"$name\">$friendly</option>\n";
			}//if
		}
		?>
	</select>
</p>
<?php
	
	}//else
	
	$forms = array("id", "tipo", "name", "friendly", "date");
	
	for($i=0; $i < count($forms); $i++)
	{
		?>
		<form name="orderby_<?php echo $forms[$i]; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="orderby" value="<?php echo $forms[$i]; ?>">
			<input type="hidden" name="order" value="<?php echo ($orderby == $forms[$i]) ? $order : "ASC"; ?>">
		</form>
		<?php echo "\n";
	}//for;
	?>

<form name="include" method="post" action="pop_editar_capitulo.php" target="pop_editar">
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

}//function list_capitulos();



function main()
{
	html_header("Cap&iacute;tulos");
	common_header('capitulo');
	list_capitulos();
	common_footer();
	html_footer();
}

main();
?>