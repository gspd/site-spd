<?php
include_once("login.php");



function list_projetos()
{
	?>
	<p align="center" class="title">Projetos cadastrados:</p>
	<?php echo "\n";

	$order = (!isset($_POST['order'])) ? "ASC" : $_POST['order'];
	
	$orderby = (!empty($_POST['orderby'])) ? $_POST['orderby']." ".$order : "id_projeto $order";
	
	//listar os projetos
	$lista = new Info_lista("projetos", $orderby);
	$projetos = $lista->dados_lista;
	
	$order = ($order == "ASC") ?  "DESC" : "ASC";
	$orderby = (!empty($_POST['orderby'])) ? $_POST['orderby'] : "titulo";
	
	if(empty($projetos))
	{
		?>
		<p align="center">
			<table align="center" border="0">
				<tr>
					<td align="left">N&atilde;o existem projetos cadastrados no momento.</td>
				</tr>
				<tr>
					<td align="left">
						<a href="javascript:submit_form('include')"
						   onMouseOver="javascript:window.status='Incluir projetos'; return true"
						   onMouseOut="javascript:window.status=''; return true">
						   <font class="subtitle">Incluir projetos</font>
						</a>
					</td>
				</tr>
			</table>
		</p>
		<?php
	}
	else
	{
?>
<p align="center">
<table align="center" border="0" cellpadding="3">
	<tr>
		<td align="left">
			<a href="javascript:submit_form('include')"
			   onMouseOver="javascript:window.status='Incluir projetos'; return true"
			   onMouseOut="javascript:window.status=''; return true">
				<font class="subtitle">Incluir projetos</font>
			</a>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table border="1">
				<tr>
					<th>
						<a href="javascript:submit_form('orderby_titulo')"
						   onMouseOver="javascript:window.status='Listar por t&iacute;tulo'; return true"
						   onMouseOut="javascript:window.status=''; return true">T&iacute;tulo</a>
					</th>
					<th colspan="2">A&ccedil;&atilde;o</th>
				</tr>
		<?php
				for($i=0; $i < count($projetos); $i++)
				{
					$id = $projetos[$i]['id_projeto'];
					$title = $projetos[$i]['titulo'];
		?>
				<tr>
					<td><?php echo $title; ?></td>
					<td>
						<a href="javascript:submit_form('alter<?php echo $i; ?>')"
						   onMouseOver="javascript:window.status='alterar t&iacute;tulo: <?php echo $title; ?>'; return true"
						   onMouseOut="javascript:window.status=''; return true">alterar</a>
						<form name="alter<?php echo $i; ?>" method="post" action="pop_editar_projeto.php" target="pop_editar">
							<input type="hidden" name="action" value="alterar">
							<input type="hidden" name="id" value="<?php echo $id; ?>">
						</form>
					</td>
					<td>
						<a href="javascript:submit_form('excl<?php echo $i; ?>')"
						   onMouseOver="javascript:window.status='excluir t&iacute;tulo: <?php echo $title; ?>'; return true"
						   onMouseOut="javascript:window.status=''; return true">excluir</a>
						<form name="excl<?php echo $i; ?>" method="post" action="pop_editar_projeto.php" target="pop_editar">
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
			   onMouseOver="javascript:window.status='Incluir projetos'; return true"
			   onMouseOut="javascript:window.status=''; return true">
				<font class="subtitle">Incluir projetos</font>
			</a>
		</td>
	</tr>
</table>
</p>
<?php
	
	}//else
	
	$forms = array("titulo");
	
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

<form name="include" method="post" action="pop_editar_projeto.php" target="pop_editar">
	<?php echo "\n";
	$newid = count($projetos)+1;
	?>
	<input type="hidden" name="id" value="<?php echo $newid; ?>">
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

}//function list_projetos();



function main()
{
	html_header("Projetos");
	common_header('projeto');
	list_projetos();
	common_footer();
	html_footer();
}

main();
?>
