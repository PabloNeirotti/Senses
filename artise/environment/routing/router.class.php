<?php
namespace Env\Routing
{
	final class Router
	{
		private
			$config,
			$devkit,
			$rules,
			$uri,
			$procedure,
			$storage,
			$request,
			$plugin_id;
		
		public
			$current_lang,	// Current language.
			$render;
		
		
		/*** User methods ***/
		
		public function &procedure()
		{
			return $this->procedure;
		}
		
		public function &request()
		{
			return $this->request;
		}
		
		public function &storage()
		{
			return $this->storage;
		}
		
		public function &uri($pattern = false, $tokens = false)
		{
			if($pattern !== false) {
				if(is_array($tokens))
					$url = new \Env\Uri\URL($pattern, $tokens);
				else
					$url = $this->uri->format($pattern);
				return $url;
			} else {
				return $this->uri;
			}
		}
		
		
		
		
		/*** Internal methods ***/
		
		
		/*
			Router contructs itself by loading the routing file.
		*/
		public function __construct(&$config, &$devkit, $filename, $plugin_id = false)
		{
			// Stores the config.
			$this->config = &$config;
			
			// Stores the development kit.
			$this->devkit = &$devkit;
			
			// Stores the Plugin id if this is a Plugin Router.
			$this->plugin_id = $plugin_id;
			
			// Instance the Request object.
			$this->request = new Request();
			
			if(is_readable($filename)) {
				$xml = secure_simplexml_load_file($filename, $errors);
				
				if($xml) {
					// Trigger event.
					\Artise::event()->trigger('env:router:loadXML', array(&$xml));
					
					// Parse XML and get the rules.
					$this->rules = $this->parseRouting($xml);
				}
				else {
					die('E0039');
					throw new \HunterException('E0039');
				}
			}
			else {
				die('E0040');
				throw new \HunterException('E0040');
			}
		}
		
		/*
			Parses routing.xml
		*/
		private function parseRouting($rules_xml)
		{
			// Return false if there are no rules.
			if(count($rules_xml) == 0)
				return false;
			
			// Define the Rules array.
			$rules = array();
			
			
			// Obtain an instance of each Rule in the file.
			foreach($rules_xml as $rule) {
				try {
					$rules[] = new Rule($rule);
				} catch (\HunterException $e) {
					$this->devkit->hunter->addError($e);
				}
			}
			
			// Return the site rules.
			return $rules;
		}
		
		private function filterRequest()
		{
			$request = &$this->request;
			
			return array_filter($this->rules, function($rule) use (&$request) {
				if(isset($rule->request)) {
					
					// Checks for AJAX.
					if($rule->request == 'ajax' && $request->get() == AJAX_Request)
						return true;
					
					// Checks for Standard.
					if($rule->request == 'std' && $request->get() == Standard_Request)
						return true;
					
					return false;
				}
				
				// If there is no value loaded on request, we keep this entry if the Router Request is the default.
				return $request->get() === Routing_Request_Default;
			});
		}
		
		private function filterUri($uri, &$tokens)
		{
			return array_filter($this->rules, function(&$rule) use ($uri, &$tokens) {
				if(isset($rule->url)) {
					foreach($rule->url as $pattern) {
						if(\Env\Uri\Validator::validate($uri, new \Env\Uri\PatternParser($pattern), $tokens)) {
							// This rule has an URL matching with the Request URI. Keep it.
							// Also define which is the matching one.
							$rule->matchURL($uri);
							return true;
						}
					}
					
					// No URL in the Rule matched with the URI.
					// Remove this Rule from the array.
					return false;
				}
				
				return false;
			});
		}
		
		/**
		 * Gets a Path relative to the Site Root or Plugin, depending wether it's a Site or Plugin Router.
		 */
		public function getPath($path)
		{
			if (!$this->plugin_id)
				return $path;
			
			$base_path = Artise_Path_Plugins . DS . $this->plugin_id;
			switch($path) {
				case Artise_Path_Renders:
					return $base_path . '/code/renders';
				case Artise_Path_Actions:
					return $base_path . '/code/actions';
				case Artise_Path_Painters:
					return $base_path . '/code/painters';
				case Artise_Path_Layouts:
					return $base_path . '/design/layouts';
				case Artise_Path_Palettes:
					return $base_path . '/design/palettes';
				case Artise_Path_Langs:
					return $base_path . '/langs';
			}
		}
		
