<?php
include_once("mysql.php");

function info_true_self($self, $absolute = 0)
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

$prefixo = "novo_";

$tabelas = array(
"admin",
"aluno",
"avisos",
"capitulo",
"defesas",
"disciplina",
"login",
"notas",
"notas_finais",
"professor",
"projeto",
"penalidades_locais",
"prof_proj",
"submete",
"avalia");

for($i=0; $i < count($tabelas); $i++)
{
	global ${"tabela_".$tabelas[$i]};
	${"tabela_".$tabelas[$i]} = $prefixo.$tabelas[$i];
}

//Entrada:

//atrav�s do m�todo construtor, informando os dados (todos opcionais)
//caso nenhum dado seja informado, ser� criada apenas a inst�ncia do objeto e n�o gerar� sa�da
//	-$codigo = RA do aluno
//	-$show_projeto = valor booleano, indicando se deve ser buscado o nome do projeto do qual o aluno faz parte
//	-$only_name = valor booleano, indicando se deve ser retornado apenas o nome do aluno

//atrav�s do m�todo de processamento (Processa), informando os mesmos dados que o m�todo construtor,
//exceto que para este o par�metro RA deve ser fornecido, sendo os dois restantes opcionais

//Sa�da:

//atrav�s da propriedade dados_aluno, que cont�m a seguinte estrutura de dados:
//$dados_aluno['codigo'] = RA do aluno
//$dados_aluno['name'] = nome do aluno
//$dados_aluno['email'] = e-mail do aluno

//caso o valor par�metro $show_projeto seja 0, ser� gerado a seguinte informa��o:
//$dados_aluno['projeto'] = c�digo do projeto do qual o aluno participa
//caso contr�rio, ser� gerada a seguinte informa��o:
//$dados_aluno['projeto']['id'] = c�digo do projeto do qual o aluno participa
//$dados_aluno['projeto']['title'] = t�tulo do projeto do qual o aluno participa
class Info_aluno
{
	var $dados_aluno;
	
	function Info_aluno($codigo = 0, $show_projeto = 1, $only_name = 0)
	{
		if($codigo)
			$this->Processa($codigo, $show_projeto, $only_name);
	}
	
	function Processa($codigo, $show_projeto = 1, $only_name = 0)
	{
		global $tabelas;

		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_aluno = NULL;
		
		$fields = ($only_name) ? "nome_aluno" : "codigo, nome_aluno, email_aluno, id_projeto";
		
		$query = new Query($fields, $tabela_aluno, "codigo = '$codigo'");
		
		$this->dados_aluno = $query->rows[0];
		
		if(!$only_name)
		{
			if($show_projeto)
			{
				$query->Query("id_projeto, titulo", $tabela_projeto, "id_projeto = '".$this->dados_aluno['id_projeto']."'");
				$this->dados_aluno['projeto'] = $query->rows[0];
			}
		}//if
	}//function Processa()
	
	function inserir_aluno($codigo, $nome_aluno, $email_aluno, $id_projeto, $username, $password)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query();
		$query->Query2("INSERT INTO $tabela_aluno VALUES ('$codigo','$nome_aluno','$email_aluno','$id_projeto')");
		$query->Query2("INSERT INTO $tabela_login VALUES ('$username',PASSWORD('$password'),'$codigo')");
		
		$this->dados_aluno = NULL;
		
		$this->Processa($codigo);
	}//function inserir_aluno()
	
	function alterar_aluno($codigo_antigo, $novo_codigo, $novo_nome_aluno, $novo_email_aluno, $novo_id_projeto)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string[] = "UPDATE IGNORE $tabela_aluno
						   SET
						   nome_aluno = '$novo_nome_aluno',
						   email_aluno = '$novo_email_aluno',
						   id_projeto = '$novo_id_projeto'
						   WHERE codigo = '$codigo_antigo'";
		
		if($novo_codigo != $codigo_antigo)
		{
			$query_string[] = "UPDATE IGNORE $tabela_aluno
							SET codigo = '$novo_codigo'
							WHERE codigo = '$codigo_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_notas_finais
							SET codigo = '$novo_codigo'
							WHERE codigo = '$codigo_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_login
							SET codigo = '$novo_codigo'
							WHERE codigo = '$codigo_antigo'";
		}//if
		
		$query = new Query();
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
		$this->dados_aluno = NULL;
		
		$this->Processa($novo_codigo);
	}//function alterar_aluno()
	
	function excluir_aluno($codigo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query();
		
		$query_string[] = "DELETE FROM $tabela_aluno WHERE codigo = '$codigo'";
		$query_string[] = "DELETE FROM $tabela_login WHERE codigo = '$codigo'";
		$query_string[] = "DELETE FROM $tabela_notas_finais WHERE codigo = '$codigo'";
		
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
		$this->dados_aluno = NULL;
	}//function excluir_aluno()
}//Info_aluno



class Info_professor
{
	var $dados_professor;
	
	function Info_professor($codigo = 0, $id = 0, $only_name = 0)
	{
		if($codigo)
			$this->Processa($codigo, $id, $only_name);
	}
	
	function Processa($codigo, $id = 0, $only_name = 0)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_professor = NULL;
		
		$fields = ($only_name) ? "doutor, nome_professor" : "codigo, doutor, nome_professor, email_professor";
		
		$query = new Query($fields, $tabela_professor, "codigo = '$codigo'");

		if($query->num_rows)
		{
			$this->dados_professor = $query->rows[0];
			
			if(!$only_name)
				$this->dados_professor['codigo'] = sprintf("%02d", $query->rows[0]['codigo']);
		}
		else
			$this->dados_professor = NULL;
		
		if(!$id)
		{
			$query->Query("{$tabela_projeto}.id_projeto, {$tabela_projeto}.titulo, {$tabela_prof_proj}.funcao",
						  "{$tabela_projeto}, {$tabela_prof_proj}",
						  "{$tabela_projeto}.id_projeto = {$tabela_prof_proj}.id_projeto AND {$tabela_prof_proj}.codigo = '$codigo'", "", "{$tabela_projeto}.id_projeto");
			
			$this->dados_professor['projeto'] = $query->rows;
		}//if
		else
		{
			$query->Query("funcao", $tabela_prof_proj, "codigo = '$codigo' AND id_projeto = '$id'");
			$this->dados_professor['funcao'] = $query->rows[0]['funcao'];
		}//else
	}//function Processa()
	
	//$projetos deve ser:
	//$projetos[n]['id_projeto']
	//$projetos[n]['funcao']
	function inserir_professor($codigo, $titulo, $nome_professor, $email_professor, $projetos, $username, $password)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		if(!empty($projetos))
		{
			$prof_proj = "('$codigo','{$projetos[0]['id_projeto']}','{$projetos[0]['funcao']}')";
			for($i=1; $i < count($projetos); $i++)
				$prof_proj .= ",\n('$codigo','{$projetos[$i]['id_projeto']}','{$projetos[$i]['funcao']}')";
		}//if
		
		
		$query_string[] = "INSERT INTO $tabela_professor VALUES ('$codigo', '$titulo', '$nome_professor', '$email_professor')";
		$query_string[] = "INSERT INTO $tabela_login VALUES ('$username',PASSWORD('$password'),'$codigo')";
		$query_string[] = "INSERT INTO $tabela_prof_proj VALUES\n".$prof_proj;
		
		$query = new Query();
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
		$this->Processa($codigo);
	}//function inserir_professor()
	
	//$novos_projetos deve ser:
	//$novos_projetos[n]['id_projeto']
	//$novos_projetos[n]['funcao']
	//$novos_projetos[n]['selecionar']
	function alterar_professor($codigo_antigo, $novo_codigo, $novo_titulo, $novo_nome_professor, $novo_email_professor, $novos_projetos)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string[] = "UPDATE IGNORE $tabela_professor
						   SET
						   doutor = '$novo_titulo',
						   nome_professor = '$novo_nome_professor',
						   email_professor = '$novo_email_professor'
						   WHERE codigo = '$codigo_antigo'";
		
		if($novo_codigo != $codigo_antigo)
		{
			$query_string[] = "UPDATE IGNORE $tabela_professor
							SET codigo = '$novo_codigo'
							WHERE codigo = '$codigo_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_login
							SET codigo = '$novo_codigo'
							WHERE codigo = '$codigo_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_prof_proj
							SET codigo = '$novo_codigo'
							WHERE codigo = '$codigo_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_avalia
							SET codigo = '$novo_codigo'
							WHERE codigo = '$codigo_antigo'";
		}//if
		
		$query = new Query("{$tabela_projeto}.id_projeto,
							{$tabela_prof_proj}.codigo IS NOT NULL as 'selecionar',
							{$tabela_prof_proj}.funcao+0 as 'funcao'",
						   "$tabela_projeto LEFT JOIN $tabela_prof_proj ON
							{$tabela_prof_proj}.id_projeto = {$tabela_projeto}.id_projeto AND
							{$tabela_prof_proj}.codigo = '$codigo_antigo'", "", "", "{$tabela_projeto}.id_projeto");
		
		$projetos_antigos = $query->rows;
		
		for($i=0; $i < count($novos_projetos); $i++)
		{
			//se determinado projeto for selecionado
			if($novos_projetos[$i]['selecionar'] && !$projetos_antigos[$i]['selecionar'])
				$query_string[] = "INSERT INTO $tabela_prof_proj VALUES ('{$novo_codigo}','{$novos_projetos[$i]['id_projeto']}','{$novos_projetos[$i]['funcao']}')";
			//se determinado projeto n�o for selecionado
			else if(!$novos_projetos[$i]['selecionar'] && $projetos_antigos[$i]['selecionar'])
				$query_string[] = "DELETE FROM $tabela_prof_proj
								   WHERE codigo = '$novo_codigo' AND id_projeto = '{$novos_projetos[$i]['id_projeto']}'";
			//se a fun��o do professor foi alterada
			else if($novos_projetos[$i]['funcao'] != $projetos_antigos[$i]['funcao'])
				$query_string[] = "UPDATE $tabela_prof_proj SET funcao = '{$novos_projetos[$i]['funcao']}'
								   WHERE codigo = '$novo_codigo' AND id_projeto = '{$novos_projetos[$i]['id_projeto']}'";
		}
		
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
		$this->Processa($novo_codigo);
	}//function alterar_professor()
	
	function excluir_professor($codigo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string[] = "DELETE FROM $tabela_professor WHERE codigo = '$codigo'";
		$query_string[] = "DELETE FROM $tabela_login WHERE codigo = '$codigo'";
		$query_string[] = "DELETE FROM $tabela_prof_proj WHERE codigo = '$codigo'";
		$query_string[] = "DELETE FROM $tabela_avalia WHERE codigo = '$codigo'";
		
		$query = new Query();
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
		$this->dados_professor = NULL;
	}//function excluir_professor()
}//class Info_professor




