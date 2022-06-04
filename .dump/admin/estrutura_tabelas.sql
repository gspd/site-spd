# MySQL dump 8.16
#
# Host: localhost    Database: projeto_final
#--------------------------------------------------------
# Server version	3.23.46-Max

#
# Table structure for table 'admin'
#

DROP TABLE IF EXISTS admin;
CREATE TABLE admin (
  username varchar(20) NOT NULL default '',
  password varchar(41) NOT NULL default '',
  PRIMARY KEY  (username),
  UNIQUE KEY username (username)
) TYPE=MyISAM;

#
# Table structure for table 'aluno'
#

DROP TABLE IF EXISTS aluno;
CREATE TABLE aluno (
  RA mediumint(6) unsigned zerofill NOT NULL default '000000',
  name varchar(255) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  projeto tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (RA,projeto),
  UNIQUE KEY RA (RA)
) TYPE=MyISAM;

#
# Table structure for table 'avisos'
#

DROP TABLE IF EXISTS avisos;
CREATE TABLE avisos (
  id tinyint(2) NOT NULL default '0',
  conteudo blob NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table 'capitulo'
#

DROP TABLE IF EXISTS capitulo;
CREATE TABLE capitulo (
  id tinyint(2) unsigned NOT NULL default '0',
  tipo enum('aluno','professor') default 'aluno',
  name varchar(50) NOT NULL default '',
  friendly varchar(255) NOT NULL default '',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  penalidade tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id),
  UNIQUE KEY name (name),
  UNIQUE KEY name_2 (name),
  UNIQUE KEY friendly (friendly)
) TYPE=MyISAM;

#
# Table structure for table 'defesas'
#

DROP TABLE IF EXISTS defesas;
CREATE TABLE defesas (
  projeto tinyint(2) unsigned NOT NULL default '0',
  local varchar(20) NOT NULL default '',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (projeto)
) TYPE=MyISAM;

#
# Table structure for table 'disciplina'
#

DROP TABLE IF EXISTS disciplina;
CREATE TABLE disciplina (
  responsavel_1_name varchar(255) NOT NULL default '',
  responsavel_1_titulo tinyint(1) unsigned NOT NULL default '1',
  responsavel_2_name varchar(255) NOT NULL default '',
  responsavel_2_titulo tinyint(1) unsigned NOT NULL default '1',
  cod_disciplina mediumint(4) unsigned NOT NULL default '0',
  cod_cursos varchar(255) NOT NULL default '',
  turma varchar(20) NOT NULL default '',
  carga_disciplina tinyint(3) unsigned NOT NULL default '0',
  ano_letivo year(4) NOT NULL default '0000',
  semestre_letivo tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (ano_letivo)
) TYPE=MyISAM;

#
# Table structure for table 'login'
#

DROP TABLE IF EXISTS login;
CREATE TABLE login (
  RA mediumint(6) unsigned zerofill NOT NULL default '000000',
  username varchar(20) NOT NULL default '',
  password varchar(41) NOT NULL default '',
  PRIMARY KEY  (RA),
  UNIQUE KEY username (username),
  UNIQUE KEY RA (RA),
  UNIQUE KEY username_2 (username)
) TYPE=MyISAM;

#
# Table structure for table 'notas'
#

DROP TABLE IF EXISTS notas;
CREATE TABLE notas (
  projeto tinyint(2) unsigned NOT NULL default '0',
  nota float(4,2) unsigned NOT NULL default '0.00',
  comentario blob,
  PRIMARY KEY  (projeto)
) TYPE=MyISAM;

#
# Table structure for table 'notas_finais'
#

DROP TABLE IF EXISTS notas_finais;
CREATE TABLE notas_finais (
  RA mediumint(6) unsigned zerofill NOT NULL default '000000',
  nota_final float(4,2) unsigned NOT NULL default '0.00',
  faltas tinyint(2) unsigned zerofill NOT NULL default '00',
  observacao blob,
  disciplina_trancada tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (RA)
) TYPE=MyISAM;

#
# Table structure for table 'novo_admin'
#

DROP TABLE IF EXISTS novo_admin;
CREATE TABLE novo_admin (
  adm_username varchar(20) NOT NULL default '',
  adm_password varchar(41) NOT NULL default '',
  PRIMARY KEY  (adm_username)
) TYPE=MyISAM;

