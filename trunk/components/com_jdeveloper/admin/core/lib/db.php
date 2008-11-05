<?php 
defined('_JEXEC') or die('Restricted access');

/**
 * JDeveloper Database Proxy Class
 */
class DatabaseProxy
{
	
	/**
	 * 
	 */
	private $object = null;
	
	/**
	 * 
	 * @return 
	 * @param $object Object
	 */
	public function __construct($object)
	{	
	 	$this->object = $object;
	}
	
	public function insertObject($tableName,$object,$pk)
	{
		$db   = $this->getObject();
		
		if ( is_array($object) ) 
		{
			$obj = new stdClass();
			foreach($object as $k=>$v) $obj->$k = $v;								
			$object = $obj;
		} 
				
		
		$db->insertObject($tableName,$object,$pk);
		
//		PerfomedQueries::captureLastQuery($db);
		
		if ($this->object->getErrorMsg())
			JError::raiseWarning(500,$db);		
				
	}
	/**
	 * 
	 * @return 
	 * @param $q Object
	 */
	public function execute()
	{
		$args = func_get_args();
		$db   = $this->object;
		$q 	  = null;
		if (count($args) > 1)
			$q = call_user_func_array('sprintf',$args);
		else if ( count($args) == 1 )
			$q = array_shift($args);

		try {	$db->execute($q); }catch(Exception $e){}
		
//		if ( $q != 'SHOW TABLES' )
			PerfomedQueries::capture($q,$db->getErrorMsg());
		
		if ($db->getErrorMsg())
			JError::raiseWarning(500,$db->getErrorMsg());	}
	
	/**
	 * 
	 * @return 
	 * @param $method Object
	 * @param $args Object
	 */
	public function __call($method,array $arguments)
	{
		$object = $this->getObject();		
		return call_user_func_array(array($object, $method), $arguments);		
		
	}
	
	/**
	 * 
	 * @return 
	 */
	public function getObject()
	{
		return $this->object;	
	}
	
	
}


class PerfomedQueries
{
	/**
	 * 
	 */
	static $queries = array();
	
	/**
	 * 
	 * @return 
	 * @param $q Object
	 * @param $error Object[optional]
	 */
	static function capture($q,$error=null)	
	{
		$session  = JFactory::getSession();
		$performedQueries = $session->get('performed_queries');
		
		if ( !$performedQueries )
			$performedQueries = array();
			
		$q  = str_replace('jos','#__',$q);
		
		$performedQueries[] = array($q,$error);
		
		$session->set('performed_queries',$performedQueries);
		
	}
	
	/**
	 * 
	 * @return 
	 */
	static function clear()
	{		
		JFactory::getSession()->set('performed_queries',array());		
	}
	
	/**
	 * 
	 * @return 
	 */
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
	if ( count($args) > 0 )
		call_user_method_array('execute',$db,$args);	
		
	return $db;
}

/**
 * fast of accessing table instance
 * @return 
 * @param $name Object
 */
function table($name)
{
	return new MySQLTable($name);	
}

/**
 * 
 * @return 
 */
class MySQLTable
{
	/**
	 * 
	 * @return 
	 * @param $tableName Object
	 */
	public function __construct($tableName)
	{
		$this->name  = $tableName;

		$this->db	 = JFactory::getDBO();		
			
		if (!preg_match('/^#__/',$this->name))
			$this->name = $this->db->replacePrefix('#__'.$this->name);
				
		$this->indices = array();
		
		$this->db->getObject()->execute('SHOW TABLES');

		$this->exists =  in_array($this->name,$this->db->loadResultArray());

		$this->cols   = array();
		$this->pk 	  = 'id';
	}	
	
	/**
	 * 
	 * @return 
	 */
	public function drop()
	{
		if ($this->exists)
			$this->db->execute("DROP TABLE IF EXISTS `%s`",$this->name);
	}
	
	/**
	 * 
	 * @return 
	 * @param $data Object
	 */
	public function insert($data)
	{
		if (!$this->exists)
			return;
							
		$this->db->execute->insertObject($this->name,$data,$this->pk);
		
	}
	
	/**	
	 * 
	 * @return 
	 * @param $name Object
	 * @param $type Object
	 * @param $options Object[optional]
	 */
	public function addCol($name,$type,$options=array())
	{
		return $this->addColumn($name,$type,$options);
	}
	
	/**
	 * 
	 * @return 
	 * @param $name Object
	 * @param $type Object
	 * @param $options Object[optional]
	 */
	public function addColumn($name,$type,$options=array())
	{
		$col = new MySQLColumn($this,$name,$type,$options);
		
		$this->cols[] = $col;
			
		return $col;
		
	}
	
	/**
	 * 
	 * @return 
	 */
	function dropCols()
	{
		$cols = func_get_args();
		call_user_method_array('dropColumns',$this,$cols);
	}
	
	/**
	 * 
	 * @return 
	 */
	public function dropColumns()
	{
		$cols = func_get_args();		
		if ($this->exists)
			foreach($cols as $col)
				$this->db->execute("ALTER TABLE %s DROP COLUMN `%s`",$this->name,$col);
	}
	
	/**
	 * 
	 * @return 
	 * @param $key Object
	 */
	public function primaryKey($key)
	{		
		$this->pk = $key;
		return $this;	
	}
	
	/**
	 * 
	 * @return 
	 * @param $indexName Object
	 */
	public function dropIndex($indexName)
	{
		$this->db->execute("ALTER TABLE `%s` DROP INDEX `%s`",$this->name,$indexName);
	}
	
	/**
	 * 
	 * @return 
	 */
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
			$this->db->execute($indexSql);
		} 			
		

			
	}
	
	/**
	 * 
	 * @return 
	 */
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
					
		$this->db->execute($q);
		
	}	
	
}

/**
 * 
 * @return 
 * @param $table Object
 * @param $name Object
 * @param $type Object
 * @param $options Object[optional]
 */
class MySQLColumn
{
	
	/**
	 * 
	 * @return 
	 * @param $table Object
	 * @param $name Object
	 * @param $type Object
	 * @param $options Object[optional]
	 */
	public function __construct($table,$name,$type,$options=array())
	{
		$this->table = $table;
		$this->name = $name;
		$this->db   = JFactory::getDBO();
		$this->type = $type;
		$this->options = $options;
		
		if ( $this->table->exists && !empty($this->options) )
			$this->up();
		
	}
	
	/**
	 * 
	 * @return 
	 */
	public function up()
	{
		$this->update();	
	}
	
	/**
	 * 
	 * @return 
	 */
	public function update()
	{
		if ( !$this->table->exists)
			return;			
		$this->db->execute("ALTER TABLE `%s` ADD COLUMN %s",$this->table->name,$this->create());
		
	}
	
	/**
	 * 
	 * @return 
	 */
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
	
	/**
	 * 
	 * @return 
	 * @param $field Object
	 * @param $values Object
	 */
	public function __call($field,$values)
	{
		
		$this->options[$field] = array_shift($values);
		return $this;		
	}

	
}
?>