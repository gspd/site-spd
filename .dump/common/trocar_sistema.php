<?php
include_once("mysql.php");

function criar_novas_tabelas($prefixo)
{
	$query_string[] = "CREATE TABLE {$prefixo}admin (
					   adm_username		VARCHAR(20)		NOT NULL,
					   adm_password		VARCHAR(41)		NOT NULL,
					   PRIMARY KEY		(adm_username)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}aluno (
					   codigo			MEDIUMINT(6)	UNSIGNED ZEROFILL NOT NULL,
					   nome_aluno		VARCHAR(255)	NOT NULL,
					   email_aluno		VARCHAR(255)	NOT NULL,
					   id_projeto		TINYINT(2)		UNSIGNED NOT NULL,
					   PRIMARY KEY		(codigo)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}avisos (
					   id_aviso			TINYINT(2)		UNSIGNED NOT NULL,
					   conteudo			BLOB			NOT NULL,
					   adm_username		VARCHAR(20)		NOT NULL,
					   PRIMARY KEY		(id_aviso)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}capitulo (
					   id_capitulo		TINYINT(2)		UNSIGNED NOT NULL,
					   tipo				ENUM('aluno','professor') NOT NULL,
					   nome_capitulo	VARCHAR(50)		UNIQUE NOT NULL,
					   legenda_capitulo	VARCHAR(255)	UNIQUE NOT NULL,
					   data_entrega		DATETIME		NOT NULL,
					   aplicar_penalidade	TINYINT(1)	UNSIGNED NOT NULL,
					   PRIMARY KEY		(id_capitulo)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}defesas (
					   id_projeto		TINYINT(2)		UNSIGNED NOT NULL,
					   local			VARCHAR(20)		NOT NULL,
					   data_defesa		DATETIME		NOT NULL,
					   PRIMARY KEY		(id_projeto)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}disciplina (
					   ano_letivo		YEAR(4)			NOT NULL,
					   nome_responsavel_1	VARCHAR(255)	NOT NULL,
					   titulo_responsavel_1	TINYINT(1)	UNSIGNED NOT NULL,
					   nome_responsavel_2	VARCHAR(255)	NOT NULL,
					   titulo_responsavel_2	TINYINT(1)	UNSIGNED NOT NULL,
					   cod_disciplina	MEDIUMINT(4)	UNSIGNED NOT NULL,
					   cod_cursos VARCHAR(255)			NOT NULL,
					   turma			VARCHAR(20)		NOT NULL,
					   carga_disciplina	TINYINT(3)		UNSIGNED NOT NULL,
					   semestre_letivo	TINYINT(1)		UNSIGNED NOT NULL,
					   adm_username		VARCHAR(20)		NOT NULL,
					   PRIMARY KEY		(ano_letivo)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}login (
					   username			VARCHAR(20)		NOT NULL,
					   password			VARCHAR(41)		NOT NULL,
					   codigo			MEDIUMINT(6)	UNSIGNED ZEROFILL NOT NULL,
					   PRIMARY KEY		(username)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}notas (
					   id_projeto		TINYINT(2)		UNSIGNED NOT NULL,
					   nota				FLOAT(4,2)		UNSIGNED NOT NULL,
					   comentario		BLOB,
					   PRIMARY KEY		(id_projeto)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}notas_finais (
					   codigo			MEDIUMINT(6)	UNSIGNED ZEROFILL NOT NULL,
					   nota_final		FLOAT(4,2)		UNSIGNED NOT NULL,
					   faltas			TINYINT(2)		UNSIGNED ZEROFILL NOT NULL,
					   observacao		BLOB,
					   disciplina_trancada	TINYINT(1)	UNSIGNED NOT NULL,
					   PRIMARY KEY		(codigo)
					   )";

	$query_string[] = "CREATE TABLE {$prefixo}professor (
					   codigo			MEDIUMINT(6)	UNSIGNED ZEROFILL NOT NULL,
					   doutor			TINYINT(1)		UNSIGNED NOT NULL,
					   nome_professor	VARCHAR(255)	NOT NULL,
					   email_professor	VARCHAR(255)	NOT NULL,
					   PRIMARY KEY		(codigo)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}projeto (
					   id_projeto		TINYINT(2)		UNSIGNED NOT NULL,
					   titulo			VARCHAR(255)	NOT NULL,
					   PRIMARY KEY		(id_projeto)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}penalidades_locais (
					   id_projeto		TINYINT(2)		UNSIGNED NOT NULL,
					   id_capitulo		TINYINT(2)		UNSIGNED NOT NULL,
					   aplicar_penalidade_local	TINYINT(1)	UNSIGNED NOT NULL,
					   nova_data_entrega	DATETIME	NOT NULL,
					   PRIMARY KEY		(id_projeto, id_capitulo)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}prof_proj (
					   codigo			MEDIUMINT(6)	UNSIGNED ZEROFILL NOT NULL,
					   id_projeto		TINYINT(2)		UNSIGNED NOT NULL,
					   funcao			ENUM('avaliador','orientador') NOT NULL,
					   PRIMARY KEY		(codigo, id_projeto)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}submete (
					   id_projeto		TINYINT(2)		UNSIGNED NOT NULL,
					   id_capitulo		TINYINT(2)		UNSIGNED NOT NULL,
					   data_submissao	DATETIME		NOT NULL,
					   PRIMARY KEY		(id_projeto, id_capitulo)
					   )";
	
	$query_string[] = "CREATE TABLE {$prefixo}avalia (
					   id_projeto		TINYINT(2)		UNSIGNED NOT NULL,
					   codigo			MEDIUMINT(6)	UNSIGNED ZEROFILL NOT NULL,
					   id_capitulo		TINYINT(2)		UNSIGNED NOT NULL,
					   data_avaliacao	DATETIME		NOT NULL,
					   PRIMARY KEY		(id_projeto, codigo, id_capitulo)
					   )";
	
	$query_string[] = "INSERT INTO {$prefixo}admin VALUES
					   ('admin',password('admin')),
					   ('admin2',password('admin')),
					   ('admin3',password('admin'))";
	
	$query = new Query();
	for($i=0; $i < count($query_string); $i++)
		$query->Query2($query_string[$i]);
}//function criar_novas_tabelas()




