<?php
namespace Ext\Rendering\Painter
{
	class Palette {
	
		/**
		 * Largo de la cadena <!--block:
		 */
		const oTagLen = 10;

		/**
		 * Largo de la cadena -->
		 */
		const oTagEndLen = 3;

		/**
		 * Largo de la cadena <!--/block-->
		 */
		const cTagLen = 13;
	
		private $htmlBlocks = array();
		private $varBlocks = array();
		protected $filename = '';
		protected
			$html = '',
			$phrases;

		final public function set($filename) {
			$this->filename = $filename;
		}
	
		public function __construct($filename) {
			$this->set($filename);
		}

		final private function load() {
			if($this->filename) {
				$path = \Artise::router()->getPath(Artise_Path_Palettes) . DS;
				$filename = $path . $this->filename . '.html';
				if(!file_exists($filename))
					throw new \HunterException(	new \Env\Source\DummyIdiom('E0050', array('element' => 'Palette')),
												new \Env\Source\DummyIdiom('file_path_abstract', array(	'path' => $path,
																										'filename' => $this->filename)),
												'TIP_E0050');
				
				if(!is_readable($filename))
					throw new \HunterException(	new \Env\Source\DummyIdiom('E0051', array('element' => 'Palette')),
												new \Env\Source\DummyIdiom('file_path_abstract', array(	'path' => $path,
																										'filename' => $this->filename)),
												'TIP_E0051');
				
				// Load the HTML of the Palette from the file.
				$this->html = file_get_contents($filename);
				$this->phrases = $this->matchPhrases();
			}
			else {
				throw new \HunterException(	new \Env\Source\DummyIdiom('W0050', array('element' => 'Palette')),
											null,
											'TIP_E0050');
			}
		}
	
		final public function __toString() {
			try {
				if(!$this->html) $this->load(); //Load html
				if(!$this->htmlBlocks) {
					$this->checkIntegrity();
					$this->replacePhrases();
					$this->htmlBlocks = array(
							'root' => array (
							'html' => $this->html,
							'blocks' => $this->parseBlocks($this->html)
						)
					);
				}
				
				return restoreTokens(restoreSlashedTokens($this->merge($this->varBlocks, $this->htmlBlocks)));
			}
			catch(\HunterException $e) {
				\Artise::hunter()->error($e);
				return '';
			}
		}

		public function setVarBlocks(array $blocks) {
			$this->varBlocks = &$blocks;
		}
	
		/**
		 * Chequea que la cantidad de bloques abiertos sea igual a la cantidad de bloques cerrados
		 *
		 * @param string $phtml Palette html
		 * @return bool
		 */
		private function checkIntegrity() {
			preg_match_all('#<!--block:(.*?)-->#s', $this->html, $openingMatches);
			preg_match_all('#<!--/block-->#s', $this->html, $closingMatches);

			if(!(count($openingMatches[0]) === count($closingMatches[0]))) {
				throw new \HunterException(	'E0033',
											new \Env\Source\DummyIdiom('file_path', array('path' => $this->filename)),
											'TIP_E0033');
			}
		}
	
