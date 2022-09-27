<html>
<head>
<title>Bugs Encontrados</title>

<script language=JavaScript>
function deletar(nro)
{
	document.deleta.id.value=nro;
	document.deleta.submit();
}
</script>

</head>

<body bgcolor=white>
<h1><center>Bugs Reportados</center></h1>

<form action=exclui.php method=post name=deleta>
	<input type=hidden name=id>
</form>
