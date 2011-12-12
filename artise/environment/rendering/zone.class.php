<?php
namespace Ext\Rendering\Layout
{
	class Zone extends \Ext\Source\SiteScope
	{
		private
			$layout = false,
			$clear = false;

		final public function __toString()
		{
			if($this->clear)
				return '';
			else
				return (string)$this->layout;
		}

		final public function &layout($filename = NULL)
		{
			// Create the Layout if it doesn't exists.
			if ($this->layout === false)
				$this->layout = new \Ext\Rendering\Layout($this->site, $this->devkit());
			
			// Set the piece simplified path.
			if($filename)
				$this->layout->set($filename);
			
			// Remove clear status if set before.
			$this->clear = false;
			
			// Return the Piece.
			return $this->layout;
		}
		
		final public function clear()
		{
			$this->clear = true;
		}
	}
}
?>
