<?php
namespace Env\Plugins
{
	class Loader
	{
		const LOADED = 2;
		const MAIN_FILE_INACCESIBLE = 4;
		
		private
			$devkit,
			$manifest = array(),
			$plugins = array(),
			$failed_manifest = array();
		
		
		
		
		/*** User functions ***/
		
		
		/**
		 * Retrieves an instance of a Plugin.
		 */
		public function &__call($plugin_name, $instance_id)
		{
			$instance_id = (isset($instance_id[0]) ? $instance_id[0] : '');
			
			// Does the Plugin exist?
			if (in_array($plugin_name, $this->manifest)) {
				// Return the Plugin instance.
				return $this->plugins[$plugin_name]->getPluginInstance($instance_id);
			} else {
				
				if (in_array($plugin_name, $this->failed_manifest)) {
					// The plugin exists, but failed to load.
					$this->devkit()->hunter()->error(	new \Env\Source\DummyIdiom('E0045', array('element' => 'Plugin', 'name' => $plugin_name)),
													'DESC_E0045',
													'TIP_E0045');
				} else {
					// Requested Plugin does not exist.
					$this->devkit()->hunter()->error(	new \Env\Source\DummyIdiom('E0010', array('plugin' => $plugin_name))
											);
				}
			}
			
			$empty_object = new \Env\Source\EmptyObject('plugins()->' . $plugin_name . "('$instance_id')");
			return $empty_object;
		}
		
		/**
		 * Devuelve la lista de plugins, o si un plugin fue cargado
		 *
		 * @author Pixelsize Artise team
		 * @param string $name Nombre del plugin
		 * @return [(if $name = NULL) ? \Readers\List : bool]
		 */
		public function manifest($plugin_name = null)
		{
			if($plugin_name) {
				return in_array($plugin_name, $this->manifest, true);
			}
			else {
				return \Readers\Listing($this->manifest);
			}
		}
		
		
		
		
		
		/*** Internal functions ***/
		
		
		public function __construct(&$devkit)
		{
			$this->devkit = &$devkit;
			$this->devkit()->setPlugins($this);
		}
		
		protected function &devkit()
		{
			return $this->devkit;
		}
		
		
		/**
		 * Loader
		 *
		 * @author Pixelsize Artise team
		 * @return void
		 */
		public function load($plugin_name)
		{
			$plugin_set = new PluginSet($this->devkit(), $plugin_name);
			
			if ($plugin_set->status == self::LOADED) {
				$this->manifest[] = &$plugin_name;
				$this->plugins[$plugin_name] = $plugin_set;
			} else {
				// Add it to the failed manifest.
				$this->failed_manifest[] = $plugin_name;
			}
			
			return $plugin_set->status;
		}
	}
}
?>
