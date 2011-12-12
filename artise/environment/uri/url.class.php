<?php
namespace Env\Uri
{
	final class URL
	{
		private $tokens = array();
		public $pattern = '';
		private
			$uri = '',
			$active_lang;
		
		
		
		/*** User methods ***/
				
		public function go()
		{
			header('Location: ' . $this->uri);
			exit;
		}
		
		public function format($pattern)
		{
			/* Si el idioma cargado esta en la url entonces reemplazo los langs pedidos en la uri
			de otra forma, lo reemplazo por nada (o por una barra segun el caso) */
			if(Parser::langExists()) {
				$pattern = str_replace('%lang', \Env\xLang\xLang::get()->active(), $pattern);
			}
			else {
				if(preg_match('#^\/%lang$#', $pattern)) {
					$replace = DS;
				}
				else {
					$replace = '';
				}

				$pattern = str_replace('/%lang', $replace, $pattern);
			}
			
			$pattern = str_replace('%all', $this->pattern, $pattern);

			$pattern = $this->applyTokens($pattern, $this->tokens);
			
			// Add trailing slash if missing.
			if(substr($pattern, -1) != DS)
				$pattern .= DS;
			
			// Remove duplicated slashes, caused by the user.
			$pattern = str_replace('://', ':/\/', $pattern);
			$pattern = str_replace('//', '/', $pattern);
			// Twice.
			$pattern = str_replace('//', '/', $pattern);
			$pattern = str_replace(':/\/', '://', $pattern);
			
			$__class = __CLASS__;
			return new $__class($pattern, $this->tokens);
		}
		
		public function slice($position, $delimiter = '/')
		{
			return $this->uri->slice($position, $delimiter);
		}
		
		
		
		/*** Internal methods ***/
		
		public function __construct($pattern, $tokens = array())
		{
			$this->pattern = $pattern;
			$this->tokens = $tokens;
			$this->uri = new Slicer($this->applyTokens($this->pattern, $this->tokens));
		}
		
		public function __get($name)
		{
			return isset($this->tokens[$name]) ? $this->tokens[$name] : null;
		}
		
		public function __set($name, $value)
		{
			$this->tokens[$name] = $value;
		}
		
		public function __toString()
		{
			return (string)$this->uri;
		}
		
		
		/**
		 * Applies the tokens on the pattern and return the URI.
		 */
		private function applyTokens($pattern, $tokens)
		{
			
			foreach($tokens as $key => $value) {
				$pattern = str_replace('%' . $key, $value, $pattern);
			}

			//Limpio tokens sin usar
			$pattern = preg_replace('#\/\%[a-zA-Z0-9]*#', '', $pattern);
			
			return $pattern;
		}
	}
}
?>
