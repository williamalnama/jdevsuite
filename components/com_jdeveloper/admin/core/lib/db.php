<?php 
defined('_JEXEC') or die('Restricted access');

class PerfomedQueries
{
	static $queries = array();
	static function captureLastQuery()
	{
		$db = JFactory::getDBO();
		self::capture($db->getQuery(),$db->getErrorMsg());
	}
	static function capture($q,$error=null)	
	{
		$session  = JFactory::getSession();
		$performedQueries = $session->get('performed_queries');
		
		if ( !$performedQueries )
			$performedQueries = array();
			
		$db = JFactory::getDBO();
		$q  = str_replace($db->_table_prefix,'#__',$q);
		
		$performedQueries[] = array($q,$error);
		
		$session->set('performed_queries',$performedQueries);
		
	}
	static function clear()
	{		
		JFactory::getSession()->set('performed_queries',array());		
	}
	static function get()
	{		
		$session  = JFactory::getSession();
		$performedQueries = $session->get('performed_queries');
		
		if ( !$performedQueries )
			$performedQueries = array();

		return $performedQueries;
	}
	
}

function jdb()
{
	$args = func_get_args();
	$db   = JFactory::getDBO();
	$q 	  = null;
	if (count($args) > 1)
		$q = call_user_func_array('sprintf',$args);
	else if ( count($args) == 1 )
		$q = array_shift($args);
	
	if ($q) {
		
		try {
			$db->execute($q);
		}catch(Exception $e){}
		PerfomedQueries::captureLastQuery();
		
		if ($db->getErrorMsg())
			JError::raiseWarning(500,$db->getErrorMsg());
	}
	return $db;
}
function table($name)
{
	return new MySQLTable($name);	
}
class MySQLTable
{
	public function drop()
	{
		if ($this->exists)
			jdb("DROP TABLE IF EXISTS `%s`",$this->name);
	}
	public function insert($array)
	{
		if (!$this->exists)
			return;
			
		$db = JFactory::getDBO();
		if ( is_array($array) ) 
		{
			$object = new stdClass();
			foreach($array as $k=>$v)
				$object->$k = $v;
		} else 
			$object = $array;
		
		$tableName = $this->name;
		
		$db->insertObject($tableName,$object,$this->pk);
		
		PerfomedQueries::captureLastQuery();
		
		if ($db->getErrorMsg())
			JError::raiseWarning(500,$db->getErrorMsg());
		
	}
	public function __construct($tableName)
	{
		$this->name  = $tableName;

		if (!preg_match('/^#__/',$this->name))
			$this->name = jdb()->replacePrefix('#__'.$this->name);

		$this->indices = array();
		jdb()->execute('SHOW TABLES');
		$this->exists =  in_array($this->name,jdb()->loadResultArray());

		$this->cols   = array();
		$this->pk 	  = 'id';
	}	
	public function addCol($name,$type,$options=array())
	{
		return $this->addColumn($name,$type,$options);
	}
	public function addColumn($name,$type,$options=array())
	{
		$col = new MySQLColumn($this,$name,$type,$options);
		
		$this->cols[] = $col;
			
		return $col;
		
	}
	function dropCols()
	{
		$cols = func_get_args();
		call_user_method_array('dropColumns',$this,$cols);
	}
	public function dropColumns()
	{
		$cols = func_get_args();		
		if ($this->exists)
			foreach($cols as $col)
				jdb("ALTER TABLE %s DROP COLUMN `%s`",$this->name,$col);
	}
	public function primaryKey($key)
	{		
		$this->pk = $key;
		return $this;	
	}
	public function dropIndex($indexName)
	{
		jdb("ALTER TABLE `%s` DROP INDEX `%s`",$this->name,$indexName);
	}
	public function addIndex()
	{		
		$cols = func_get_args();
		$options = array_pop($cols);

		if ( !is_array($options) ) {
			array_push($cols,$options);
			$options = array();
		}
		
		$quotedCols = array_map(create_function('$n','return "`".$n."`";'),$cols);
		$quotedCols	= implode(',',$quotedCols);
		
		if ( isset($options['name']) )
			$indexName = $options['name'];
		else	
			$indexName = implode('_and_',$cols);
					
		if ( !$this->exists ) 
		{
			
			$indexType = 'INDEX';
		
			if ( isset($options['unique']) && $options['unique'] == true )
				$indexType = 'UNIQUE';
			else if ( isset($options['primary_key']) && $options['primary_key'] == true)
				$indexType = 'PRIMARY KEY';
				
			$indexSql = sprintf("%s `%s` (%s)",$indexType,$indexName,$quotedCols);
			
			if ( isset($options['push']) && $options['push'] == true) {
				array_unshift($this->indices,$indexSql);			
			}
			else 
				$this->indices[] =  $indexSql;
			
		} else {
			
			$indexType = 'INDEX';
			
			if ( isset($options['unique']) && $options['unique'] == true )
				$indexType = 'UNIQUE INDEX';
			else if ( isset($options['fulltext']) && $options['fulltext'] == true )
				$indexType = 'FULLTEXT INDEX';
			if ( isset($options['spatial']) && $options['spatial'] == true )
				$indexType = 'SPATIAL INDEX';
				
			$indexSql = sprintf("CREATE %s `%s` ON `%s` (%s)",$indexType,$indexName,$this->name,$quotedCols);
			jdb($indexSql);
		} 			
		

			
	}
	public function create()
	{
		if ( $this->exists )
			return;
			
		$pk = new MySQLColumn($this,$this->pk,'INT(11)');
		$pk->allowNull(false);
		
		array_unshift($this->cols,$pk);
		
		$this->addIndex($this->pk,array('primary_key'=>true,'push'=>true));
		
		$this->cols = array_map(create_function('$n','return $n->create();'),$this->cols);

		$this->cols	   = implode(",\n\t",$this->cols);
		if ( count($this->indices) > 0 )
			$this->cols .= ',';
						
		$this->indices = implode(",\n\t",$this->indices);
		
		$q = sprintf("CREATE TABLE `%s` (\n\t%s\n\n\t%s\n) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`",
					$this->name,
					$this->cols,
					$this->indices);
		jdb($q);
		
	}	
	
}