class Info_projeto
{
	var $dados_projeto;
	
	function Info_projeto($id = 0, $only_names = 0)
	{
		if($id)
			$this->Processa($id, $only_names);
	}
	
	function Processa($id, $only_names = 0)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_projeto = NULL;
		$this->dados_projeto['professores'] = array();
		$this->dados_projeto['alunos'] = array();
		
		$aluno = new Info_aluno();
		$professor = new Info_professor();
		
		$query = new Query("id_projeto, titulo", $tabela_projeto, "id_projeto = '$id'");
		
		$this->dados_projeto = $query->rows[0];
		
		$query->Query("codigo", $tabela_aluno, "id_projeto = '$id'");
		
		
		for($i=0; $i < $query->num_rows; $i++)
		{
			$aluno->Processa($query->rows[$i]['codigo'], 0, $only_names);
			$this->dados_projeto['alunos'][$i] = $aluno->dados_aluno;
		}
		
		$query->Query("{$tabela_prof_proj}.codigo, {$tabela_prof_proj}.funcao",
					  "{$tabela_prof_proj}, {$tabela_professor}",
					  "{$tabela_prof_proj}.id_projeto = '$id' AND {$tabela_prof_proj}.codigo = {$tabela_professor}.codigo", "", "funcao DESC, nome_professor");
		
		$j = 0;
		$k = 0;
		
		for($i=0; $i < $query->num_rows; $i++)
		{
			$professor->Processa($query->rows[$i]['codigo'], $id, $only_names);
			$this->dados_projeto['professores'][$i] = $professor->dados_professor;
			
			if($query->rows[$i]['funcao'] == "avaliador")
				$this->dados_projeto['banca']['avaliador'][$j++] = $i;
			else
				$this->dados_projeto['banca']['orientador'][$k++] = $i;
		}//for
	}//function Processa()
	
	function inserir_projeto($id_projeto, $titulo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string[] = "INSERT INTO $tabela_projeto VALUES ('$id_projeto','$titulo')";
		
		$query = new Query();
		$query->Query2($query_string[0]);
		
		$this->Processa($id_projeto);
	}//function inserir_projeto()
	
	
	function alterar_projeto($id_projeto_antigo, $novo_id_projeto, $novo_titulo = '')
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		if(!empty($novo_titulo))
		{
			$query_string[] = "UPDATE IGNORE $tabela_projeto
							   SET
							   titulo = '$novo_titulo'
							   WHERE id_projeto = '$id_projeto_antigo'";
		}//if
		
		if($novo_id_projeto != $id_projeto_antigo)
		{
			
			//implementar a altera��o do nome dos diret�rios
			
			$query_string[] = "UPDATE IGNORE $tabela_projeto
							   SET id_projeto = '$novo_id_projeto'
							   WHERE id_projeto = '$id_projeto_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_defesas
							   SET id_projeto = '$novo_id_projeto'
							   WHERE id_projeto = '$id_projeto_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_notas
							   SET id_projeto = '$novo_id_projeto'
							   WHERE id_projeto = '$id_projeto_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_aluno
							   SET id_projeto = '$novo_id_projeto'
							   WHERE id_projeto = '$id_projeto_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_prof_proj
							   SET id_projeto = '$novo_id_projeto'
							   WHERE id_projeto = '$id_projeto_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_avalia
							   SET id_projeto = '$novo_id_projeto'
							   WHERE id_projeto = '$id_projeto_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_submete
							   SET id_projeto = '$novo_id_projeto'
							   WHERE id_projeto = '$id_projeto_antigo'";
			
			$query_string[] = "UPDATE IGNORE $tabela_penalidades_locais
							   SET id_projeto = '$novo_id_projeto'
							   WHERE id_projeto = '$id_projeto_antigo'";
		}//if
		
		$self = info_true_self($_SERVER['PHP_SELF'],1);
		$dir = $self."/arquivos/";
		
		if(file_exists($dir.$id_projeto_antigo))
		{
			if(!rename($dir.$id_projeto_antigo, $dir.$novo_id_projeto))
				die("Erro ao renomear o diret&oacute;rio!");
		}//if
		
		$query = new Query();
		
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
	}//function alterar_projeto()
	
	
	function excluir_projeto($id_projeto)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$num_projetos = $this->num_projetos();	
		$query = new Query();
		$aluno = new Info_aluno();
		
		$this->mover_projeto($id_projeto, $num_projetos);
		
		
		$self = info_true_self($_SERVER['PHP_SELF'],1);
		$dir = $self."/arquivos/$num_projetos";
		
		if(file_exists($dir))
		{
			if(!$dp = opendir($dir))
				die("Erro ao abrir o diret&oacute;rio!");
			
			while(($name = readdir($dp)) !== false)
			{
				if($name != "." && $name != "..")
				{
					if(!unlink("$dir/$name"))
						die("Erro ao excluir o arquivo '$name'!");
				}//if
			}//if
			
			//observar o comportamento desse comando
			if(!rmdir($dir))
				die("Erro ao excluir o diret&oacute;rio '$dir'!");
		}
		
		$query_string[] = "DELETE FROM $tabela_projeto WHERE id_projeto = '$num_projetos'";
		$query_string[] = "DELETE FROM $tabela_defesas WHERE id_projeto = '$num_projetos'";
		$query_string[] = "DELETE FROM $tabela_notas WHERE id_projeto = '$num_projetos'";
		
		//$query_string[] = "DELETE FROM $tabela_aluno WHERE id_projeto = '$num_projetos'";
		$query->Query("codigo", $tabela_aluno, "id_projeto = '$num_projetos'");
		
		for($i=0; $i < $query->num_rows; $i++)
			$aluno->excluir_aluno($query->rows[$i]['codigo']);
		
		$query_string[] = "DELETE FROM $tabela_prof_proj WHERE id_projeto = '$num_projetos'";
		$query_string[] = "DELETE FROM $tabela_avalia WHERE id_projeto = '$num_projetos'";
		$query_string[] = "DELETE FROM $tabela_submete WHERE id_projeto = '$num_projetos'";
		$query_string[] = "DELETE FROM $tabela_penalidades_locais WHERE id_projeto = '$num_projetos'";
		
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
		$this->dados_projeto = NULL;
	}//function excluir_projeto()
	
	
	function mover_projeto($id_projeto_antigo, $novo_id_projeto)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		if($novo_id_projeto != $id_projeto_antigo)
		{
			$this->alterar_projeto($id_projeto_antigo, 0);
			
			if($id_projeto_antigo < $novo_id_projeto)
			{
				for($i=$id_projeto_antigo; $i < $novo_id_projeto; $i++)
					$this->alterar_projeto($i+1, $i);
			}//if
			else
			if($id_projeto_antigo > $novo_id_projeto)
			{
				for($i=$id_projeto_antigo; $i > $novo_id_projeto; $i--)
					$this->alterar_projeto($i-1, $i);
			}//elseif
			
			$this->alterar_projeto(0, $novo_id_projeto);
			
			$this->dados_projeto = NULL;
		}
	}//function mover_aviso()
	
	
	function num_projetos()
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query("COUNT(*) AS 'num_projetos'", $tabela_projeto);
		return $query->rows[0]['num_projetos'];
	}//function num_projetos()
}//class Info_projeto




