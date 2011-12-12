<?php

use Env\Hunter\PHPErrorHandler;
use Env\Hunter\Logger as HunterLogger;
use Env\Hunter\Hunter as Hunter;

class Artise
{
	private static
		$event_handler,
		$hunter,
		$lang,
		$devkit,
		$router,
		$config,
		$fallback;
	
	public static
		$hunter_logger;
		
			
	public static function &router(&$router = false)
	{
		if (!self::$router && $router)
			self::$router = &$router;
		
		return self::$router;
	}
	
		
	public static function &devkit(&$devkit = false)
	{
		if (!self::$devkit && $devkit)
			self::$devkit = &$devkit;
		
		return self::$devkit;
	}
	
	public static function &hunter()
	{
		if(!isset(self::$hunter)) {
			//Hunter Initialize
			self::$hunter_logger = new HunterLogger();
			self::$hunter_logger->uri(URI);
			self::$hunter_logger->read(HUNTER_LOG_READ_DEFAULT);
			
			// Register Hunter Logger as the PHP Error Handler.
			set_error_handler(array(new PHPErrorHandler(self::$hunter_logger), 'register'));
			
			// Instance Hunter.
			self::$hunter = new Hunter(self::$hunter_logger);
		}
		
		return self::$hunter;
	}
		
	public static function &lang(&$lang = false)
	{
		if (!self::$lang && $lang)
			self::$lang = &$lang;
		
		return self::$lang;
	}
	
	public static function &event()
	{
		if(!isset(self::$event_handler)) {
			self::$event_handler = new \Env\Events\Handler();
		}
		
		return self::$event_handler;
	}
	
	public static function &config($config = false)
	{
		if (!self::$config && $config)
			self::$config = &$config;
		
		return self::$config;
	}
	
	
	
	public static function initialize()
	{
		// Begin Session.
		session_start();
		
		// Set Error reporting.
		error_reporting(E_ALL | E_STRICT);
		
		// Force on the circular reference collector.
		gc_enable();
		
		// Start Output Buffering.
		ob_start();
	}
	
	public static function finalize($html = false)
	{
		// Print the HTML.
		echo $html;
		
		// Save and Print Hunter.
		if(self::$hunter->enabled()) {
			// Save Hunter's Log.
			if (self::$hunter_logger->save(Artise_Path_Hunter_Logs . DS . time()) && $html !== false) {
				// Display the console.
				if(!self::$router->request()->isAjax())
				{
					echo '<div id="artise_plugins_hunter_console_spacing" style="clear:both; display:block; width:100%; height:300px; visibility:visible;"></div>
<iframe id="artise_plugins_hunter_console" style="position:fixed; width:100%; height:300px; left:0; bottom:0; border:0; border-top: 1px solid rgba(0, 0, 0, .2); background:#fff;" scrolling="yes" src="/plugins:hunter/"></iframe>';
				}
			}
		}
		
		// Print fallback alert.
		if(self::$fallback) {
			echo '<div style="position: fixed; top: 0; left: 0; z-index: 9000; width: 100%; height: 1px; text-align: center;">
	<div style="display: inline-block; padding: 12px; background: rgba(0, 0, 0, .8); color: #fff; font-size: 14px; font-family: Helvetica, Arial, sans-serif; font-style: none; font-weight: normal; border: 2px solid rgba(255, 255, 255, .7); border-top: 0; -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, .8); -moz-box-shadow: 0 1px 6px rgba(0, 0, 0, .8); border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">Artise is in Fallback mode. The behavior will be unstable.</div>
</div>';
		}
	}
	
	public static function fallback($status)
	{
		self::$fallback = (bool)$status;
	}
	
}

?>