<?php 
defined('_JEXEC') or die('Restricted access');
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
		
		$db->execute($q);
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
		
		$db->insertObject($this->name,$object,$this->pk);
	}
	public function __construct($tableName)
	{
		$this->name  = $tableName;
		if (!preg_match('/^#__/',$this->name))
			$this->name = jdb()->replacePrefix('#__'.$this->name);
		$this->indices = array();
		$this->exists =  in_array($this->name,jdb('SHOW TABLES')->loadResultArray());

		$this->cols   = array();
		$this->pk 	  = 'id';
	}	
	public function addColumn($name,$type)
	{
		$col = new MySQLColumn($this,$name,$type);
		
		$this->cols[] = $col;
			
		return $col;
		
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
		array_unshift($this->cols,$pk);
		
		$this->addIndex($this->pk,array('primary_key'=>true,'push'=>true));
		
		$this->sqlRows = array_map(create_function('$n','return $n->create();'),$this->cols);
		$this->sqlRows = array_merge($this->sqlRows,$this->indices);
		
		$this->sqlRows = implode(",",$this->sqlRows);
		
		$q = sprintf("CREATE TABLE `%s` ( %s ) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`",
					$this->name,
					$this->sqlRows);
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
		if ( isset($options['allowNull']) && $options['allowNull'] == false )
			$sql .= ' NOT NULL';
		if ( isset($options['defaultValue']) )
			$sql .= ' '.sprintf("DEFAULT %s",preg_match('/\(\)/',$options['defaultValue']) ? strtoupper($options['defaultValue']) : "'".$options['defaultValue']."'");
			
		if ( $this->table->exists )
		{
			if ( isset($options['after']) )
				$sql .=  ' '.sprintf("AFTER `%s`",$options['after']);
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