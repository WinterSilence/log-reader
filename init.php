<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Nooby protect 
 * Remove after that you configure your access
 */
if (Kohana::$environment !== Kohana::DEVELOPMENT)
{
	throw new Kohana_Exception('Note! Use Log Viewer in PRODUCTION not recommended. This text in init.php');
}

if ( ! Route::cache())
{
	Route::set('log', 'log(/<action>(/<year>(/<month>(/<day>))))', array(
			'action' => '(show|delete)',
			'year'   => '[0-9]+', // '[0-9]'
			'month'  => '[0-9]+', // '[0-9]'
			'day'    => '[0-9]+', // '[0-9]{2}'
		))->defaults(array(
			'controller' => 'Log',
			'action'     => 'show',
			'year'       => 0,
			'month'      => 0,
			'day'        => 0,
		));
}
