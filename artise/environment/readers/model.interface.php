<?php
namespace Readers
{
	interface Model
	{
		public function append($data);
		public function __get($name);
		public function __set($name, $value);
		
		public function rowCount();
		public function index();

		public function rewind();
		public function jump($index);
		public function next();
		public function read();
		public function prev();
		public function last();
	}
}
?>
