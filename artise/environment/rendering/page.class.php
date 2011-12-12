<?php
namespace Ext\Rendering
{
	class Page extends \Ext\Source\SiteScope
	{
		private
			$body = false,
			$head = false,
			$class;
		
		/**
		 * Obtains the Body instance of the Page.
		 *
		 * @return Referential instance of the Tag.
		 */
		final public function &body() {
			// Create Layout if it's not created yet.
			if ($this->body === false)
				$this->body = new \Env\Rendering\Page\Body($this->site, $this->devkit());
			
			return $this->body;
		}
		
		/**
		 * Obtains the Head instance of the Page.
		 *
		 * @return Referential instance of the Tag.
		 */
		final public function &head() {
			// Create Layout if it's not created yet.
			if ($this->head === false)
				$this->head = new \Env\Rendering\Page\Head($this->site, $this->devkit());
			
			return $this->head;
		}
		
		final public function __toString()
		{
			return $this->buildSite();
		}
		
		final protected function buildSite() {
			// Executes the user settings.
			$this->render();
			
			//$this->head(new HeadTitle(self::config('site:title')));
			$contentObject = $this->body();
			
			try {
			
				if(is_object($contentObject)) {
					if($contentObject instanceof \Env\Rendering\Page\Body) {
						return (
							$this->getDoctype() . PHP_EOL .
							'<html xmlns="' . $this->site()->config()->get('document:xmlns') . '">' . PHP_EOL .
							'<head>' . PHP_EOL . 
							(string)$this->head . PHP_EOL . 
							'</head>' . PHP_EOL . 
							'<body>' . PHP_EOL . 
							(string)$contentObject . PHP_EOL .
							'</body>' . PHP_EOL . '</html>'
						);
					}
					
					if($this->site->router()->request->isAjax()) {
						// [WARNING] This code is old, wouldn't work.
						/*if($contentObject instanceof Layout || $contentObject instanceof Piece || $contentObject instanceof Output) {
							return (string)$contentObject;
						}*/
					}
				
					//TODO ERROR Tipo de objeto no permitido
					return '';
				}
			} catch (\HunterException $e) {
				
			}
			//TODO ERROR No es objeto
			return '';
		}
	
		/**
		 * Genera la etiqueta <!DOCTYPE del sitio
		 *
		 * @author Pixelsize Artise team
		 * @return string
		 */
		final private function getDoctype() {
			$config = $this->site()->config();
			
			$version = $config->get('document:version');
			$lang = strtoupper($config->get('document:lang'));
	
			if(defined($version)) {
				$version = constant($version);
			}
	
			return sprintf($version, $lang);
		}
		
		
		public function __construct(&$site, &$devkit)
		{
			$this->site = &$site;
			$this->devkit = &$devkit;
			
			$this->class = get_class($this);
			
			// Add the page encoding head tag.
			$this->head()->addTag('meta', array('http-equiv' => 'Content-Type',
												'content' => 'text/html; charset=' . \Artise::config()->get('site:charset')));
		}
	}
}