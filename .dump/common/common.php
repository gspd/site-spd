<?php
include_once("mysql.php");

# !obsoleta!obsoleta!obsoleta!obsoleta! #
# ===================================== #
# Função para conectar o banco de dados #
# ===================================== #
# !obsoleta!obsoleta!obsoleta!obsoleta! #
#
# retorna o identificador da conexão estabelecida
# usar a classe Connection ao invés dessa

function db_connect($dbname = '')
{
	$connection = new Connection($dbname);
	return $connection->link_id();
}

# !obsoleta!obsoleta!obsoleta!obsoleta!obs #
# ======================================== #
# Função para retornar linhas de consultas #
# ======================================== #
# !obsoleta!obsoleta!obsoleta!obsoleta!obs #
#
# Query ao invés dessa função
# Devem ser fornecidas as opções:
# $fields = campos;
# $tables = tabelas;
# $condition = condições, opcional;
# $orderby = ordem de classificação, opcional;
# $groupby = forma de agrupamento;
#
# O retorno dessa função é um array multidimensional, onde
# o primeiro índice é o número da linha e o segundo índice
# é o número ou o nome do campo da consulta

function db_query($fields, $tables, $condition = '', $orderby = '', $groupby = '', $show_error = 0)
{
	$query = new Query($fields, $tables, $condition, $groupby, $orderby, $show_error);
	return $query->rows;
}//function db_query



function undo_html_entities($string)
{
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);

	for($i=0; $i < 256; $i++)
		$trans_tbl[sprintf("&#%03d;", $i)] = chr($i);

    return strtr($string, $trans_tbl);
}



function split_string($string, $split)
{
	$len = strlen($string);
	for($i=0; $i < $len - 1; $i += $split)
		$result[] = substr($string, $i, $split);

	return $result;
}




function true_self($self, $absolute = 0)
{
	if(strpos(dirname($self), "admin") !== false)
		$self = dirname($self);

	/*if(strpos(dirname($self), "teste") !== false)
		$self = dirname($self);*/

	$self = dirname($self);

	if($absolute)
		$self = $_SERVER['DOCUMENT_ROOT'].$self;

	return $self;
}




function debug($variavel)
{
	echo "<pre>\n";
	print_r($variavel);
	echo "\n</pre>\n";
}



# ----------------------------------------
# Função para criar o início de uma página
# ----------------------------------------
# Folhas de estilos e scripts devem ser colocados aqui

function html_header($title = '', $bodyload = '', $bodyclass = '')
{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="common.css">
</head>

<body<?php
			if(!empty($bodyload))
				echo " onLoad=\"$bodyload\"";
			
			if(!empty($bodyclass))
				echo " class=\"$bodyclass\"";
	?>>
<?php echo "\n";
}

# -------------------------------------
# Função para criar o fim de uma página
# -------------------------------------

function html_footer()
{
?>
<script language="javascript">
	<!--
	<?php echo "\n";
	if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == "professor")
	{
	?>
	function toggle_fontSize()
	{
		magnify = document.forms['toggle_fontsize'].magnify;
		zoom = 2;
		if(magnify.value == 0)
		{
			for(i=0;document.styleSheets[0].rules[i];i++)
			{
				fontSize = parseInt(document.styleSheets[0].rules[i].style.fontSize);
				document.styleSheets[0].rules[i].style.fontSize = Math.ceil(fontSize*zoom) + "px";
			}
			magnify.value = 1;
		}
		else
		{
			for(i=0;document.styleSheets[0].rules[i];i++)
			{
				fontSize = parseInt(document.styleSheets[0].rules[i].style.fontSize);
				document.styleSheets[0].rules[i].style.fontSize = Math.floor(fontSize/zoom) + "px";
			}
			magnify.value = 0;
		}
	
	}
	
	<?php echo "\n";
	}//if
	?>
	
	function session_close()
	{
		document.forms['close_session'].submit();
	}
	-->
</script>
<?php echo "\n";
if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == "professor")
{
?>
<form name="toggle_fontsize">
    <input type="hidden" name="magnify" value="0">
</form>
<?php echo "\n";
}//if
?>
<form name="close_session" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="logout" value="1">
</form>
<div align="center" style="font-size:10px;">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&copy; <?php echo date("Y"); ?> Universidade Estadual Paulista.
DCCE - Departamento de Ci&ecirc;ncias de Computa&ccedil;&atilde;o e Estat&iacute;stica.<br>
Todos os direitos reservados. Desenvolvido por: Tiago Dias da Costa</p>
</div>
</body>
</html>
<?php echo "\n";
}//function html_footer()

