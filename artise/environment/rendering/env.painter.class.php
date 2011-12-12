<?php
namespace Env\Rendering
{
	class Painter
	{
		private static $instances = array(/* [path -Sin metodo ni argumentos-] => [obj] */);

		/* eg. <!--painter:path:method[argv]--> */
		public $path = '';
		public $method = '';
		private $argv = array();
		private $isCallable = false;
		private $isLoaded = false;

		final public function __construct($value, &$site, &$devkit)
		{
			
			if(preg_match('#\[(.*)\]#', $value, $args)) {
				$args = $args[1]; //String
				$this->argv = explode(':', $args); //Array
			}

			$clearvalue = preg_replace('#\[.*\]#', '', $value); //Borro los args
			list($this->path, $this->method) = explode(':', $clearvalue);

			if(!isset(self::$instances[$this->path])) {
				$filename = $site->router()->getPath(Artise_Path_Painters) . DS . $this->path . '.php';
				
				if(is_readable($filename)) {
					include $filename;

					$lastClass = get_declared_classes();
					$lastClass = end($lastClass);
					self::$instances[$this->path] = new $lastClass($site, $devkit);
					
					$this->isLoaded = true;
				}
				else {
					\Artise::hunter()->warning('E0019', $this->path);
				}
			}
			else {
				$this->isLoaded = true;
			}

			if($this->isLoaded) {

				$refObj = new \ReflectionObject(self::$instances[$this->path]);
				
				if($refObj->hasMethod($this->method)) {
					$refMethod = $refObj->getMethod($this->method);

					if($refMethod->isPublic()) {
						$this->isCallable = true;
					}
					else {
						\Artise::hunter()->warning('E0023', $value);
					}
				}
				else {
					\Artise::hunter()->warning(new \HunterException('E0024', $value, null, 'layout'));
				}
			}
		}

		final public function __toString()
		{
			if($this->isCallable) {
				return (string)call_user_func_array(array(self::$instances[$this->path], $this->method), $this->argv);
			}
			else {
				return '';
			}
		}
	
		final public function __clone()
		{
			trigger_error('Painter cannot be clonned', E_USER_ERROR);
		}
	}
}
?>
