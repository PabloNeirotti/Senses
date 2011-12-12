<?php
namespace Env\Routing
{
	final class Procedure
	{
		private
			$actions = array(),			// array Actions ( string Path )
			$redirects = array(),
			$return_states = array(),	// array Actions Return values ( null | true | false ).
			$on_error,					// string URL
			$on_success,				// string URL
			$status = null;				// Current status of the Procedure.
		
		
		/*** User methods ***/
		
		/**
		 * Get the current status for the whole Procedure.
		 * If one or multiple actions are provided, it will check status only against them.
		 * Inexistent actions will be ignored.
		 * 
		 * @param actions [ string | array (string, ...) ]
		 */
		public function status($actions = null)
		{
			/* Procedure status */
			if($actions == null)
				return $this->status;
			
			if(is_array($actions)) {
				/* Multiple actions status */
				
				$status = true;
				
				foreach($actions as $action) {
					if(isset($this->return_states[$action]))
						$status = $status && $this->return_states[$action];
				}
				
				return $status;
				
			} elseif (is_string($actions)) {
				/* Single action status */
				
				if(isset($this->return_states[$action]))
					return $this->return_states[$action];
				
			} else {
				/* Wrong type provided */
				\Artise::hunter()->warning('W0003', array('DESC_W0003', array(	'arg' => 'actions',
																						'type' => gettype($actions),
																						'allowed_types' => 'string, array'
																						)));
			}
			
			return false;
		}
		
		
		
		
		/*** Private methods ***/
		
		/*
			Rule constructs with the XMLElement obtained from the routing file.
			
			Errors while constructing will be considered as Warnings.
		*/
		public function __construct($xmlElement)
		{
			/* Verifications for valid rule */
			if (!is_object($xmlElement))
				throw new \HunterException('W0002');
			
			// Must have SOMETHING.
			if (!property_exists($xmlElement, 'action'))
				throw new \HunterException('W0002');
			
			/* Store data to the Object */
			
			// Actions.
			foreach($xmlElement->action as $action) {
				$this->actions[] = (string)$action->attributes()->src;
			}
			
			// Redirects.
			foreach($xmlElement->redirect as $redirect) {
				$this->redirects[(string)$redirect->attributes()->state] = \Artise::router()->uri()->format((string)$redirect->attributes()->target);
			}
			
			// On Error and Success
			if (property_exists($xmlElement, '@attributes')) {
				$att = $xmlElement->attributes();
				
				if (property_exists($att, 'on_error'))
					$this->on_error = (string)$att->onerror;
					
				if (property_exists($att, 'on_success'))
					$this->on_success = (string)$att->onsuccess;
			}
		}
		
		
		/**
		 * Runs all actions.
		 */
		public function runActions(&$site, &$devkit)
		{
			$actions_return = array();
			
			if(file_exists($site->router()->getPath(Artise_Path_Actions) . '/commons.func.php'))
				include $site->router()->getPath(Artise_Path_Actions) . '/commons.func.php';
			
			foreach($this->actions as $action_path) {
			
				try {
					$path = \Artise::router()->getPath(Artise_Path_Actions) . DS;
					$action_file_path = $path . DS . $action_path . '.php';
					
					// If the Action file does not exist, skip it.
					if(!file_exists($action_file_path))
						throw new \HunterException(	new \Env\Source\DummyIdiom('E0050', array('element' => 'Action')),
													new \Env\Source\DummyIdiom('file_path_abstract', array(	'path' => $path,
																											'filename' => $action_path)),
													'TIP_E0050',
													'router');
					
					// If the Action file is not readable, skip it.
					if(!is_readable($action_file_path))
						throw new \HunterException(	new \Env\Source\DummyIdiom('E0051', array('element' => 'Action')),
													new \Env\Source\DummyIdiom('file_path_abstract', array(	'path' => $path,
																											'filename' => $action_path)),
													'TIP_E0051');
					
					include $action_file_path;
					
					$parts = explode('/', $action_path);
					$class_name = '';
					if (count($parts) > 1) {
						$class_name = $parts[count($parts) - 1];
						unset($parts[count($parts) - 1]);
						$action_ns = 'actions\\' . implode('\\', $parts);
					} else {
						$class_name = $action_path;
						$action_ns = 'actions';
					}
					
					$ns_path = '\\' . $action_ns . '\\';
					$action_path = $ns_path . $class_name;
					
					// Verify if the class exists.
					if(class_exists($action_path, false)) {
						// Instances the Action.
						$action = new $action_path($site, $devkit);
						
						// Is it an Action?
						if(get_parent_class($action) != 'Ext\Action')
							throw new \HunterException(	new \Env\Source\DummyIdiom('E0060', array(	'element' => 'Action',
																									'name' => $class_name)),
														new \Env\Source\DummyIdiom('namespace_path_abstract', array('path' => $ns_path,
																													'filename' => $class_name)),
														new \Env\Source\DummyIdiom('TIP_E0060_A'),
														'router');
					} else {
						// Skip it. The Action cannot be found.
						throw new \HunterException(	new \Env\Source\DummyIdiom('E0053', array(	'element' => 'Action',
																								'name' => $class_name)),
													new \Env\Source\DummyIdiom('namespace_path_abstract', array('path' => $ns_path,
																												'filename' => $class_name)),
													new \Env\Source\DummyIdiom('TIP_W0053', array('component' => 'action')),
													'router');
					}
					
					// Executes the Action.
					$actions_return[] = $this->return_states[$action_path] = $action->execute();
					
					// If the return of an action is neither true nor false, then it will be null.
					if (is_bool($this->return_states[$action_path]) === false)
						$this->return_states[$action_path] = null;
					else
						$this->updateStatus($this->return_states[$action_path]);
					
				
				} catch (\HunterException $e) {
					// Warn if an Action is skipped.
					\Artise::hunter()->warning($e);
				}
			}
			
			return $actions_return;
		}
		
		/**
		 * Returns where to redirect to, if necessary.
		 */
		public function resultRedirect()
		{
			$status = $this->status();
			
			// Redirect on success.
			if($status === true && $this->on_success)
				return $this->on_success;
			
			// Redirect on error.
			if($status === false && $this->on_error)
				return $this->on_error;
		}
		
		/**
		 * Updates the status of the Procedure.
		 */
		private function updateStatus($new_status)
		{
			if (!is_bool($new_status))
				return;
			
			if($this->status === null)
				$this->status = $new_status;
			else
				$this->status = $this->status && $new_status;
		}
	}
}
?>
