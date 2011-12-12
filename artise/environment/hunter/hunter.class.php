<?php
namespace Env\Hunter
{
	final class Hunter
	{
		static private
			$lh,
			$instance;
		
		/*
			Hunter constructs itself with a Referential intance of a Hunter Logger.
		*/
		public function __construct(\Env\Hunter\Logger &$lh)
		{
			static $called = false;
			if($called) return; $called = true;
			
			self::$lh = &$lh;
			self::$instance = &$this;
		}
		
		/**
		 * Agrega un item al log
		 *
		 * @param integer $errno Constante de error (H_WARNING|H_ERROR|H_FLAG)
		 * @param string $errstr [String | array(Mensaje, Descripcion, Sugerencia)]
		 * @return bool false
		 */
		public function warning($title, $desc = null, $tip = null)
		{
			// If a Hunter Exception is passed, fetch it's values.
			if($title instanceof \HunterException) {
				$he = $title;
				$title = $he->getTitle();
				$desc = $he->getDescription();
				$tip = $he->getTip();
				$component = $he->getComponent();
			} else {
				$component = null;
			}
			
			$trace = $this->processTrace(debug_backtrace(true));
			self::$lh->addArtise($trace, Warning, $title, $desc, $tip, $component);
			return false;
		}

		public function error($title, $desc = null, $tip = null)
		{
			// If a Hunter Exception is passed, fetch it's values.
			if($title instanceof \HunterException) {
				$he = $title;
				$title = $he->getTitle();
				$desc = $he->getDescription();
				$tip = $he->getTip();
				$component = $he->getComponent();
			} else {
				$component = null;
			}
			
			$trace = $this->processTrace(debug_backtrace(true));
			self::$lh->addArtise($trace, Error, $title, $desc, $tip, $component);
			return false;
		}

		public function flag($title, $desc = null)
		{
			// If a Hunter Exception is passed, fetch it's values.
			if($title instanceof \HunterException) {
				$he = $title;
				$title = $he->getTitle();
				$desc = $he->getDescription();
				$tip = $he->getTip();
			}
			
			$trace = $this->processTrace(debug_backtrace(true));
			self::$lh->addArtise($trace, Flag, $title, $desc);
			return false;
		}
		
		public function enabled($status = null)
		{
			if($status !== null)
				self::$lh->enabled = (bool)$status;
			else
				return self::$lh->enabled;
		}
		
		
		private function processTrace($trace)
		{
			// False until the array_walk finds a step containing one of the four code components.
			$reached_component = false;
			
			// Enables one extra loop after finding the component.
			$next_step = false;
			
			
//			die('<pre>' . print_r($trace, true) . '</pre>');
			// Define the possible components this function can match.
			$components_ns = array('renders', 'actions', 'plugins');
			$components_ext = array('Layout');
			
			array_walk($trace, function(&$step) use (&$reached_component, &$next_step, $components_ns, $components_ext) {
				// If we already found the component, there is no need of keeping the rest of the upcoming steps.
				if($reached_component && !$next_step) {
					$step = array();
					return false;
				}
				
				// Remove arguments.
				unset($step['args']);
				
				if(isset($step['object'])) {
					$object_class = get_class($step['object']);
					switch($object_class) {
						case 'Env\Rendering\Painter':
							$object_class = 'painters\\' . $step['object']->path;
							$step['function'] = $step['object']->method;
							break;
						case 'Ext\Rendering\Layout':
							$step['graphic'] = $step['object']->filename;
							break;
					}
					
					// Replace the object with just it's class name.
					$step['object'] = $object_class;
					
					
					// Check if we have finally reached a component.
					$sliced_object = explode('\\', $step['object']);
					if(in_array($sliced_object[0], $components_ns))
						$reached_component = true;
					
					$sliced_object = explode('\\', $step['object']);
					if(isset($sliced_object[2])) {
						if(in_array($sliced_object[2], $components_ext))
							$reached_component = true;
					}
					$next_step = true;
				}
				
			});
			
			//die(print_r($trace, true));
			
			return $trace;
		}
	}
}
?>