class Handle_arquivo
{
	var $dados_arquivo;
	
	function Handle_arquivo()
	{
		//empty
	}
	
	function existe($projeto, $name)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_arquivo = NULL;
		
		$self = info_true_self($_SERVER['PHP_SELF']);
		
		if($projeto)
		{
			$remotename = $filename = $self."/arquivos/$projeto/$name";
		}
		else
			$remotename = $filename = $self."/$name";
		
		$remotename = "http://".$_SERVER['HTTP_HOST'].$filename;
		$filename = $_SERVER['DOCUMENT_ROOT'].$filename;
		
		//extens�es de arquivos aceitas para upload
		$accept = array(".doc", ".pdf", ".zip", ".rar");
		
		for($i=0; $i < count($accept); $i++)
		{
			if(file_exists($filename.$accept[$i]))
			{
				$filename .= $accept[$i];
				$remotename .= $accept[$i];
				$this->dados_arquivo = array("local" => $filename, "remoto" => htmlentities($remotename, ENT_QUOTES));
				return 1;
				break;
			}
		}//for
		
		if($i == count($accept))
			return 0;
	} //function existe()
	
	function delete($id, $filename)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		if($this->existe($id, $filename))
		{
			if(!unlink($this->dados_arquivo['local']))
				die("Erro ao excluir o arquivo! Favor contactar respons&aacute;vel!");
			
			$this->dados_arquivo = NULL;
		}//if
	}//function delete()
			
}//class Handle_arquivo



class Info_entregas
{
	var $dados_entregas;
	
	function Info_entregas($id = 0)
	{
		if($id)
			$this->Processa($id);
	}
	
	function Processa($id)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_entregas = NULL;
		
		$arquivo = new Handle_arquivo();
		$query = new Query();
		
		$query->Query("{$tabela_submete}.id_capitulo IS NOT NULL AS 'entregue',
					   UNIX_TIMESTAMP({$tabela_submete}.data_submissao) AS 'data_submissao',
					   {$tabela_capitulo}.id_capitulo,
					   {$tabela_capitulo}.nome_capitulo,
					   {$tabela_capitulo}.tipo",
				      "$tabela_capitulo LEFT JOIN $tabela_submete ON
					   {$tabela_submete}.id_capitulo = {$tabela_capitulo}.id_capitulo AND
					   {$tabela_submete}.id_projeto = '$id'",
				      "{$tabela_capitulo}.tipo = 'aluno'", "",
					  "{$tabela_capitulo}.id_capitulo");
		
		$submissoes = $query->rows;
		$num_submissoes = $query->num_rows;
		
		$query->Query("{$tabela_avalia}.id_capitulo IS NOT NULL AS 'entregue',
					   {$tabela_avalia}.codigo,
					   UNIX_TIMESTAMP({$tabela_avalia}.data_avaliacao) AS 'data_avaliacao',
					   {$tabela_capitulo}.id_capitulo,
					   {$tabela_capitulo}.nome_capitulo,
					   {$tabela_capitulo}.tipo",
					  "$tabela_capitulo LEFT JOIN $tabela_avalia ON
					   {$tabela_avalia}.id_capitulo = {$tabela_capitulo}.id_capitulo AND
					   {$tabela_avalia}.id_projeto = '$id'",
					  "{$tabela_capitulo}.tipo = 'professor'", "",
					  "{$tabela_capitulo}.id_capitulo");
		
		$avaliacoes = $query->rows;
		$num_avaliacoes = $query->num_rows;
		
		
		if($num_submissoes || $num_avaliacoes)
		{
			
			
			for($i=0; $i < $num_submissoes; $i++)
			{
				$this->dados_entregas[$i] = $submissoes[$i];
				$this->dados_entregas[$i]['codigo'] = "000000";
				
				if($submissoes[$i]['entregue'])
				{
					$query->Query("codigo", $tabela_aluno, "id_projeto = '$id'");
					
					for($j=0; $j < $query->num_rows; $j++)
					{
						$filename = $submissoes[$i]['nome_capitulo']."-".$query->rows[$j]['codigo'];
						
						if($arquivo->existe($id, $filename))
						{
							$this->dados_entregas[$i]['nome_arquivo'] = $arquivo->dados_arquivo;
							$this->dados_entregas[$i]['codigo'] = $query->rows[$j]['codigo'];
							break;
						}//if
					}
					
					if($this->dados_entregas[$i]['codigo'] == "000000")
					{
						$id_capitulo = $submissoes[$i]['id_capitulo'];
						$query->Query2("DELETE FROM $tabela_submete WHERE id_projeto = '$id' AND id_capitulo = '$id_capitulo'");
						$this->dados_entregas[$i]['entregue'] = 0;
					}//else
				}//if
			}//for
			
			
			for($i=$num_submissoes, $j=0; $j < $num_avaliacoes; $i++, $j++)
			{
				$this->dados_entregas[$i] = $avaliacoes[$j];
				$this->dados_entregas[$i]['codigo'] = $avaliacoes[$j]['codigo'] = sprintf("%02d", $avaliacoes[$j]['codigo']);
				
				if($avaliacoes[$j]['entregue'])
				{
					$filename = $avaliacoes[$j]['nome_capitulo']."-".$avaliacoes[$j]['codigo'];
					
					if($arquivo->existe($id, $filename))
						$this->dados_entregas[$i]['nome_arquivo'] = $arquivo->dados_arquivo;
					else
					{
						$id_capitulo = $avaliacoes[$j]['id_capitulo'];
						$codigo = $avaliacoes[$j]['codigo'];
						
						$query->Query2("DELETE FROM $tabela_avalia WHERE id_projeto = '$id' AND codigo = '$codigo' AND id_capitulo = '$id_capitulo'");
						$this->dados_entregas[$i]['entregue'] = 0;
						$this->dados_entregas[$i]['codigo'] = "00";
					}//else
				}//if
			}//for
		}//if
	}//function Processa()
	
	
	
	
	function inserir_entrega($id_projeto, $codigo = 0, $id_capitulo, $tipo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query();
		
		if($tipo == "aluno")
			$query_string = "INSERT INTO $tabela_submete VALUES ('$id_projeto','$id_capitulo',NOW())";
		else if($tipo == "professor")
			$query_string = "INSERT INTO $tabela_avalia VALUES ('$id_projeto','$codigo','$id_capitulo',NOW())";
		
		$query->Query2($query_string);
		$this->Processa($id_projeto);
	}//function inserir_entrega
	
	
	
	
	function excluir_entrega($id_projeto, $codigo = 0, $id_capitulo, $tipo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$arquivo = new Handle_arquivo();
		$capitulos = new Info_capitulos();
		$query = new Query();
		
		$capitulos->Dados_capitulo($id_capitulo);
		
		$nome_capitulo = $capitulos->dados_capitulos['capitulos'][$i]['nome_capitulo'];
		
		if($tipo == "aluno")
		{
			$query->Query("codigo", $tabela_aluno, "id_projeto = '$id_projeto'");
			
			for($i=0; $i < $query->num_rows; $i++)
			{
				$nome_arquivo = $nome_capitulo."-".$query->rows[$i]['codigo'];
				$arquivo->delete($id_projeto, $nome_arquivo);
			}
		}//if
		else
		{
			$nome_arquivo = "$nome_capitulo-$codigo";
			$arquivo->delete($id_projeto, $nome_arquivo);
		}//else
		
		if($tipo == "aluno")
			$query_string = "DELETE FROM $tabela_submete WHERE id_projeto = '$id_projeto' AND id_capitulo = '$id_capitulo'";
		else
			$query_string = "DELETE FROM $tabela_avalia WHERE id_projeto = '$id_projeto' AND codigo = '$codigo' AND id_capitulo = '$id_capitulo'";
		
		$query->Query2($query_string);
		
		$this->dados_entregas = NULL;
	}//function excluir_entrega
}//class InfoEntregas