class MySQLColumn
{
	
	public function __construct($table,$name,$type,$options=array())
	{
		$this->table = $table;
		$this->name = $name;
		$this->type = $type;
		$this->options = $options;
		
		if ( $this->table->exists && !empty($this->options) )
			$this->up();
		
	}
	public function up()
	{
		$this->update();	
	}
	public function update()
	{
		if ( !$this->table->exists)
			return;			
		jdb("ALTER TABLE `%s` ADD COLUMN %s",$this->table->name,$this->create());
		
	}
	public function create()
	{
		$options = $this->options;
		$sql = sprintf("`%s` %s",$this->name,strtoupper($this->type));
		
		if ( $this->name == $this->table->pk )
		{
			$sql .=' NOT NULL AUTO_INCREMENT';
			return $sql;
		}
		if ( (isset($options['allowNull']) && $options['allowNull'] == false) || (isset($options['null']) && $options['null'] == false) )
			$sql .= ' NOT NULL';
		if ( isset($options['defaultValue']) || isset($options['default']) )
		{
			$default = isset($options['defaultValue']) ? $options['defaultValue'] : $options['default'];
			$sql .= ' '.sprintf("DEFAULT %s",preg_match('/\(\)/',$default) ? strtoupper($default) : "'".$default."'");
		}	
		if ( $this->table->exists )
		{
			if ( isset($options['after']) )
				$sql .=  ' '.sprintf("AFTER `%s`",$options['after']);
			else if ( isset($optinos['before']) )
				$sql .=  ' '.sprintf("BEFORE `%s`",$options['after']);
			
		} else {
			
			if ( isset($options['primaryKey']) && isset($options['primaryKey']) == true )
				$sql .= ' PRIMARY KEY';	
			
		}
		
		return $sql;
	}
	public function __call($field,$values)
	{
		
		$this->options[$field] = array_shift($values);
		return $this;		
	}

	
}
?>