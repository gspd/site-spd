<?php
include_once("login.php");



function list_cadastros($tipo)
{
	if($tipo == "professor")
	{
		$plural = "professores";
		$codigoname = "c&oacute;digo";
	}//if
	else
	{
		$plural = "alunos";
		$codigoname = "RA";
	}//else
	
	?>
	<p align="center" class="title"><?php echo ucfirst($plural); ?> cadastrados:</p>
	<?php echo "\n";

	$order = (!isset($_POST['order'])) ? "ASC" : $_POST['order'];
	
	$orderby = (!empty($_POST['orderby'])) ? $_POST['orderby']." ".$order : "nome_{$tipo} $order";
	
	//listar os alunos
	if($tipo == 'aluno')
		$lista = new Info_lista("alunos", $orderby);
	else
		$lista = new Info_lista("professores", $orderby);
	
	$cadastros = $lista->dados_lista;
	
	$order = ($order == "ASC") ?  "DESC" : "ASC";
	$orderby = (!empty($_POST['orderby'])) ? $_POST['orderby'] : "codigo";
	
	
	if(empty($cadastros))
	{
		?>
		<table align="center" border="0">
			<tr>
				<td align="left">N&atilde;o existem <?php echo $plural; ?> cadastrados no momento.</td>
			</tr>
			<tr>
				<td align="left">
					<a href="javascript:submit_form('include')"
						onMouseOver="javascript:window.status='incluir cadastro'; return true"
						onMouseOut="javascript:window.status=''; return true">
						<font class="subtitle">Incluir cadastros</font>
					</a>
				</td>
			</tr>
		</table>
		<?php
	}//if
	else
	{
?>
<table align="center" border="0" cellpadding="3">
	<tr>
		<td align="left">
			<a href="javascript:submit_form('include')"
			   onMouseOver="javascript:window.status='incluir cadastro'; return true"
			   onMouseOut="javascript:window.status=''; return true">
				<font class="subtitle">Incluir cadastro</font>
			</a>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table border="1">
				<tr>
					<th>
						<a href="javascript:submit_form('orderby_codigo')"
						   onMouseOver="javascript:window.status='Listar por <?php echo $codigoname; ?>'; return true"
						   onMouseOut="javascript:window.status=''; return true"><?php echo ucfirst($codigoname); ?></a>
					</th>
					<th>
						<a href="javascript:submit_form('orderby_nome_<?php echo $tipo; ?>')"
						   onMouseOver="javascript:window.status='Listar por nome'; return true"
						   onMouseOut="javascript:window.status=''; return true">Nome</a>
					</th>
					<?php
					if($tipo == 'professor')
					{
						?>
						<th>
							<a href="javascript:submit_form('orderby_doutor')"
							   onMouseOver="javascript:window.status='Listar por T&iacute;tulo'; return true"
							   onMouseOut="javascript:window.status=''; return true">T&iacute;tulo</a>
						</th>
						<?php
					}//if
					?>
					<th>
						<a href="javascript:submit_form('orderby_email_<?php echo $tipo; ?>')"
						   onMouseOver="javascript:window.status='Listar por E-mail'; return true"
						   onMouseOut="javascript:window.status=''; return true">E-mail</a>
					</th>
					<?php
					if($tipo == 'aluno')
					{
						?>
						<th>
							<a href="javascript:submit_form('orderby_id_projeto')"
							   onMouseOver="javascript:window.status='Listar por Projeto'; return true"
							   onMouseOut="javascript:window.status=''; return true">Projeto</a>
						</th>
						<?php
					}//if
					else
					{
						?>
						<th>Projetos</th>
						<?php
					}
					?>
					<th colspan="2">A&ccedil;&atilde;o</th>
				</tr>
				<?php
				for($i=0;$i < count($cadastros);$i++)
				{
					$name = $cadastros[$i]["nome_{$tipo}"];
					$codigo = $cadastros[$i]['codigo'];
					$email = $cadastros[$i]["email_{$tipo}"];
					if($tipo == 'aluno')
						$projeto = $cadastros[$i]['projeto']['titulo'];
					else
					{
						$doutor = ($cadastros[$i]['doutor']) ? "Doutor(a)" : "-";
						$projeto = $cadastros[$i]['projeto'];
					}//else
				?>
				<tr>
					<td><?php echo $codigo; ?></td>
					<td><?php echo $name; ?></td>
					<?php
					if($tipo == 'professor')
					{
						?>
						<td><?php echo $doutor; ?></td>
						<?php echo "\n";
					}//if
					?>
					<td><?php echo $email; ?></td>
					<?php
					if($tipo == 'aluno')
					{
						?>
						<td><?php echo $projeto; ?></td>
						<?php echo "\n";
					}//if
					else
					{
						?>
						<td>
						<table border="1" cellpadding="2" cellspacing="0" width="100%">
						<?php echo "\n";
						for($j=0; $j < count($projeto); $j++)
						{
							?>
							<tr><td><?php echo $projeto[$j]['titulo']; ?>: <?php echo $projeto[$j]['funcao']; ?></td></tr>
							<?php echo "\n";
						}//for
						?>
						</table>
						</td>
						<?php echo "\n";
					}//else
					?>
					<td>
						<a href="javascript:submit_form('alter<?php echo $i; ?>')"
						   onMouseOver="javascript:window.status='alterar cadastro: <?php echo "$tipo(a) $name"; ?>'; return true"
						   onMouseOut="javascript:window.status=''; return true">alterar</a>
						<form name="alter<?php echo $i; ?>" method="post" action="pop_editar_cadastro.php" target="pop_editar">
							<input type="hidden" name="action" value="alterar">
							<input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
							<input type="hidden" name="tipo" value="<?php echo $tipo ?>">
						</form>
					</td>
					<td>
						<a href="javascript:submit_form('excl<?php echo $i; ?>')"
						   onMouseOver="javascript:window.status='excluir cadastro: <?php echo "$tipo(a) $name"; ?>'; return true"
						   onMouseOut="javascript:window.status=''; return true">excluir</a>
						<form name="excl<?php echo $i; ?>" method="post" action="pop_editar_cadastro.php" target="pop_editar">
							<input type="hidden" name="action" value="excluir">
							<input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
							<input type="hidden" name="tipo" value="<?php echo $tipo ?>">
						</form>
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
			   onMouseOver="javascript:window.status='incluir cadastro'; return true"
			   onMouseOut="javascript:window.status=''; return true">
				<font class="subtitle">Incluir cadastro</font>
			</a>
		</td>
	</tr>
</table>

<?php
	
	}//else
	
	if($tipo == "aluno")
		$forms = array("nome_aluno", "codigo", "email_aluno", "id_projeto");
	else
		$forms = array("nome_professor", "codigo", "doutor", "email_professor");
	
	for($i=0; $i < count($forms); $i++)
	{
		?>
		<form name="orderby_<?php echo $forms[$i]; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
			<input type="hidden" name="orderby" value="<?php echo $forms[$i]; ?>">
			<input type="hidden" name="order" value="<?php echo ($orderby == $forms[$i]) ? $order : "ASC"; ?>">
		</form>
		<?php echo "\n";
	}//for;
	
	?>

<form name="include" method="post" action="pop_editar_cadastro.php" target="pop_editar">
	<input type="hidden" name="action" value="incluir">
	<input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
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

}//function list_cadastros()

function main()
{
	html_header("Cadastros");
	
	if(isset($_POST['tipo']))
	{
		common_header($_POST['tipo']);
		if($_POST['tipo'] == 'aluno' || $_POST['tipo'] == 'professor')
			list_cadastros($_POST['tipo']);
	}
	else
	{
		header("Location: https://".$_SERVER['HTTP_HOST'].true_self($_SERVER['PHP_SELF'])."/");
		exit();
	}//else
	
	common_footer();
	html_footer();
}

main();
?>