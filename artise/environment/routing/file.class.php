<?php
namespace Env\Routing
{
	
	final class File
	{
		private
			$rules = array();
			
		public function __construct($filename)
		{
			if(is_readable($filename)) {
				$xml = secure_simplexml_load_file($filename, $errors);
				
				if($xml) {
					$this->xml = $xml;
					$this->parse();
				}
				else {
					throw new \HunterException('E0039');
				}
			}
			else {
				throw new \HunterException('E0040');
			}
		}
		
		private function parse()
		{
			
		}
		
		public function hasRules()
		{
			return (count($this->rules) > 0);
		}
		
		public function getRules()
		{
			if($this->hasRules()) {
				return $this->rules;
			}
			return array();
		}
	}
}
?>
