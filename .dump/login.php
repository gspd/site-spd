<?php
include_once("common/common.php");
include_once("common/classes_info.php");

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
<center>
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
<!--<p>&nbsp;</p>-->
<p>
  <table width="80%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" bgcolor="#FFFFFF">
    <tr>
		<td align="left">
			<p align="center" class="title" style="color: #000066">Quadro de avisos</p>
			<ul>
			<?php echo "\n";
			$avisos = new Info_avisos();
			
			for($i=0; $i < count($avisos->dados_avisos); $i++)
			{
				?>
				<li><p class="subtitle"><?php echo ereg_replace("\n", "<br>\n", $avisos->dados_avisos[$i]['conteudo']); ?></p></li>
				<?php echo "\n";
			}//for
			if(!$i)
			{
				?>
				<li><h4><b>Nenhum aviso no momento</b></h4></li>
				<?php echo "\n";
			}//if
			?>
			</ul>
		</td>
	</tr>
</table>
</p>
<p>
<table border="0"><tr><td valign="middle" align="center">
<form method="post" action="<?php echo dirname($_SERVER['PHP_SELF'])."/"; ?>">
	<?php echo "\n";
	if($show_error)
	{
		?>
		<p align="center" class="errmsg">Digite usu&aacute;rio e senha v&aacute;lidos!</p>
		<?php echo "\n";
	}
	?>
	<p class="subtitle" style="color: #006699"><big>Para professores e alunos da disciplina:</big></p>
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
	<input type="hidden" name="visited" value="yes">
</form>
</td></tr></table></p>
<p><a href="info/" style="font-size:16px; font-weight:bold; color:#000099; text-decoration:underline;">Informa&ccedil;&otilde;es importantes</a></p>
</center>
<?php echo "\n";
}//login_form()




#função principal
function login()
{
    //modificado para acessar o site em modo seguro
    if (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on' )
    {
       header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
       exit();
    }//if

    session_start();
	$show_error = 0;

	//fazer logout caso usuário tenha saído do módulo administrador
	if(isset($_SESSION['modulo']) && $_SESSION['modulo'] != "user")
	{
		logout();
		exit();
	}//if

	if(!empty($_POST['username']) && !empty($_POST['password']))
	{
		$_POST['username'] = trim($_POST['username']);
		$_POST['password'] = trim($_POST['password']);
	}//if

	if(empty($_POST['username']) || empty($_POST['password']))
	{
		if(!isset($_SESSION['username']))
		{
			$info = pathinfo($_SERVER['REQUEST_URI']);

			if(!isset($info['extension']))
			{
				html_header("Login");
				if(isset($_POST['visited']))
					$show_error = 1;
				login_form($show_error);
				html_footer();
				exit();
			}//if
			else
			{
				if(strpos($info['dirname'], 'common') !== false)
					header('Location: https://'.$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF'])));
				else
					header('Location: https://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']));
				exit();
			}//else
		}
	}//if
	else
	{
		# verificar se usuário e senha existem no banco de dados
		$username = stripslashes(htmlentities($_POST['username'], ENT_QUOTES));
		$password = stripslashes(htmlentities($_POST['password'], ENT_QUOTES));
		
		$modulo = "user";
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
			$_SESSION['tipo'] = $login->dados_login['tipo'];
			$_SESSION['codigo'] = $login->dados_login['codigo'];
			$_SESSION['nome'] = $login->dados_login['nome'];
			$_SESSION['modulo'] = $modulo;
			
			if($login->dados_login['tipo'] == 'aluno')
				$_SESSION['id_projeto'] = $login->dados_login['id_projeto'];
			
		}//else
	}//else
}

//chamada da função principal
if(!isset($_POST['logout']))
	login();
else
	logout();
?>