function common_header($pagename = '')
{
?>
<table border="0" width="100%"><tr><td width="20%" valign="top">
<p>&nbsp;</p>
<p>&nbsp;</p>
<center><img src="images/logomin.gif"><br><font style="font-size:11px; font-weight:bold; color:#000000">Departamento de Ci&ecirc;ncias de Computa&ccedil;&atilde;o e Estat&iacute;stica</font></center>
<table border="1" cellpadding="5" cellspacing="2" bordercolor="#FFFFFF">
	<tr>
		<td class="menu">
			<b>Cadastros:</b><br>
			<table border="0" cellspacing="0">
			<tr>
				<td width="10"></td>
				<td class="<?php echo ($pagename == 'projeto') ? "selected" : "menu"; ?>">
					<a href="editar_projeto.php"
					   onMouseOver="javascript:window.status='Projetos e uploads'; return true"
					   onMouseOut="javascript:window.status=''; return true">Projetos</a>
				</td>
			</tr>
			<tr>
				<td width="10"></td>
				<td class="<?php echo ($pagename == 'professor') ? "selected" : "menu"; ?>">
				<a href="javascript:document.forms['editar_professores'].submit()"
				   onMouseOver="javascript:window.status='Cadastros de professores'; return true"
				   onMouseOut="javascript:window.status=''; return true">Professores</a>
				</td>
			</tr>
			<tr>
				<td width="10"></td>
				<td class="<?php echo ($pagename == 'aluno') ? "selected" : "menu"; ?>">
				<a href="javascript:document.forms['editar_alunos'].submit()"
				   onMouseOver="javascript:window.status='Cadastros de alunos'; return true"
				   onMouseOut="javascript:window.status=''; return true">Alunos</a>
				</td>
			</tr>
			<tr>
				<td width="10"></td>
				<td class="<?php echo ($pagename == 'capitulo') ? "selected" : "menu"; ?>">
					<a href="editar_capitulo.php"
					   onMouseOver="javascript:window.status='Nomes de cap&iacute;tulos'; return true"
					   onMouseOut="javascript:window.status=''; return true">Nomes de cap&iacute;tulos</a>
				</td>
			</tr>
			<tr>
				<td width="10"></td>
				<td class="<?php echo ($pagename == 'defesas') ? "selected" : "menu"; ?>">
				<a href="defesas.php"
				   onMouseOver="javascript:window.status='Cadastros de datas e notas de defesas'; return true"
				   onMouseOut="javascript:window.status=''; return true">Defesas</a>
				</td>
			</tr>
			</table>
	  </td>
	</tr>
	<tr>
		<td class="menu">
			<b>Relat&oacute;rios:</b><br>
			<table border="0" cellspacing="0">
			<tr>
				<td width="10"></td>
				<td class="<?php echo ($pagename == 'atestados') ? "selected" : "menu"; ?>">
					<a href="atestados.php"
					   onMouseOver="javascript:window.status='Atestados'; return true"
					   onMouseOut="javascript:window.status=''; return true">Atestados</a>
				</td>
			</tr>
			<tr>
				<td width="10"></td>
				<td class="<?php echo ($pagename == 'notas_finais') ? "selected" : "menu"; ?>">
					<a href="notas_finais.php"
					   onMouseOver="javascript:window.status='Notas finais'; return true"
					   onMouseOut="javascript:window.status=''; return true">Notas Finais</a>
				</td>
			</tr>
			</table>
	  </td>
	</tr>
	<td class="<?php echo ($pagename == 'uploads') ? "selected" : "menu"; ?>">
		<a href="uploads.php"
		   onMouseOver="javascript:window.status='Editar submiss&otilde;es'; return true"
		   onMouseOut="javascript:window.status=''; return true">Editar submiss&otilde;es</a>
	</td>
	<tr>
		<td class="<?php echo ($pagename == 'senhas') ? "selected" : "menu"; ?>">
			<a href="senhas.php"
			   onMouseOver="javascript:window.status='Usu&uacute;rios e senhas'; return true"
			   onMouseOut="javascript:window.status=''; return true">Usu&aacute;rios e senhas</a>
		</td>
	</tr>
	<tr>
		<td class="<?php echo ($pagename == 'penalidades') ? "selected" : "menu"; ?>">
			<a href="penalidades.php"
			   onMouseOver="javascript:window.status='Editar penalidades'; return true"
			   onMouseOut="javascript:window.status=''; return true">Editar penalidades</a>
		</td>
	</tr>
	<tr>
		<td class="<?php echo ($pagename == 'avisos') ? "selected" : "menu"; ?>">
			<a href="avisos.php"
			   onMouseOver="javascript:window.status='Quadro de avisos'; return true"
			   onMouseOut="javascript:window.status=''; return true">Quadro de avisos</a>
		</td>
	</tr>
	<tr>
		<td class="<?php echo ($pagename == 'disciplina') ? "selected" : "menu"; ?>">
			<a href="disciplina.php"
			   onMouseOver="javascript:window.status='Dados da disciplina'; return true"
			   onMouseOut="javascript:window.status=''; return true">Dados da disciplina</a>
		</td>
	</tr>
	<tr>
		<td class="<?php echo ($pagename == 'backup') ? "selected" : "menu"; ?>">
			<a href="backup.php"
			   onMouseOver="javascript:window.status='Backup do sistema'; return true"
			   onMouseOut="javascript:window.status=''; return true">Backup do sistema</a>
		</td>
	</tr>
	<tr>
		<td class="menu">
			<a href="javascript:document.forms['logout_session'].submit()"
			   onMouseOver="javascript:window.status='Encerrar sess&atilde;o'; return true"
			   onMouseOut="javascript:window.status=''; return true">Encerrar sess&atilde;o</a>
		</td>
	</tr>
</table>
<form name="editar_alunos" action="editar_cadastro.php" method="post">
	<input type="hidden" name="tipo" value="aluno">
</form>
<form name="editar_professores" action="editar_cadastro.php" method="post">
	<input type="hidden" name="tipo" value="professor">
</form>
<form name="logout_session" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="logout" value="1">
</form>
</td>
<td width="80%">
<?php echo "\n";
}

function common_footer()
{
?>
</td></tr>
</table>
<?php
}

//ini_set('error_reporting', E_ALL);

$global_link_id = db_connect();
?>
