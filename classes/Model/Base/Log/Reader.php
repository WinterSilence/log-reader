<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Abstract Log Reader for Kohana Log_Syslog files
 */
abstract class Model_Base_Log_Reader extends Model
{
	
	protected $_config = array(
		// 'time --- level: body in file:line'
		//"/(.*) --- ([A-Z]*): ([^:]*):? ([^~]*)~? (.*)/"
		'format'    => '/(.*) --- ([A-Z]*): ([^:]*):? ([^~]*)~? (.*)/',
		'logs_path' => 'logs',
		'year'      => NULL,
		'month'     => NULL,
		'day'       => NULL,
		'level'     => NULL,
	);

	
	public static function factory($name, array $config = array())
	{
		// Add the model prefix
		$name = 'Model_'.$name;
		return new $name($config);
	}

	
	public function __get($key)
	{
		if (method_exists($this, 'get_'.$key))
		{
			return $this->{'get_'.$key}();
		}
		return $this->_config[$key];
	}

	
	protected function _fix_name(array $files)
	{
		foreach ($files as $key => $val)
		{
			unset($files[$key]);
			$key = basename($key);
			$files[$key] = is_array($val) ? $this->_fix_name($val) : basename($val, EXT);
		}
		return $files;
	}

	
	protected function _get_file()
	{
		$path = Arr::extract($this->_config, array('year', 'month', 'day'));
		$path = implode(DIRECTORY_SEPARATOR, $path);
		return Kohana::find_file('logs', $path);
	}

	
	public function set_config(array $config = array())
	{
		$this->_config = Arr::merge($this->_config, $config);
		return $this;
	}

	
	public function get_logs()
	{
		$files = Kohana::list_files($this->_config['logs_path']);
		return $this->_fix_name($files);
	}

	
	public function get_levels()
	{
		return array_keys((new ReflectionClass('Log'))->getConstants());
	}
	
	public function get_messages($level = NULL)
	{
		if ($file = $this->_get_file())
		{
			$result = array();
			$file = fopen($file, 'r');
			$i = 0;
			$msg = array();
			while ( ! feof($file))
			{
				$str = trim(fgets($file));
				if (preg_match($this->_config['format'], $str, $msg))
				{
					$i++;
					if ($msg[2] == $level OR empty($level))
					{
						$msg[6] = preg_filter('/^(.+):([0-9]*)/', '${2}', $msg[5]);
						$result[$i] = array(
							'time'      => preg_filter('/^(.*) /', '', $msg[1]),
							'level'     => $msg[2],
							'exception' => preg_filter('/ (.*)$/', '', $msg[3]),
							'text'      => $msg[4].PHP_EOL,
							'str_num'   => $msg[6],
							'string'    => Debug::source(preg_filter('/^(.*)in /', '', $msg[5]), $msg[6]),
							'file'      => preg_filter('/ \[(.*)/', '', $msg[5]),
						);
					}
				}
				elseif (isset($result[$i]) AND ! preg_match('/\{main\}/',  $str) AND ! preg_match('/^--/',  $str))
				{
					if ($str = preg_replace(array('/^#/'), PHP_EOL, $str))
					{
						$result[$i]['text'] .= $str;
					}
				}
			}
			fclose($file);
			
			return $result;
		}
	}

	
	public function delete()
	{
		if ($file = $this->_get_file())
		{
			unlink($file);
		}
		return $this;
	}
	
} // End Log_Reader