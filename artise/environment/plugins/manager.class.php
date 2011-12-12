<?php
namespace Env\Plugins
{
	final class Manager
	{
		static private $instances = array();
		
		/**
		 * Devuelve la instancia de un plugin
		 *
		 * @author Pixelsize Artise team
		 * @param string $name Nombre del plugin
		 * @param string $instance Nombre de la instancia
		 * @return Plugin Instance
		 */
		static public function &get($name, $instance = 'default')
		{
			if(!isset(self::$instances[$name])) {
				self::$instances[$name] = array();
			}
			
			if(isset(self::$instances[$name][$instance])) {
				return self::$instances[$name][$instance] = new Prototype($name, $instance);
			}
			else {
				return self::$instances[$name][$instance];
			}
		}
	}
}
?>