<?php
namespace Env\Plugins
{
	
	/**
	 * PluginSet equals a Plugin instance manager.
	 */
	
	class PluginSet extends \Ext\Source\Instances
	{
		const LOADED = 2;
		const MAIN_FILE_INACCESIBLE = 4;
		
		public
			$status;
		
		protected
			$config,
			$devkit,
			$event_handler,
			$name,
			$plugins = array();
		
		
		
		/*** Internal functions ***/
		
		
		public function &getPluginInstance($instance_id = '')
		{
			try {
				return $this->getInstance($instance_id, array($this->name, &$this->config, &$this->devkit, &$this->event_handler));
			} catch (\HunterException $e) {
				/* Failed to instance */
				
				// Report error.
				\Artise::hunter()->error($e);
				
				// Return empty object.
				$empty_object = new \Env\Source\EmptyObject('...->lang()');
				return $empty_object;
			}
		}
		
		
		public function __construct(&$devkit, $plugin_name)
		{
		
			/* Prepare for instances */
		
			// Store the Plugin name.
			$this->name = $plugin_name;
			
			// Store Devkit.
			$this->devkit = &$devkit;
			
			$main_file = Artise_Path_Plugins . \DS . $plugin_name . \DS . $plugin_name . '.php';
			
			if(!is_readable($main_file)) {
				$this->status = self::MAIN_FILE_INACCESIBLE;
				return;
			}
			
			// Includes the file containing the main class.
			include_once $main_file;
			
			// Stores the path of the class.
			$this->instances_class_path = '\plugins\\' . $plugin_name . '\Main';
			
			
			/* Load Config and store it */
			try {
				$config = null;
				$file_path = Artise_Path_Plugins . DS . $plugin_name . '/config.xml';
				
				// Only try loading if the plugin comes with a config.
				if(file_exists($file_path))
					$config = \Env\Config\Loader::load($file_path, $errors);
			} catch (\HunterException $e) {
				$devkit->hunter()->error($e);
			}
			
			$this->config = &$config;
			
			
			
			/* Events */
			
			// Create the event handler for this set of plugins.
			$this->event_handler = new \Env\Events\Handler();
			
			// Load the listeners and append them.
			$this->loadListeners();
			
			
			
			/* Misc */
			
			// Store the status
			$this->status = self::LOADED;
		}
		
		protected function &devkit()
		{
			return $this->devkit;
		}
		
		
		private function loadListeners()
		{
			$filename = Artise_Path_Plugins . DS . $this->name . '/listeners.xml';
			
			if(!file_exists($filename))
				return false;
			
			try {
			
				if(!is_readable($filename))
					throw new \HunterException(	new \Env\Source\DummyIdiom('E0051', array('element' => 'listeners')),
												new \Env\Source\DummyIdiom('file_path', array('path' => $filename)),
												'TIP_E0051');
				
				$xml = secure_simplexml_load_file($filename, $errors);
				
				if($xml) {
					$this->parseListeners($xml, $filename);
					return true;
				}
				else {
					throw new \HunterException(	'E0003',
												array('file_path', array('path' => $filename)),
												'TIP_E0031');
				}
			
			} catch (\HunterException $e) {
				$this->devkit()->hunter()->error($e);
			}
			
			return false;
		}
		
		private function parseListeners($xml, $filename)
		{
			/*
			if (!is_object($xml))
				throw new \HunterException('W0001');
			*/
			
			foreach($xml as $listen) {
				
				try {
					// Must listen to an event.
					if (!property_exists($listen, 'event'))
						throw new \HunterException('W0010', '', 'TIP_W0010');
					
					// Must be linked to a Plugin Method.
					if (!property_exists($listen, 'callback'))
						throw new \HunterException('W0011', '', 'TIP_W0011');
					
					// Fetch the event.
					$event = (string)$listen->event->attributes()->value;
					
					// Get the event parts.
					$parts = explode(':', $event);
					
					// Fetch the callback method.
					$callback_method = (string)$listen->callback->attributes()->value;
					
					// Fetch the instance.
					$instance = property_exists($listen, 'instance') ? (string)$listen->instance->attributes()->value : '';
					$plugin_instance = $this->getPluginInstance($instance);
					
					switch($parts[0]) {
						case 'env':
							// Add the listener to the Artise event.
							\Artise::event()->addListener($event, array(&$plugin_instance, $callback_method));
							break;
						
						case 'plugins':
							$plugin_name = $parts[1];
							
							if($this->devkit()->plugins()->manifest($plugin_name)) {
								// Add the listener to the Plugin.
								$this->devkit()->plugins()->$plugin_name()->event()->addListener($parts[2], array(&$plugin_instance, $callback_method));
							} else {
								// Plugin does not exist.
								throw new \HunterException(	new \Env\Source\DummyIdiom('W0013', array('plugin' => $plugin_name)),
															new \Env\Source\DummyIdiom('file_path', array('path' => $filename)),
															'TIP_E0010');
							}
								
							break;
						
						default:
							// The Event field is not build correctly.
							throw new \HunterException(	new \Env\Source\DummyIdiom('W0012', array('expression' => $event)),
														new \Env\Source\DummyIdiom('file_path', array('path' => $filename)),
														'TIP_W0012');
							break;
					}
					
				} catch (\HunterException $e) {
					$this->devkit()->hunter()->warning($e);
				}
				
			} // End Foreach
			
		} // End function
	}
}
?>
