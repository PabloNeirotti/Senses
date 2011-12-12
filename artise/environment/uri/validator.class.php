<?php
namespace Env\Uri
{
	class Validator
	{
		static public function validate($uri, PatternParser &$patternParser, &$tokens)
		{
			$parts = $patternParser->parse();
			
			if($parts !== false) {
				foreach((array)$parts[0] as $regexp) {
					if(self::compare($uri, $regexp, $parts, $tokens)) {
						$tokens = array_filter_keys($tokens, function($key) {
							return !is_numeric($key) || $key === 0; 
						});
						return true;
					}
				}
			}
			return false;
		}

		//En la primer llamada se cargan los datos del offset 0 por ende el proximo en la primer ejecucion es por default 1
		static public function compare(&$uri, $regexp, &$patternParts, &$tokens, $nextoffset = 1)
		{
			if(preg_match('#^' . $regexp . '#', $uri, $tokens)) {
				if(!isset($patternParts[$nextoffset])) {
					return true; //Se supero el limite de patternParts (por ende toda la url coincidio)
				}
			
				if(is_array($patternParts[$nextoffset])) {
					foreach($patternParts[$nextoffset] as $part) {
						if(self::compare($uri, $regexp . $part, $patternParts, $tokens, $nextoffset + 1)) {
							return true;
						}
					}
					return false; //Ninguno de las opciones del array funciono
			
				} else {
					return self::compare($uri, $regexp . $patternParts[$nextoffset], $patternParts, $tokens, $nextoffset + 1);
				}
			} else {
				return false;
			}
		}
	}
}
?>