class Info_capitulos
{
	var $dados_capitulos;
	
	function Info_capitulos($id = 0)
	{
		if($id)
			$this->Processa($id);
	}
	
	
	
	function Dados_capitulo($id_capitulo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_capitulos = NULL;
		
		$query = new Query("id_capitulo, tipo, nome_capitulo, legenda_capitulo, UNIX_TIMESTAMP(data_entrega) AS 'data_entrega', aplicar_penalidade",
						   $tabela_capitulo, "id_capitulo = '$id_capitulo'");
		
		if($query->num_rows)
			$this->dados_capitulos = $query->rows[0];
		else
		{
			$this->dados_capitulos['id_capitulo'] = $id_capitulo;
			$this->dados_capitulos['tipo'] = "";
			$this->dados_capitulos['nome_capitulo'] = "";
			$this->dados_capitulos['legenda_capitulo'] = "";
			$this->dados_capitulos['data_entrega'] = 0;
			$this->dados_capitulos['aplicar_penalidade'] = "";
		}
	}
	
	
	function Processa($id = 0)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_capitulos = NULL;
		$this->dados_capitulos['penalidades'] = array();
		
		$query = new Query("id_capitulo, tipo, nome_capitulo, legenda_capitulo, UNIX_TIMESTAMP(data_entrega) AS 'data_entrega', aplicar_penalidade",
						   $tabela_capitulo,
						   "", "", "id_capitulo");
		$this->dados_capitulos['capitulos'] = $query->rows;
		
		if($id)
		{
			$query->Query("{$tabela_penalidades_locais}.id_capitulo IS NOT NULL AS 'existe',
						   IF({$tabela_penalidades_locais}.id_capitulo IS NULL, 0, {$tabela_penalidades_locais}.aplicar_penalidade_local) AS 'ignorar_penalidade',
						   UNIX_TIMESTAMP({$tabela_penalidades_locais}.nova_data_entrega) AS 'nova_data_entrega'",
						  "$tabela_capitulo LEFT JOIN $tabela_penalidades_locais ON
						   {$tabela_penalidades_locais}.id_projeto = '$id' AND
						   {$tabela_capitulo}.id_capitulo = {$tabela_penalidades_locais}.id_capitulo",
						  "{$tabela_capitulo}.tipo = 'aluno'",
						  "", "{$tabela_capitulo}.id_capitulo");
			$controle_penalidades = $query->rows;
			
			$entregas = new Info_entregas($id);
			
			for($i=0; $i < count($controle_penalidades); $i++)
			{
				if($controle_penalidades[$i]['existe'])
					$this->dados_capitulos['capitulos'][$i]['data_entrega'] = $controle_penalidades[$i]['nova_data_entrega'];
				
				if(!$entregas->dados_entregas[$i]['entregue'])
					$entregas->dados_entregas[$i]['data_submissao'] = time();
				
				$entrega = getdate($entregas->dados_entregas[$i]['data_submissao']);
				$prazo = getdate($this->dados_capitulos['capitulos'][$i]['data_entrega']);
				$atraso = $entrega[0] - $prazo[0];
				
				if($this->dados_capitulos['capitulos'][$i]['aplicar_penalidade'] && !$controle_penalidades[$i]['ignorar_penalidade'])
				{
					if($atraso > 0)
					{
						$a = mktime(0, 0, 0, $entrega['mon'], $entrega['mday'], $entrega['year']);
						$b = mktime(0, 0, 0, $prazo['mon'], $prazo['mday'], $prazo['year']);
						$atraso = $a - $b;
						
						$atraso = ceil($atraso/86400);
						if($atraso == 0)
							$atraso = 1;
						$this->dados_capitulos['penalidades'][$i] = $atraso*0.2;
					}//if
					else
						$this->dados_capitulos['penalidades'][$i] = 0;
				}//if
				else if(!$this->dados_capitulos['capitulos'][$i]['aplicar_penalidade'])
					$this->dados_capitulos['penalidades'][$i] = "-";
				else
					$this->dados_capitulos['penalidades'][$i] = "No";
			}//for
			
			$this->dados_capitulos['entregas'] = $entregas->dados_entregas;
		}//if
	}//function Processa()
	
	
	
	function inserir_capitulo($id_capitulo, $tipo, $nome_capitulo, $legenda_capitulo, $data_entrega, $aplicar_penalidade)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$num_capitulos = $this->num_capitulos()+1;
		
		$query_string = "INSERT INTO $tabela_capitulo VALUES ('$num_capitulos','$tipo','$nome_capitulo','$legenda_capitulo','$data_entrega','$aplicar_penalidade')";
		
		$query = new Query();
		$query->Query2($query_string);
		
		$this->mover_capitulo($num_capitulos, $id_capitulo);
		
		$this->dados_capitulos = NULL;
	}//function inserir_capitulo()
	
	
	function alterar_capitulo($id_capitulo_antigo, $novo_id_capitulo, $novo_nome_capitulo = '', $novo_legenda_capitulo = '', $novo_data_entrega = '', $novo_aplicar_penalidade = '')
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query();
		$entregas = new Info_entregas();
		
		if(!empty($novo_nome_capitulo))
		{
			$this->Dados_capitulo($id_capitulo_antigo);
			$query = new Query();
			
			$nome_capitulo = $this->dados_capitulos['nome_capitulo'];
			
			if($novo_nome_capitulo != $nome_capitulo)
			{
				$query->Query("COUNT(id_projeto) AS 'num_projetos'", $tabela_projeto);
				
				$num_projetos = $query->rows[0]['num_projetos'];
				
				for($i=1; $i <= $num_projetos; $i++)
				{
					$entregas->Processa($i);
					
					for($j=0; $j < count($entregas->dados_entregas); $j++)
					{
						if($entregas->dados_entregas[$j]['id_capitulo'] == $id_capitulo_antigo)
						{
							$tipo = $entregas->dados_entregas[$j]['tipo'];
							$nome_arquivo = $entregas->dados_entregas[$j]['nome_arquivo']['local'];
							$dir_arquivo = dirname($nome_arquivo);
							$extensao_arquivo = pathinfo($nome_arquivo, PATHINFO_EXTENSION);
							
							if($tipo == "aluno")
								$novo_nome_arquivo = "{$dir_arquivo}/{$novo_nome_capitulo}.{$extensao_arquivo}";
							else
								$novo_nome_arquivo = $dir_arquivo."/".$novo_nome_capitulo."-".$entregas->dados_entregas[$j]['codigo'].".".$extensao_arquivo;
							
							rename($nome_arquivo, $novo_nome_arquivo);
						}//if
					}//for
				}//for
			}//if
			
			$query_string[] = "UPDATE IGNORE $tabela_capitulo
							   SET
							   nome_capitulo = '$novo_nome_capitulo',
							   legenda_capitulo = '$novo_legenda_capitulo',
							   data_entrega = '$novo_data_entrega',
							   aplicar_penalidade = '$novo_aplicar_penalidade'
							   WHERE id_capitulo = '$id_capitulo_antigo'";
		}//if
		
		if($novo_id_capitulo != $id_capitulo_antigo)
		{
			$query_string[] = "UPDATE $tabela_capitulo
							   SET id_capitulo = '$novo_id_capitulo'
							   WHERE id_capitulo = '$id_capitulo_antigo'";
			
			$query_string[] = "UPDATE $tabela_penalidades_locais
							   SET id_capitulo = '$novo_id_capitulo'
							   WHERE id_capitulo = '$id_capitulo_antigo'";
			
			$query_string[] = "UPDATE $tabela_submete
							   SET id_capitulo = '$novo_id_capitulo'
							   WHERE id_capitulo = '$id_capitulo_antigo'";
			
			$query_string[] = "UPDATE $tabela_avalia
							   SET id_capitulo = '$novo_id_capitulo'
							   WHERE id_capitulo = '$id_capitulo_antigo'";
		}//if
		
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
	}//function alterar_capitulo()
	
	
	function excluir_capitulo($id_capitulo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$entregas = new Info_entregas();
		$query = new Query("COUNT(id_projeto) AS 'num_projetos'", $tabela_projeto);
		
		$num_projetos = $query->rows[0]['num_projetos'];
		
		for($i=1; $i <= $num_projetos; $i++)
		{
			$entregas->Processa($i);
			echo "i1=$i<br>\n";
			for($j=0; $j < count($entregas->dados_entregas); $j++)
			{
				if($entregas->dados_entregas[$j]['id_capitulo'] == $id_capitulo)
					$entregas->excluir_entrega($i, $entregas->dados_entregas[$j]['codigo'], $id_capitulo, $entregas->dados_entregas[$j]['tipo']);
			}//for
		}//for
		
		$num_capitulos = $this->num_capitulos();
		$this->mover_capitulo($id_capitulo, $num_capitulos);
		
		$query_string[] = "DELETE FROM $tabela_capitulo WHERE id_capitulo = '$num_capitulos'";
		$query_string[] = "DELETE FROM $tabela_penalidades_locais WHERE id_capitulo = '$num_capitulos'";
		$query_string[] = "DELETE FROM $tabela_submete WHERE id_capitulo = '$num_capitulos'";
		$query_string[] = "DELETE FROM $tabela_avalia WHERE id_capitulo = '$num_capitulos'";
		
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
		$this->dados_capitulos = NULL;
	}//function excluir_capitulo()
	
	
	function mover_capitulo($id_capitulo_antigo, $novo_id_capitulo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		if($novo_id_capitulo != $id_capitulo_antigo)
		{
			$this->alterar_capitulo($id_capitulo_antigo, 0);
			
			if($id_capitulo_antigo < $novo_id_capitulo)
			{
				for($i=$id_capitulo_antigo; $i < $novo_id_capitulo; $i++)
					$this->alterar_capitulo($i+1, $i);
			}//if
			else
			if($id_capitulo_antigo > $novo_id_capitulo)
			{
				for($i=$id_capitulo_antigo; $i > $novo_id_capitulo; $i--)
					$this->alterar_capitulo($i-1, $i);
			}//elseif
			
			$this->alterar_capitulo(0, $novo_id_capitulo);
			
			$this->dados_capitulo = NULL;
		}//if
	}//function mover_aviso()
	
	
	function num_capitulos($tipo = '')
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		if(!empty($tipo))
			$condicao = "tipo = '$tipo'";
		else
			$condicao = "";
		
		$query = new Query("COUNT(*) AS 'num_capitulos'", $tabela_capitulo, $condicao);
		return $query->rows[0]['num_capitulos'];
	}//function num_capitulos()
}//class Info_capitulos