		/**
		 * Recursiva.
		 * Obtiene el html de cada bloque padre dentro del html de la paleta
		 * y los agrupa por orden de inclucion
		 *
		 * -> Muchas cosas del funcionamiento de este metodo son similares a las de cleanBlocksHtml,
		 * asi que la explicacion esta ahi
		 *
		 * @param string $html
		 * @return
		 */
		private function parseBlocks(&$html) {
			$blocksArray = array(); //Defino el array que contiene los bloques a ser devueltos
			$oBlocks = 0; //Opened blocks: Contador para controlar la cantidad de bloques abiertos y bloques cerrados
			$offset = 0;
			$oTagPos = 0;
			$cTagPos = 0;
			$mBlock = array();

			while(true) {
				//Busco la primera aparicion de una etiqueta de cierre y una de apertura
				$oTagPos = strpos($html, '<!--block:', $offset);
				$cTagPos = strpos($html, '<!--/block-->', $offset);

				if(false === $oTagPos && false !== $cTagPos)
					$oTagPos = $cTagPos + 1;

				if($oTagPos < $cTagPos) {
					++$oBlocks;

					//Necesito recolectar el nombre del style asi que me muevo al final de la etiqueta de apertura
					//y guardo ese valor en la variable $charPos
					$charPos = $oTagPos + self::oTagLen;
					$style = '';

					//Recojo cada letra hasta que se encuentre el cierre de la etiqueta de apertura
					while(substr($html, $charPos, self::oTagEndLen) !== '-->') {
						$style .= substr($html, $charPos, 1);
						++$charPos; //me muevo caracter a caracter
					}

					$offset = $charPos;

					//Registro la etiqueta encontrada y en la posicion que se encontro
					//Solo lo registro la primera vez que se encuentre un Open tag
					//Ya que siempre el primer Open tag encontrado corresponde a un Main Block
					//Cuando guardo la posicion le sumo el largo del final de la etiqueta Open tag a la posicion actual($i)

					if(!isset($mBlock['style'])) {
						$mBlock['style'] = $style;
						$mBlock['pos'] = $offset + self::oTagEndLen;
					}

				}
				elseif($cTagPos < $oTagPos) {
					--$oBlocks;

					//el principio de busqueda tiene que estar al final de la etiqueta de cierre
					//por eso a la posicion de la etiqueta de cierre le sumo el largo de la misma
					$offset = $cTagPos + self::cTagLen;

					if(0 === $oBlocks) {
						//Si llegue a este punto es porque encontre un bloque maestro
						//Guardo los datos del bloque maestro actual
						//Y pido los sub-bloques del mismo
						//Tambien guardo el html de este bloque maestro
						$blocksArray[$mBlock['style']] = array();

						//El inicio del html del bloque es la $mBlock[pos] y la cantidad de caracteres a caminar
						// es el resultado de la diferencia de $offset y $mBlock[pos]
						//Pero esto nos recolectaria tambien la etiqueta de cierre! ya que en offset nosotros nos habiamos ido hasta el final de la misma
						//asi que tengo que restarle tambien a esto, el largo de la etiqueta y resto una posicion mas
						$blocksArray[$mBlock['style']]['html'] = substr($html, $mBlock['pos'], $offset - $mBlock['pos'] - self::cTagLen);
						$blocksArray[$mBlock['style']]['blocks'] = $this->parseBlocks($blocksArray[$mBlock['style']]['html']);

						//Limpio este array para permitir guardar los datos del siguiente bloque maestro que se encuentre
						$mBlock = array();
					}
				}
				else {
					break;
				}
			}

			return $blocksArray;
		}

		/**
		 * Recursiva.
		 * Parsea todo el html generado, y elimina los bloques basura que hayan quedado tras ejecutar mergeAll()
		 *
		 * @param string $html
		 * @return
		 */
		private function cleanWastedBlocks($html) {
			$oBlocks = 0; //Opened blocks: Contador para controlar la cantidad de bloques abiertos y bloques cerrados
			$offset = 0;
			$oTagPos = 0;
			$cTagPos = 0;

			while(true) {
				//Busco la primera aparicion de una etiqueta de cierre y una de apertura
				$oTagPos = strpos($html, '<!--block:', $offset);
				$cTagPos = strpos($html, '<!--/block-->', $offset);

				/* Pido la posicion de la primera etiqueta de apertura
			 y la posicion de la primer etiqueta de cierre encontrada. Si ninguna existe -> else
			 Cuando existe la etiqueta de cierre pero no de apertura, se asume que se habla de la
			 ultima etiqueta de cierre que existe en el html
			 Entonces, compruebo que la etiqueta de apertura sea igual a false!, pero! que la etiqueta
			 de cierre no lo sea!
			 Entonces, fuerzo la entrada de los datos a la segunda condicion del if!
			 Para esto la posicion de la etiqueta de apertura tienen que ser mayor a la de cierre!
			 De esta manera el sistema se da cuenta que se encontro una etiqueta de cierre!
			 Sumando 1 a la posicion de la etiqueta de cierre logro esto */
				if(false === $oTagPos && false !== $cTagPos) {
					$oTagPos = $cTagPos + 1;
				}


				/* BLOQUE ABIERTO ENCONTRADO = pos etiqueta apertura < pos atiqueta cierre
			   BLOQUE CIERRE ENCONTRADO = pos etiqueta apertura > pos atiqueta cierre
			   ELSE = bye bye */

				if($oTagPos < $cTagPos) {
					++$oBlocks;

					$offset = $oTagPos + self::oTagLen;

					if(!isset($blockPos['ini']))
						$blockPos['ini'] = $oTagPos;
				}
				elseif($cTagPos < $oTagPos) {
					--$oBlocks;

					$offset = $cTagPos + self::cTagLen + 1;

					//Cuando el contador sea 0 se ha encontrado un bloque padre
					if(0 === $oBlocks) {
						//Guardo la posicion de cierre del bloque
						//en este caso la posicion de cierre la tomo como offset, porque tengo que tomar el final de la etiqueta
						//diferente de la posicion ini, ya que tengo que tomar en ese caso, el principio de la etiqueta
						$blockPos['end'] = $offset;

						//Borro este bloque
						$html = substr_replace($html, '', $blockPos['ini'], ($blockPos['end'] - $blockPos['ini'] - 1));

						$offset = $blockPos['ini']; /* Arranco la busqueda desde la posicion
												en donde se encontro el ultimo bloque */

						//Reseteo variables para volver a buscar otros bloques padres
						$blockPos = array();
					}
				}
				else {
					break;
				}

			}

			return $html;
		}
	