#
# Table structure for table 'novo_aluno'
#

DROP TABLE IF EXISTS novo_aluno;
CREATE TABLE novo_aluno (
  codigo mediumint(6) unsigned zerofill NOT NULL default '000000',
  nome_aluno varchar(255) NOT NULL default '',
  email_aluno varchar(255) NOT NULL default '',
  id_projeto tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (codigo)
) TYPE=MyISAM;

#
# Table structure for table 'novo_avalia'
#

DROP TABLE IF EXISTS novo_avalia;
CREATE TABLE novo_avalia (
  id_projeto tinyint(2) unsigned NOT NULL default '0',
  codigo mediumint(6) unsigned zerofill NOT NULL default '000000',
  id_capitulo tinyint(2) unsigned NOT NULL default '0',
  data_avaliacao datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id_projeto,codigo,id_capitulo)
) TYPE=MyISAM;

#
# Table structure for table 'novo_avisos'
#

DROP TABLE IF EXISTS novo_avisos;
CREATE TABLE novo_avisos (
  id_aviso tinyint(2) unsigned NOT NULL default '0',
  conteudo blob NOT NULL,
  adm_username varchar(20) NOT NULL default '',
  PRIMARY KEY  (id_aviso)
) TYPE=MyISAM;

#
# Table structure for table 'novo_capitulo'
#

DROP TABLE IF EXISTS novo_capitulo;
CREATE TABLE novo_capitulo (
  id_capitulo tinyint(2) unsigned NOT NULL default '0',
  tipo enum('aluno','professor') NOT NULL default 'aluno',
  nome_capitulo varchar(50) NOT NULL default '',
  legenda_capitulo varchar(255) NOT NULL default '',
  data_entrega datetime NOT NULL default '0000-00-00 00:00:00',
  aplicar_penalidade tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (id_capitulo),
  UNIQUE KEY legenda_capitulo (legenda_capitulo),
  UNIQUE KEY nome_capitulo (nome_capitulo)
) TYPE=MyISAM;

#
# Table structure for table 'novo_defesas'
#

DROP TABLE IF EXISTS novo_defesas;
CREATE TABLE novo_defesas (
  id_projeto tinyint(2) unsigned NOT NULL default '0',
  local varchar(20) NOT NULL default '',
  data_defesa datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id_projeto)
) TYPE=MyISAM;

#
# Table structure for table 'novo_disciplina'
#

DROP TABLE IF EXISTS novo_disciplina;
CREATE TABLE novo_disciplina (
  ano_letivo year(4) NOT NULL default '0000',
  nome_responsavel_1 varchar(255) NOT NULL default '',
  titulo_responsavel_1 tinyint(1) unsigned NOT NULL default '0',
  nome_responsavel_2 varchar(255) NOT NULL default '',
  titulo_responsavel_2 tinyint(1) unsigned NOT NULL default '0',
  cod_disciplina mediumint(4) unsigned NOT NULL default '0',
  cod_cursos varchar(255) NOT NULL default '',
  turma varchar(20) NOT NULL default '',
  carga_disciplina tinyint(3) unsigned NOT NULL default '0',
  semestre_letivo tinyint(1) unsigned NOT NULL default '0',
  adm_username varchar(20) NOT NULL default '',
  PRIMARY KEY  (ano_letivo)
) TYPE=MyISAM;

#
# Table structure for table 'novo_login'
#

DROP TABLE IF EXISTS novo_login;
CREATE TABLE novo_login (
  username varchar(20) NOT NULL default '',
  password varchar(41) NOT NULL default '',
  codigo mediumint(6) unsigned zerofill NOT NULL default '000000',
  PRIMARY KEY  (username)
) TYPE=MyISAM;

#
# Table structure for table 'novo_notas'
#

DROP TABLE IF EXISTS novo_notas;
CREATE TABLE novo_notas (
  id_projeto tinyint(2) unsigned NOT NULL default '0',
  nota float(4,2) unsigned NOT NULL default '0.00',
  comentario blob,
  PRIMARY KEY  (id_projeto)
) TYPE=MyISAM;

#
# Table structure for table 'novo_notas_finais'
#