function converte_antigo_novo($prefixo)
{
	$query = new Query();

	//tabela admin
	$query->Query("username, password", "admin", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}admin VALUES ($values)");
	}
	unset($rows);



	//tabela aluno
	$query->Query("RA, name, email, projeto", "aluno", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}aluno VALUES ($values)");
	}
	unset($rows);



	//tabela avisos
	$query->Query("id, conteudo", "avisos", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}avisos VALUES ($values,'')");
	}
	unset($rows);





	//tabela capitulo
	$query->Query("id, tipo, name, friendly, date, penalidade", "capitulo", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}capitulo VALUES ($values)");
	}
	unset($rows);






	//tabela defesas
	$query->Query("projeto, local, date", "defesas", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}defesas VALUES ($values)");
	}
	unset($rows);






	//tabela disciplina
	$query->Query("ano_letivo,
				   responsavel_1_name, responsavel_1_titulo,
				   responsavel_2_name, responsavel_2_titulo,
				   cod_disciplina, cod_cursos, turma, carga_disciplina,
				   semestre_letivo", "disciplina", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}disciplina VALUES ($values,'')");
	}
	unset($rows);






	//tabela login
	$query->Query("username, password, RA", "login", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}login VALUES ($values)");
	}
	unset($rows);






	//tabela notas
	$query->Query("projeto, nota, comentario", "notas", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}notas VALUES ($values)");
	}
	unset($rows);







	//tabela notas_finais
	$query->Query("RA, nota_final, faltas, observacao, disciplina_trancada", "notas_finais", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}notas_finais VALUES ($values)");
	}
	unset($rows);







	//tabela professor
	$query->Query("RA, doutor, name, email", "professor", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}professor VALUES ($values)");
	}
	unset($rows);







	//tabela prof_proj
	$query->Query("RA, projeto, funcao", "prof_proj", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}prof_proj VALUES ($values)");
	}
	unset($rows);







	//tabela projeto
	$query->Query("id, title", "projeto", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}projeto VALUES ($values)");
	}
	unset($rows);







	//tabela penalidades_locais
	$query->Query("projeto, capitulo, penalidade, date", "penalidades", "", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}penalidades_locais VALUES ($values)");
	}
	unset($rows);







	//tabela submete
	$query->Query("projeto, capitulo, date", "proj_cap", "RA > 99", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}submete VALUES ($values)");
	}
	unset($rows);







	//tabela avalia
	$query->Query("projeto, RA, capitulo, date", "proj_cap", "RA < 99", "", "", 1, MYSQL_NUM);
	$rows = $query->rows;
	$num_rows = $query->num_rows;

	for($i=0; $i < $num_rows; $i++)
	{
		$values = "'".$rows[$i][0]."'";

		for($j=1; $j < count($rows[$i]); $j++)
			$values .= ",'".$rows[$i][$j]."'";

		$query->Query2("INSERT INTO {$prefixo}avalia VALUES ($values)");
	}
	unset($rows);
}//function converte_antigo_novo;
?>
