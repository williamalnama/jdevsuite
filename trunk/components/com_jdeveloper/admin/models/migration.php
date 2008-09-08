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
		$db = $this->getDBO();
		$db->execute("SHOW TABLES");
		$tables = $this->filterTables($db->loadResultArray());
		$tables = implode("\n\n\n",$db->getTableCreate($tables));
		$tables = preg_replace('/jos/','#_',$tables);
		JFile::write($this->getFolder().DS.'schema.sql',$tables);
		JPath::setPermissions($this->getFolder(),'0777','0777');
		
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
		JPath::setPermissions($this->getFolder(),'0777','0777');		
		return $this;		
	}
	private function filterTables($tables)
	{

		$coreTables = array('jos_banner','jos_bannerclient','jos_bannertrack','jos_categories','jos_components','jos_contact_details','jos_content','jos_content_frontpage','jos_content_rating','jos_core_acl_aro','jos_core_acl_aro_groups','jos_core_acl_aro_map','jos_core_acl_aro_sections','jos_core_acl_groups_aro_map','jos_core_log_items','jos_core_log_searches','jos_fabrik_form_sessions','jos_groups','jos_menu','jos_menu_types','jos_messages','jos_messages_cfg','jos_migration_backlinks','jos_modules','jos_modules_menu','jos_newsfeeds','jos_plugins','jos_poll_data','jos_poll_date','jos_poll_menu','jos_polls','jos_sections','jos_session','jos_stats_agents','jos_templates_menu','jos_users','jos_weblinks');		
		return array_diff($tables,$coreTables);	
		
	}
	
	
}

?>