class Info_defesa
{
	var $dados_defesa;
	
	function Info_defesa($id = 0)
	{
		if($id)
			$this->Processa($id);
	}//function
	
	function Processa($id)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_defesa = NULL;
		
		$query = new Query("local, UNIX_TIMESTAMP(data_defesa) AS 'data_defesa'", $tabela_defesas, "id_projeto = '$id'");
		$this->dados_defesa = $query->rows[0];
	}//function Processa()
	
	
	function inserir_defesa($id_projeto, $local, $data_defesa)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string = "INSERT INTO $tabela_defesas VALUES ('$id_projeto', '$local', '$data_defesa')";
		$query = new Query();
		$query->Query2($query_string);
		
		$this->gera_pdf($id_projeto);
	}//function inserir_defesa()
	
	
	function alterar_defesa($id_projeto, $novo_local, $novo_data_defesa)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string = "UPDATE IGNORE $tabela_defesas
						 SET
						 local = '$novo_local',
						 data_defesa = '$novo_data_defesa'
						 WHERE id_projeto = '$id_projeto'";
		
		$query = new Query();
		$query->Query2($query_string);
		
		$this->gera_pdf($id_projeto);
		$this->dados_defesa = NULL;
	}//function alterar_defesa()
	
	
	function excluir_defesa($id_projeto)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$arquivo = new Handle_arquivo();
		$filename = info_true_self($_SERVER['PHP_SELF'])."/arquivos/$id/relatorio_ata";
		$arquivo->delete($id, $filename);
		
		$query_string = "DELETE FROM $tabela_defesas WHERE id_projeto = '$id_projeto'";
		$query = new Query();
		$query->Query2($query_string);
		
		$this->dados_defesa = NULL;
	}//function excluir_defesa()
	
	
	
	function gera_pdf($id)
	{
		$projeto = new Info_Projeto($id);
		
		$this->Processa($id);
		
		$titulo = undo_html_entities($projeto->dados_projeto['titulo']);
		$alunos = undo_html_entities($projeto->dados_projeto['alunos'][0]['nome_aluno']);
		for($i=1; $i < count($projeto->dados_projeto['alunos']); $i++)
			$alunos .= "\n".undo_html_entities($projeto->dados_projeto['alunos'][$i]['nome_aluno']);
		
		$temp = $projeto->dados_projeto['banca']['orientador'][0];
		$orientador = undo_html_entities($projeto->dados_projeto['professores'][$temp]['nome_professor']);
		
		foreach($projeto->dados_projeto['banca']['avaliador'] as $key => $value)
			$avaliador[] = undo_html_entities($projeto->dados_projeto['professores'][$value]['nome_professor']);
		
		$date = date("d/m/Y", $this->dados_defesa['data_defesa']);
		$horario = date("H:i:s", $this->dados_defesa['data_defesa']);
		$local = $this->dados_defesa['local'];
		
		
		#=====================================================
		$header = "UNESP - Universidade Estadual Paulista\n";
		$header .= "IBILCE - Instituto de Bioci�ncias, Letras e Ci�ncias Exatas - Campus de S�o Jos� do Rio Preto\n";
	//	$title .= "Campus de S�o Jos� do Rio Preto\n";
		$header .= "DCCE - Departamento de Ci�ncias de Computa��o e Estat�stica\n";
		$header .= "\n";
		$header2 = "Avalia��o da Monografia de Projeto Final";
		
		require("../FPDF/fpdf.php");
		
		$pdf=new FPDF();
		$pdf->AddPage();
		$pdf->Ln();
		
		//cabe�alho
		$pdf->SetFont('Times','B','12');
		$pdf->MultiCell(0,10,$header,0,"C",0);
		$pdf->SetFont('Times','B','16');
		$pdf->MultiCell(0,10,$header2,0,"C",0);
		$pdf->Ln();
		
		//t�tulo do projeto
		$pdf->SetFont('Times','B','12');
		$pdf->Cell(0,10,'T�tulo:',0,1);
		$pdf->SetFont('Times','','12');
		$pdf->Cell(0,10,$titulo,0,1);
		$pdf->Cell(0,10,"",0,1);
		
		//alunos do projeto
		$pdf->SetFont('Times','B','12');
		$pdf->Cell(0,10,'Aluno(s):',0,1);
		$pdf->SetFont('Times','','12');
		$pdf->MultiCell(0,5,$alunos);
		$pdf->Cell(0,10,"",0,1);
		
		//data
		$pdf->SetFont('Times','B','12');
		$pdf->Cell(12,05,'Data:');
		$pdf->SetFont('Times','','12');
		$pdf->Cell(50,05,$date);
		$pdf->Ln();
		
		//hor�rio
		$pdf->SetFont('Times','B','12');
		$pdf->Cell(18,05,'Hor�rio:');
		$pdf->SetFont('Times','','12');
		$pdf->Cell(50,05,$horario);
		$pdf->Cell(0,10,"",0,1);
		
		//presidente da banca
		$pdf->SetFont('Times','B','12');
		$pdf->Cell(40,10,'Presidente da banca:');
		$pdf->SetFont('Times','','12');
		$pdf->Cell(90,10,$orientador);
		
		//assinatura
		$pdf->SetFont('Times','B','12');
		$pdf->Cell(23,10,'Assinatura:');
		$pdf->SetFont('Times','','12');
		$pdf->Cell(0,10,"_______________");
		$pdf->Ln();
		
		//membros da banca
		for($i=0; $i < count($avaliador); $i++)
		{
			//membro da banca
			$pdf->SetFont('Times','B','12');
			$pdf->Cell(40,10,'Membro da banca:');
			$pdf->SetFont('Times','','12');
			$pdf->Cell(90,10,$avaliador[$i]);
			
			//assinatura
			$pdf->SetFont('Times','B','12');
			$pdf->Cell(23,10,'Assinatura:');
			$pdf->SetFont('Times','','12');
			$pdf->Cell(0,10,"_______________");
			$pdf->Ln();
		}//for
		$pdf->Cell(0,20,"",0,1);
		
		
		//texto final
		$pdf->SetFont('Times','B','14');
		$pdf->Cell(0,20,'A avalia��o contabiliza o trabalho, a escrita da monografia e a apresenta��o.',0,1,'C');
		$pdf->Cell(0,10,'Nota:____________________',0,1,'C');
		
		//===========================================================================
		//p�gina 2
		$pdf->AddPage();
		
		//t�tulo
		$titulo = "Regras para apresenta��o da monografia";
		$texto[0] = "O aluno ter� 15 min para apresenta��o do trabalho, com toler�ncia de +/- 5 min.";
		$texto[1] = "A banca efetuar� a arg�i��o em 10 a 20 min.";
		$texto[2] = "O aluno efetuar� a demonstra��o do seu trabalho em at� 15 min.";
		$texto[3] = "A banca deliberar� sobre a nota, que ser� homologada somente ap�s a entrega do recibo da monografia para um dos professores da disciplina PF. ";
		$texto[3] .= "Lembrando que ser�o subtra�dos desta nota os pontos perdidos pelos atrasos eventuais.";
		$texto_local = "Local de apresenta��o: $local";
		
		
		$pdf->Cell(0,80, "",0,1);
		$pdf->SetFont('Times','B','16');
		$pdf->Cell(0,10,$titulo,0,1,"C",0);
		$pdf->Cell(0,10,"",0,1);
		
		//texto
		for($i=0; $i < count($texto); $i++)
		{
			$pdf->SetFont('Times','','12');
			$pdf->MultiCell(0,5,$texto[$i],0);
			$pdf->Cell(0,5,"",0,1);
		}//for
		
		$pdf->SetFont('Times','B','12');
		$pdf->MultiCell(0,5,$texto_local,0);
		$pdf->Cell(0,5,"",0,1);
		
		$doc_root = $_SERVER['DOCUMENT_ROOT'];
		$self = info_true_self($_SERVER['PHP_SELF']);
		
		$dir = "$doc_root/$self/arquivos/$id";
		$filename = "$dir/relatorio_ata.pdf";
		
		if(!file_exists($dir))
			mkdir($dir, 0700);
		//salvar o arquivo
		$pdf->Output($filename,"F");
		chmod($filename, 0600);
	}//gera_pdf()	
}//class Info_defesa




