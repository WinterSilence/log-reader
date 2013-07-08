<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Controller_Base_Log extends Controller_Template
{
	
	public $template = NULL;
	
	public function before()
	{
		parent::before();
		$this->template->param = Arr::extract($this->request->param(), array('year', 'month', 'day'));
	}
	
	public function action_show()
	{
		$this->template->param['level'] = $this->request->post('level');
		$this->template->log = Model::factory('Log_Reader')->set_config($this->template->param);
		/**
		 * Moved in view but maybe bestway call here?
		 * 
		 * $this->template->logs = $reader->get_logs();
		 * $this->template->levels = $reader->get_levels();
		 */
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;
		Model::factory('Log_Reader')->set_config($this->template->param)->delete();
		HTTP::redirect(Route::url('log'));
	}
	
	public function after()
	{
		if ($this->auto_render)
		{
			$content = 'log'.DIRECTORY_SEPARATOR.$this->request->action();
			$this->template->content = $this->template->render($content);
			
			if ($this->request->is_initial())
			{
				$this->template->set_filename('log'.DIRECTORY_SEPARATOR.'layout');
			}
			else
			{
				$this->template->set_filename('log'.DIRECTORY_SEPARATOR.'layout-min');
			}
		}
		parent::after();
	}
	
} // End Controller Log
