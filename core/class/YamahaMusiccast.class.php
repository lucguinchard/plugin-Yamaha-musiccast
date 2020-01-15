<?php

/*
 * This file is part of the NextDom software (https://github.com/NextDom or http://nextdom.github.io).
 * Copyright (c) 2018 NextDom.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once 'YamahaMusiccastCmd.class.php';
require_once 'YamahaMusiccastSocket.class.php';

class YamahaMusiccast extends eqLogic {
	/*	 * *************************Attributs****************************** */


	/*	 * ***********************Methode static*************************** */

	/*
	 * Fonction exécutée automatiquement toutes les minutes par Jeedom
	  public static function cron() {

	  }
	 */


	/*
	 * Fonction exécutée automatiquement toutes les heures par Jeedom
	  public static function cronHourly() {

	  }
	 */

	/*
	 * Fonction exécutée automatiquement tous les jours par Jeedom
	  public static function cronDaily() {

	  }
	 */


	/*	 * *********************Méthodes d'instance************************* */

	public function preInsert() {
		
	}

	public function postInsert() {
		
	}

	public function preSave() {
		
	}

	public function postSave() {
		
	}

	public function preUpdate() {
		
	}

	public function postUpdate() {
		
	}

	public function preRemove() {
		
	}

	public function postRemove() {
		
	}

	/*
	 * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
	  public function toHtml($_version = 'dashboard') {

	  }
	 */

	/*
	 * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
	  public static function postConfig_<Variable>() {
	  }
	 */

	/*
	 * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
	  public static function preConfig_<Variable>() {
	  }
	 */

	/**
	 * Non obligatoire
	 * Obtenir l'état du daemon
	 *
	 * @return [log] message de log
	 *         [state]  ok  Démarré
	 *                  nok Non démarré
	 *         [launchable] ok  Démarrable
	 *                      nok Non démarrable
	 *         [launchable_message] Cause de non démarrage
	 *         [auto]   0 Démarrage automatique désactivé
	 *                  1 Démarrage automatique activé
	 */
	public static function deamon_info() {
		$return = array();
		$return['log'] = '';
		$return['state'] = 'nok';
		$port = config::byKey('socket.port', 'YamahaMusiccast');
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or log::add('YamahaMusiccast', 'error', 'Création du deamon_info refusée');
		if(!socket_connect($sock, "127.0.0.1", $port)) { 
			log::add('YamahaMusiccast', 'error', 'Connexion impossible pour deamon_info');
			$return['state'] = 'ko';
			$return['log'] = "Connexion impossible pour deamon_info";
		}
		if(!socket_write($sock, "test")) {
			log::add('YamahaMusiccast', 'error', 'Envoie du test en echec deamon_info');
			$return['state'] = 'ko';
			$return['log'] = 'Envoie du test en echec deamon_info';
		} else {
			$cron = cron::byClassAndFunction('YamahaMusiccast', 'socket_start');
			if (is_object($cron) && $cron->running()) {
				$return['state'] = 'ok';
			}
		}
		socket_close($sock);
		$return['launchable'] = 'ok';
		return $return;
	}

	/**
	 * Démarre le daemon
	 *
	 * @param Debug (par défault désactivé)
	 */
	public static function deamon_start($_debug = false) {
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') {
			throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
		}
		$cron = cron::byClassAndFunction('YamahaMusiccast', 'socket_start');
		if (!is_object($cron)) {
			throw new Exception(__('Tache cron introuvable', __FILE__));
		}
		$cron->run();
	}

	/**
	 * Démarre le daemon
	 *
	 * @param Debug (par défault désactivé)
	 */
	public static function deamon_stop() {
		$cron = cron::byClassAndFunction('YamahaMusiccast', 'socket_start');
		if (!is_object($cron)) {
			throw new Exception(__('Tache cron introuvable', __FILE__));
		}
		YamahaMusiccast::socket_stop();
		$cron->halt();
	}

	public static function socket_start() {
		$port = config::byKey('socket.port', 'YamahaMusiccast');
		log::add('YamahaMusiccast', 'debug', 'Lancement d’un socket sur le port ' . $port);
		$socket = new YamahaMusiccastSocket("0.0.0.0", $port);
		$socket->run();
	}

	public static function socket_stop() {
		$port = config::byKey('socket.port', 'YamahaMusiccast');
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or log::add('YamahaMusiccast', 'error', 'Création du socket_stop refusée');
		socket_connect($sock, "127.0.0.1", $port) or log::add('YamahaMusiccast', 'error', 'Connexion impossible pour socket_stop');
		socket_write($sock, "stop");
		socket_close($sock);
	}

	public static function cron5() {
		$devices = self::byType('YamahaMusiccast');
		foreach ($devices as $eqLogic) {
			if ($eqLogic->getIsEnable() == 0) {
				continue;
			}
			$result = YamahaMusiccast::CallAPI("GET", "http://192.168.222.230/YamahaExtendedControl/v1/system/getNameText");
			log::add('YamahaMusiccast', 'debug', 'Appel du Cron5 ' . $result);
			
			if ($eqLogic->getLogicalId() == '') {
				continue;
			}
		}
	}
	public static function traitement_message($host, $port, $body) {
		log::add('YamahaMusiccast', 'debug', 'Traitement  : ' . $host . ':' . $port . ' → ' . $body);
		$json = json_decode($body);
		log::add('YamahaMusiccast', 'debug', print_r($json, true));
	}

	static function CallAPI($method, $url, $data = false) {
		$port = config::byKey('socket.port', 'YamahaMusiccast');
		$curl = curl_init();

		switch ($method) {
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);

				if ($data) {
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				}
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_PUT, 1);
				break;
			default:
				if ($data) {
					$url = sprintf("%s?%s", $url, http_build_query($data));
				}
		}
		// Optional Authentication:
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$header[0] = "Content-Type: application/json";
		$header[1] = "X-AppName: Musiccast/Jeedom";
		$header[2] = "X-AppPort: $port";
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);

		curl_close($curl);

		return $result;
	}

	/*	 * **********************Getteur Setteur*************************** */
}
