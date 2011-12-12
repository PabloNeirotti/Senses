<?php
namespace Ext
{
	class Painter extends \Ext\Source\SiteScope
	{
		final public function createSlate($filename = null)
		{
			$sl = new \Ext\Rendering\Painter\Slate($this->site->router()->getPath(Artise_Path_Palettes));
			
			if($filename)
				$sl->set($filename);
			
			return $sl;
		}
	}
}
?>
