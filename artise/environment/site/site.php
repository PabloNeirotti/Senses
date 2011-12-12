<?php
namespace Env
{
	final class Site
	{
		private $config,
				$lang,
				$router;
		
		public function &config()
		{
			return $this->config;
		}
		
		public function &lang()
		{
			return $this->lang;
		}

		public function &router()
		{
			return $this->router;
		}
		
		public function __construct(&$config, &$lang, &$router)
		{
			$this->config = &$config;
			$this->lang = &$lang;
			$this->router = &$router;
		}
	}
}
?>