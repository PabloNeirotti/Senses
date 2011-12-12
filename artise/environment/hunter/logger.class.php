<?php
namespace Env\Hunter
{
	final class Logger
	{
		private
			$log = array(
				'read' => false,
				'uri' => '',
				'php' => array(),
				'artise' => array()
			);
		
		public
			$enabled = true;
		
		public function addPHP($errno, $errstr, $errfile, $errline)
		{
			$this->log['php'][] = array (
				'errno'   => (string)$errno,
				'errstr'  => $errstr,
				'errfile' => $errfile,
				'errline' => $errline
			);
		}
		
		public function addArtise($trace, $errno, $errtit, $errdesc = null, $errtip = null, $component = null)
		{
			$this->log['artise'][] = array(
				'errno' 		=> (string)$errno,
				'errtit' 		=> $errtit,
				'errdesc' 		=> $errdesc,
				'errtip' 		=> $errtip,
				'component' 	=> $component,
				'trace' 		=> $trace
			);
		}

		public function uri($uri)
		{
			$this->log['uri'] = $uri;
		}
		
		public function read($bool)
		{
			$this->log['read'] = $bool;
		}
		
		/**
		 * Guarda el log
		 *
		 * @return bool - wether a log was created or not.
		 */
		public function save($filename)
		{
			if(count($this->log['php']) > 0 || count($this->log['artise']) > 0) {
				$fp = fopen($filename, 'w+');
				fwrite ($fp, serialize($this->log));
				fclose ($fp);
				
				return true;
			}
			
			return false;
		}
	}
}
?>