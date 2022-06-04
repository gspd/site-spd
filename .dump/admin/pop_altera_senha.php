<?php
include_once("login.php");



function valida_dados()
{
	function erro($num)
	{
		$erro[0] = "Nome de usu&aacute;rio inv&aacute;lido!";
		$erro[1] = "Nome de usu&aacute;rio j&aacute; existente!";
		$erro[2] = "Senha inv&aacute;lida!";
		$erro[3] = "Senha n&atilde;o coincidente!";
		$erro[4] = "Senha atual inv&aacute;lida!";
		?>
		<p align="center" class="errmsg"><?php echo $erro[$num]; ?></p>
		<?php echo "\n";
	}//function erro();
	
	
	foreach($_POST as $key => $value)
		$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
	
	$valid = 1;
	
	if(empty($newusername))
	{
		erro(0);
		$valid = 0;
	}//if
	else
	{
		$newusername = trim($newusername);
		
		if(empty($newusername))
		{
			erro(0);
			$valid = 0;
		}//if
		else if($newusername != $oldusername)
		{
			$login = new Info_login($newusername, "", "user");
			$username_exists = $login->dados_login['username_existe'];
			
			if($username_exists)
			{
				erro(1);
				$valid = 0;
			}//if
		}//elseif
	}//else
	
	if(empty($newpassword) || empty($confirmpassword))
	{
		erro(2);
		$valid = 0;
	}//if
	else
	{
		$newpassword = trim($newpassword);
		$confirmpassword = trim($confirmpassword);
		
		if($type == "admin")
		{
			$login = new Info_login($oldusername, $atualpassword, "admin");
			
			if(!$login->dados_login['login_validado'])
			{
				erro(4);
				$valid = 0;
			}
		}
		
		if(empty($newpassword) || empty($confirmpassword))
		{
			erro(2);
			$valid = 0;
		}//if
		else if($newpassword != $confirmpassword)
		{
			erro(3);
			$valid = 0;
		}//else
	}//else
	
	return $valid;
}//function valida_dados




function altera_senha()
{
	foreach($_POST as $key => $value)
		$$key = (!is_array($value)) ? stripslashes(htmlentities(trim($value), ENT_QUOTES)) : $value;
		
	if(valida_dados())
	{
		$login = new Info_login($newusername, $newpassword, "user");
		
		if($type == "normal")
			$login->alterar_login($oldusername, "", $newusername, $newpassword, "user");
		else
			$login->alterar_login($oldusername, $atualpassword, $newusername, $newpassword, "admin");
		
		if($login->dados_login['login_validado'])
		{
			?>
			<p align="center" class="subtitle">Senha alterada com sucesso!</p>
			<script language="javascript">
			<!--
			if(opener.document.forms['reload'])
				opener.document.forms['reload'].submit();
			-->
			</script>
			<p align="center"><a href="javascript: self.close()">Fechar janela</a>
			<?php
		}//if
	}//if
	else
		formulario($type);
}//function altera_senha();
		
		

function formulario($type)
{
	if($type == "normal")
	{
		$login = new Info_login();
		$codigo = $_POST['codigo'];
		$login->Dados_login($codigo);
		$username = $login->dados_login['username'];
	}
	else
		$username = $_POST['admin'];
	
	if(empty($username))
	{
		?>
		<script language="javascript">
		<!--
		self.close();
		-->
		</script>
		<?php echo "\n";
	}//if
	else
	{
		?>
		<p align="center" class="title">Alterar usu&aacute;rio e senha:</p>
		<p align="center">
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<table align="center" border="0">
					<tr>
						<th align="right">Nome de usu&aacute;rio:</th>
						<td><input type="text" name="newusername" value="<?php echo $username; ?>"></td>
					</tr>
					<?php echo "\n";
					if($type == "admin")
					{
						?>
						<tr>
							<th align="right">Senha atual:</th>
							<td><input type="password" name="atualpassword"></td>
						</tr>
						<?php echo "\n";
					}//if
					?>
					<tr>
						<th align="right">Nova senha:</th>
						<td><input type="password" name="newpassword"></td>
					</tr>
					<tr>
						<th align="right">Confirmar senha:</th>
						<td><input type="password" name="confirmpassword"></td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" value="Alterar">
							<input type="button" value="Cancelar" onClick="javascript:self.close();">
						</td>
					</tr>
				</table>
				<input type="hidden" name="oldusername" value="<?php echo $username; ?>">
				<?php echo "\n";
				if($type == "normal")
				{
					?>
					<input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
					<?php echo "\n";
				}//if
				else
				{
					?>
					<input type="hidden" name="admin" value="<?php echo $username; ?>">
					<?php echo "\n";
				}//else
				?>
				<input type="hidden" name="type" value="<?php echo $type; ?>">
			</form>
		</p>

		<?php echo "\n";
		
	}//else
}//function altera_senha()



function main()
{
	if(isset($_POST['codigo']) || isset($_POST['admin']))
	{
		$type = (isset($_POST['codigo'])) ? "normal" : "admin";
		if(!isset($_POST['oldusername']) && !isset($_POST['newpassword']) && !isset($_POST['confirmpassword']))
		{
			html_header("Alterar senha");
			formulario($type);
			html_footer();
		}//if
		else
		{
			html_header("Alterar senha");
			altera_senha();
			html_footer();
		}//else
	}//if
}//main()

main();
?>