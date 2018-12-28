<?php
/**
 * fonfig that help us to access the values from the
 * init.php
 */
class Config
{
	public static function get($path = null) {
		if ($path) {
			$config = $GLOBALS['config'];
			$path   = explode('/', $path);

			foreach ($path as $bit) {
				if (isset($config[$bit])) {
					$config = $config[$bit];
				}
			}

			return $config;
		}

		return false;
	}
}