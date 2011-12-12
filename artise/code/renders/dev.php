<?php
namespace renders
{
	class dev extends \Ext\Rendering\Page 
	{
		public function render()
		{
			/* Head */
			
			$this->head()->title = 'Senses - Developers';
			
			// Stylesheets.
			$this->head()->addCSS('/style/dev/reset.css');
			$this->head()->addCSS('/style/dev/style.css');
			
			
			/* Page */
			
			// Define the target page.
			$page = $this->site()->router()->uri()->page;
			
			if(!$page)
				$page = 'home';
			
			// Available pages.
			$available_pages = array('documentation');
			
			// Redirect if the page does not exist.
			if(!in_array($page, $available_pages) && $page != 'home') {
				$this->site()->router()->uri('/dev/')->go();
			}
			
			$layout = $this->body()->layout('dev/mainframe');
			$layout->zone('article')->layout("dev/documents/$page");
		}
	}
}

?>