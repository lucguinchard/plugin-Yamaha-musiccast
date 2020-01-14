<?php

class YamahaMusiccastSocket {

	var $address = null;
	var $port = null;
	var $socket = null;

	public function __construct($adress, $port) {
		$this->adress = $adress;
		$this->port = $port;
	}

	function run() {
		$this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_bind($this->socket, $this->adress, $this->port) or $this->Logging(($this->close()));
		socket_listen($this->socket);
		while (true) {
			if ((socket_set_block($this->socket)) !== false) {
				//On tente d'obtenir l'IP du client.
				$message = null;
				$adress = null;
				$port = null;
				$bytes_received = socket_recvfrom($this->socket, $message, 65536, 0, $adress, $port);
				$this->Logging('Nouvelle connexion client : ' . $adress . ':' . $port);
				if ($message === 'stop') {
					$this->Logging('Close');
					$this->close();
				} else {
					$this->Logging('NouveauTRAITEMETNT : ' . $message);
				}
			}
		}
	}

	/**
	 * 
	 * @param type $msg
	 * @return type
	 */
	function Logging($msg) {
		log::add('YamahaMusiccast', 'debug', 'Message ' . $msg);
		return;
	}

	/**
	 * Permet de fermer le socket ouver
	 * @param type $err
	 */
	function close($err = null) {
		if ($err != null) {
			$this->Logging($err);
		} else {
			$this->Logging(socket_strerror(socket_last_error()));
		}

		if (is_resource($fp)) {
			fclose($fp);
		}
		@socket_close($this->socket);
	}

}