		/**
		 * Recursiva.
		 * Fuciona los bloques htmls, con los bloques de datos, para generar el contenido final
		 *
		 * @param array $varBlocks
		 * @param array $htmlBlocks
		 * @return
		 */
		private function merge(array &$varBlocks, array &$htmlBlocks) {
			$html = ''; //$contiene el html que se va a ir generando
			$blockPos = array(); //Array para guardar la posicion inicial y la final de cada bloque padre encontrado

			/* Recorro bloque a bloque.
			EL parametro $varBlocks recibe el conjunto de bloques hijos, del bloque padre que llamo la funcion */
			foreach($varBlocks as $block) {
				if(!isset($htmlBlocks[$block['style']])) //Check exists
					throw new \HunterException('E0036', $block['style']); //TODO: deberia mostrar todo el path

				/* Obtengo el html del bloque actual
				El parametro $htmlBlocks obtiene el conjunto de bloques html para los styles del conjutno de bloques actual */
				$blockHtml = $htmlBlocks[$block['style']]['html'];

				/* Si este bloque actual contiene subbloques entonces llamo a esta misma funcion
				para que me genere el html correspondiente para ser reemplazado en este bloque */
				if(count($block['blocks']) > 0) {
					//Guardo el html generado con los bloques internos del actual
					$htmlIntBlocks = $this->merge($block['blocks'], $htmlBlocks[$block['style']]['blocks']);

					//Busco el puntero sobre el cual voy a reemplazar el html de los bloques internos
					$pointer = strpos($blockHtml, '<!--block:');

					//Reemplazo el html de los bloques internos sobre el puntero en el html del bloque actual
					$html .= substr_replace($blockHtml, $htmlIntBlocks, $pointer, 0);
				}
				else {
					//En caso de no haber bloques internos para completar, concateno el html del bloque actual
					$html .= $blockHtml;
				}

				//reemplazo variables en el html actual
				//arsort($block['vars']); //Reverse
				
				// Sort by Key Length, descending. Prevent replacing a part of a token as a full token.
				sortByKeyLength($block['vars']);
				
				foreach($block['vars'] as $name => $value) {
					$html = str_replace('%' . $name, slashTokens(slashSlashedTokens($value)), $html);
				}
			}

			return $this->cleanWastedBlocks($html);
		}
		
		final private function matchPhrases()
		{
			preg_match_all('#\@([a-zA-Z0-9_]+)#s', $this->html, $matches);
			arsort($matches[1]); //Reverse
			return $matches[1];
		}
	
		final private function replacePhrases()
		{
			foreach($this->phrases as $phrase) {
				if(\Artise::lang()->phraseExists($phrase)) {
					$value = \Artise::lang()->phrase($phrase, array());
					$this->html = str_replace('@' . $phrase, slashTokens(slashSlashedTokens($value)), $this->html);
				}
			}
		}

		final public function __clone() {
			trigger_error('Palette cannot be cloned', E_USER_ERROR);
		}
	}
}
?>
