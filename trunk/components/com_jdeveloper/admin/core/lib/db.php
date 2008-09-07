<?php 
defined('_JEXEC') or die('Restricted access');
function jdb()
{
	$args = func_get_args();
	$db   = JFactory::getDBO();
	if (count($args) > 1)
		$q = call_user_func_array('sprintf',$args);
	else	
		$q = array_shift($args);

	$db->execute($q);
	return $db;
}
function table($name)
{
	return new MySQLTable($name);	
}
class MySQLTable
{
	public function insert($array)
	{
		$db = JFactory::getDBO();
		if ( is_array($array) ) 
		{
			$object = new stdClass();
			foreach($array as $k=>$v)
				$object->$k = $v;
		} else 
			$object = $array;
		
		$db->insertObject('#__'.$this->name,$object,$this->pk);
	}
	public function __construct($tableName)
	{
		$this->name  = $tableName;
		$this->cols   = array();
		$this->pk 	  = 'id';
	}	
	public function addColumn($name,$type)
	{
		$col = new MySQLColumn($name,$type);
		$this->cols[] = $col;
		return $col;
		
	}
	public function primaryKey($key)
	{
		
		$this->pk = $key;
		return $this;	
	}	
	public function create()
	{
		$pk = new MySQLColumn($this->pk,'INT(11)');
		array_push($this->cols,$pk);
		foreach($this->cols as $col)
			print $col->create();
		die;
	}	
	
}

class MySQLColumn
{
	
	public function __construct($name,$type,$options=array())
	{
		$this->name = $name;
		$this->type = $type;
		$this->options = $options;
		
	}
	public function getType()
	{
		$this->type = preg_replace('/\(.*\)/','',$this->type);
		if ($this->type == 'boolean') 
		{
			$this->type = 'tinyint';
			$this->options['limit'] = 1;
		}

		if ( !@$this->options['limit'] )
			$type = $this->type;
		else
			$type = sprintf("%s(%s)",$this->type,$this->options['limit']);

		return strtoupper($type);
	}
	public function create()
	{
		$options = $this->options;
		$sql = sprintf("`%s` %s",$this->name,$this->getType());
		if ( isset($options['allowNull']) && $options['allowNull'] == false )
			$sql .= ' NOT NULL';
		if (@$options['defaultValue'])
			$sql .= ' '.sprintf("DEFAULT '%s'",$options['defaultValue']);
						
		print $sql;
		die;
	}
	public function __call($field,$values)
	{
		
		$this->options[$field] = array_shift($values);
		return $this;		
	}

	
}
?>