<?php
namespace Ext
{
	
	class Plugin
	{
		protected
			$name,
			$instance_id = null,
			$lang,
			$event_handler = null,
			
			$config,
			$devkit;
		
		final public function __construct($instance_id = false, $plugin_name = false, &$config = false, &$devkit = false, &$event_handler = false)
		{
			
			try {
			
				// Allow this class to be used only at the \plugins\ namespace, and Main classes.
				
				$this_class = explode('\\', get_class($this));
				if(count($this_class) != 3)
					throw new \HunterException('E0018', null, 'TIP_E0018', 'plugin');
				
				if(strtolower($this_class[0]) != 'plugins' || strtolower($this_class[2]) != 'main')
					throw new \HunterException('E0018', null, 'TIP_E0018', 'plugin');
				
				
				
				/* Set up */
				
				$this->config = &$config;
				$this->devkit = &$devkit;
				$this->event_handler = &$event_handler;
				
				$this->instance_id = $instance_id;
				$this->name = $plugin_name;
				
				if(method_exists($this, '__initialize'))
					$this->__initialize();
				
				$this->lang = new \Env\xLang\xLang($plugin_name);
			
			} catch(\HunterException $e) {
				
				\Artise::hunter()->error($e);
				
			}
		}
		
		protected function &devkit()
		{
			return $this->devkit;
		}
		
		protected function &config()
		{
			return $this->config;
		}
		
		protected function &lang()
		{
			return $this->lang;
		}
		
		public function &event()
		{
			return $this->event_handler;
		}
	}
}
?>
