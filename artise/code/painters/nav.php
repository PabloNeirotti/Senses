<?php
namespace painters;

class nav extends \Ext\Painter
{
	
	public function navigator($fld)
	{
		// Get Senses plugin.
		$senses = $this->devkit()->plugins()->senses();
		
		// We create a Slate.
		$sl = $this->createSlate('selector');
		
		if ($fld == '') {
			$sl->block('item');
			
		} else {
			$fld = split('/', $fld);
			
			switch($mediaType)
			{
	
			}
			
			return $sl;
		}
	}
	
	public function mediaSelect($mediaType)
	{
		// Get Senses plugin.
		$senses = $this->devkit()->plugins()->senses();
		
		// We create a Slate.
		$sl = $this->createSlate('selector');
		
		switch($mediaType)
		{
			case 'video':
				$item = $senses->getMovies();
				
				while($item->read())
				{
					$b = $sl->block('item');
					$b->title = $item->title;
					$b->artist = $item->author;
					$b->link = '#vid:' . (substr($item->filename, 0, 7) == 'http://' ? '' : '/media/videos/' ) . $item->filename;
					$b->thumb = '/graphics/thumbs/videos/' . basename($item->codename) . '.png';
				}
				
				break;
				
			case 'music':
				$item = $senses->getMusic();
				
				while($item->read())
				{
					$b = $sl->block('item');
					$b->title = $item->title;
					$b->artist = $item->author;
					$b->link = '#aud:' . (substr($item->filename, 0, 7) == 'http://' ? '' : '/media/audio/' ) . $item->filename;
					$b->thumb = '/graphics/thumbs/audio/' . $item->codename . '.png';
				}
				break;
			
			default:
				return 'Error nav.php at line ' . __LINE__;
		}
		return $sl;
	}
}
?>