class Info_nota_final
{
	var $dados_nota_final;
	
	function Info_nota_final($id = 0)
	{
		if($id)
			$this->Processa($id);
	}//function
	
	
	
	function Dados_nota_final($codigo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_nota_final = NULL;
		
		$query = new Query("codigo, nota_final, faltas, observacao, disciplina_trancada", $tabela_notas_finais, "codigo = '$codigo'");
		
		if($query->num_rows)
			$this->dados_nota_final = $query->rows[0];
		else
		{
			$this->dados_nota_final['codigo'] = $codigo;
			$this->dados_nota_final['nota_final'] = 0;
			$this->dados_nota_final['faltas'] = 0;
			$this->dados_nota_final['observacao'] = "";
			$this->dados_nota_final['disciplina_trancada'] = "";
		}//else
	}
	
	
	function Processa($id)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$disciplina = new Info_disciplina();
		
		$faltas_max = $disciplina->dados_disciplina['faltas_max'];
		
		$this->dados_nota_final = NULL;
		
		$projeto = new Info_projeto($id);
		
		for($i=0; $i < count($projeto->dados_projeto['alunos']); $i++)
		{
			$this->dados_nota_final['alunos'][$i]['codigo'] = $codigo = $projeto->dados_projeto['alunos'][$i]['codigo'];
			
			$query = new Query("nota_final, faltas, observacao, disciplina_trancada", $tabela_notas_finais, "codigo = '$codigo'");
			
			$this->dados_nota_final['alunos'][$i]['nota'] = $nota_final = $query->rows[0]['nota_final']+0;
			$this->dados_nota_final['alunos'][$i]['faltas'] = $faltas = $query->rows[0]['faltas']+0;
			$this->dados_nota_final['alunos'][$i]['observacao'] = $query->rows[0]['observacao'];
			$disciplina_trancada = $query->rows[0]['disciplina_trancada'];
			
			if($disciplina_trancada)
			{
				$this->dados_nota_final['alunos'][$i]['nota'] = "***";
				$this->dados_nota_final['alunos'][$i]['conceito'] = "DT";
			}//if
			else if($faltas > $faltas_max)
			{
				$this->dados_nota_final['alunos'][$i]['nota'] = "-";
				$this->dados_nota_final['alunos'][$i]['conceito'] = "RF";
			}//if
			else if($nota_final < 5)
				$this->dados_nota_final['alunos'][$i]['conceito'] = "RN";
			else
				$this->dados_nota_final['alunos'][$i]['conceito'] = "AP";
		}//for
	}//function Processa();
	
	
	function inserir_nota_final($codigo, $nota_final, $faltas = '', $observacao = '', $disciplina_trancada = '')
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string = "INSERT INTO $tabela_notas_finais VALUES ('$codigo', '$nota_final', '$faltas', '$observacao', '$disciplina_trancada')";
		$query = new Query();
		$query->Query2($query_string);
	}//function inserir_nota_final()
	
	
	function alterar_nota_final($codigo, $novo_nota_final, $novo_faltas = '', $novo_observacao = '', $novo_disciplina_trancada = '', $somente_nota = 0)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		if($somente_nota)
			$query_string = "UPDATE IGNORE $tabela_notas_finais
							SET
							nota_final = '$novo_nota_final'
							WHERE codigo = '$codigo'";
		else
			$query_string = "UPDATE IGNORE $tabela_notas_finais
							SET
							nota_final = '$novo_nota_final',
							faltas = '$novo_faltas',
							observacao = '$novo_observacao',
							disciplina_trancada = '$novo_disciplina_trancada'
							WHERE codigo = '$codigo'";
		
		$query = new Query();
		$query->Query2($query_string);
		$this->dados_nota_final = NULL;
	}//function alterar_nota_final()
	
	
	function excluir_nota_final($codigo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string = "DELETE FROM $tabela_notas_finais WHERE codigo = '$codigo'";
		$query = new Query();
		$query->Query2($query_string);
		$this->dados_nota_final = NULL;
	}//function excluir_nota_final()
}//class Info_nota_final



class Info_disciplina
{
	var $dados_disciplina;
	
	function Info_disciplina()
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query("nome_responsavel_1, titulo_responsavel_1, nome_responsavel_2, titulo_responsavel_2, ".
						   "cod_disciplina, cod_cursos, turma, carga_disciplina, ano_letivo, semestre_letivo", $tabela_disciplina);
		
		if($query->num_rows)
		{
			$dados['responsaveis'][0]['nome'] = $query->rows[0]['nome_responsavel_1'];
			$dados['responsaveis'][0]['titulo'] = $query->rows[0]['titulo_responsavel_1'];
			$dados['responsaveis'][1]['nome'] = $query->rows[0]['nome_responsavel_2'];
			$dados['responsaveis'][1]['titulo'] = $query->rows[0]['nome_responsavel_2'];
			$dados['cod_cursos'] = $query->rows[0]['cod_cursos'];
			$dados['cod_disciplina'] = $query->rows[0]['cod_disciplina'];
			$dados['turma'] = $query->rows[0]['turma'];
			$dados['carga_disciplina'] = $query->rows[0]['carga_disciplina'];
			$dados['faltas_max'] = $dados['carga_disciplina']*0.3;
			$dados['ano_letivo'] = $query->rows[0]['ano_letivo'];
			$dados['semestre_letivo'] = $query->rows[0]['semestre_letivo'];
		}//if
		else
		{
			$dados['responsaveis'][0]['nome'] = "";
			$dados['responsaveis'][0]['titulo'] = 1;
			$dados['responsaveis'][1]['nome'] = "";
			$dados['responsaveis'][1]['titulo'] = 1;
			$dados['cod_cursos'] = "";
			$dados['cod_disciplina'] = "";
			$dados['turma'] = "";
			$dados['carga_disciplina'] = 0;
			$dados['faltas_max'] = $dados['carga_disciplina']*0.3;
			$dados['ano_letivo'] = 0;
			$dados['semestre_letivo'] = 1;
		}//else
		
