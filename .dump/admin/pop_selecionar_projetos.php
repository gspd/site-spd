<?php
include_once("login.php");
html_header("Selecionar projetos", "javascript:seleciona_projetos()");
?>
<p align="center" class="title">Selecionar projetos:</p>
<center>
<table border="0">
	<form name="projetos">
		<?php echo "\n";
		$lista = new Info_lista("projetos");
		$projetos = $lista->dados_lista;
		$numprojetos = count($projetos);
		
		for($i=0; $i < $numprojetos; $i++)
		{
			$title = $projetos[$i]['titulo'];
			?>
			<tr>
				<td align="left">
					<input type="checkbox" name="newprojeto[]"><?php echo $title; ?>
				</td>
				<td align="right">
					<select name="newfuncao[]">
						<option value="1">Avaliador</option>
						<option value="2">Orientador</option>
					</select>
				</td>
			</tr>
			<?php echo "\n";
		}//for
		?>
		<tr>
			<td colspan="2" align="center">
				<input type="button" value="Confirmar" onClick="javascript:confirm();">
				<input type="button" value="Cancelar" onClick="javascript:self.close();">
			</td>
		</tr>
	</form>
</table>
</center>
<script language="javascript">
	<!--
	function search_field(form, fieldname, num)
	{
		i = 0;
		for(j=i; form.elements[j]; j++)
		{
			if(form.elements[j].name == fieldname)
				if (i == num)
					return form.elements[j];
				else
					i++;
		}//for
	}//function
	
	function seleciona_projetos()
	{
		numprojetos = <?php echo $numprojetos; ?>;
		form_source = opener.document.forms.cadastro;
		form_target = document.forms['projetos'];
		
		for(i=0; i < numprojetos; i++)
		{
			projeto_source = search_field(form_source, "newprojeto[]", i);
			projeto_target = search_field(form_target, "newprojeto[]", i);
			funcao_source = search_field(form_source, "newfuncao[]", i);
			funcao_target = search_field(form_target, "newfuncao[]", i);
			
			if (projeto_source.value == 1)
			{
				projeto_target.checked = true;
				funcao_target.selectedIndex = funcao_source.value - 1;
			}//if
		}//for
	}//function
	
	function confirm()
	{
		numprojetos = <?php echo $numprojetos; ?>;
		form_source = opener.document.forms['cadastro'];
		form_target = document.forms['projetos'];
		
		for(i=0; i < numprojetos; i++)
		{
			projeto_source = search_field(form_source, "newprojeto[]", i);
			projeto_target = search_field(form_target, "newprojeto[]", i);
			funcao_source = search_field(form_source, "newfuncao[]", i);
			funcao_target = search_field(form_target, "newfuncao[]", i);
			
			projeto_source.value = (projeto_target.checked) ? "1" : "0";
			funcao_source.value = funcao_target.selectedIndex + 1;
			
			if(projeto_target.checked)
			{
				eval("opener.document.getElementById('projeto" + i + "').style.display = \"\"");
				if(funcao_target.selectedIndex)
				{
					eval("opener.document.getElementById('orientador" + i + "').style.display = \"\"");
					eval("opener.document.getElementById('avaliador" + i + "').style.display = \"none\"");
				}//if
				else
				{
					eval("opener.document.getElementById('avaliador" + i + "').style.display = \"\"");
					eval("opener.document.getElementById('orientador" + i + "').style.display = \"none\"");
				}//else
			}//if
			else
			{
				eval("opener.document.getElementById('projeto" + i + "').style.display = \"none\"");
				eval("opener.document.getElementById('avaliador" + i + "').style.display = \"none\"");
				eval("opener.document.getElementById('orientador" + i + "').style.display = \"none\"");
			}//else
		}//for
		
		self.close();
	}
	
	-->
</script>
<?php echo "\n";
html_footer();
?>