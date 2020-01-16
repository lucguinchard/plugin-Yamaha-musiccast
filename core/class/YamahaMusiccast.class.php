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
		$this->setCategory('multimedia', 1);
		if(empty($this->getLogicalId())) {
			$host = $this->getName();
			$this->setLogicalId($host);
		} else {
			$host = $this->getLogicalId();
		}
		$jsonGetNetworkStatus = YamahaMusiccast::CallAPI("GET", "http://$host/YamahaExtendedControl/v1/system/getNetworkStatus");
		if($jsonGetNetworkStatus === false) {
			$this->setIsVisible(0);
			$this->setIsEnable(0);
		} else {
			$this->setIsVisible(1);
			$this->setIsEnable(1);
			$getNetworkStatus = json_decode($jsonGetNetworkStatus);
			$this->setName($getNetworkStatus->network_name);

			$jsonGetDeviceInfo = YamahaMusiccast::CallAPI("GET", "http://$host/YamahaExtendedControl/v1/system/getDeviceInfo");
			$getDeviceInfo = json_decode($jsonGetDeviceInfo);
			$this->setConfiguration('model_name', $getDeviceInfo->model_name);

			$jsonGetFeatures = YamahaMusiccast::CallAPI("GET", "http://$host/YamahaExtendedControl/v1/system/getFeatures");
			$getFeatures = json_decode($jsonGetFeatures);
			foreach ($getFeatures->zone as $zone) {
				$zoneName = $zone->id;
				foreach ($zone->func_list as $func) {
					$cmd = $this->getCmd(null, $zoneName. '_' .$func . '_state');
					if (!is_object($cmd)) {
						$cmd = new YamahaMusiccastCmd();
						$cmd->setLogicalId($zoneName. '_' .$func . '_state');
						$cmd->setName(__($zoneName. '_' .$func . '_state', __FILE__));
					}
					$cmd->setType('info');
					$cmd->setSubType('string');
					$cmd->setConfiguration('repeatEventManagement', 'never');
					$cmd->setEqLogic_id($this->getId());
					$cmd->save();
				}
			}
		}
	}

	public function postSave() {
		
		$jsonGetFeatures = YamahaMusiccast::CallAPI("GET", "http://$host/YamahaExtendedControl/v1/system/getFeatures");
		$getFeatures = json_decode($jsonGetFeatures);
		foreach ($getFeatures->zone as $zone) {
			YamahaMusiccast::callZoneGetStatus($this, $zone->id);
		}
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
		if (!socket_connect($sock, "127.0.0.1", $port)) {
			log::add('YamahaMusiccast', 'error', 'Connexion impossible pour deamon_info');
			$return['state'] = 'ko';
			$return['log'] = "Connexion impossible pour deamon_info";
		}
		if (!socket_write($sock, "test")) {
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
	}

	public static function socket_stop() {
		$port = config::byKey('socket.port', 'YamahaMusiccast');
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or log::add('YamahaMusiccast', 'error', 'Création du socket_stop refusée');
		socket_connect($sock, "127.0.0.1", $port) or log::add('YamahaMusiccast', 'error', 'Connexion impossible pour socket_stop');
		socket_write($sock, "stop");
		//socket_close($sock);
	}

	public static function cron5() {
		$devices = self::byType('YamahaMusiccast');
		foreach ($devices as $eqLogic) {
			if ($eqLogic->getIsEnable() == 0) {
				continue;
			}
			$host = $eqLogic->getLogicalId();
			$result = YamahaMusiccast::CallAPI("GET", "http://$host/YamahaExtendedControl/v1/system/getDeviceInfo");
			log::add('YamahaMusiccast', 'debug', 'Appel du Cron5 ' . $result);
		}
	}

	public static function traitement_message($host, $port, $json) {
		//log::add('YamahaMusiccast', 'debug', 'Traitement  : ' . $host . ':' . $port . ' → ' . $json);
		$result = json_decode($json);
		$device = null;
		$devices = self::byType('YamahaMusiccast');
		foreach ($devices as $eqLogic) {
			if ($eqLogic->getLogicalId() === $host) {
				$device = $eqLogic;
				break;
			}
		}
		if(empty($device)){
			log::add('YamahaMusiccast', 'error', 'Erreur lors de traitement_message : device is null');
			return;
		}
		$device_id = $result->device_id;
		$system = $result->system;
		if (!empty($system)) {
			$bluetooth_info_updated = $system->bluetooth_info_updated;
			if (!empty($bluetooth_info_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: $bluetooth_info_updated - pull renewed info using /system/getBluetoothInfo ' . print_r($bluetooth_info_updated, true));
			}
			$func_status_updated = $system->func_status_updated;
			if (!empty($func_status_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: $func_status_updated - pull renewed info using /system/getFuncStatus ' . print_r($func_status_updated, true));
			}
			$location_info_updated = $system->location_info_updated;
			if (!empty($location_info_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: $location_info_updated - pull renewed info using /system/getLocationInfo ' . print_r($location_info_updated, true));
			}
			$name_text_updated = $system->name_text_updated;
			if (!empty($name_text_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: $name_text_updated - pull renewed info using /system/getNameText ' . print_r($name_text_updated, true));
			}
			$speaker_settings_updated = $system->speaker_settings_updated;
			if (!empty($speaker_settings_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: $speaker_settings_updated - Reserved ' . print_r($speaker_settings_updated, true));
			}
			$stereo_pair_info_updated = $system->stereo_pair_info_updated;
			if (!empty($stereo_pair_info_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: $stereo_pair_info_updated - Reserved ' . print_r($stereo_pair_info_updated, true));
			}
			$tag_updated = $system->tag_updated;
			if (!empty($tag_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: $tag_updated - Reserved ' . print_r($tag_updated, true));
			}
		}
		$main = $result->main;
		if (!empty($main)) {
			YamahaMusiccast::callZone($device, 'main', $main);
		}
		$zone2 = $result->zone2;
		if (!empty($zone2)) {
			YamahaMusiccast::callZone($device, 'zone2', $main);
		}
		$zone3 = $result->zone3;
		if (!empty($zone3)) {
			YamahaMusiccast::callZone($device, 'zone3', $main);
		}
		$zone4 = $result->zone4;
		if (!empty($zone4)) {
			YamahaMusiccast::callZone($device, 'zone4', $main);
		}
		$tuner = $result->tuner;
		if (!empty($tuner)) {
			$play_info_updated = $tuner->play_info_updated;
			if (!empty($play_info_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: Mise à jour du isPlayInfoUpdated Main - Note: If so, pull renewed info using /tuner/getPlayInf' . print_r($play_info_updated, true));
			}
			$preset_info_updated = $tuner->preset_info_updated;
			if (!empty($preset_info_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: Mise à jour du isPresetInfoUpdated Main - Note: If so, pull renewed info using /tuner/getPresetInfo' . print_r($preset_info_updated, true));
			}
		}
		$netusb = $result->netusb;
		if (!empty($netusb)) {
			$play_error = $netusb->play_error;
			if (!empty($play_error)) {
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
				log::add('YamahaMusiccast', 'debug', 'TODO: Mise à jour du $play_error ' . print_r($play_error, true));
			}
			$multiple_play_errors = $netusb->multiple_play_errors;
			if (!empty($multiple_play_errors)) {
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
				log::add('YamahaMusiccast', 'debug', 'TODO: Mise à jour du $multiple_play_errors ' . print_r($multiple_play_errors, true));
			}
			$play_message = $netusb->play_message;
			if (!empty($play_message)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: Playback related message ' . print_r($play_message, true));
			}
			$account_updated = $netusb->account_updated;
			if (!empty($account_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: Whether or not account info has changed. Note: If so, pull renewed info using /netusb/getAccountStatus. ' . print_r($account_updated, true));
			}
			$play_time = $netusb->play_time;
			if (!empty($play_time)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: Current playback time (unit in second). ' . print_r($play_time, true));
			}
			$preset_info_updated = $netusb->preset_info_updated;
			if (!empty($preset_info_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: Whether or not preset info has changed. - Note: If so, pull renewed info using netusb/getPresetInfo ' . print_r($preset_info_updated, true));
			}
			$recent_info_updated = $netusb->recent_info_updated;
			if (!empty($recent_info_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO:  Whether or not playback history info has changed. - Note: If so, pull renewed info using netusb/getRecentInfo ' . print_r($recent_info_updated, true));
			}
			$preset_control = $netusb->preset_control;
			if (!empty($preset_control)) {
				log::add('YamahaMusiccast', 'debug', 'TODO:  Results of Preset operations. ' . print_r($preset_control, true));
			}
			$trial_status = $netusb->trial_status;
			if (!empty($trial_status)) {
				log::add('YamahaMusiccast', 'debug', 'TODO:  Trial status of a Device. ' . print_r($trial_status, true));
			}
			$trial_time_left = $netusb->trial_time_left;
			if (!empty($trial_time_left)) {
				log::add('YamahaMusiccast', 'debug', 'TODO:  Remaining time of a trial. ' . print_r($trial_time_left, true));
			}
			$play_info_updated = $netusb->play_info_updated;
			if (!empty($play_info_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO:  Returns whether or not playback info has changed. Note: If so, pull renewed info using /netusb/getPlayInfo.. ' . print_r($play_info_updated, true));
			}
			$list_info_updated = $netusb->list_info_updated;
			if (!empty($list_info_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: Returns whether or not list info has changed. Note: If so, pull renewed info using /netusb/getListInfo. ' . print_r($list_info_updated, true));
			}
		}
		$cd = $result->cd;
		if (!empty($cd)) {
			log::add('YamahaMusiccast', 'debug', 'TODO: CD. ' . print_r($cd, true));
		}
		$dist = $result->dist;
		if (!empty($dist)) {
			log::add('YamahaMusiccast', 'debug', 'TODO: $dist. ' . print_r($dist, true));
		}
		$clock = $result->clock;
		if (!empty($clock)) {
			$settings_updated = $clock->settings_updated;
			if (!empty($settings_updated)) {
				log::add('YamahaMusiccast', 'debug', 'TODO: isSettingsUpdated ' . print_r($settings_updated, true));
			}
		}
		$device->refreshWidget();
		//log::add('YamahaMusiccast', 'debug', '$device_id' . $device_id . '       ' . print_r($result, true));
	}

	static function callZone($device, $zoneName, $zone) {
		$power = $zone->power;
		if (!empty($power)) {
			$device->checkAndUpdateCmd($zoneName. '_power_state', $power);
		}
		$input = $zone->input;
		if (!empty($input)) {
			$device->checkAndUpdateCmd($zoneName. '_input_change_state', $input);
		}
		$volume = $zone->volume;
		if (!empty($volume)) {
			$device->checkAndUpdateCmd($zoneName. '_volume_state', $volume);
		}
		$mute = $zone->mute;
		if (!empty($mute)) {
			$device->checkAndUpdateCmd($zoneName. '_mute_state', $mute);
		}
		$status_updated = $zone->status_updated;
		if (!empty($status_updated)) {
			YamahaMusiccast::callZoneGetStatus($device, $zoneName);
		}
		$signal_info_updated = $zone->signal_info_updated;
		if (!empty($signal_info_updated)) {
			log::add('YamahaMusiccast', 'debug', 'TODO: Mise à jour du signal_info_updated - If so, pull renewed info using /'.$zoneName.'/getSignalInfo  ' . print_r($signal_info_updated, true));
		}
	}

	static function callZoneGetStatus($device, $zoneName) {
		$host = $device->getLogicalId();
		$jsonGetStatusZone = YamahaMusiccast::CallAPI("GET", "http://$host/YamahaExtendedControl/v1/$zoneName/getStatus");
		$getStatusZone = json_decode($jsonGetStatusZone);
		$this->checkAndUpdateCmd($zoneName. '_power_state', $getStatusZone->power);
		$this->checkAndUpdateCmd($zoneName. '_volume_state', $getStatusZone->volume);
		$this->checkAndUpdateCmd($zoneName. '_mute_state', $getStatusZone->mute);
		$this->checkAndUpdateCmd($zoneName. '_input_change_state', $getStatusZone->input);
		$this->checkAndUpdateCmd($zoneName. '_sound_program_state', $getStatusZone->sound_program);
		$this->checkAndUpdateCmd($zoneName. '_link_audio_quality_state', $getStatusZone->link_audio_quality);
		$this->checkAndUpdateCmd($zoneName. '_link_audio_delay_state', $getStatusZone->link_audio_delay);
		$this->checkAndUpdateCmd($zoneName. '_link_control_state', $getStatusZone->link_control);
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