		$this->dados_disciplina = $dados;
	}//function Info_disciplina
	
	function alterar_disciplina($novo_ano_letivo,
								$novo_nome_responsavel_1,
								$novo_titulo_responsavel_1,
								$novo_nome_responsavel_2,
								$novo_titulo_responsavel_2,
								$novo_cod_disciplina,
								$novo_cod_cursos,
								$novo_turma,
								$novo_carga_disciplina,
								$novo_semestre_letivo,
								$adm_username)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string[] = "DELETE FROM $tabela_disciplina";
		$query_string[] = "INSERT INTO $tabela_disciplina VALUES (
						   '$novo_ano_letivo',
						   '$novo_nome_responsavel_1',
						   '$novo_titulo_responsavel_1',
						   '$novo_nome_responsavel_2',
						   '$novo_titulo_responsavel_2',
						   '$novo_cod_disciplina',
						   '$novo_cod_cursos',
						   '$novo_turma',
						   '$novo_carga_disciplina',
						   '$novo_semestre_letivo',
						   '$adm_username')";
		
		$query = new Query();
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
		$this->dados_disciplina = NULL;
	}//function altera_disciplina()
}//class Info_disciplina




class Info_avisos
{
	var $dados_avisos;
	
	function Info_avisos()
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_avisos = NULL;
		
		$query = new Query("id_aviso, conteudo", $tabela_avisos, "", "", "id_aviso");
		
		if($query->num_rows)
			$this->dados_avisos = $query->rows;
	}//function Info_avisos
	
	
	function Dados_aviso($id_aviso)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query("id_aviso, conteudo", $tabela_avisos, "id_aviso = '$id_aviso'");
		
		if($query->num_rows)
			$this->dados_avisos = $query->rows[0];
		else
		{
			$this->dados_avisos['id_aviso'] = $id_aviso;
			$this->dados_avisos['conteudo'] = "";
		}
	}//function Dados_aviso()
	
	
	function inserir_aviso($id_aviso, $conteudo, $adm_username)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$num_avisos = $this->num_avisos()+1;
		$query_string = "INSERT INTO $tabela_avisos VALUES ('$num_avisos', '$conteudo', '$adm_username')";
		$query = new Query();
		$query->Query2($query_string);
		
		$this->mover_aviso($num_avisos, $id_aviso);
	}//function inserir_aviso()
	
	
	function alterar_aviso($id_aviso_antigo, $novo_id_aviso, $novo_conteudo = '', $novo_adm_username = '')
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		if(!empty($novo_conteudo))
			$query_string[] = "UPDATE IGNORE $tabela_avisos
							   SET
							   conteudo = '$novo_conteudo',
							   adm_username = '$novo_adm_username'
							   WHERE id_aviso = '$id_aviso_antigo'";
			
		if($novo_id_aviso != $id_aviso_antigo)
		{
			$query_string[] = "UPDATE IGNORE $tabela_avisos
							   SET id_aviso = '$novo_id_aviso'
							   WHERE id_aviso = '$id_aviso_antigo'";
		}//if
		
		$query = new Query();
		for($i=0; $i < count($query_string); $i++)
			$query->Query2($query_string[$i]);
		
	}//function alterar_aviso()
	
	
	function mover_aviso($id_aviso_antigo, $novo_id_aviso)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		if($novo_id_aviso != $id_aviso_antigo)
		{
			$this->alterar_aviso($id_aviso_antigo, 0);
			
			if($id_aviso_antigo < $novo_id_aviso)
			{
				for($i=$id_aviso_antigo; $i < $novo_id_aviso; $i++)
					$this->alterar_aviso($i+1, $i);
			}//if
			else
			if($id_aviso_antigo > $novo_id_aviso)
			{
				for($i=$id_aviso_antigo; $i > $novo_id_aviso; $i--)
					$this->alterar_aviso($i-1, $i);
			}//elseif
			
			$this->alterar_aviso(0, $novo_id_aviso);
			
			$this->dados_avisos = NULL;
		}//if
	}//function mover_aviso()
	
	function excluir_aviso($id_aviso)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$num_avisos = $this->num_avisos();
		
		$this->mover_aviso($id_aviso, $num_avisos);
		
		$query_string = "DELETE FROM $tabela_avisos WHERE id_aviso = '$num_avisos'";
		$query = new Query();
		$query->Query2($query_string);
		
		$this->dados_avisos = NULL;
	}//function excluir_aviso()
	
	
	function num_avisos()
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query("COUNT(*) AS 'num_avisos'", $tabela_avisos);
		return $query->rows[0]['num_avisos'];
	}//function num_avisos()
}//class Info_avisos



class Info_login
{
	var $dados_login;
	
	
	function Dados_login($codigo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$this->dados_login = NULL;
		
		$query = new Query("username", $tabela_login, "codigo = '$codigo'");
		
		if($query->num_rows)
			$this->dados_login = $query->rows[0];
		else
			$this->dados_login['username'] = "";
	}//function Dados_login()
	
	
	function Info_login($username = "", $password = "", $modulo = "")
	{
		if($username)
		{
			global $tabelas;
			
			for($i=0; $i < count($tabelas); $i++)
				global ${"tabela_".$tabelas[$i]};
			
			
			$this->dados_login = NULL;
			$this->dados_login['login_validado'] = 0;
			$this->dados_login['username_existe'] = 0;
			
			if($modulo == "admin")
			{
				$query = new Query("adm_username", $tabela_admin, "adm_username = '$username' AND adm_password = PASSWORD('$password')");
				
				$this->dados_login['login_validado'] = $query->num_rows;
				$this->dados_login['username_existe'] = $query->num_rows;
				
				if($query->num_rows)
					$this->dados_login['username'] = $username;
				else
				{
					$query->Query("COUNT(adm_username) as 'existe'", $tabela_admin, "adm_username = 'username'");
					$this->dados_login['username_existe'] = $query->rows[0]['existe'];
				}//else
			}
			else if($modulo == "user")
			{
				$query = new Query("username, codigo", $tabela_login, "username = '$username' AND password = password('$password')");
				$this->dados_login['login_validado'] = $query->num_rows;
				$this->dados_login['username_existe'] = $query->num_rows;
				
				if($query->num_rows)
				{
					if($query->rows[0]['codigo'] < 100)
					{
						$this->dados_login['tipo'] = "professor";
						$this->dados_login['codigo'] = sprintf("%02d", $query->rows[0]['codigo']);
						$professor = new Info_professor($query->rows[0]['codigo'], 0, 1);
						$this->dados_login['nome'] = $professor->dados_professor['nome_professor'];
					}//if
					else
					{
						$this->dados_login['tipo'] = "aluno";
						$this->dados_login['codigo'] = $query->rows[0]['codigo'];
						$aluno = new Info_aluno($query->rows[0]['codigo']);
						$this->dados_login['id_projeto'] = $aluno->dados_aluno['id_projeto'];
						$this->dados_login['nome'] = $aluno->dados_aluno['nome_aluno'];
					}//if
				}//if
				else
				{
					$query->Query("COUNT(username) as 'existe'", $tabela_login, "username = '$username'");
					$this->dados_login['username_existe'] = $query->rows[0]['existe'];
				}//if
			}//else if
		}//if
	}//function Info_login
	
	
	function alterar_login($username_antigo, $password_antigo = "", $novo_username, $novo_password, $modulo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query();
		
		if($modulo == "user")
		{
			$query_string = "UPDATE IGNORE $tabela_login
							 SET
							 username = '$novo_username',
							 password = password('$novo_password')
							 WHERE username = '$username_antigo'";
			
			$query->Query2($query_string);
			$this->Info_login($novo_username, $novo_password, "user");
		}//if
		else
		if($modulo == "admin")
		{
			$query_string = "UPDATE IGNORE $tabela_admin
							 SET
							 adm_username = '$novo_username',
							 adm_password = password('$novo_password')
							 WHERE adm_username = '$username_antigo' AND
							 adm_password = password('$password_antigo')";
			
			$query->Query2($query_string);
			$this->Info_login($novo_username, $novo_password, "admin");
		}//else if
		
	}//function alterar_login
}//class Info_avisos




