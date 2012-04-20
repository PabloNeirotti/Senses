<?php
namespace Env\Uri
{
	class Parser
	{
		static private
			$parts = array(),
			$clear = '',
			$lang_exists = false;
		
		static public function langExists()
		{
			return self::$lang_exists;
		}
		
		static public function parse(&$config, &$lang, $uri, $plugin_id = false)
		{
			//static $called = false; if($called) return; $called = true;

			//try to catch get-variables like ?var1=value&var2=value&...
			$argv_str = isset($_SERVER['argc']) ? $_SERVER['argv'][0] : NULL;
			if($argv_str) {
				$_GET = array();
				parse_str($argv_str, $_GET);
			}

			//normalize data
			if($uri) {
				//quit $argvStr from uri
				self::$clear = str_replace('?' . $argv_str, '', $uri);
				self::$parts = explode(DS, self::$clear);
			}
		
			try {
				if(self::$parts) {
					if(isset(self::$parts[1])) {
						$tmp_lang = strtolower(self::$parts[1]);
						
						// If it retuns true, it means that the lang IS in the URL.
						if ($lang->set($tmp_lang)) {
							// Redirect to remove default language in URI.
							if ($tmp_lang == $config->get('site:lang')) {
								
								// Define target location, and if some double slashes form, remove them.
								$target_location = implode('/', array_slice(self::$parts, 2));
								//print_r(self::$parts);
								
								// Redirect.
								header("HTTP/1.1 301 Moved Permanently");
								header("Location: /$target_location");
							}
						
							self::$lang_exists = true; //El idioma figura en la url
							self::$parts = array_merge(array(''), array_slice(self::$parts, 2)); //Quito lang de las uri-parts
						}
					}
				}
				
				if (!$lang->active()) {
					// Set language to default.
					$lang->set($config->get('site:lang'));
				}
			}
			catch(\Exception $e) {
			}
			
			return implode(DS, self::$parts);
		}
	}
}
?>
