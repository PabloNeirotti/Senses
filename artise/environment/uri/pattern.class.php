<?php
namespace Env\Uri
{
	class PatternParser
	{
		private $pattern = '';
	
		public function __construct($pattern)
		{
			$this->pattern = $pattern;
		}
	
		/**
		 * Busca el primer caracter no escapado en la cadena dada
		 */
		private function nonSlashedCharPos($str, $char, $offset = 0)
		{
			if(preg_match('#(?<!\\\\)' . quotemeta($char) . '#', substr($str, $offset), $matches, PREG_OFFSET_CAPTURE)) {
				return $matches[0][1];
			}
			return false;
		}

		/**
		  * Parsea el patron de la URI buscando partes de texto simple, y partes opcionales entre parentesis
		  * Se chequea si existe un parentesis abierto y se busca su cierre.
		  * Se obtiene el texto que hay desde el principio de la cadena hasta la apertura de los parentesis
		  * Luego, se obtiene (si existen esos parentesis) su contenido
		  * Ambas cosas se van guardando en orden (Texto primero y luego parentesis) en un array, para mantener el orden de aparicion 
		  * de cada fragmento en el patron
		  * Tambien detecta si el parentesis es XOR o OR simple y lo guarda en una clave especial del array en donde van a estar las opciones del parentesis
		  * si el parentesis tiene (/admin|/pepe), /admin es opcion y /pepe es otra
		  * La p es de Parenthesis
		  */
		public function parse()
		{
			$offset = 0;
			$parts = array();
			$patternLen = strlen($this->pattern);
		
			while(true) {
				$openedParenthesis = $this->nonSlashedCharPos($this->pattern, '(', $offset);
				$closedParenthesis = $this->nonSlashedCharPos($this->pattern, ')', $offset);
				$readp = true;
			
				if($openedParenthesis === false && $closedParenthesis === false) {
					$openedParenthesis = $patternLen;
					$closedParenthesis = $patternLen;
					$readp = false;
				}
				elseif($openedParenthesis !== false && $closedParenthesis === false || $openedParenthesis === false && $closedParenthesis !== false) {
					return false; //Error de formato falta cerrar un parentesis
				}
			
				$text = substr($this->pattern, $offset, $openedParenthesis - $offset);
				if(!empty($text)) {
					$parts[] = $this->meta2regexp($text);
				}
			
				if($readp) {
					++$openedParenthesis; //salteo el parentesis
					$contentp = substr($this->pattern, $openedParenthesis, $closedParenthesis - $openedParenthesis);
				
					$xor = false;
					if(substr($this->pattern, ++$closedParenthesis, 1) === '+')
						$xor = true;
					else --$closedParenthesis;
				
					$divided = preg_split('#(?<!\\\\)\\|#', $contentp);
					foreach($divided as &$value) $value = $this->meta2regexp($value);
					if(!$xor) $divided[] = '';
					$parts[] = $divided;
				}
			
				$offset = $closedParenthesis + 1; //Luego empiezo desde el cierre del grupo
			
				if($offset >= $patternLen) {
					return $parts;
				}
			}
		}
	
		/**
		 * Convierte todos los tokens en formato %token a una expresion regular valida de captura
		 */
		private function meta2regexp($str)
		{
			$str = preg_replace('#(?<!\\\\)%([a-zA-Z_]+)#', '(?<$1>.+?)', $str);
			return $str;
		}
	}
}
?>
