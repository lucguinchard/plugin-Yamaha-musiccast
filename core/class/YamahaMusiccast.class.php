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
			$this->setLogicalId($this->getName());
		}
		$jsonGetNetworkStatus = YamahaMusiccast::CallAPI("GET", $this, "/YamahaExtendedControl/v1/system/getNetworkStatus");
		if($jsonGetNetworkStatus === false) {
			$this->setIsVisible(0);
			$this->setIsEnable(0);
		} else {
			$this->setIsVisible(1);
			$this->setIsEnable(1);
			$getNetworkStatus = json_decode($jsonGetNetworkStatus);
			$this->setName($getNetworkStatus->network_name);

			$jsonGetDeviceInfo = YamahaMusiccast::CallAPI("GET", $this, "/YamahaExtendedControl/v1/system/getDeviceInfo");
			$getDeviceInfo = json_decode($jsonGetDeviceInfo);
			$this->setConfiguration('model_name', $getDeviceInfo->model_name);

			$jsonGetFeatures = YamahaMusiccast::CallAPI("GET", $this, "/YamahaExtendedControl/v1/system/getFeatures");
			$getFeatures = json_decode($jsonGetFeatures);
			foreach ($getFeatures->zone as $zone) {
				$zoneName = $zone->id;
				foreach ($zone->func_list as $func) {
					$this->createCmd($zoneName. '_' .$func . '_state');
				}
				$this->createCmd($zoneName. '_audio_error');
				$this->createCmd($zoneName. '_audio_format');
				$this->createCmd($zoneName. '_audio_fs');
			}

			$this->createCmd('netusb_input');
			$this->createCmd('netusb_play_queue_type');
			$this->createCmd('netusb_playback');
			$this->createCmd('netusb_repeat');
			$this->createCmd('netusb_shuffle');
			$this->createCmd('netusb_play_time');
			$this->createCmd('netusb_total_time');
			$this->createCmd('netusb_artist');
			$this->createCmd('netusb_album');
			$this->createCmd('netusb_track');
			$this->createCmd('netusb_albumart_url');
			$this->createCmd('netusb_albumart_id');
			$this->createCmd('netusb_usb_devicetype');
			$this->createCmd('netusb_usb_auto_stopped');
			$this->createCmd('netusb_attribute');
			$this->createCmd('netusb_repeat_available');
			$this->createCmd('netusb_shuffle_available');
		}
	}

	public function createCmd($name, $type = 'info', $subtype = 'string', $repeatEventManagement = 'never') {
		$cmd = $this->getCmd(null, $name);
		if (!is_object($cmd)) {
			$cmd = new YamahaMusiccastCmd();
			$cmd->setLogicalId($name);
			$cmd->setName(__($name, __FILE__));
		}
		$cmd->setType($type);
		$cmd->setSubType($subtype);
		$cmd->setConfiguration('repeatEventManagement', $repeatEventManagement);
		$cmd->setEqLogic_id($this->getId());
		$cmd->save();
	}

	public function postSave() {
		$jsonGetFeatures = YamahaMusiccast::CallAPI("GET", $this, "/YamahaExtendedControl/v1/system/getFeatures");
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
		$devices = self::byType('YamahaMusiccast');
		$date = date("Y-m-d H:i:s");
		foreach ($devices as $device) {
			if ($device->getIsEnable() == 0) {
				continue;
			}
			$lastCallAPI = $device->getStatus('lastCallAPI');
			$deltaSeconds = strtotime($date) - strtotime($lastCallAPI);
			if($deltaSeconds > (5*60)) {
				$result = YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/getDeviceInfo");
				log::add('YamahaMusiccast', 'debug', $deltaSeconds + 'Appel du Cron5 ' . $result);
			}
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
				$device->checkAndUpdateCmd('netusb_play_time', $play_time);
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
				YamahaMusiccast::callNetusbGetPlayInfo($device);
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
			YamahaMusiccast::callZoneGetSignalInfo($device, $zoneName);
		}
	}

	static function callZoneGetSignalInfo($device, $zoneName) {
		$json = YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zoneName/getSignalInfo");
		$result = json_decode($json);
		$audio = $result->audio;
		if (!empty($audio)) {
			if (!empty($audio->error)) {
				$device->checkAndUpdateCmd($zoneName. '_audio_error', $audio->error);
			}
			if (!empty($audio->format)) {
				$device->checkAndUpdateCmd($zoneName. '_audio_format', $audio->format);
			}
			if (!empty($audio->fs)) {
				$device->checkAndUpdateCmd($zoneName. '_audio_fs', $audio->fs);
			}
		}
	}

	static function callNetusbGetPlayInfo($device) {
		$json = YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/getPlayInfo");
		$result = json_decode($json);

		if (!empty($result->input)) {
			$device->checkAndUpdateCmd('netusb_input', $result->input);
		}
		if (!empty($result->play_queue_type)) {
			$device->checkAndUpdateCmd('netusb_play_queue_type', $result->play_queue_type);
		}
		if (!empty($result->playback)) {
			$device->checkAndUpdateCmd('netusb_playback', $result->playback);
		}
		if (!empty($result->repeat)) {
			$device->checkAndUpdateCmd('netusb_repeat', $result->repeat);
		}
		if (!empty($result->shuffle)) {
			$device->checkAndUpdateCmd('netusb_shuffle', $result->shuffle);
		}
		if (!empty($result->play_time)) {
			$device->checkAndUpdateCmd('netusb_play_time', $result->play_time);
		}
		if (!empty($result->total_time)) {
			$device->checkAndUpdateCmd('netusb_total_time', $result->total_time);
		}
		if (!empty($result->artist)) {
			$device->checkAndUpdateCmd('netusb_artist', $result->artist);
		}
		if (!empty($result->album)) {
			$device->checkAndUpdateCmd('netusb_album', $result->album);
		}
		if (!empty($result->track)) {
			$device->checkAndUpdateCmd('netusb_track', $result->track);
		}
		if (!empty($result->albumart_url)) {
			$device->checkAndUpdateCmd('netusb_albumart_url', $result->albumart_url);
		}
		if (!empty($result->albumart_id)) {
			$device->checkAndUpdateCmd('netusb_albumart_id', $result->albumart_id);
		}
		if (!empty($result->usb_devicetype)) {
			$device->checkAndUpdateCmd('netusb_usb_devicetype', $result->usb_devicetype);
		}
		if (!empty($result->usb_auto_stopped)) {
			$device->checkAndUpdateCmd('netusb_usb_auto_stopped', $result->usb_auto_stopped);
		}
		if (!empty($result->usb_auto_stopped)) {
			$device->checkAndUpdateCmd('netusb_usb_auto_stopped', $result->usb_auto_stopped);
		}
		if (!empty($result->attribute)) {
			$device->checkAndUpdateCmd('netusb_attribute', $result->attribute);
		}
		if (!empty($result->repeat_available)) {
			$device->checkAndUpdateCmd('netusb_repeat_available', $result->repeat_available);
		}
		if (!empty($result->shuffle_available)) {
			$device->checkAndUpdateCmd('netusb_shuffle_available', $result->shuffle_available);
		}
	}

	static function callZoneGetStatus($device, $zoneName) {
		$jsonGetStatusZone = YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zoneName/getStatus");
		$getStatusZone = json_decode($jsonGetStatusZone);
		$device->checkAndUpdateCmd($zoneName. '_power_state', $getStatusZone->power);
		$device->checkAndUpdateCmd($zoneName. '_volume_state', $getStatusZone->volume);
		$device->checkAndUpdateCmd($zoneName. '_mute_state', $getStatusZone->mute);
		$device->checkAndUpdateCmd($zoneName. '_input_change_state', $getStatusZone->input);
		$device->checkAndUpdateCmd($zoneName. '_sound_program_state', $getStatusZone->sound_program);
		$device->checkAndUpdateCmd($zoneName. '_link_audio_quality_state', $getStatusZone->link_audio_quality);
		$device->checkAndUpdateCmd($zoneName. '_link_audio_delay_state', $getStatusZone->link_audio_delay);
		$device->checkAndUpdateCmd($zoneName. '_link_control_state', $getStatusZone->link_control);
	}

	static function CallAPI($method, $device, $path, $data = false) {
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
					$path = sprintf("%s?%s", $path, http_build_query($data));
				}
		}
		// Optional Authentication:
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$header[0] = "Content-Type: application/json";
		$header[1] = "X-AppName: Musiccast/Jeedom";
		$header[2] = "X-AppPort: $port";
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$url = "http://" . $device->getLogicalId() . $path;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);

		curl_close($curl);
		
		$device->setStatus('lastCallAPI', date("Y-m-d H:i:s"));

		return $result;
	}

	/*	 * **********************Getteur Setteur*************************** */
}
