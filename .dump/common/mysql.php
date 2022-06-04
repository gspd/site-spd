<?php

class Connection
{
	var $_dbname = "projeto_final";
	var $_dbhost = "localhost";
	var $_dbusername = "tiago";
	var $_dbpassword = "tiago";
	var $_link_id;
	
	function Connection($dbname = '', $show_error = 0)
	{
		if(!empty($dbname))
			$this->_dbname = $dbname;
		
		if(!isset($GLOBALS['global_link_id']))
		{
			$this->_link_id = mysql_connect($this->_dbhost, $this->_dbusername, $this->_dbpassword);
			
			if(!$this->_link_id)
			{
				if(!$show_error)
					die("Erro ao conectar com o banco de dados!");
				else
				{
					$error = ($show_error == 2) ? mysql_error($this->_link_id) : mysql_errno($this->_link_id);
					die($error);
				}//else
			}
		}//if
		else
			$this->_link_id = $GLOBALS['global_link_id'];
		
		mysql_select_db($this->_dbname, $this->_link_id);
	}
	
	function link_id()
	{
		return $this->_link_id;
	}
}//class Connection




class Query extends Connection
{
	var $_result;
	var $query;
	var $num_rows;
	var $num_fields;
	var $field_order;
	var $rows;
	
	function Query($fields = '', $tables = '', $condition = '', $groupby = '', $orderby = '', $show_error = 0, $fetch_mode = MYSQL_ASSOC)
	{
		$this->_result = NULL;
		$this->query = NULL;
		$this->num_rows = NULL;
		$this->num_fields = NULL;
		$this->field_order = NULL;
		$this->rows = NULL;
		
		if(!empty($fields))
		{
			if(!empty($condition))
				$condition = "WHERE $condition";
			
			if(!empty($groupby))
				$groupby = "GROUP BY $groupby";
			
			if(!empty($orderby))
				$orderby = "ORDER BY $orderby";
			
			$this->Connection("", $show_error);
			
			$this->query = "SELECT $fields FROM $tables $condition $groupby $orderby";
			
			$this->_result = mysql_query($this->query, $this->_link_id);
			
			//para debug
			//$show_error = 2;
			//echo "<br>\n".$this->query;

			if(mysql_error($this->_link_id))
			{
				if(!$show_error)
					die("Erro na consulta ao banco de dados!");
				else
				{
					$error = ($show_error == 2) ? mysql_error($this->_link_id) : mysql_errno($this->_link_id);
					die($error);
				}
			}

			$this->num_fields = mysql_num_fields($this->_result);

			for($i=0; $i < $this->num_fields; $i++)
				$this->field_order[$i] = mysql_field_name($this->_result, $i);

			$this->num_rows = mysql_num_rows($this->_result);

			for($i=0; $i < $this->num_rows; $i++)
			{
				$this->rows[$i] = mysql_fetch_array($this->_result, $fetch_mode);
			}

			if(!isset($GLOBALS['global_link_id']))
				mysql_close($this->link_id());
		}//if
	} // function Query()

	function Query2($query, $show_error = 0)
	{
		$this->Connection("", $show_error);
		$link_id = $this->link_id();

		mysql_query($query, $link_id);

		//debug
		//$show_error = 1;
		//echo "<pre>\n".$query."\n</pre>";

		if(mysql_error($link_id))
		{
			if($show_error)
				die("Erro: ".mysql_error($link_id));
			else
				die("Erro ao executar o comando! (".mysql_errno($link_id).")");
		}

		$this->num_rows = $this->num_fields = 0;
		$this->rows = $this->field_order = NULL;

		if(!isset($GLOBALS['global_link_id']))
			mysql_close($this->link_id());
	}//function Query2
}//class Query




class Tabela
{
	var $tablespec = "<table>";
	var $trspec = "<tr>";
	var $thspec = "<th>";
	var $tdspec = "<td>";
	var $dados;
	var $no_display;
	
	function Tabela($info, $dont = array(-1))
	{
		$this->dados = $info;
		$this->no_display = $dont;
	}//função construtora;
	
	function Exibe()
	{
		$dados = $this->dados;
		$num_rows = $dados->num_rows;
		$num_fields = $dados->num_fields;
				
		$count = 0;
		
		$this->_header();
		
		for($i=0; $i < $num_rows; $i++)
		{
			echo "\n\t", $this->trspec;
			
			for($j=0; $j < $num_fields; $j++)
			{
				if(!empty($no_display) && $j == $no_display[$count])
					$count++;
				else
					echo "\n\t\t", $this->tdspec, $dados->rows[$i][$j], "</td>";
			}//for
			
			$this->append_fields($i);
			
			echo "\n\t</tr>";
		}//for
		
		echo "\n\t</table>\n";
	}//function Tabela
	
	function _header()
	{
		$num_fields = $this->dados->num_fields;
		
		$field_order = $this->dados->field_order;
		
		$count = 0;
		
		echo "\n", $this->tablespec, "\n\t", $this->trspec;
		
		for($i=0; $i < $num_fields; $i++)
		{
			if($i == $this->no_display[$count])
				$count++;
			else
				echo "\n\t\t<th>", $field_order[$i], "</th>";
		}//for
		
		$this->append_header();
		
		echo "\n\t</tr>";
	}//function _header()
	
	function append_fields($i)
	{
		//personalizar a tabela;
	}
	
	function append_header()
	{
		//personalizar os títulos;
	}
}//class Tabela
?>
