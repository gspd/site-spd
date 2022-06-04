<?php
include_once("../common/common.php");
include_once("../common/classes_info.php");


function logout()
{
	session_start();
	session_unset();
	session_destroy();
	header("Location: https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/");
}//function logout;




# corpo do formulário de login
function login_form($show_error = 0)
{
?>
<style type="text/css">
table.login {
	background-color: #99CCFF;
	font-size: 14px;
}
</style>
<form method="post" action="<?php echo dirname($_SERVER['PHP_SELF'])."/"; ?>">
	<table align="center" width="100%">
	<tr><td align="center">
		<table width="100%" border="0">
			<tr> 
			  <td align="right"><img src="images/logo.gif"></td>
			  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			  <!--<td align="left" style="font-weight:bold; color:#000000;">
				UNIVERSIDADE ESTADUAL PAULISTA<br>
				&quot;J&Uacute;LIO DE MESQUITA FILHO&quot;<br>
				Campus de S&atilde;o Jos&eacute; do Rio Preto</td>-->
			  <td align="left" valign="middle" style="color:#000000; font-weight:bold;">
				IBILCE - Instituto de Bioci&ecirc;ncias, Letras e Ci&ecirc;ncias Exatas<br>
				DCCE - Departamento de Ci&ecirc;ncias de Computa&ccedil;&atilde;o e Estat&iacute;stica<br>
				Curso - Bacharelado em Ci&ecirc;ncia da Computa&ccedil;&atilde;o</td>
			</tr>
		</table>
		<?php echo "\n";
		$disciplina = new Info_disciplina();
		
		$semestre_letivo = $disciplina->dados_disciplina['semestre_letivo'];
		$ano_letivo = $disciplina->dados_disciplina['ano_letivo'];
		
		$semestre = "$semestre_letivo&ordm; semestre de ".$ano_letivo;
		
		$responsaveis = "";
		
		for($i=0; $i < 2; $i++)
		{
			$responsavel = $disciplina->dados_disciplina['responsaveis'][$i];
			$responsaveis .= ($responsavel['titulo']) ? "Prof(&ordf;). Dr(&ordf;). " : "Prof(&ordf;).";
			$responsaveis .= $responsavel['nome'];
			if($i+1 < 2)
				$responsaveis .= " e ";
		}//for
		?>
		<br>
		<br>
		<p class="subtitle">Disciplina: Projeto Final - <?php echo $semestre; ?><br>
		Respons&aacute;veis: <?php echo $responsaveis; ?></p>
		<p>
		<p>&nbsp;</p>
		<p>
		<table border="0"><tr><td>
		<?php echo "\n";
		if($show_error)
		{
			?>
			<p align="center" class="errmsg">P&aacute;gina restrita: acesso negado</p>
			<?php echo "\n";
		}
		?>
		<p class="title" style="color: #006699">Digite seu nome de usu&aacute;rio e senha:</p>
		<table width="20%" border="0" class="login">
			<tr>
				<th align="right" width="50%" nowrap>Nome de usu&aacute;rio:</th>
				<td align="left" width="50%" nowrap><input type="text" name="username" maxlength="20" size="20"></td>
			</tr>
			<tr>
				<th align="right" width="50%" nowrap>Senha:</th>
				<td align="left" width="50%" nowrap><input type="password" name="password" size="20"></td>
			</tr>
			<tr>
				<td></td>
				<td width="100%" align="left" nowrap><input type="submit" value="Enviar"></td>
			</tr>
		</table>
		</td></tr>
		</table>
		</p>
		<p align="center"><a href="../" style="font-size:14px; font-weight:bold; color:#000099">P&aacute;gina inicial</a></p>
	</td></tr>
	</table>
	<input type="hidden" name="visited" value="yes">
</form>
<?php
}


# autentica usuário
function login()
{
    if (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on' )
    {
       header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
       exit();
    }//if

    session_start();
	$show_error = 0;
	
	if(isset($_SESSION['modulo']) && $_SESSION['modulo'] != "admin")
	{
		logout();
		exit();
	}//else
	
	if(empty($_POST['username']) || empty($_POST['password']))
	{
		if(!isset($_SESSION['username']))
		{
			html_header("Login", "", "login");
			if(isset($_POST['visited']))
				$show_error = 1;
			login_form($show_error);
			html_footer();
			exit();
		}
	}//if
	else
	{
		# verificar se usuário e senha existem no banco de dados
		$username = stripslashes(htmlentities($_POST['username'], ENT_QUOTES));
		$password = stripslashes(htmlentities($_POST['password'], ENT_QUOTES));
		
		$modulo = "admin";
		// retorna somente uma linha
		$login = new Info_login($username, $password, $modulo);
		
		if(!$login->dados_login['login_validado'])
		{
			$show_error = 1;
			html_header("Login", "", "login");
			login_form($show_error);
			html_footer();
			exit();
		}
		else
		{
			$_SESSION['username'] = $username;
			$_SESSION['modulo'] = $modulo;
		}//else
	}//else
}

//chamada da função principal
if(!isset($_POST['logout']))
	login();
else
	logout();
?>
