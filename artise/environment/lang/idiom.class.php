<?php
namespace Env\xLang;

class Idiom
{
	private
		$filename = '',
		$source = array(),
		$parseable = true,
		$id = null;




	/*** User methods ***/

	/**
	 *
	 * @param string $phrase
	 * @param array $vars
	 * @return string
	 */
	public function phrase($phrase, $vars = false) {
		// If the first argument is an array, get all arguments from there.
		if(is_array($phrase))
			return $this->phrase($phrase[0], $phrase[1]);
		
		// If no variables were provided, turn the variable into an empty array.
		if (!$vars)
			$vars = array();
		
		if(!$this->parseable) {
			return $phrase;
		}
	
		// Check if the phrase exists.
		if(!isset($this->source['phrases'][$phrase])) {
			return $phrase;
		}

		if(is_array($this->source['phrases'][$phrase])) {
			// Duplicated phrase definiton.
			\Artise::hunter()->warning(new \HunterException(new \Env\Source\DummyIdiom('E0038', array('phrase' => $phrase)),
															new \Env\Source\DummyIdiom('file_path', array('path' => $this->filename)),
															null,
															array('lang', $this->id)
															));
			
			// Use the last one as default.
			$phrase = $this->source['phrases'][$phrase][count($this->source['phrases'][$phrase]) - 1];
		} else {
			$phrase = $this->source['phrases'][$phrase];
		}
	
		/* -------------- FUNCTION SEARCH -------------- */
		try {
		
			/* Replacing tokens */
			
			foreach($vars as $key => $value)
				$phrase = str_replace('%' . $key, $value, $phrase);
			
			
			
			/* Applying Functions */
			
			preg_match_all('#w\[(.*?)\]#s', $phrase, $calls);
			foreach($calls[1] as $argstr) {
				$phrase = $this->__w(explode(':', $argstr), $vars, $phrase);
			}
	
			preg_match_all('#lc\[(.*?)\]#s', $phrase, $calls);
			foreach($calls[1] as &$value) {
				$phrase = $this->__lc($value, $phrase);
			}
	
			preg_match_all('#uc\[(.*?)\]#s', $phrase, $calls);
			foreach($calls[1] as &$value) {
				$phrase = $this->__uc($value, $phrase);
			}
		
			preg_match_all('#ucf\[(.*?)\]#s', $phrase, $calls);
			foreach($calls[1] as &$value) {
				$phrase = $this->__ucf($value, $phrase);
			}
		
			preg_match_all('#ucw\[(.*?)\]#s', $phrase, $calls);
			foreach($calls[1] as &$value) {
				$phrase = $this->__ucw($value, $phrase);
			}

		return $phrase;
		
		} catch (\HunterException $e) {
			\Env\Hunter\Hunter::warning($e);
			return $parse;
		}
	}
	
	
	public function word($word, $quantity = 0, $genre = 'n')
	{
		try {
			return $this->getWord(array($word, $quantity, $genre), array());
		} catch (\HunterException $e) {
			\Artise::hunter()->warning($e);
			return $word;
		}
	}
	
	public function phraseExists($phrase = '')
	{
		return isset($this->source['phrases'][$phrase]);
	}



	/*** Internal methods ***/

