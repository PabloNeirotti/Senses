<?php
namespace actions\library
{
	class increasePlayCount extends \Ext\Action
	{
		public function execute()
		{
			// Fetch the Senses plugin.
			$senses = $this->devkit()->plugins()->senses();
			
			$id = $_POST['id'];
			
			if(isset($_SESSION['play_count'][$id]))
			{
				$_SESSION['play_count'][$id] ++;
				
				return false;
			}
			else
			{
				$_SESSION['play_count'][$id] = 1;
				
				$media_type = $_POST['media_type'];
				
				// Fetch a media object.
				$media = $senses->library()->mediaObject($id, $media_type);
				
				// Increase play count.
				$media->increasePlayCount();
				
				return true;
			}
		}
	}
}

?>