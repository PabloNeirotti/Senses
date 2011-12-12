<?php
/*
somethingChanged es usada para marcar si algo cambio o no, 
en pro de no contruir todo el tag cada vez que se llama al metodo ::__toString()
*/
namespace Ext\Rendering\Page
{
	class Tag
	{
		private $attributes = array();
		private $somethingChanged = true;
		private
			$name,
			$built,
			$self_closing;
		public $innerHTML = null;

		public function __construct($name, array $attributes = null, $self_closing = true)
		{
			$this->name = $name;
			$this->attributes = $attributes;
			$this->selfClosing($self_closing);
		}

		public function __set($name, $value)
		{	
			$this->somethingChanged = true;
			$this->attributes[$name] = $value;
		}

		public function __toString()
		{
			if($this->somethingChanged) {
				$this->built = $this->build($this->buildAttributes());
			}
			
			return $this->built;
		}
		
		protected function selfClosing($state)
		{
			$this->self_closing = (bool)$state;
		}

		private function buildAttributes()
		{
			$attr = array();
			foreach($this->attributes as $name => $value) {
				$attr[] = $name . '="' . $value . '"';
			}
			return implode(' ', $attr);
		}

		private function build($attributes)
		{
			if($this->self_closing === true && $this->innerHTML == null) {
				return "<$this->name $attributes/>";
			}
			else {
				return "<$this->name $attributes>$this->innerHTML</$this->name>";
			}
		}
	}
}
?>