		public function route($uri, &$lang)
		{
			// If lang is empty, create it.
			// It will come empty the first time Route is called.
			// If it's called a second time, then we are on a Plugin Router now, and the Lang has been picked before.
			if(!is_object($lang)) {
			
				// Create a Site Lang.
				if (!$this->plugin_id)
					$lang = new \Env\xLang\xLang();
				
				// Parses URI to obtain Lang, and redirects if default lang is used.
				$uri = \Env\Uri\Parser::Parse($this->config, $lang, $uri);
			
			}
			
			//[REMOVE IT] FIXED FROM HTACCESS
			// Redirect to add the ending slash.
			//if (substr($uri, -1) != '/') {
			//	header("HTTP/1.1 301 Moved Permanently");
			//	header("Location: $uri/");
			//}
			
			// Filter by request.
			$this->rules = $this->filterRequest();
			
			// Filter by URI.
			$tokens = array();
			$rules = $this->filterUri($uri, $tokens);
			
			if(count($rules) === 0) {
				// WTF?
				die('No rules matched');
			} else {
				$selected_rule = false;
				
				// Pick the longest one, which will be the most accurate match.
				foreach($rules as $rule) {
					if ($selected_rule === false) {
						$selected_rule = $rule;
					} else {
						if (substr_count($rule->matched_url, '/') > substr_count($selected_rule->matched_url, '/'))
							$selected_rule = $rule;
					}
				}
				
				
				// Call the Plugin Router if this rule is calling one.
				// Second check is to prevent a Plugin Router to call another Plugin Router.
				if ($selected_rule->plugin && !$this->plugin_id) {
					/* Calling a Plugin Router */
					
					// Get the Plugin Router.
					$plugin_router = new Router($this->config, $this->devkit, Artise_Path_Plugins . DS . $selected_rule->plugin . DS . 'code' . DS . 'routing.xml', $selected_rule->plugin);
					
					// Define the plugin's URL to parse...
					// ... by removing the part matched by the Rule.
					$plugin_url = substr($uri, strlen($selected_rule->matched_url) - 1);
					
					// Create a Plugin Lang.
					//$selected_lang = $lang->active();
					$lang = new \Env\xLang\xLang($selected_rule->plugin);
					$uri = \Env\Uri\Parser::Parse($this->config, $lang, $uri);
					//$lang->set($selected_lang);
					
					// Tell the Plugin Router to route it's URL.
					$plugin_router->route($plugin_url, $lang);
					
					return $plugin_router;
				}
				
				// If it's not a perfect match, permanently redirect to it.
				if ((string)$selected_rule->matched_url != $uri) {
					// die('Fallback! Redirecting to: ' . $selected_rule->matched_url);
					// [WARNING] Temporarily disabled Permantently ;)
					//header("HTTP/1.1 301 Moved Permanently");
					header("Location: " . $selected_rule->matched_url);
				}
				
				
				// Set the current Render.
				$this->render = $selected_rule->render;
				
				// Set the Request object.
				$this->request = new \Env\Routing\Request();
				
				// Set the URI object.
				$this->uri = $selected_rule->matched_url;
				
				// Define the active Procedure, if any.
				$uri_procedure = $this->uri->procedure ? $this->uri->procedure : '';
				
				if(isset($selected_rule->procedures[$uri_procedure]))
					$this->procedure = $selected_rule->procedures[$uri_procedure];
				
				// Set the Storage.
				$this->storage = new \Env\Routing\Storage($this->uri);
			}
			
			return true;
			
		}
		
		/**
		 * Instances and Executes the Render, if possible and if any.
		 *
		 * @return The Page's HTML.
		 */
		public function render(&$site, &$devkit)
		{
			try {
				
				/* Setting up */
			
				// Include the Render commons.
				if(file_exists($this->getPath(Artise_Path_Renders) . '/commons.func.php'))
					include $this->getPath(Artise_Path_Renders) . '/commons.func.php';
				
				$path = $this->getPath(Artise_Path_Renders) . DS;
				$filename = $path . DS . $this->render . '.php';
				
				
				
				/* File Verifications */
				
				
				// Check if the file exists.
				if(!file_exists($filename))
					throw new \HunterException(	new \Env\Source\DummyIdiom('E0050', array('element' => 'Render')),
												new \Env\Source\DummyIdiom('file_path_abstract', array(	'path' => $path,
																										'filename' => $this->render)),
												'TIP_E0050',
												'router');
				
				// Check if the file is readable.
				if(!is_readable($filename))
					throw new \HunterException(	new \Env\Source\DummyIdiom('E0051', array('element' => 'Render')),
												new \Env\Source\DummyIdiom('file_path_abstract', array(	'path' => $path,
																										'filename' => $this->render)),
												'TIP_E0051');
				
				
				/* Including */
				
				// Includes the corresponding Render.
				include $filename;
				
				
				
				/* Namespace + Class Verifications */
				
				$parts = explode('/', $this->render);
				$class_name = '';
				if (count($parts) > 1) {
					$class_name = $parts[count($parts) - 1];
					unset($parts[count($parts) - 1]);
					$render_ns = 'renders\\' . implode('\\', $parts);
				} else {
					$class_name = $this->render;
					$render_ns = 'renders';
				}
				
				$ns_path = '\\' . $render_ns . '\\';
				$render_path = $ns_path . $class_name;
				
				
				// Verify if the class exists.
				if(class_exists($render_path, false)) {
					// Instances the Render.
					$render = new $render_path($site, $devkit);
					
					// Is it an Render?
					$render_parent_classes = array('Ext\Rendering\Page', 'Ext\Rendering\Layout', 'Ext\Rendering\Painter');
					if(!in_array(get_parent_class($render), $render_parent_classes))
						throw new \HunterException(	new \Env\Source\DummyIdiom('E0060', array(	'element' => 'Render',
																								'name' => $class_name)),
													new \Env\Source\DummyIdiom('namespace_path_abstract', array('path' => $ns_path,
																												'filename' => $class_name)),
													new \Env\Source\DummyIdiom('TIP_E0060_A'),
													'router');
				} else {
					// Exiting. The Render cannot be found.
					throw new \HunterException(	new \Env\Source\DummyIdiom('E0053', array(	'element' => 'Render',
																							'name' => $class_name)),
												new \Env\Source\DummyIdiom('namespace_path_abstract', array('path' => $ns_path,
																											'filename' => $class_name)),
												new \Env\Source\DummyIdiom('TIP_W0053', array('component' => 'render')),
												'router');
				}
				
				
				
				/* Execution */
				
				// Execute the Render and return the HTML.
				return (string)$render;
				
			} catch (\HunterException $e) {
				// Error if the Render cannot be used.
				\Artise::hunter()->warning($e);
			}
		}
	}
}
?>
