<?php
namespace Env\Events
{
	final class Handler
	{
		/*
		 * Listeners
		 */
		private $listeners = array();


		/**
		 * Agrega un callback a un evento
		 *
		 * @author Pixelsize Artise team
		 * @param integer $event Constante del evento
		 * @param callback $callback
		 * @return void
		 */
		public function &addListener($event, $callback)
		{
			if(!isset($this->listeners[$event])) {
				$this->listeners[$event] = array();
			}
			
			if(is_callable($callback) || is_array($callback)) {
				$this->listeners[$event][] = new Callback($callback);
				return $this->listeners[$event][count($this->listeners[$event])-1];
			}
			else {
				//TODO: ErrorLog - No es una funcion valida
			}
		}


		/**
		 * Ejecuta callbacks de un evento
		 *
		 * @author Pixelsize Artise team
		 * @param integer $event Constante del evento
		 * @return void
		 */
		public function trigger($event, array $argv = NULL)
		{
			if(isset($this->listeners[$event])) {
				foreach($this->listeners[$event] as &$callback) {
					$callback->exec($argv);
				}
			}
		}
	}
}
?>
