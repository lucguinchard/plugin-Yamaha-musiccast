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

	public function createCmd($name, $type = 'info', $subtype = 'string', $repeatEventManagement = 'never', $generic_type = null) {
		$cmd = $this->getCmd(null, $name);
		if (!is_object($cmd)) {
			$cmd = new YamahaMusiccastCmd();
			$cmd->setLogicalId($name);
			$cmd->setName(__($name, __FILE__));
		}
		$cmd->setType($type);
		$cmd->setSubType($subtype);
		$cmd->setConfiguration('repeatEventManagement', $repeatEventManagement);
		$cmd->setGeneric_type($generic_type);
		$cmd->setEqLogic_id($this->getId());
		$cmd->save();
	}

	public function preUpdate() {
		
	}

	public function postUpdate() {
		
	}

	public function preRemove() {
		rrmdir(dirname(__FILE__) . '/../../../../plugins/YamahaMusiccast/ressources/' . $this->getId());
	}

	// When the directory is not empty:
	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir . "/" . $object) == "dir") {
						rmdir($dir . "/" . $object);
					} else {
						unlink($dir . "/" . $object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

	public function postRemove() {
		
	}

	public function toHtml($_version = 'dashboard') {
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}
		$version = jeedom::versionAlias($_version);
		if ($this->getDisplay('hideOn' . $version) == 1) {
			return '';
		}
		/* ------------ Ajouter votre code ici ------------ */
		foreach ($this->getCmd('info') as $cmd) {
			$replace['#' . $cmd->getLogicalId() . '_history#'] = '';
			$replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
			$replace['#' . $cmd->getLogicalId() . '#'] = $cmd->execCmd();
			$replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();
			if ($cmd->getLogicalId() == 'encours') {
				$replace['#thumbnail#'] = $cmd->getDisplay('icon');
			}
			if ($cmd->getIsHistorized() == 1) {
				$replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
			}
		}

		if ($this->getCmd(null, 'power_state')->execCmd() === 'on') {
			$replace['#power_action_id#'] = $this->getCmd(null, 'power_off')->getId();
		} else {
			$replace['#power_action_id#'] = $this->getCmd(null, 'power_on')->getId();
		}
		if ($this->getCmd(null, 'mute_state')->execCmd() === 'true') {
			$replace['#mute_action_id#'] = $this->getCmd(null, 'mute_off')->getId();
		} else {
			$replace['#mute_action_id#'] = $this->getCmd(null, 'mute_on')->getId();
		}

		foreach ($this->getCmd('action') as $cmd) {
			$replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
		}

		if (file_exists(dirname(__FILE__) . '/../../../../plugins/YamahaMusiccast/ressources/' . $this->getId() . '/AlbumART.jpg')) {
			$replace['#netusb_albumart_url#'] = '/plugins/YamahaMusiccast/ressources/' . $this->getId() . '/AlbumART.jpg';
		} else {
			$replace['#netusb_albumart_url#'] = '/plugins/YamahaMusiccast/plugin_info/YamahaMusiccast_icon.png';
		}
		/* ------------ N'ajouter plus de code apres ici------------ */

		return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'YamahaMusiccast', 'YamahaMusiccast')));
	}

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
		if (!socket_connect($sock, "127.0.0.1", $port)) {
			log::add('YamahaMusiccast', 'error', 'Connexion impossible pour deamon_info');
			$return['state'] = 'ko';
			$return['log'] = "Connexion impossible pour deamon_info";
		} else if (!socket_write($sock, "test")) {
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
		$return['auto'] = 1;
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
		YamahaMusiccast::callYamahaMusiccast();
	}

	public static function socket_stop() {
		$port = config::byKey('socket.port', 'YamahaMusiccast');
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or log::add('YamahaMusiccast', 'error', 'Création du socket_stop refusée');
		socket_connect($sock, "127.0.0.1", $port) or log::add('YamahaMusiccast', 'error', 'Connexion impossible pour socket_stop');
		socket_write($sock, "stop");
		//socket_close($sock);
	}

	public static function cron5() {
		log::add('YamahaMusiccast', 'debug', 'Appel du Cron5');
		YamahaMusiccast::callYamahaMusiccast();
	}

	public static function searchAndSaveDeviceList() {
		$return = array();
		$ipList = YamahaMusiccast::searchDeviceIpList();
		foreach ($ipList as $ip) {
			$return[$ip] = YamahaMusiccast::saveDeviceIp($ip);
		}
		return $return;
	}

	public static function saveDeviceIp($ip) {
		$deviceZoneList = array();
		$jsonGetNetworkStatus = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getNetworkStatus");
		if ($jsonGetNetworkStatus === false) throw new Exception(__('L’appareil avec ip ' . $this->getLogicalId() . ' n’est pas joingnable ou n’existant !'), __FILE__);
		$jsonGetDeviceInfo = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getDeviceInfo");
		$getDeviceInfo = json_decode($jsonGetDeviceInfo);

		$jsonGetFeatures = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getFeatures");
		$getFeatures = json_decode($jsonGetFeatures);
		if (!empty($getFeatures)) {
			foreach ($getFeatures->zone as $zone) {
				$zoneName = $zone->id;
				array_push($deviceZoneList, $zoneName);
				$logicalId = $ip.':'.$zoneName;
				$device = YamahaMusiccast::byLogicalId($logicalId, 'YamahaMusiccast');
				if (!is_object($device)) {
					$device = new YamahaMusiccast();
					$device->setEqType_name('YamahaMusiccast');
				}
				$device->setName($logicalId);
				$device->setLogicalId($logicalId);
				$device->setCategory('multimedia', 1);
				$device->setIsVisible(1);
				$device->setIsEnable(1);
				$device->setConfiguration('zone', $zoneName);
				$device->setConfiguration('ip', $ip);
				$device->save();
			
				$deviceDir = dirname(__FILE__) . '/../../../../plugins/YamahaMusiccast/ressources/' . $device->getId() . '/';
				if (!file_exists($deviceDir)) {
					mkdir($deviceDir, 0700);
				}
				
				foreach ($zone->func_list as $func) {
					$device->createCmd($func . '_state');
				}
				$device->createCmd('max_volume');
				$device->createCmd('input');
				$device->createCmd('power_on', 'action', 'other', null, 'ENERGY_ON');
				$device->createCmd('power_off', 'action', 'other', null, 'ENERGY_OFF');
				$device->createCmd('volume_change', 'action', 'slider', null, 'SET_VOLUME');

				$device->createCmd('audio_error');
				$device->createCmd('audio_format');
				$device->createCmd('audio_fs');

				$device->createCmd('mute_on', 'action', 'other', null, null);
				$device->createCmd('mute_off', 'action', 'other', null, null);

				$device->createCmd('netusb_playback_play', 'action', 'other', null, 'MEDIA_RESUME');
				$device->createCmd('netusb_playback_stop', 'action', 'other', null, 'MEDIA_STOP');
				$device->createCmd('netusb_playback_pause', 'action', 'other', null, 'MEDIA_PAUSE');
				$device->createCmd('netusb_playback_play_pause', 'action', 'other', null);
				$device->createCmd('netusb_playback_previous', 'action', 'other', null, 'MEDIA_PREVIOUS');
				$device->createCmd('netusb_playback_next', 'action', 'other', null, 'MEDIA_NEXT');
				$device->createCmd('netusb_playback_fast_reverse_start', 'action', 'other', null);
				$device->createCmd('netusb_playback_fast_reverse_end', 'action', 'other', null);
				$device->createCmd('netusb_playback_fast_forward_start', 'action', 'other', null);
				$device->createCmd('netusb_playback_fast_forward_end', 'action', 'other', null);

				$device->createCmd('netusb_shuffle_off', 'action', 'other', null);
				$device->createCmd('netusb_shuffle_on', 'action', 'other', null);
				$device->createCmd('netusb_shuffle_songs', 'action', 'other', null);
				$device->createCmd('netusb_shuffle_albums', 'action', 'other', null);
				$device->createCmd('netusb_repeat_off', 'action', 'other', null);
				$device->createCmd('netusb_repeat_one', 'action', 'other', null);
				$device->createCmd('netusb_repeat_all', 'action', 'other', null);

				$device->createCmd('netusb_input');
				$device->createCmd('netusb_play_queue_type');
				$device->createCmd('netusb_playback');
				$device->createCmd('netusb_repeat');
				$device->createCmd('netusb_shuffle');
				$device->createCmd('netusb_play_time');
				$device->createCmd('netusb_total_time');
				$device->createCmd('netusb_artist');
				$device->createCmd('netusb_album');
				$device->createCmd('netusb_track');
				$device->createCmd('netusb_albumart_url');
				$device->createCmd('netusb_albumart_id');
				$device->createCmd('netusb_usb_devicetype');
				$device->createCmd('netusb_usb_auto_stopped');
				$device->createCmd('netusb_attribute');
				$device->createCmd('netusb_repeat_available');
				$device->createCmd('netusb_shuffle_available');
				YamahaMusiccast::callZoneGetStatus($device, $zoneName);
				$getNetworkStatus = json_decode($jsonGetNetworkStatus);
				$device->setName($getNetworkStatus->network_name . " " . $zoneName);
				$device->setConfiguration('model_name', $getDeviceInfo->model_name);
				$device->save();
			}
		}
		return $deviceZoneList;
	}

	public static function searchDeviceIpList() {
		log::add('YamahaMusiccast', 'debug', 'searchDeviceList');
		$ipCast = "239.255.255.250";
		$port = 1900;
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		$level = getprotobyname("ip");
		socket_set_option($sock, $level, IP_MULTICAST_TTL, 2);

		$data = "M-SEARCH * HTTP/1.1\r\n";
		$data .= "HOST: $ipCast:$port\r\n";
		$data .= "MAN: \"ssdp:discover\"\r\n";
		$data .= "MX: 2\r\n";
		$data .= "ST: urn:schemas-upnp-org:device:MediaRenderer:1\r\n";

		socket_sendto($sock, $data, strlen($data), null, $ipCast, $port);

		$read = [$sock];
		$write = [];
		$except = [];
		$name = null;
		$port = null;
		$tmp = "";

		$response = "";
		while (socket_select($read, $write, $except, 1)) {
			socket_recvfrom($sock, $tmp, 2048, null, $name, $port);
			$response .= $tmp;
		}


		$devices = [];
		foreach (explode("\r\n\r\n", $response) as $reply) {
			if (!$reply) {
				continue;
			}

			$data = [];
			foreach (explode("\r\n", $reply) as $line) {
				if (!$pos = strpos($line, ":")) {
					continue;
				}
				$key = strtolower(substr($line, 0, $pos));
				$val = trim(substr($line, $pos + 1));
				$data[$key] = $val;
			}
			$devices[] = $data;
		}

		$return = [];
		$unique = [];
		foreach ($devices as $device) {
			if ($device["st"] !== "urn:schemas-upnp-org:device:MediaRenderer:1") {
				continue;
			}
			if (in_array($device["usn"], $unique)) {
				continue;
			}

			$url = parse_url($device["location"]);
			$ip = $url["host"];

			$return[] = $ip;
			$unique[] = $device["usn"];
		}
		log::add('YamahaMusiccast', 'debug', print_r($return, true));
		return $return;
	}

	public static function callYamahaMusiccast() {
		$devices = self::byType('YamahaMusiccast');
		$date = date("Y-m-d H:i:s");
		foreach ($devices as $device) {
			if ($device->getIsEnable() == 0) {
				continue;
			}
			$lastCallAPI = $device->getStatus('lastCallAPI');
			$deltaSeconds = strtotime($date) - strtotime($lastCallAPI);
			if ($deltaSeconds > (4.5 * 60)) {
				$result = YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/getDeviceInfo");
				log::add('YamahaMusiccast', 'debug', 'Mise à jour ' . $device->getName());
			}
		}
	}

	public static function traitement_message($host, $port, $json) {
		//log::add('YamahaMusiccast', 'debug', 'Traitement  : ' . $host . ':' . $port . ' → ' . $json);
		$result = json_decode($json);
		$deviceList = array();
		$devices = self::byType('YamahaMusiccast');
		foreach ($devices as $eqLogic) {
			$ip = $eqLogic->getConfiguration('ip');
			$zone = $eqLogic->getConfiguration('zone');
			if ($ip === $host) {
				$device_id = $result->device_id;
				if (!empty($result->system)) {
					$system = $result->system;
					$bluetooth_info_updated = $system->bluetooth_info_updated;
					if (!empty($bluetooth_info_updated)) {
						log::add('YamahaMusiccast', 'info', 'TODO: $bluetooth_info_updated - pull renewed info using /system/getBluetoothInfo ' . print_r($bluetooth_info_updated, true));
					}
					if (!empty($system->func_status_updated)) {
						$func_status_updated = $system->func_status_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: $func_status_updated - pull renewed info using /system/getFuncStatus ' . print_r($func_status_updated, true));
					}
					if (!empty($system->location_info_updated)) {
						$location_info_updated = $system->location_info_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: $location_info_updated - pull renewed info using /system/getLocationInfo ' . print_r($location_info_updated, true));
					}
					if (!empty($system->name_text_updated)) {
						$name_text_updated = $system->name_text_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: $name_text_updated - pull renewed info using /system/getNameText ' . print_r($name_text_updated, true));
					}
					if (!empty($system->speaker_settings_updated)) {
						$speaker_settings_updated = $system->speaker_settings_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: $speaker_settings_updated - Reserved ' . print_r($speaker_settings_updated, true));
					}
					if (!empty($system->stereo_pair_info_updated)) {
						$stereo_pair_info_updated = $system->stereo_pair_info_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: $stereo_pair_info_updated - Reserved ' . print_r($stereo_pair_info_updated, true));
					}
					$tag_updated = $system->tag_updated;
					if (!empty($tag_updated)) {
						log::add('YamahaMusiccast', 'info', 'TODO: $tag_updated - Reserved ' . print_r($tag_updated, true));
					}
				}
				if (!empty($result->$zone)) {
					YamahaMusiccast::callZone($eqLogic, $zone, $result->$zone);
				}
				if (!empty($result->tuner)) {
					$tuner = $result->tuner;
					if (!empty($tuner->play_info_updated)) {
						$play_info_updated = $tuner->play_info_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: Mise à jour du isPlayInfoUpdated Main - Note: If so, pull renewed info using /tuner/getPlayInf' . print_r($play_info_updated, true));
					}
					if (!empty($tuner->preset_info_updated)) {
						$preset_info_updated = $tuner->preset_info_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: Mise à jour du isPresetInfoUpdated Main - Note: If so, pull renewed info using /tuner/getPresetInfo' . print_r($preset_info_updated, true));
					}
				}
				if (!empty($result->netusb)) {
					$netusb = $result->netusb;
					if (!empty($netusb->play_error)) {
						$play_error = $netusb->play_error;
						/**
						 * Error codes happened during playback for displaying appropriate messages to the external application user interface.
						 * <p>If multiple errors happen at the same time, refer to the value of {@link NetUsb#multiplePlayErrors} sent together for proper messaging </p>
						 * <p>Values :
						 * <ul>
						 * 	<li>0: No Error</li>
						 * 	<li>1: Access Error (common for all Net/USB sources)</li>
						 * 	<li>2: Playback Unavailable (common for all Net/USB sources)</li>
						 * 	<li>3: Skip Limit Reached (Rhapsody / Napster / Pandora)</li>
						 * 	<li>4: Invalid Session (Rhapsody / Napster / SiriusXM)</li>
						 * 	<li>5: High-Resolution File Not Playable at MusicCast Leaf (Server)</li>
						 * 	<li>6: User Uncredentialed (Qobuz)</li>
						 * 	<li>7: Track Restricted by Right Holders (Qobuz)</li>
						 * 	<li>8: Sample Restricted by Right Holders (Qobuz)</li>
						 * 	<li>9: Genre Restricted by Streaming Credentials (Qobuz)</li>
						 * 	<li>10: Application Restricted by Streaming Credentials (Qobuz)</li>
						 * 	<li>11: Intent Restricted by Streaming Credentials (Qobuz)</li>
						 * 	<li>100: Multiple Errors (common for all Net/USB sources)</li>
						 * </ul>
						 * </p>
						 * <p>Note: Rhapsody service name will be changed to Napster.</p>
						 */
						log::add('YamahaMusiccast', 'info', 'TODO: Mise à jour du $play_error ' . print_r($play_error, true));
					}
					if (!empty($netusb->multiple_play_errors)) {
						$multiple_play_errors = $netusb->multiple_play_errors;
						/**
						 * Bit field flags of multiple playback errors.
						 * <p>Flags are expressed as OR of bit field.</p>
						 * <p>{@link NetUsb#playError} code x is stored as a flag in b[x] shown below. x=0 is reserved for it is for No Error, and x=100 is ignored here</p>
						 * <ul>
						 * 	<li>b[0] reserved (for it’s No Error)</li>
						 * 	<li>b[1] Access Error (common for all Net/USB sources)</li>
						 * 	<li>...</li>
						 * 	<li>b[11] Intent Restricted by Streaming Credentials (Qobuz)</li>
						 * </ul>
						 */
						log::add('YamahaMusiccast', 'info', 'TODO: Mise à jour du $multiple_play_errors ' . print_r($multiple_play_errors, true));
					}
					if (!empty($netusb->play_message)) {
						$play_message = $netusb->play_message;
						log::add('YamahaMusiccast', 'info', 'TODO: Playback related message ' . print_r($play_message, true));
					}
					if (!empty($netusb->account_updated)) {
						$account_updated = $netusb->account_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: Whether or not account info has changed. Note: If so, pull renewed info using /netusb/getAccountStatus. ' . print_r($account_updated, true));
					}
					if (!empty($netusb->play_time)) {
						$play_time = $netusb->play_time;
						$eqLogic->checkAndUpdateCmd('netusb_play_time', $play_time);
					}
					if (!empty($netusb->preset_info_updated)) {
						$preset_info_updated = $netusb->preset_info_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: Whether or not preset info has changed. - Note: If so, pull renewed info using netusb/getPresetInfo ' . print_r($preset_info_updated, true));
					}
					if (!empty($netusb->recent_info_updated)) {
						$recent_info_updated = $netusb->recent_info_updated;
						log::add('YamahaMusiccast', 'info', 'TODO:  Whether or not playback history info has changed. - Note: If so, pull renewed info using netusb/getRecentInfo ' . print_r($recent_info_updated, true));
					}
					if (!empty($netusb->preset_control)) {
						$preset_control = $netusb->preset_control;
						log::add('YamahaMusiccast', 'info', 'TODO:  Results of Preset operations. ' . print_r($preset_control, true));
					}
					if (!empty($netusb->trial_status)) {
						$trial_status = $netusb->trial_status;
						log::add('YamahaMusiccast', 'info', 'TODO:  Trial status of a Device. ' . print_r($trial_status, true));
					}
					if (!empty($netusb->trial_time_left)) {
						$trial_time_left = $netusb->trial_time_left;
						log::add('YamahaMusiccast', 'info', 'TODO:  Remaining time of a trial. ' . print_r($trial_time_left, true));
					}
					if (!empty($netusb->play_info_updated)) {
						$play_info_updated = $netusb->play_info_updated;
						YamahaMusiccast::callNetusbGetPlayInfo($eqLogic);
					}
					if (!empty($netusb->list_info_updated)) {
						$list_info_updated = $netusb->list_info_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: Returns whether or not list info has changed. Note: If so, pull renewed info using /netusb/getListInfo. ' . print_r($list_info_updated, true));
					}
				}
				if (!empty($result->cd)) {
					$cd = $result->cd;
					log::add('YamahaMusiccast', 'info', 'TODO: CD. ' . print_r($cd, true));
				}
				if (!empty($result->dist)) {
					$dist = $result->dist;
					log::add('YamahaMusiccast', 'info', 'TODO: $dist. ' . print_r($dist, true));
				}
				if (!empty($result->clock)) {
					$clock = $result->clock;
					if (!empty($clock->settings_updated)) {
						$settings_updated = $clock->settings_updated;
						log::add('YamahaMusiccast', 'info', 'TODO: isSettingsUpdated ' . print_r($settings_updated, true));
					}
				}
				$eqLogic->refreshWidget();
			}
		}
		if (empty($deviceList)) {
			log::add('YamahaMusiccast', 'info', 'L’appareil ' . $host . ' n’existe plus');
		}
		//log::add('YamahaMusiccast', 'debug', '$device_id' . $device_id . '       ' . print_r($result, true));
	}

	static function callZone($eqLogic, $zoneName, $zone) {
		if (!empty($zone->power)) {
			$eqLogic->checkAndUpdateCmd('power_state', $zone->power);
		}
		if (!empty($zone->input)) {
			$eqLogic->checkAndUpdateCmd('input', $zone->input);
		}
		if (!empty($zone->volume)) {
			$eqLogic->checkAndUpdateCmd('volume_state', $zone->volume);
		}
		if (!empty($zone->mute)) {
			$eqLogic->checkAndUpdateCmd('mute_state', $zone->mute);
		}
		if (!empty($zone->status_updated)) {
			YamahaMusiccast::callZoneGetStatus($eqLogic, $zoneName);
		}
		if (!empty($zone->signal_info_updated)) {
			YamahaMusiccast::callZoneGetSignalInfo($eqLogic, $zoneName);
		}
	}

	static function callZoneGetSignalInfo($eqLogic, $zoneName) {
		$json = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/$zoneName/getSignalInfo");
		$result = json_decode($json);
		if (!empty($result->audio)) {
			$audio = $result->audio;
			if (!empty($audio->error)) {
				$eqLogic->checkAndUpdateCmd('audio_error', $audio->error);
			}
			if (!empty($audio->format)) {
				$eqLogic->checkAndUpdateCmd('audio_format', $audio->format);
			}
			if (!empty($audio->fs)) {
				$eqLogic->checkAndUpdateCmd('audio_fs', $audio->fs);
			}
		}
	}

	static function callNetusbGetPlayInfo($eqLogic) {
		$json = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/netusb/getPlayInfo");
		$result = json_decode($json);

		if (!empty($result->input)) {
			$eqLogic->checkAndUpdateCmd('netusb_input', $result->input);
		}
		if (!empty($result->play_queue_type)) {
			$eqLogic->checkAndUpdateCmd('netusb_play_queue_type', $result->play_queue_type);
		}
		if (!empty($result->playback)) {
			$eqLogic->checkAndUpdateCmd('netusb_playback', $result->playback);
		}
		if (!empty($result->repeat)) {
			$eqLogic->checkAndUpdateCmd('netusb_repeat', $result->repeat);
		}
		if (!empty($result->shuffle)) {
			$eqLogic->checkAndUpdateCmd('netusb_shuffle', $result->shuffle);
		}
		if (!empty($result->play_time)) {
			$eqLogic->checkAndUpdateCmd('netusb_play_time', $result->play_time);
		}
		if (!empty($result->total_time)) {
			$eqLogic->checkAndUpdateCmd('netusb_total_time', $result->total_time);
		}
		if (!empty($result->artist)) {
			$eqLogic->checkAndUpdateCmd('netusb_artist', $result->artist);
		}
		if (!empty($result->album)) {
			$eqLogic->checkAndUpdateCmd('netusb_album', $result->album);
		}
		if (!empty($result->track)) {
			$eqLogic->checkAndUpdateCmd('netusb_track', $result->track);
		}
		$fileAlbumART = dirname(__FILE__) . '/../../../../plugins/YamahaMusiccast/ressources/' . $eqLogic->getId() . '/AlbumART.jpg';
		if (!empty($result->albumart_url)) {
			$url = "http://" . $eqLogic->getLogicalId() . $result->albumart_url;
			file_put_contents($fileAlbumART, file_get_contents($url));
			$eqLogic->checkAndUpdateCmd('netusb_albumart_url', $result->albumart_url);
		} else {
			$eqLogic->checkAndUpdateCmd('netusb_albumart_url', '');
			if (file_exists($fileAlbumART)) {
				unlink($fileAlbumART);
			}
		}
		if (!empty($result->albumart_id)) {
			$eqLogic->checkAndUpdateCmd('netusb_albumart_id', $result->albumart_id);
		}
		if (!empty($result->usb_devicetype)) {
			$eqLogic->checkAndUpdateCmd('netusb_usb_devicetype', $result->usb_devicetype);
		}
		if (!empty($result->usb_auto_stopped)) {
			$eqLogic->checkAndUpdateCmd('netusb_usb_auto_stopped', $result->usb_auto_stopped);
		}
		if (!empty($result->usb_auto_stopped)) {
			$eqLogic->checkAndUpdateCmd('netusb_usb_auto_stopped', $result->usb_auto_stopped);
		}
		if (!empty($result->attribute)) {
			$eqLogic->checkAndUpdateCmd('netusb_attribute', $result->attribute);
		}
		if (!empty($result->repeat_available)) {
			$eqLogic->checkAndUpdateCmd('netusb_repeat_available', $result->repeat_available);
		}
		if (!empty($result->shuffle_available)) {
			$eqLogic->checkAndUpdateCmd('netusb_shuffle_available', $result->shuffle_available);
		}
	}

	static function callZoneGetStatus($eqLogic, $zoneName) {
		$jsonGetStatusZone = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/$zoneName/getStatus");
		$getStatusZone = json_decode($jsonGetStatusZone);
		if (!empty($getStatusZone->power)) {
			$eqLogic->checkAndUpdateCmd('power_state', $getStatusZone->power);
		}
		if (!empty($getStatusZone->max_volume)) {
			$eqLogic->checkAndUpdateCmd('max_volume', $getStatusZone->max_volume);
		}
		if (!empty($getStatusZone->volume)) {
			$eqLogic->checkAndUpdateCmd('volume_state', $getStatusZone->volume);
		}
		if (!empty($getStatusZone->mute)) {
			$eqLogic->checkAndUpdateCmd('mute_state', $getStatusZone->mute);
		}
		if (!empty($getStatusZone->input)) {
			$eqLogic->checkAndUpdateCmd('input', $getStatusZone->input);
		}
		if (!empty($getStatusZone->sound_program)) {
			$eqLogic->checkAndUpdateCmd('sound_program_state', $getStatusZone->sound_program);
		}
		if (!empty($getStatusZone->link_audio_quality)) {
			$eqLogic->checkAndUpdateCmd('link_audio_quality_state', $getStatusZone->link_audio_quality);
		}
		if (!empty($getStatusZone->link_audio_delay)) {
			$eqLogic->checkAndUpdateCmd('link_audio_delay_state', $getStatusZone->link_audio_delay);
		}
		if (!empty($getStatusZone->link_control)) {
			$eqLogic->checkAndUpdateCmd('link_control_state', $getStatusZone->link_control);
		}
	}

	static function CallAPI($method, $eqLogic, $path, $data = false) {
		$port = config::byKey('socket.port', 'YamahaMusiccast');
		$name = config::byKey('socket.name', 'YamahaMusiccast');
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
					$path = sprintf("%s?%s", $path, http_build_query($data));
				}
		}
		// Optional Authentication:
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$header[0] = "Content-Type: application/json";
		$header[1] = "X-AppName: MusicCast/1.0 ($name)";
		$header[2] = "X-AppPort: $port";
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		if(is_string($eqLogic)) {
			$url = "http://" . $eqLogic . $path;
		} else {
			$url = "http://" . $eqLogic->getConfiguration('ip') . $path;
			$eqLogic->setStatus('lastCallAPI', date("Y-m-d H:i:s"));
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);

		curl_close($curl);

		return $result;
	}

	/*	 * **********************Getteur Setteur*************************** */
}
