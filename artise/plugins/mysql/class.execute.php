<?php
namespace plugins\mysql {

	class execute {

		/**
		 * Connection Resource
		 */
		private $cResource;


		/**
		 * Query String
		 */
		private $sql = '';


		/**
		 * Ultimo Id Autogenerado
		 */
		private $insertId = -1;


		/**
		 * Cantidad de filas afectadas
		 */
		private $affectedRows = -1;


		/**
		 * Constructor
		 *
		 * @author Pixelsize Artise team
		 * @param string $sql
		 * @param resource $cResource
		 * @return void
		 */
		public function __construct($sql, &$cResource) {
			$this->cResource = &$cResource;
			$this->sql = $sql;

			if(is_resource($this->cResource)) {
				mysql_query($this->sql, $this->cResource);
				$this->setAffectedRows();
				$this->setInsertId();
			}
		}


		/**
		 * Guarda las filas afectadas por la operacion
		 *
		 * @author Pixelsize Artise team
		 * @return void
		 */
		private function setAffectedRows() {
			$this->affectedRows = mysql_affected_rows($this->cResource);
		}


		/**
		 * Guarda el id generado por la consulta
		 *
		 * @author Pixelsize Artise team
		 * @return void
		 */
		private function setInsertId() {
			$this->insertId = mysql_insert_id($this->cResource);
		}


		/**
		 * Devuelve el id generado con la consulta
		 *
		 * @author Pixelsize Artise team
		 * @return integer
		 */
		public function insertId() {
			return $this->insertId;
		}


		/**
		 * Devuelve el numero de filas afectadas por la consulta
		 *
		 * @author Pixelsize Artise team
		 * @return integer
		 */
		public function affectedRows() {
			return $this->affectedRows;
		}
	}
}
?>