<?php
namespace Env\Rendering\Page
{
	final class Body extends \Ext\Rendering\Page\Tag
	{
		private
			$layout = false,		// The Layout instance.
			$site,
			$devkit,
			$attributes = array();	// HTML tag attributes.
		
		/*** User methods ***/
		
		
		
		/**
		 * Obtains the Layout instance for the Body.
		 *
		 * @param path - Simple path of the Layout.
		 * @return Referential instance of the Tag.
		 */
		final public function &layout($path = false) {
			// Create Layout if it's not created yet.
			if ($this->layout === false)
				$this->layout = new \Ext\Rendering\Layout($this->site, $this->devkit());
			
			// Set the layout simplified path.
			if ($path !== false)
				$this->layout->set($path);
			
			return $this->layout;
		}
		
		
		/*** Internal methods ***/
		
		public function __construct(&$site, &$devkit)
		{
			$this->site = &$site;
			$this->devkit = &$devkit;
			
			// Set itself.
			$this->name = 'body';
			$this->selfClosing(false);
		}
		
		protected function &site()
		{
			return $this->site;
		}
		
		protected function &devkit()
		{
			return $this->devkit;
		}
		
		public function __get($name)
		{
			return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
		}
		
		public function __set($name, $value)
		{
			$this->attributes[$name] = $value;
		}
		
		public function __toString()
		{
			return (string)$this->layout();
		}
		
		
	}
}
?>
