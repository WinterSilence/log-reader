<?php
/**
 * Controller to display system logs.
 */
abstract class Controller_Base_Log extends Controller_Template
{
	/**
	 * @var Model_Log_Reader
	 */
	protected $log_reader;

	public function before()
	{
		parent::before();
		$this->template->param = Arr::extract($this->request->param(), ['year', 'month', 'day']);
		$this->log_reader = new Model_Log_Reader();
	}
	
	public function action_index()
	{
		$this->template->param['level'] = $this->request->post('level');
		$this->template->log = $this->log_reader->set_config($this->template->param);
	}
	
	public function action_delete()
	{
		$this->auto_render = false;
		$this->log_reader->set_config($this->template->param)->delete();
		static::redirect($this->request->route->uri(['controller' => 'Base_Log']));
	}
	
	public function after()
	{
		if ($this->auto_render)
		{
			$path_prefix = 'log' . DIRECTORY_SEPARATOR;
			$content = $path_prefix . $this->request->action();
			$this->template->content = $this->template->render($content);
			
			$layout = $this->request->is_initial() ? 'layout' : 'layout-min';
			$this->template->set_filename($path_prefix . $layout);
		}
		
		parent::after();
	}	
}
