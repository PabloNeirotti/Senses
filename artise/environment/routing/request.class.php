<?php
namespace Env\Routing
{
	class Request
	{
		public function get()
		{
			if ($this->isAjax())
				return AJAX_Request;
			else
				return Standard_Request;
		}
		
		public function isAjax()
		{
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
				if($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
					return true;
				}
			}
			
			return false;
		}
		
		public function isSecure()
		{
			return (!empty($_SERVER['HTTPS']));
		}
	}
}
?>