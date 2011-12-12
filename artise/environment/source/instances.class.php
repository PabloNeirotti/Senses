<?php
namespace Ext\Source
{
	class Instances
	{
		protected
			$instances_class_path,
			$instances = array();

		public function &getInstance($name, array $args = array())
		{
			try {
				// Create instance if not created yet.
				if (!isset($this->instances[$name])) {
					
					// Obtain a ReflectionClass of the class we are about to instance.
					$reflection  = new \ReflectionClass($this->instances_class_path);
					
					// Define the instance arguments.
					$args = array_merge(array($name), $args);
					
					// Create the instance.
					$this->instances[$name] = $reflection->newInstanceArgs($args);
				}
				
				return $this->instances[$name];
				
			} catch (\HunterException $e) {
				/* Failed to instance */
				
				throw $e;
			}
		}
	}
}
?>