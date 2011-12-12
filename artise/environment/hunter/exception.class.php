<?php

	final class HunterException extends \Exception
	{
		private
			$data = array();

		public function __construct($errtit = null, $errdesc = null, $errtip = null, $component = null)
		{
			$this->data['errtit'] = $errtit;
			$this->data['errdesc'] = $errdesc;
			$this->data['errtip'] = $errtip;
			$this->data['component'] = $component;
		}
		
		public function getTitle()
		{
			return $this->data['errtit'];
		}
		
		public function getDescription()
		{
			return $this->data['errdesc'];
		}
		
		public function getTip()
		{
			return $this->data['errtip'];
		}
		
		public function getComponent()
		{
			return $this->data['component'];
		}
	
	}
?>
