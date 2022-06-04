<?php
include_once("login.php");



function list_usuarios()
{
	$order = (!isset($_POST['order'])) ? "ASC" : $_POST['order'];

	$orderby = (!empty($_POST['orderby'])) ? "'".stripslashes(htmlentities($_POST['orderby'], ENT_QUOTES))."' ".$order : "'nome' $order";

	$lista = new Info_lista("login_user", $orderby);

	$orderby = (!empty($_POST['orderby'])) ? stripslashes(htmlentities($_POST['orderby'], ENT_QUOTES)) : "nome";

	$forms = array("codigo" => "RA/C&oacute;digo", "nome" => "Nome", "username" => "Nome de usu&aacute;rio");
	
	?>
	<p align="center" class="title">Usu&aacute;rios do sistema:</p>
	<table border="1" align="center">
		<tr>
			<?php echo "\n";
			foreach($forms as $key => $value)
			{
				?>
				<th>
					<a href="javascript:order_by('<?php echo $key; ?>')"
					onMouseOver="javascript:window.status='Listar por <?php echo $value; ?>'; return true"
					onMouseOut="javascript:window.status=''; return true"><?php echo $value; ?></a>
				</th>
				<?php echo "\n";
			}//for
			?>
			<th>A&ccedil;&atilde;o</th>
		</tr>
			<?php echo "\n";
			for($i=0; $i < count($lista->dados_lista); $i++)
			{
				?>
				<tr>
					<?php echo "\n";
					foreach($forms as $key => $value)
					{
						?>
						<td align="center"><?php echo $lista->dados_lista[$i][$key]; ?></td>
						<?php echo "\n";
					}//foreach
					?>
					<td>
						<a href="javascript:submit_form('alter<?php echo $i; ?>')"
						   onMouseOver="javascript:window.status='alterar usu&aacute;rio e senha'; return true"
						   onMouseOut="javascript:window.status=''; return true">alterar senha</a>
					</td>
				</tr>
				<?php echo "\n";
			}//for
		?>
	</table>
	<?php echo "\n";
	for($i=0; $i < count($lista->dados_lista); $i++)
	{
		?>
		<form name="alter<?php echo $i; ?>" method="post" action="pop_altera_senha.php" target="altera_senha">
			<input type="hidden" name="codigo" value="<?php echo $lista->dados_lista[$i]['codigo']; ?>">
		</form>
		<?php echo "\n";
	}//for
	
	echo "\n"; ?>
	<form name="order_by" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
		<input type="hidden" name="order" value="<?php echo $order; ?>">
	</form>
	<p>&nbsp;</p>
	<p align="center" class="title">Usu&aacute;rios administradores:</p>
	<table border="1" align="center">
		<tr>
			<th>Nome de usu&aacute;rio</th>
			<th>A&ccedil;&atilde;o</th>
		</tr>
		<?php echo "\n";
		$lista->Info_lista("login_admin");
		for($i=0; $i < count($lista->dados_lista); $i++)
		{
			?>
			<tr>
				<td align="center"><?php echo $lista->dados_lista[$i]['adm_username']; ?></td>
				<td>
					<a href="javascript:submit_form('admin<?php echo $i; ?>')"
					   onMouseOver="javascript:window.status='alterar usu&aacute;rio e senha'; return true"
					   onMouseOut="javascript:window.status=''; return true">alterar senha</a>
			</td>
			</tr>
			<?php echo "\n";
		}//for
		
		
		for($i=0; $i < count($lista->dados_lista); $i++)
		{
			?>
			<form name="admin<?php echo $i; ?>" method="post" action="pop_altera_senha.php" target="altera_senha">
				<input type="hidden" name="admin" value="<?php echo $lista->dados_lista[$i]['adm_username']; ?>">
			</form>
			<?php echo "\n";
		}//for
		?>
	</table>
	<form name="reload" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	</form>
	
	<script language="javascript">
	<!--
	function submit_form(name)
	{
		document.forms[name].submit();
	}
	
	function order_by(orderby)
	{
		form = document.forms['order_by'];
		
		if(form.orderby.value != orderby)
		{
			form.orderby.value = orderby;
			form.order.value = "ASC";
		}
		else
			form.order.value = (form.order.value == "ASC") ? "DESC" : "ASC";
		
		submit_form('order_by');
	}//function order_by;
	-->
	</script>
	<?php echo "\n";
}//function list_uploads();




function main()
{
	html_header("Usu&aacute;rios e senhas");
	common_header('senhas');
	list_usuarios();
	common_footer();
	html_footer();
}//function main();



main();
?>
