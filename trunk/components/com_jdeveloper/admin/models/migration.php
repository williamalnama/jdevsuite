<?php

class ModelMigration extends JModel
{
	public function __construct()
	{
		parent::__construct(array());
		$this->config = new ModelConfig();
			
	}
	public function getFolder()
	{
		
		$folder = $this->config->getDevFolder().DS.'migrations';
		if ( !JFolder::exists($folder) ) {
			JFolder::create($folder);
			JPath::setPermissions($folder,'0777','0777');
		}
		return $folder;
	}
	public function getMigrationFile($ver)
	{		
		if (!@$this->files)
			$this->files = JFolder::files($this->getFolder());
		foreach($this->files as $file)
		{
			$m = $this->parseFileName($file);
			if ( $m->version == $ver)
				return $m;
		}	
	}
	public function migrate($targetVer)
	{
		$currVer = $this->version();

		if ( $targetVer <= 0 )
			return;
			
		if ($targetVer > $currVer)
		{
			$versions = range($currVer + 1,$targetVer);			
			$method   = 'up';			
		} else {
			$versions = array_reverse(range($targetVer,$currVer));
			$method   = 'down';			
		}
		foreach($versions as $ver)
		{
			$migration = $this->getMigrationFile($ver);
			require $migration->path;
			$migrationClass = new $migration->className();
			$migrationClass->$method();
	
			$this->setVersion( $method == 'up' ? $ver : $ver-1);
		}
		
	}
	public function setVersion($ver)
	{
		return $this->config->setMigrationVersion($ver);
	}
	public function version()
	{
		return $this->config->getMigrationVersion();
	}
	public function parseFileName($file)
	{
		$m = new stdClass();		
		$m->fileName = $file;
		$m->path	 = $this->getFolder().DS.$file;
		$m->name	  = preg_replace('/\d+_|\.php/','',$file);
		$m->className = $m->name.'Migration';
		$m->version  = (int) preg_replace('/[^0-9]*/','',$file);
		return $m;
	}	
	public function getList()
	{
		$folder = $this->getFolder();
		$files = (array) JFolder::files($folder);
		
		foreach($files as $index=>$file)
		{
			if ( preg_match('/^\d+_/',$file) )	
			{
				$migrations[] = $this->parseFileName($file);
			}
		}
		return (array) @$migrations;
	}
	public function create($name)
	{

		$folder = $this->getFolder();
			
		$files = JFolder::files($folder);

		$migrationFile  = null;
		$migrationFiles = array();

		foreach($files as $index=>$file)
		{
			if ( preg_match('/^\d+_/',$file) )			
				$migrationFiles[] = $file;		
			
			$currentFilemigrationName = preg_replace('/\d+_|\.php/','',$file);
			
			if ($currentFilemigrationName == $name)
				$migrationFile = $file;
			
		}
		if ( !$migrationFile )
		{
			$newMigrationIndex = (string) count($migrationFiles) + 1;

			if ( strlen($newMigrationIndex) == 1 )			
				$newMigrationIndex = '0'.$newMigrationIndex;				

			
			$migrationFile = $newMigrationIndex.'_'.$name.'.php';
			
			template('migration')->copy($folder.DS.$migrationFile,array('migrationClassName'=>ucfirst($name)));
					

		}
				
		return $this;		
	}
	
	
}

?>