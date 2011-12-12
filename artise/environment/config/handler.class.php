<?php
namespace Env\Config
{
	class Handler
	{
		private
			$xml;
		
		
		/*** User methods ***/
		
		
		/**
		 * Funcion get
		 *
		 * @author Pixelsize Artise team
		 *
		 * @param string $path Ruta de la variable, en el formato "grupo:variable"
		 * @return mixed
		 * @return bool Cuando se asigna nuevo valor, u ocurre un error
		 */
		public function get($path)
		{
			$stretchs = explode(':', $path);
			$tmp = $this->xml; //Local copy

			foreach($stretchs as $s) {
				if(isset($tmp[$s])) {
					$tmp = $tmp[$s];
				}
				else {
					// The config does not contain that value.
					return '';
				}
			}
			
			return $tmp;
		}
		
		
		
		/*** Internal methods ***/
		
		
		public function __construct($xml = false)
		{
			if(!$xml)
				$xml = $this->fallbackConfig();
			
			$this->xml = simplexml_to_array($xml);
		}
		
		private function fallbackConfig()
		{
			return simplexml_load_string('<?xml version="1.0" encoding="UTF-8" ?>
<config>
	<document>
		<version>DOCTYPE_HTML_5</version>
		<lang>en</lang>
		<xmlns>http://www.w3.org/1999/xhtml</xmlns>
	</document>

	<site>
		<charset>utf-8</charset>
		<contentType>text/html</contentType>
		<title>Fallback Config</title>
		<lang>en</lang>
	</site>

	<saveHunterLogs>1</saveHunterLogs>
</config>');
		}
	}
}
?>