	/**
	 * Internal Word function
	 *
	 * @param array $argv
	 * @param array $vars
	 * @param string $phrase
	 * @return string
	 */
	private function __w(array $argv, array &$vars, &$phrase) {

		//argv
		//0 word
		//1 cantidad
		//2 genero
		//w[ string word, int cantidad, string genero ]
		
		try {
			$search = 'w[' . implode(':', $argv) . ']';
			
			$result_word = $this->getWord($argv, $vars);
			
			return str_replace($search, $result_word, $phrase);
		} catch (\HunterException $e) {
			throw $e;
		}
	}
	
	
	private function getWord(array $argv, array $vars)
	{
		
		/* ------------------------------- CANTIDAD ------------------------------- */
		if(isset($argv[1])) {
			if(is_numeric($argv[1])) {
				$quantity = (int)$argv[1];
			} elseif(preg_match('#^%([a-zA-Z0-9_]+)$#', $argv[1], $varQuantity)) {
				if(isset($vars[$varQuantity[1]])) {
					$quantity = (int)$vars[$varQuantity[1]];
				} else {
					$quantity = 0; //Fallback
				}
			} else {
				$quantity = 0; //Fallback
			}
		} else {
			$quantity = 0; //Fallback
		}
	
		/* ------------------------------- GENERO ------------------------------- */
		if(isset($argv[2])) {
	
			//¿Es algun genero aceptado?
			if($argv[2] == 'f' || $argv[2] == 'm' || $argv[2] == 'n') {
				$genre = $argv[2];
	
			//¿es una variable?
			} elseif(preg_match('#^%([a-zA-Z0-9_]+)$#', $argv[2], $varWord)) {
	
				//¿Esta en las variables recibidas?
				if(isset($vars[$varWord[1]])) {
	
					//¿el valor de la variable es una word?
					if(isset($this->source['words'][$vars[$varWord[1]]])) {
	
						//¿la word tiene alguno de los generos?
						if(isset($this->source['words'][$vars[$varWord[1]]]['m'])) {
							$genre = 'm';
						} elseif(isset($this->source['words'][$vars[$varWord[1]]]['f'])) {
							$genre = 'f';
						} elseif(isset($this->source['words'][$vars[$varWord[1]]]['n'])) {
							$genre = 'n';
						} else {
							$genre = 'n'; //Fallback
						}
	
					} else {
	
						//¿la variable tiene el genero en su valor?
						if($vars[$varWord[1]] == 'f' || $vars[$varWord[1]] == 'm' || $vars[$varWord[1]] == 'n') {
							$genre = $vars[$varWord[1]];
						} else {
							$genre = 'n'; //Fallback
						}
					}
				} else {
					$genre = 'n'; //Fallback
				}
	
			} else {
				$genre = 'n';
			}
		} else {
			$genre = 'n';
		}
	
		/* ------------------------------- GET WORD ------------------------------- */
		//$wfd (Word Final Values), En teoria el array de plural, singular, y nulo, del cual se obtiene el valor final
		if(isset($argv[0])) {
	
			//¿es una variable?
			if(preg_match('#^%([a-zA-Z0-9_]+)$#', $argv[0], $varWord)) {
	
				//¿Esta en las variables?
				if(isset($vars[$varWord[1]])) {
					$wname = $vars[$varWord[1]];
				} else {
					$wname = NULL;
				}
			} else {
				$wname = $argv[0];
			}
	
	
			//¿la word existe?
			if(isset($this->source['words'][$wname])) {
	
				//¿Tiene el genero actual?
				if(isset($this->source['words'][$wname][$genre])) {
					$wfv = $this->source['words'][$wname][$genre];
	
				//¿tiene genero neutro entonces?
				} elseif(isset($this->source['words'][$wname]['n'])) {
					$wfv = $this->source['words'][$wname]['n'];
	
				} else {
					throw new \HunterException(array('W0005', array('word' => $wname)), array('file_path', array('path' => $this->filename)), 'TIP_W0005');
				}
			} else {
				// Word does not exist. Return the provided parameter.
				return $wname;
			}
		} else {
			return '';
		}
	
		/* ------------------------------- REPLACEMENT ------------------------------- */
	
		if(0 === $quantity && isset($wfv['n'])) {
			return $wfv['n'];
		} elseif(1 === $quantity && isset($wfv['s'])) {
			return $wfv['s'];
		} elseif(1 < $quantity && isset($wfv['p'])) {
			return $wfv['p'];
		}
		
		throw new \HunterException(array('W0006', array('word' => $wname)), array('file_path', array('path' => $this->filename)), 'TIP_W0006');
	}

	private function __lc(&$value, &$phrase) {
		return str_replace('lc[' . $value . ']', strtolower($value), $phrase);
	}

	private function __uc(&$value, &$phrase) {
		return str_replace('uc[' . $value . ']', strtoupper($value), $phrase);
	}

	private function __ucf(&$value, &$phrase) {
		return str_replace('ucf[' . $value . ']', ucfirst($value), $phrase);
	}

	private function __ucw(&$value, &$phrase) {
		return str_replace('ucw[' . $value . ']', ucwords($value), $phrase);
	}
	
	
	public function __construct($id, $path) {
		
		$this->id = $id;
		$this->filename = $path . $id . '.xml';
		
		if(!file_exists($this->filename))
			throw new \HunterException(	new \Env\Source\DummyIdiom('E0050', array('element' => 'language')),
										new \Env\Source\DummyIdiom('file_path', array('path' => $this->filename)),
										'TIP_E0050');
		
		if(!is_readable($this->filename))
			throw new \HunterException(	new \Env\Source\DummyIdiom('E0051', array('element' => 'language')),
										new \Env\Source\DummyIdiom('file_path', array('path' => $this->filename)),
										'TIP_E0051');
		
		$xml = secure_simplexml_load_file($this->filename);
		
		if($xml) {
			$this->source = simplexml_to_array($xml);
		}
		else {
			throw new \HunterException(	'E0031',
										new \Env\Source\DummyIdiom('file_path', array('path' => $this->filename)),
										'TIP_E0031');
			$this->parseable = false;
			return;
		}
		
		if(!(isset($this->source['phrases']) && isset($this->source['words']))) {
			throw new \HunterException(	'E0032',
										new \Env\Source\DummyIdiom('file_path', array('path' => $this->filename)),
										'TIP_E0032');
			$this->parseable = false;
			return;
		}
		
		$this->source['phrases'] = (array)$this->source['phrases'];
		$this->source['words'] = (array)$this->source['words'];
	
	}
}
?>
