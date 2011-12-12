<?php
namespace Env\Hunter
{
	final class PHPErrorHandler
	{
		private
			$lh;
		
		public function __construct(\Env\Hunter\Logger &$lh)
		{
			$this->lh = &$lh;
		}
		
		/**
		 * Controlador de errores que reemplaza al interno de php
		 *
		 * @param string $errno
		 * @param string $errstr
		 * @param string $errfile
		 * @param string $errline
		 * @return bool true previene que se ejecute el gestor interno de php
		 */
		public function register($errno, $errstr, $errfile, $errline)
		{
			switch($errno) {
				case E_USER_ERROR:
					$this->deathScreen($errstr);
					break;

				case E_USER_WARNING:
				case E_USER_NOTICE:
				default:
					return false; //TODO: Quitar linea al terminar el desarrollo
					$this->lh->addPHP($errno, $errstr, $errfile, $errline);
					break;
			}

			return true;
		}
		
		/**
		 * Devuelve la pantalla de error critico y reemplaza el mensaje
		 *
		 * @param string $data
		 * @return string
		 */
		private function deathScreen($data)
		{
			die(str_replace('%error-text', $data, base64_decode(
				'PHN0eWxlPg0KcCB7DQoJcGFkZGluZzogMTVweCAwIDA7DQoJZm9udC13ZWlnaHQ6IGJvbGQ7DQp9DQpj
			b2RlIHsNCglmb250LWZhbWlseTogJ01vbmFjbycsIG1vbm9zcGFjZTsNCgljb2xvcjogIzY5NmM2ZjsNCgltYXJnaW46IDA
			gMCAxMHB4Ow0KCXdoaXRlLXNwYWNlOiBwcmU7DQp9DQo8L3N0eWxlPg0KPGRpdiBzdHlsZT0ibWFyZ2luOjMwcHggYXV0bz
			sgbWluLWhlaWdodDogMTUwcHg7IHdpZHRoOjcwMHB4OyBiYWNrZ3JvdW5kOiNlMWVhZjM7IGJvcmRlcjoxcHggc29saWQgI
			2JhY2FkYTsgLXdlYmtpdC1ib3JkZXItcmFkaXVzOjhweDsgLW1vei1ib3JkZXItcmFkaXVzOjhweDsgLXdlYmtpdC1ib3gt
			c2hhZG93OjAgMnB4IDhweCAjY2NjY2NjOyAtd2Via2l0LWJveC1zaGFkb3c6MCAycHggOHB4ICNjY2NjY2M7IGNvbG9yOiM
			yMjIyMjI7Ij4NCgk8ZGl2IHN0eWxlPSJ3aWR0aDo2NjBweDsgcGFkZGluZzoxNXB4IDIwcHggMTBweDsgYmFja2dyb3VuZD
			ojYmFjYWRhOyBmb250LXNpemU6MzJweDsgZm9udC1mYW1pbHk6J015cmlhZCBQcm8nLCAnU3dpc3M3MjEgQlQnLCBIZWx2Z
			XRpY2EsIEFyaWFsLCBzYW5zLXNlcmlmOyB0ZXh0LXNoYWRvdzogMCAxcHggMXB4IHJnYmEoMjU1LDI1NSwyNTUsLjQpLCAw
			IC0xcHggMXB4IHJnYmEoMCwwLDAsLjQpOyAtd2Via2l0LWJvcmRlci10b3AtbGVmdC1yYWRpdXM6OHB4OyAtd2Via2l0LWJ
			vcmRlci10b3AtcmlnaHQtcmFkaXVzOjhweDsgLW1vei1ib3JkZXItcmFkaXVzLXRvcGxlZnQ6OHB4OyAtbW96LWJvcmRlci
			1yYWRpdXMtdG9wcmlnaHQ6OHB4OyAtd2Via2l0LXVzZXItc2VsZWN0Om5vbmU7IC1tb3otdXNlci1zZWxlY3Q6bm9uZTsgY
			3Vyc29yOmRlZmF1bHQ7Ij4NCgkJPHNwYW4gc3R5bGU9ImZsb2F0OnJpZ2h0OyI+RmF0YWwgZXJyb3I8L3NwYW4+DQoJCUFy
			dGlzZQ0KCTwvZGl2Pg0KCTxkaXYgc3R5bGU9InBhZGRpbmc6MCAyMHB4IDIwcHg7IGZvbnQtZmFtaWx5OidTd2lzczcyMSB
			CVCcsIEhlbHZldGljYSwgQXJpYWwsIHNhbnMtc2VyaWY7IGZvbnQtc2l6ZToxMnB4OyI+DQoJCSVlcnJvci10ZXh0DQoJPC
			9kaXY+DQo8L2Rpdj4=')));
		}
	}
}
?>
