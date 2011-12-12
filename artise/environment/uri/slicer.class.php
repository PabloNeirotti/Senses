<?php
namespace Env\Uri
{
	final class Slicer
	{
		private
			$string;
			
			
			
		
		/*** User methods ***/
		
		
		public function slice($position, $delimiter = "/")
		{
			$string = $this->string;
			
			// If the string is a containing type, like: '/duck/white/' then it's transformed to 'duck/white'.
			if(substr($string, 0, 1) == substr($string, -1) && substr($string, 0, 1) == $delimiter)
				$string = substr($string, 1, -1);
			
			// Get the parts
			$array = explode($delimiter, $string);
			
			if(is_array($array)) {
				if(isset($array[$position]))
					return new Slicer($array[$position]);
			}
			
			return null;
		}
		
		
		
		/*** Internal methods ***/
		
		
		public function __construct($string)
		{
			$this->string = $string;
		}
		
		public function __toString()
		{
			return $this->string;
		}
	}
}