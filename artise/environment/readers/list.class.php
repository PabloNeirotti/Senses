<?php
namespace Readers
{
	final class Listing extends Table implements Model
	{
		public function __construct(array $rows = null)
		{
			if($rows) {
				foreach($rows as $r) {
					$this->append($r);
				}
			}
		}
		
		public function append($data)
		{
			$this->rows[] = array('value' => $data);
			++$this->rowCount;
		}
		
		public function find($value)
		{
			if($this->row)
				return array_search($value, $this->row);
			else
				return false;
		}
	}
}
?>