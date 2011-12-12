<?php

namespace Env;

/*
	Devkit
	
	Contains development access to Hunter or Plugins.
*/

final class Devkit
{
	private
		$hunter,
		$plugins = false;
	
	/*** Public methods ***/
	
	public function &hunter() {
		return $this->hunter;
	}
	
	public function &plugins() {
		return $this->plugins;
	}
	
	
	/*** Private methods ***/
	
	public function setPlugins(&$plugins)
	{
		if ($this->plugins === false)
			$this->plugins = &$plugins;
	}
	
	public function __construct(&$hunter)
	{
		$this->hunter = &$hunter;
	}
}

?>