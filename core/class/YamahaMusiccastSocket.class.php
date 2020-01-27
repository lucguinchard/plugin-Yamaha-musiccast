<?php

require_once 'YamahaMusiccast.class.php';

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
		if (socket_bind($this->socket, $this->adress, $this->port)) {
			//socket_listen($this->socket);
			while (true) {
				if ((socket_set_block($this->socket)) !== false) {
					//On tente d'obtenir l'IP du client.
					$message = null;
					$host = null;
					$port = null;
					$bytes_received = socket_recvfrom($this->socket, $message, 65536, 0, $host, $port);
					if ($message === 'stop') {
						log::add('YamahaMusiccast', 'debug', 'Arrêt du socket');
						$this->close();
					} else if ($message === 'test') {
						log::add('YamahaMusiccast', 'debug', 'Test du Socket');
					} else {
						YamahaMusiccast::traitement_message($host, $port, $message);
					}
				}
			}
		} else {
			$this->close("Impossible d’ouvrir le socket sur le port " + $this->port + " : " +socket_strerror(socket_last_error($this->socket)));
		}
	}

	/**
	 * Permet de fermer le socket ouver
	 * @param type $err
	 */
	function close($err = null) {
		if ($err != null) {
			log::add('YamahaMusiccast', 'error', $err);
		}
		socket_close($this->socket);
	}

}