DROP TABLE IF EXISTS novo_notas_finais;
CREATE TABLE novo_notas_finais (
  codigo mediumint(6) unsigned zerofill NOT NULL default '000000',
  nota_final float(4,2) unsigned NOT NULL default '0.00',
  faltas tinyint(2) unsigned zerofill NOT NULL default '00',
  observacao blob,
  disciplina_trancada tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (codigo)
) TYPE=MyISAM;

#
# Table structure for table 'novo_penalidades_locais'
#

DROP TABLE IF EXISTS novo_penalidades_locais;
CREATE TABLE novo_penalidades_locais (
  id_projeto tinyint(2) unsigned NOT NULL default '0',
  id_capitulo tinyint(2) unsigned NOT NULL default '0',
  aplicar_penalidade_local tinyint(1) unsigned NOT NULL default '0',
  nova_data_entrega datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id_projeto,id_capitulo)
) TYPE=MyISAM;

#
# Table structure for table 'novo_prof_proj'
#

DROP TABLE IF EXISTS novo_prof_proj;
CREATE TABLE novo_prof_proj (
  codigo mediumint(6) unsigned zerofill NOT NULL default '000000',
  id_projeto tinyint(2) unsigned NOT NULL default '0',
  funcao enum('avaliador','orientador') NOT NULL default 'avaliador',
  PRIMARY KEY  (codigo,id_projeto)
) TYPE=MyISAM;

#
# Table structure for table 'novo_professor'
#

DROP TABLE IF EXISTS novo_professor;
CREATE TABLE novo_professor (
  codigo mediumint(6) unsigned zerofill NOT NULL default '000000',
  doutor tinyint(1) unsigned NOT NULL default '0',
  nome_professor varchar(255) NOT NULL default '',
  email_professor varchar(255) NOT NULL default '',
  PRIMARY KEY  (codigo)
) TYPE=MyISAM;

#
# Table structure for table 'novo_projeto'
#

DROP TABLE IF EXISTS novo_projeto;
CREATE TABLE novo_projeto (
  id_projeto tinyint(2) unsigned NOT NULL default '0',
  titulo varchar(255) NOT NULL default '',
  PRIMARY KEY  (id_projeto)
) TYPE=MyISAM;

#
# Table structure for table 'novo_submete'
#

DROP TABLE IF EXISTS novo_submete;
CREATE TABLE novo_submete (
  id_projeto tinyint(2) unsigned NOT NULL default '0',
  id_capitulo tinyint(2) unsigned NOT NULL default '0',
  data_submissao datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id_projeto,id_capitulo)
) TYPE=MyISAM;

#
# Table structure for table 'penalidades'
#

DROP TABLE IF EXISTS penalidades;
CREATE TABLE penalidades (
  projeto tinyint(2) unsigned NOT NULL default '0',
  capitulo tinyint(2) unsigned NOT NULL default '0',
  penalidade tinyint(1) NOT NULL default '0',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (projeto,capitulo)
) TYPE=MyISAM;

#
# Table structure for table 'prof_proj'
#

DROP TABLE IF EXISTS prof_proj;
CREATE TABLE prof_proj (
  RA tinyint(2) unsigned NOT NULL default '0',
  projeto tinyint(2) unsigned NOT NULL default '0',
  funcao enum('avaliador','orientador') NOT NULL default 'avaliador',
  PRIMARY KEY  (RA,projeto)
) TYPE=MyISAM;

#
# Table structure for table 'professor'
#

DROP TABLE IF EXISTS professor;
CREATE TABLE professor (
  RA tinyint(2) unsigned NOT NULL default '0',
  doutor tinyint(1) unsigned NOT NULL default '1',
  name varchar(50) NOT NULL default '',
  email varchar(50) NOT NULL default '',
  PRIMARY KEY  (RA),
  UNIQUE KEY RA (RA)
) TYPE=MyISAM;

#
# Table structure for table 'proj_cap'
#

DROP TABLE IF EXISTS proj_cap;
CREATE TABLE proj_cap (
  projeto tinyint(2) unsigned NOT NULL default '0',
  RA mediumint(6) unsigned zerofill NOT NULL default '000000',
  capitulo tinyint(2) unsigned NOT NULL default '0',
  date timestamp(14) NOT NULL,
  PRIMARY KEY  (projeto,RA,capitulo)
) TYPE=MyISAM;

#
# Table structure for table 'projeto'
#

DROP TABLE IF EXISTS projeto;
CREATE TABLE projeto (
  id tinyint(2) unsigned NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;

