<?php
namespace plugins\mysql {
	include dirname(__FILE__) . '/class.execute.php';
	
	class Main extends \Ext\Plugin {

		/**
		 * Handler de la conexion
		 */
		private $resource;


		/**
		 * mixed Estado de conexion {'connected' OR false}
		 */
		private $state = false;


		/**
		 * Host del server
		 */
		private $host;


		/**
		 * Nombre de la base de datos
		 */
		private $dbase;


		/**
		 * Usuario
		 */
		private $user;


		/**
		 * Contraseña
		 */
		private $passwd;


		/**
		 * Juego de caracteres
		 */
		private $charset;


		/**
		 * Establece la conexion
		 *
		 * @author Pixelsize Artise team
		 */
		public function __initialize() {
			$this->setData();
		}


		/**
		 * Cierra la conexion
		 *
		 * @author Pixelsize Artise team
		 */
		public function __destruct() {
			if(is_resource($this->resource)) {
				mysql_close($this->resource);
			}
		}


		/**
		 * Obtiene los datos de la configuracion(Espera un array y sino lo fuerza)
		 *
		 * Guarda cada dato en su respectiva variable
		 *
		 * @author Pixelsize Artise team
		 * @return void
		 */
		private function setData() {
			$data = $this->config->get('connection');
			$this->host 		= isset($data['host']) ? $data['host'] : NULL;
			$this->dbase 		= isset($data['dbase']) ? $data['dbase'] : NULL;
			$this->user 		= isset($data['user']) ? $data['user'] : NULL;
			$this->passwd 	= isset($data['passwd']) ? $data['passwd'] : NULL;
			$this->charset 	= isset($data['charset']) ? $data['charset'] : NULL;
		}


		/**
		 * Entabla la conexion y guarda el estado en $this->state
		 * Si no pudo conectarse alerta a hunter
		 *
		 * @author Pixelsize Artise team
		 * @return void
		 */
		private function connect() {
			if(is_resource($this->resource)) {
				return true;
			}
			else {
				$this->setData();
				$this->resource = @mysql_connect($this->host, $this->user, $this->passwd);
				$this->state = is_resource($this->resource) ? 'connected' : false;

				if($this->state) {
					if($this->dbase) {
						$this->dbase($this->dbase);
					}

					if($this->charset) {
						$this->charset($this->charset);
					}

					return true;
				}
				else {
					$this->devkit->hunter()->error($this->lang()->phrase('could_not_connect'), NULL, $this->lang()->phrase('cnc_recommend'));
				}
			}
		}


		/**
		 * Devuelve el resource de la conexion
		 *
		 * @author Pixelsize Artise team
		 * @return resource
		 */
		public function resource() {
			return $this->resource;
		}


		/**
		 * Devuelve el numero del error
		 *
		 * @author Pixelsize Artise team
		 * @return integer si la conexion esta entablada
		 * @return bool FALSE si la conexion no existe
		 */
		public function errno() {
			if($this->connect()) {
				return mysql_errno($this->resource);
			}
			else {
				return false;
			}
		}


		/**
		 * Devuelve la cadena del error
		 *
		 * @author Pixelsize Artise team
		 * @return string si la conexion esta entablada
		 * @return bool FALSE si la conexion no existe
		 */
		public function error() {
			if($this->connect()) {
				return mysql_error($this->resource);
			}
			else {
				return false;
			}
		}


		/**
		 * Devuelve el el estado de la conexion
		 *
		 * @author Pixelsize Artise team
		 * @return string 'connected' si la conexion esta entablada
		 * @return false si la conexion fallo
		 */
		public function state() {
			return $this->state;
		}


		/**
		 * Establece la base de datos a utilizar
		 *
		 * @author Pixelsize Artise team
		 * @param string $dbase
		 * @return void
		 */
		public function dbase($dbase) {
			if($this->connect()) {
				mysql_select_db((string)$dbase, $this->resource);
			}
		}


		/**
		 * Setea el juego de caracteres a utilizar
		 *
		 * @author Pixelsize Artise team
		 * @param string $charset
		 * @return void
		 */
		public function charset($charset) {
			if($this->connect()) {
				mysql_query('SET NAMES \'' . (string)$charset . '\';', $this->resource);
			}
		}


		/**
		 * Limpia el contenido ingresado para prevenir injecciones SQL
		 *
		 * @author Pixelsize Artise team
		 * @param mixed $var1,$var2,...
		 * @return void
		 */
		public function clean() {
			if($this->connect()) {
				$argc = func_num_args();
				if($argc > 0) {
					$argv = func_get_args();

					foreach($argv as &$arg) {
						if(is_array($arg)) {
							foreach($arg as &$value) {
								$value = mysql_real_escape_string($value, $this->resource);
							}
						}
						elseif(is_string($arg)) {
							$arg = mysql_real_escape_string($arg, $this->resource);
						}
					}
					return ($argc > 1) ? $argv : $argv[0];
				}
				else {
					return false;
				}
			}
			return false;
		}


		/**
		 * Crea una instancia de una consulta que no devuelve filas
		 *
		 * @author Pixelsize Artise team
		 * @param string $sql
		 * @return object
		 */
		public function execute($sql) {
			if($this->connect()) {
				return new execute((string)$sql, $this->resource);
			}
			return NULL;
		}


		/**
		 * Crea una instancia de una consulta que devuelve filas
		 *
		 * @author Pixelsize Artise team
		 * @param string $sql
		 * @return object
		 * @return false si $sql esta vacio
		 */
		public function reader($sql) {
			$dt = new \Readers\Table();
			
			if($this->connect()) {
				$query = mysql_query($sql, $this->resource);
				
				if(is_resource($query)) {
					if(mysql_num_rows($query) > 0) {
						while($row = mysql_fetch_assoc($query)) {
							$dt->append($row);
						}
					}
				}
			}
			return $dt;
		}
	}
}
?>