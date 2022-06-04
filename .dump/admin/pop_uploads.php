<?php
include_once("login.php");




function executar()
{
	foreach($_POST as $key => $value)
	{
		if($key != 'action')
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}//foreach
	
	ereg("([[:alnum:]_]+)-([[:digit:]]+)\.(pdf|doc|zip|rar)", $name, $dados);
	
	$name_capitulo = $dados[1];
	$codigo = $dados[2];
	$tipo_arquivo = $dados[3];
	$name = "$name_capitulo-$codigo";
	
	$capitulos = new Info_capitulos();
	$capitulos->Processa();
	
	for($i=0; $i < count($capitulos->dados_capitulos); $i++)
	{
		if($name_capitulo == $capitulos->dados_capitulos['capitulos'][$i]['nome_capitulo'])
		{
			$id_capitulo = $capitulos->dados_capitulos['capitulos'][$i]['id_capitulo'];
			break;
		}
	}
	
	$arquivo = new Handle_arquivo();
	
	$arquivo->delete($id, $name);
	?>
	<p class="errmsg" align="center">Arquivo exclu&iacute;do.</p>
	<p align="center"><a href="javascript:self.close()">Fechar janela.</a></p>
	<?php echo "\n";
}//function executar();




function recarrega_uploads()
{
	?>
	<script language="javascript">
	<!--
	opener.document.forms['reload'].submit();
	-->
	</script>
	<?php
} //function recarrega_cadastros();




function confirmar()
{
	foreach($_POST as $key => $value)
	{
		if($key != "action")
			$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	}//foreach
	
	?>
	<p class="errmsg" align="center">Tem certeza de que deseja excluir o arquivo "<?php echo $name; ?>"?</p>
	<p align="center">
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<input type="hidden" name="name" value="<?php echo $name; ?>">
			<input type="hidden" name="action" value="<?php echo $_POST['action']; ?>_executar">
			<input type="submit" value="Excluir arquivo">
			<input type="button" value="Cancelar" onClick="javascript:self.close()">
		</form>
	</p>
	<?php echo "\n";
}//function confirmar();



function main()
{
	if(isset($_POST['action']))
		$action = $_POST['action'];
	else
	{
		html_header();
		?>
		<script language="javascript">
		<!--
		self.close();
		-->
		</script>
		<?php
		html_footer();
		exit();
	}//else
	
	if((strpos($action, "excluir") !== false))
	{
		$step = substr($action, strpos($action, "_")+1);
		if(strpos($action, "_"))
			$action = substr($action, 0, strpos($action, "_"));
		
		switch($step)
		{
			// funções para alterar um cadastro
			case "executar":
				html_header("Dados exclu&iacute;dos");
				executar();
				recarrega_uploads();
				html_footer();
				break;
			default:
				html_header("Confirmar exclus&atilde;o de dados");
				confirmar();
				html_footer();
				break;
		}//switch
	}//else if
}//function main();



main();
?>