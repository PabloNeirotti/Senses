<?php
namespace Plugins\hunter;

class Main extends \Ext\Plugin
{
	function getLatestLog()
	{
		$logs = scandir(Artise_Path_Hunter_Logs, 1);
		foreach($logs as $entry) {
			if(strlen($entry) >= 10)
				break;
		}
		
		// Exit if no entry was found.
		if(strlen($entry) < 10)
			return false;
		
		// Get the log.
		$log = unserialize(file_get_contents(Artise_Path_Hunter_Logs . DS . $entry));
		
		// Define the final log.
		$final_log = new \Readers\Table();
		
		// Define error numbers.
		$errno = array(2 => 'flag', 4 => 'warning', 8 => 'error');
		
		//die(print_r($log, true));
		
		// Rebuild the log in a simplified fashion.
		foreach($log['artise'] as $entry) {
			$final_entry = array();
			
			$final_entry['type'] = $errno[$entry['errno']];
			$final_entry['title'] = $entry['errtit'];
			$final_entry['desc'] = $entry['errdesc'];
			$final_entry['tip'] = $entry['errtip'];
			$find_component = isset($entry['component']) ? $entry['component'] : null;
			
			$log_object = $this->getComponent($entry['trace'], $find_component);
			
			// Mix all the data together.
			$final_entry = array_merge($final_entry, $log_object);
			
			// Append to the final log this final entry.
			$final_log->append($final_entry);
		}
		
		return $final_log;
	}
	
	/**
	 * Returns the component responsable of this log entry.
	 *
	 * @param trace
	 */
	private function getComponent($trace, $find_component = null)
	{
		// Define the Result Array.
		$result = array();
		
		// Define the possible components this function can match.
		$components = array('renders', 'actions', 'painters', 'plugins');
		
		// Define methods that are called but hiding identity.
		$private_called_methods = array('__toString');
		
		// Define previous step.
		$previous_step = false;
		
		if(false) {
			echo '<pre>';
			var_dump($trace);
			echo '</pre>';
			exit;
		}
		
		// Fetch Array find_component data.
		if(is_array($find_component)) {
			$find_component_id = $find_component[1];
			$find_component = $find_component[0];
		} else {
			$find_component_id = null;
		}
		
		// Straight forward find_component.
		if($find_component == 'lang' || $find_component == 'router') {
			$result['component'] = ucfirst($find_component);
			$result['name'] = $find_component_id;
			
			return $result;
		}
		
		// Used to perform one extra loop.
		$keep_alive = false;
		
		// Go through each step of the trace.
		foreach($trace as $key => $step) {
			
			// Find the step containing an Artise Extended class.
			if($previous_step && strpos($step['object'], '\\')) {
				
				if(isset($trace[$key + 1])) {
					$next_step = $trace[$key + 1];
					$next_class = explode('\\', $next_step['object']);
				} else {
					$next_step = false;
					$next_class = array();
				}
				
				// Is this a Palette?
				if($step['object'] == 'Ext\Rendering\Painter\Palette')
					$result['component'] = 'palette';
				
				// Is this a Layout?
				if($step['object'] == 'Ext\Rendering\Layout')
					$result['component'] = 'layout';
				
				$class = explode('\\', $step['object']);

				// Define the Ext class.
				if($class[0] == 'Ext') {
					$ext_class = strtolower($class[2]);
				} else {
					$ext_class = '';
				}
				
				// Is this a detectable component?
				// Keep going if there is a Layout waiting.
				if ((in_array($class[0], $components) && !$find_component) ||
					($ext_class == $find_component && $find_component)) {
					
					/* Component found: Now retrieve data relative to it's location */
					
					
					if($class[0] == 'plugins') {
						// We are only executing this if this is a standalone Plugin execution.
						
						if(!in_array($next_class[0], $components)) {
							/* Stand alone call */
							$result['component'] = substr($class[0], -1) == 's' ? substr($class[0], 0, -1) : $class[0];
							$result['name'] = array_slice($class, 1);
							$result['name'] = implode('/', $result['name']);
							
							$result['method'] = $step['function'];
							$result['line'] = $step['line'];
							
							if($next_step) {
								
								$next_class = explode('\\', $next_step['object']);
								
								// Show only functions that weren't called by the user. 
								if(!in_array($next_step['function'], $private_called_methods)) {
									$result['call'] = $next_step['object'] . '::' . $next_step['function'];
								} else {
									// This function will be hidden, so the code line too.
									$result['line'] = 0;
								}
							}
							
							// Quit the foreach. We found what we were looking for.
							break;
						} else {
							/* Called by a component. Perform an extra loop */
							$keep_alive = true;
						}
					}
					
					switch($find_component) {
						case 'layout':
							$result['component'] = ucfirst($find_component);
							$result['name'] = $step['graphic'];
							break;
							
						default:
							$result['component'] = substr($class[0], -1) == 's' ? substr($class[0], 0, -1) : $class[0];
							
							$result['name'] = array_slice($class, 1);
							$result['name'] = implode('/', $result['name']);
							
							//echo "[" . $step['function'] . '-' . $previous_step['line'] . ']';
							
							$result['method'] = $step['function'];
							$result['line'] = $previous_step['line'];
							
							
							// If thre previous step was Hunter, it means the Component logged directly to Hunter.
							// Otherwise, the function the Component called, logged to Hunter.
							if ($previous_step['object'] == 'Env\Hunter\Hunter') {
								$result['call'] = '';
							} else {
								$previous_class = explode('\\', $previous_step['object']);
								
								// Show only functions that weren't called by the user. 
								if(!in_array($previous_step['function'], $private_called_methods)) {
									
									if($previous_class[0] == 'plugins' && strtolower($previous_class[2]) == 'main' && count($previous_class <= 3)) {
										/* This is the Plugin Main class */
										$result['call'] = $previous_class[1] . '::' . $previous_step['function'];
									} else {
										/* Other */
										$result['call'] = $previous_class[count($previous_class) - 1] . '::' . $previous_step['function'];
									}
									
								} else {
									// This function will be hidden, so the code line too.
									$result['line'] = 0;
								}
							}
							break;
					
					}
					
					// Quit the foreach once we found what we were looking for.
					if(!$keep_alive)
						break;
				}
			}
			
			// Set the previous step.
			$previous_step = $step;
		}
		
		return $result;
	}
}
?>