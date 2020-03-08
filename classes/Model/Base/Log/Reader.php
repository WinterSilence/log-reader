<?php
/**
 * Log reader for `Kohana_Log_Syslog`.
 */
abstract class Model_Base_Log_Reader
{
    /**
     * @var array
     */
    protected $config = [
        // 'time --- level: body in file:line'
        // '/(.*) --- ([A-Z]*): ([^:]*):? ([^~]*)~? (.*)/xu',
        'format'    => '/(.*) --- ([A-Z]*): ([^:]*):? ([^~]*)~? (.*)/xu',
        'logs_path' => 'logs',
        'year'      => null,
        'month'     => null,
        'day'       => null,
        'level'     => null,
    ];

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $method = 'get_' . $key;
        if (method_exists($this, $method))
        {
            return $this->{$method}();
        }
        return $this->config[$key];
    }

    /**
     * @param array $files
     * @return array
     */
    protected function fix_filenames(array $files)
    {
        foreach ($files as $key => $value)
        {
            unset($files[$key]);
            $key = basename($key);
            $files[$key] = is_array($value) ? $this->fix_filenames($value) : basename($value, EXT);
        }
        return $files;
    }

    /**
     * @return string|null
     */
    protected function get_path()
    {
        $path = Arr::extract($this->_config, ['year', 'month', 'day']);
        $path = implode(DIRECTORY_SEPARATOR, $path);
        return Kohana::find_file('logs', $path);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function set_config(array $config = [])
    {
        $this->config = array_replace($this->config, $config);
        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function get_logs()
    {
        $files = Kohana::list_files($this->config['logs_path']);
        return $this->fix_filenames($files);
    }

    /**
     * @return array
     */
    public function get_levels()
    {
        return array_keys((new ReflectionClass('Kohana_Log'))->getConstants());
    }

    /**
     * @param string $level
     * @return array
     */
    public function get_messages($level = null)
    {
        $file = $this->get_path();
        if (! $file)
        {
            return [];
        }
        
        $messages = $message = [];
        $file = fopen($file, 'r');
        $i = 0;
        while (! feof($file))
        {
            $str = trim(fgets($file));
            if (preg_match($this->config['format'], $str, $message))
            {
                $i++;
                if (empty($level) || $message[2] == $level)
                {
                    // @todo use `(?<key>...)` in regexp
                    $message[6] = preg_filter('/^(.+):([0-9]*)/', '${2}', $message[5]);
                    $messages[$i] = [
                        'time'      => preg_filter('/^(.*) /', '', $message[1]),
                        'level'     => $message[2],
                        'exception' => preg_filter('/ (.*)$/', '', $message[3]),
                        'text'      => $message[4],
                        'str_num'   => $message[6],
                        'string'    => Debug::source(preg_filter('/^(.*)in /', '', $message[5]), $message[6]),
                        'file'      => preg_filter('/ \[(.*)/', '', $message[5]),
                    ];
                }
            }
            elseif (isset($messages[$i]) && ! preg_match('/\{main\}/',  $str) && ! preg_match('/^--/',  $str))
            {
		$str = preg_replace('/^#/', PHP_EOL, $str);
                if ($str)
                {
                    $messages[$i]['text'] .= $str;
                }
            }
        }
        fclose($file);
        
        return $messages;
    }

    /**
     * @return $this
     */
    public function delete_file()
    {
        $file = $this->get_path();
        if ($file)
        {
            unlink($file);
        }
        return $this;
    }
}
