<?php
namespace renders
{
	class main extends \Ext\Rendering\Page 
	{
		public function render()
		{
			/* Head */
			
			$this->head()->title = 'Senses - HTML5 Media Center';
			
			// Frameworks.
			$this->head()->addJS('/js/frameworks/jquery.js');
			$this->head()->addJS('/js/frameworks/jquery-plugins.js');
			
			// Artise JavaScript prototype.
			$this->head()->addJS('/js/artise/palettes.js');
			$this->head()->addJS('/js/artise/render.js');
			
			// Senses client-side code.
			$this->head()->addJS('/js/main.js');
			$this->head()->addJS('/js/navigation.js');
			$this->head()->addJS('/js/player.js');
			$this->head()->addJS('/js/input.js');
			$this->head()->addJS('/js/notifications.js');
			
			// Stylesheets.
			$this->head()->addCSS('/style/reset.css');
			$this->head()->addCSS('/style/style.css');
			$this->head()->addCSS('/style/gui/browser.css');
			$this->head()->addCSS('/style/gui/player.css');
			
			// Favicon.
			$favicon = $this->head()->addLink(array('rel' => 'icon',
													'type' => 'image/png',
													'href' => '/graphics/icon.png'));
			
			
			/* Page */
			
			$layout = $this->body()->layout('default');
			$layout->zone('content')->layout('main');
		}
	}
}

?>