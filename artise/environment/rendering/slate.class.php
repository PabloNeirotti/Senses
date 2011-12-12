<?php
namespace Ext\Rendering\Painter
{
	class Slate extends PaletteBlock
	{
		/**
		 * Store de instancias de paletas
		 */
		private static $palettes = array();
		private $palette;
			
			
		/*** User methods ***/
		
		final public function set($palette)
		{
			$this->palette = $palette;
		}
		
		
		/*** Internal methods ***/


		/**
		 * Crea la base para los bloques
		 *
		 */
		public function __construct()
		{
			$this->block = array(
				'style' => 'root',
				'vars' => array(),
				'blocks' => array()
			);
		}

		/**
		 * Devuelve el contenido ya procesado de una paleta con los datos previamente cargados de los bloques
		 *
		 * @param string $palette
		 * @return string
		 */
		final public function __toString()
		{
			if(!isset(self::$palettes[$this->palette])) {
				self::$palettes[$this->palette] = new Palette($this->palette);
			}

			//Genero el html final
			try {
				/* lo envuelvo en un array a root para que figure como parte de un conjunto de bloques, aunque solo haya uno */
				self::$palettes[$this->palette]->setVarBlocks(array(&$this->block));
				return (string)self::$palettes[$this->palette];
			}
			catch(\HunterException $e) {
				throw $e;
				return '';
			}
		}
	}
}
?>
