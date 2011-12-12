<?php
namespace Env\Source
{
	class DummyIdiom
	{
		public
			$phrase,
			$vars;
		
		public function __construct($phrase, $vars = array())
		{
			$this->phrase = $phrase;
			$this->vars = $vars;
		}
	}
}
?>