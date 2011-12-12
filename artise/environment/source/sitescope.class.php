<?php
namespace Ext\Source
{
	class SiteScope
	{
		protected
			$site,
			$devkit;

		public function __construct(&$site, &$devkit)
		{
			$this->site = &$site;
			$this->devkit = &$devkit;
		}
		
		public function &site()
		{
			return $this->site;
		}
		
		public function &devkit()
		{
			return $this->devkit;
		}
	}
}
?>