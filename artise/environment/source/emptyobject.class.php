<?php
namespace Env\Source
{
	class EmptyObject
	{
		public
			$call;
		
		public function __construct($call)
		{
			$this->call = $call;
		}
		
		public function __call($method, $args = false)
		{
			\Artise::hunter()->error(	new \Env\Source\DummyIdiom('E0041', array('method' => $method)),
												new \Env\Source\DummyIdiom('DESC_E0041', array('call' => $this->call)),
												'TIP_E0041');
		}
		
		public function __get($property)
		{
			\Artise::hunter()->error(	new \Env\Source\DummyIdiom('E0042', array('property' => $property)),
												new \Env\Source\DummyIdiom('DESC_E0041', array('call' => $this->call)),
												'TIP_E0041');
		}
		
		public function __set($property, $value = false)
		{
			\Artise::hunter()->error(	new \Env\Source\DummyIdiom('E0043', array('property' => $property)),
												new \Env\Source\DummyIdiom('DESC_E0041', array('call' => $this->call)),
												'TIP_E0041');
		}
	}
}
?>