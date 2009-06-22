<?php

class JDeveloperViewMigrations extends ComponentView
{
	
	public function prepareDefaultLayout()
	{
		$migrationModel 	= KFactory::get('admin::com.jdeveloper.model.migration');
		$this->migrations   = $migrationModel->getList();
		$this->currentVersion = $migrationModel->version();
	}
	
}