class Info_nota
{
	var $dados_nota;
	
	function Info_nota($id_projeto = 0)
	{
		if($id_projeto)
			$this->Processa($id_projeto);
	}//function Info_nota()
	
	
	function Processa($id_projeto)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query("id_projeto, nota, comentario", $tabela_notas, "id_projeto = '$id_projeto'");
		$this->dados_nota = $query->rows[0];
	}//function Processa()
	
	
	function inserir_nota($id_projeto, $nota, $comentario)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query();
		$query_string = "INSERT INTO $tabela_notas VALUES ('$id_projeto','$nota','$comentario')";
		
		$query->Query2($query_string);
		
		$this->dados_nota = NULL;
	}//function inserir_nota()
	
	function alterar_nota($id_projeto, $novo_nota, $novo_comentario)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string = "UPDATE IGNORE $tabela_notas
						 SET
						 nota = '$novo_nota',
						 comentario = '$novo_comentario'
						 WHERE id_projeto = '$id_projeto'";
		
		$query = new Query();
		$query->Query2($query_string);
	}//function alterar_nota()
	
	function excluir_nota($id_projeto)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string = "DELETE FROM $tabela_notas WHERE id_projeto = '$id_projeto'";
		$query = new Query();
		
		$query->Query2($query_string);
		$this->dados_nota = NULL;
	}//function excluir_nota()
}//class Info_nota



class Info_penalidades_locais
{
	var $dados_penalidades_locais;
	
	function Info_penalidades_locais($id_projeto = 0)
	{
		if($id_projeto)
			$this->Processa($id_projeto);
	}//function Info_penalidades_locais()
	
	
	function Processa($id_projeto)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query = new Query("{$tabela_capitulo}.id_capitulo,
							{$tabela_capitulo}.nome_capitulo,
							{$tabela_penalidades_locais}.id_capitulo IS NOT NULL AS 'existe',
							{$tabela_penalidades_locais}.aplicar_penalidade_local,
							{$tabela_penalidades_locais}.nova_data_entrega",
						   "$tabela_capitulo LEFT JOIN $tabela_penalidades_locais ON
							{$tabela_penalidades_locais}.id_capitulo = {$tabela_capitulo}.id_capitulo AND
							{$tabela_penalidades_locais}.id_projeto = '$id_projeto'",
						   "", "", "{$tabela_capitulo}.id_capitulo");
		
		$this->dados_penalidades_locais = $query->rows;
	}//function Processa()
	
	
	function inserir_penalidade_local($id_projeto, $id_capitulo, $aplicar_penalidade_local, $nova_data_entrega)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string = "INSERT INTO $tabela_penalidades_locais VALUES (
						 '$id_projeto',
						 '$id_capitulo',
						 '$aplicar_penalidade_local',
						 '$nova_data_entrega')";
		
		$query = new Query();
		$query->Query2($query_string);
		
		$this->dados_penalidades_locais = NULL;
	}//function inserir_penalidade_local()
	
	
	function alterar_penalidade_local($id_projeto, $id_capitulo, $novo_aplicar_penalidade_local, $nova_data_entrega)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string = "UPDATE IGNORE $tabela_penalidades_locais
						 SET
						 aplicar_penalidade_local = '$novo_aplicar_penalidade_local',
						 nova_data_entrega = '$nova_data_entrega'
						 WHERE id_projeto = '$id_projeto'
						 AND id_capitulo = '$id_capitulo'";
		
		$query = new Query();
		$query->Query2($query_string);
		
		$this->dados_penalidades_locais = NULL;
	}//function alterar_penalidade_local()
	
	
	function excluir_penalidade_local($id_projeto, $id_capitulo)
	{
		global $tabelas;
		
		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};
		
		$query_string = "DELETE FROM $tabela_penalidades_locais
						 WHERE id_projeto = '$id_projeto'
						 AND id_capitulo = '$id_capitulo'";
		
		$query = new Query();
		$query->Query2($query_string);
		
		$this->dados_penalidades_locais = NULL;
	}//function excluir_penalidade_local()
}//class Info_penalidades_locais




class Info_lista
{
	var $dados_lista;
	
	function Info_lista($tipo_lista, $ordem_lista = "", $codigo = "")
	{
		global $tabelas;

		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};

		$this->dados_lista = NULL;
		
		if($tipo_lista == "projetos")
		{
			$query = new Query("id_projeto", $tabela_projeto, "", "", $ordem_lista);
			$projeto = new Info_projeto();
			
			for($i=0; $i < $query->num_rows; $i++)
			{
				$projeto->Processa($query->rows[$i]['id_projeto']);
				$this->dados_lista[$i] = $projeto->dados_projeto;
			}//for
		}//if
		else if($tipo_lista == "alunos")
		{
			$query = new Query("codigo", $tabela_aluno, "", "", $ordem_lista);
			$aluno = new Info_aluno();
			
			for($i=0; $i < $query->num_rows; $i++)
			{
				$aluno->Processa($query->rows[$i]['codigo'],1);
				$this->dados_lista[$i] = $aluno->dados_aluno;
			}//for
		}//else if
		else if($tipo_lista == "professores")
		{
			$query = new Query("codigo", $tabela_professor, "", "", $ordem_lista);
			$professor = new Info_professor();
			
			for($i=0; $i < $query->num_rows; $i++)
			{
				$professor->Processa($query->rows[$i]['codigo']);
				$this->dados_lista[$i] = $professor->dados_professor;
			}//for
		}//else if
		else if($tipo_lista == "prof_proj")
		{
			$query = new Query("{$tabela_projeto}.id_projeto,
								{$tabela_projeto}.titulo,
								{$tabela_prof_proj}.id_projeto IS NOT NULL AS 'cadastrado',
								{$tabela_prof_proj}.funcao+0 as 'funcao'",
							   "$tabela_projeto LEFT JOIN $tabela_prof_proj ON
							    {$tabela_projeto}.id_projeto = {$tabela_prof_proj}.id_projeto
								AND {$tabela_prof_proj}.codigo = '$codigo'", "", "", "{$tabela_projeto}.id_projeto");
			
			$this->dados_lista = $query->rows;
		}//else if
		else if($tipo_lista == "login_user")
		{
			$query = new Query("DISTINCT {$tabela_login}.codigo,
								IF({$tabela_login}.codigo < 100, {$tabela_professor}.nome_professor, {$tabela_aluno}.nome_aluno) AS 'nome',
								{$tabela_login}.username",
							   "$tabela_login, $tabela_professor, $tabela_aluno",
							   "{$tabela_professor}.codigo = {$tabela_login}.codigo OR
								{$tabela_aluno}.codigo = {$tabela_login}.codigo", "", $ordem_lista);
			
			$this->dados_lista = $query->rows;
		}//else if
		else if($tipo_lista == "login_admin")
		{
			$query = new Query("adm_username", $tabela_admin, "", "", "adm_username");
			
			$this->dados_lista = $query->rows;
		}//else if
	}//function Info_lista
}//class Info_lista


class Backup
{
	function Backup()
	{
		//vazia
	}//function Backup();


	function Limpar_dados()
	{
		global $tabelas;

		for($i=0; $i < count($tabelas); $i++)
			global ${"tabela_".$tabelas[$i]};

		$query = new Query();

		$self = true_self($_SERVER['PHP_SELF'], 1);
		$path = $self."/admin/backup";

		//in�cio do script de backup
		chdir($path);
		system("rm -f -R ../../arquivos/*");

		system("rm -f -R ../arquivos/*");

		for($i=0; $i < count($tabelas); $i++)
		{
			if($tabelas[$i] != "admin")
				$query->Query2("DELETE FROM ".${"tabela_".$tabelas[$i]});
		}
		chdir("../");
		echo "</pre>";
		//fim do script de backup
	}//function Limpar_dados()
}//class Limpar_dados
?>
