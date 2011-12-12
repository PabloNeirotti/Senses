<?php
namespace Env\Events
{
	final class Callback
	{
		private $callback;
		private $result;

		public function __construct(&$callback)
		{
			$this->callback = &$callback;
		}

		public function exec(array $argv = NULL)
		{
			$this->result = call_user_func_array($this->callback, $argv);
		}

		public function result()
		{
			return $this->result;
		}
	}
}
?>
