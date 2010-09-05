<?php
/**
 * @package Swiftlet
 * @copyright 2009 ElbertF http://elbertf.com
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU Public License
 */

if ( !isset($this) ) die('Direct access to this file is not allowed');

/**
 * Buffer
 * @abstract
 */
class Buffer_Plugin extends Plugin
{
	public
		$version    = '1.0.0',
		$compatible = array('from' => '1.3.0', 'to' => '1.3.*'),
		$hooks      = array('init' => 4, 'end' => 999, 'error' => 999)
		;

	function init()
	{
		$this->start();
	}

	function end()
	{
		if ( !empty($this->ready) && !$this->app->controller->standAlone )
		{
			$this->app->debugOutput['buffer output size'] = round(strlen(ob_get_contents()) / 1024 / 1024, 3) . ' MB';

			$this->flush();
		}
	}

	function error()
	{
		if ( !empty($this->ready) )
		{
			$this->clean();
		}
	}

	/**
	 * Start buffering
	 */
	function start()
	{
		if ( !$this->ready )
		{
			ob_start();

			$this->ready = TRUE;
		}
	}

	/**
	 * Flush the buffer, send output to the browser
	 */
	function flush()
	{
		if ( $this->ready )
		{
			$contents = ob_get_contents();

			$params['contents'] = &$contents;

			$this->app->hook('cache', $params);

 			if ( ob_get_length() > 0 )
			{
				ob_end_clean();
			}

			$this->ready = FALSE;

			// Output debug messages
			ob_start();

			if ( $this->app->debugMode )
			{
				echo "\n<!--\n\n[ DEBUG OUTPUT ]\n\n";

				print_r($this->app->debugOutput);

				echo "\n-->";
			}

			$contents .= ob_get_contents();

			ob_end_clean();

			// gZIP compression
			if ( !empty($_SERVER['HTTP_ACCEPT_ENCODING']) && strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') )
			{
				$contents = gzencode($contents);

				header('Content-Encoding: gzip');
			}

			header('X-Powered-By: Swiftlet - http://swiftlet.org');

			echo $contents;
		}
	}

	/**
	 * Clean the buffer, cancel output
	 */
	function clean()
	{
		if ( $this->ready )
		{
			if ( ob_get_length() > 0 )
			{
				ob_end_clean();
			}

			$this->active = FALSE;
		}
	}
}
