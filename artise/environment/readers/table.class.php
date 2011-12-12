<?php
namespace Readers
{
	class Table implements Model
	{
		protected $rows = array();
		protected $index = -1;
		protected $rowCount = 0;
	
		public function __construct(array $rows = null)
		{
			if($rows) {
				if(dim_count($rows) >= 2) {
					foreach($rows as $cols) {
						$this->append($cols);
					}
				}
			}
		}
		
		public function append($data)
		{
			$this->rows[] = $data;
			++$this->rowCount;
		}
		
		final public function read()
		{
			if($this->index < $this->rowCount - 1) {
				++$this->index;
				return true;
			}
			else {
				return false;
			}
		}
		
		final public function rowToArray()
		{
			if($this->index < 0)
				$this->index = 0;
			
			if(isset($this->rows[$this->index])) {
				return $this->rows[$this->index];
			}
			
			return null;
		}
		
		final public function tableToArray()
		{
			return $this->rows;
		}
	
		final public function __get($name)
		{
			if($this->index < 0)
				$this->index = 0;
			
			if(isset($this->rows[$this->index])) {
				if(isset($this->rows[$this->index][$name])) {
					return $this->rows[$this->index][$name];
				}
			}
			
			return null;
		}
		
		final public function __set($name, $value)
		{
			if(isset($this->rows[$this->index])) {
				$this->rows[$this->index][$name] = $value;
			}
			
			//TODO: Si no existe la fila o la lista esta vacia, error??
		}
		
		final public function rowCount() {
			return $this->rowCount;
		}
	
		final public function index()
		{
			return $this->index;
		}

		final public function rewind()
		{
			$this->index = -1;
		}

		final public function jump($index)
		{
			if($index >= 0 && $index <= $this->rowCount) {
				$this->index = $index;
				return true;
			}
			else {
				return false;
			}
		}

		final public function next()
		{
			return $this->read();
		}

		final public function prev()
		{
			if($this->index > 0) {
				--$this->index;
				return true;
			}
			else {
				return false;
			}
		}
	
		final public function last()
		{
			if($this->index > -1) {
				$this->index = $this->rowCount - 1;
				return true;
			}
			else {
				return false;
			}
		}
	}
}
?>
