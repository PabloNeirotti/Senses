<?php
namespace Ext\Rendering
{
	class Painter extends \Ext\Source\SiteScope
	{
		private
			$painter = false;

		final public function __toString()
		{
			$this->render();
			return (string)$this->painter;
		}

		final public function set($token)
		{
			$this->painter = new \Env\Rendering\Painter($token, $this->site, $this->devkit());
		}
	}
}
?>
