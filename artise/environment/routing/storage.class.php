<?php
namespace Env\Routing
{
	final class Storage
	{
		private
			$storage = array(),
			$uri;
		
		public function __set($id, $value)
		{
			
			// Store the value in the active Storage session.
			$this->storage[$id] = $value;
		}
		
		public function __get($id)
		{
			if(isset($this->storage[$id]))
				return $this->storage[$id];
			else
				return false;
		}
		
		public function setUri($uri)
		{
			$this->uri = $uri;
		}
		
		public function __construct($uri)
		{
			if(!isset($_SESSION['artise']))
				return;
			
			if(!isset($_SESSION['artise']['storage']))
				return;
			
			
			if(isset($_SESSION['artise']['storage'][$uri])) {
				// Restore the Storage for this URI.
				$this->storage = unserialize($_SESSION['artise']['storage'][$uri]);
				
				// Remove the Session Storage entry for this URI.
				unset($_SESSION['artise']['storage'][$uri]);
			}
		}
		
		/**
		 * Method called by Router when redirecting.
		 */
		public function redirecting($target_uri)
		{
			$this->cs_sessionArrays();
			
			// Store the Storage for this URI.
			$_SESSION['artise']['storage'][$target_uri] = serialize($this->storage);
		}
		
		private function cs_sessionArrays()
		{
			if(!isset($_SESSION['artise']))
				$_SESSION['artise'] = array();
			
			if(!isset($_SESSION['artise']['storage']))
				$_SESSION['artise']['storage'] = array();
		}
		
	}
}
?>
