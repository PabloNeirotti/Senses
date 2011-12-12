<?php
namespace Ext\Rendering\Painter
{
	class PaletteBlock
	{
		protected $block;

		public function __construct(&$blockRef, $values = false)
		{
			$this->block = &$blockRef;
			
			if(is_array($values)) {
				foreach($values as $name => $value) {
					$this->block['vars'][$name] = $value;
				}
			}
		}

		public function block($name, $values = false)
		{
			$this->block['blocks'][] = array(
				'style' => $name,
				'vars' => array(),
				'blocks' => array()
			);
			
			return new PaletteBlock($this->block['blocks'][count($this->block['blocks']) - 1], $values);
		}

		public function __set($name, $value)
		{
			$this->block['vars'][$name] = $value;
		}
	}
}
?>
