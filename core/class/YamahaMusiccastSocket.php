<?php
class YamahaMusiccastSocket extends pht\Thread {

	var $address = null;
	var $port = null;
	var $socket = null;

	public function __construct($adress, $port) {
		$this->adress = $adress;
		$this->port = $port;
	}

	function run() {
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_UDP);
		socket_bind($this->socket, $address, $port) or die($this->close());
		socket_listen($this->socket);
		while (true) {
			$this->socketMessage(socket_accept($this->socket));
		}
	}

	/**
	 * Méthode qui traite le message.
	 */
	private function socketMessage($socketMessage) {
		$message = socket_read($socketMessage, 1024);
		if ($message == 'stop') {
			$this->close();
		}
		//On tente d'obtenir l'IP du client.
		socket_getpeername($socketMessage, &$adress, &$port);
		$this->Logging('Nouvelle connexion client : ' . $adress . ':' . $port);
		$this->Logging('Message : ' . $message);
		socket_close($socketMessage);
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
	function close($err) {
		if ($err != null) {
			$this->Logging($err);
		} else {
			$this->Logging(socket_strerror(socket_last_error()));
		}
		reset($this->clients);
		while ($sock_cli = current($this->clients)) {
			@socket_close($sock_cli);
			next($this->clients);
		}

		if (is_resource($fp)) {
			fclose($fp);
		}
		@socket_close($this->socket);
		die();
	}

}
