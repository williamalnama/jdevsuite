<?php
defined('_JEXEC') or die('Restricted access');
class ControllerTask extends ComponentController
{
	public $taskModel = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->taskModel = $this->getModel('task','',$this->configModel);
	}
	
	/**
	 * 
	 * @return 
	 */
	public function default_()
	{
		$tasks = $this->taskModel->getList();
		$this->view->assignRef('tasks',$tasks);	
	}
	
	/**
	 * 
	 * @return 
	 */
	public function run()
	{
		
		$output = $this->taskModel->run( JRequest::getVar('name',null) );
		$this->view->assignRef('output',$output);
		$this->default_();
		
	}
	
	/**
	 * 
	 * @return 
	 */
	public function create()
	{
		$this->taskModel->create( JRequest::getVar('name',null) );		
	}
	
}