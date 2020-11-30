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
require_once __DIR__ . '/../../../../core/php/core.inc.php';
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

	public function createCmd($name, $type = 'info', $subtype = 'string', $icon = false, $generic_type = null, $configurationList = [], $placeholderList = []) {
		$cmd = $this->getCmd(null, $name);
		if (!is_object($cmd)) {
			$cmd = new YamahaMusiccastCmd();
			$cmd->setLogicalId($name);
			$cmd->setName(__($name, __FILE__));
		}
		$cmd->setType($type);
		$cmd->setSubType($subtype);
		$cmd->setGeneric_type($generic_type);
		if($icon) {
			$cmd->setDisplay('icon',$icon);
		}
		foreach ($configurationList as $key => $value){
			$cmd->setConfiguration($key, $value);
		}
		foreach ($placeholderList as $value){
			$cmd->setDisplay($value . '_placeholder', __('placeholder.'.$value, __FILE__));
		}
		$cmd->setEqLogic_id($this->getId());
		return $cmd;
	}

	public function preUpdate() {
		
	}

	public function postUpdate() {
		
	}

	public function preRemove() {
		rrmdir(__DIR__ . '/../../../../plugins/' . __CLASS__ . '/ressources/' . $this->getId());
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
			$replace['#' . $cmd->getLogicalId() . '#'] = str_replace(array("\'", "'"), array("'", "\'"), $cmd->execCmd());
			$replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();
			if ($cmd->getLogicalId() == 'encours') {
				$replace['#thumbnail#'] = $cmd->getDisplay('icon');
			}
			if ($cmd->getIsHistorized() == 1) {
				$replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
			}
		}

		foreach ($this->getCmd('action') as $cmd) {
			$replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
			if(!empty($cmd->getDisplay('icon'))) {
				$replace['#' . $cmd->getLogicalId() . '_icon#'] = $cmd->getDisplay('icon');
			} else {
				$replace['#' . $cmd->getLogicalId() . '_icon#'] = "<i class='icon divers-vlc1' title='Veuillez mettre une icon à l’action : ". $cmd->getLogicalId() ."'></i>";
			}
		}

		$program_select = $this->getCmd(null, 'sound_program_change');
		if (!empty($program_select) && $program_select->getConfiguration('listValue', '') != '') {
			$replace['#sound_program_change_select#'] = $program_select->toHtml();
		} else {
			$replace['#sound_program_change_select#'] = '';
		}

		$netusb_recall_preset = $this->getCmd(null, 'netusb_recall_preset');
		if (!empty($netusb_recall_preset) && $netusb_recall_preset->getConfiguration('listValue', '') != '') {
			$replace['#netusb_recall_preset#'] = $netusb_recall_preset->toHtml();
			$replace['#netusb_recall_preset_change_list#'] = $netusb_recall_preset->getConfiguration('listValue', '');
		} else {
			$replace['#netusb_recall_preset#'] = '';
			$replace['#netusb_recall_preset_change_list#'] = '';
		}

		$netusb_recall_recent = $this->getCmd(null, 'netusb_recall_recent');
		if (!empty($netusb_recall_recent) && $netusb_recall_recent->getConfiguration('listValue', '') != '') {
			$replace['#netusb_recall_recent#'] = $netusb_recall_recent->toHtml();
			$replace['#netusb_recall_recent_change_list#'] = $netusb_recall_recent->getConfiguration('listValue', '');
		} else {
			$replace['#netusb_recall_recent#'] = '';
			$replace['#netusb_recall_recent_change_list#'] = '';
		}

		$input_select = $this->getCmd(null, 'input_change');
		if (!empty($input_select) && $input_select->getConfiguration('listValue', '') != '') {
			$replace['#input_change_select#'] = $input_select->toHtml();
			$replace['#input_change_list#'] = $input_select->getConfiguration('listValue', '');
		} else {
			$replace['#input_change_select#'] = '';
		}

		$equalizer_low_change = $this->getCmd(null, 'equalizer_low_change');
		if (!empty($equalizer_low_change)) {
			$replace['#equalizer_low_change#'] = $equalizer_low_change->toHtml();
		} else {
			$replace['#equalizer_low_change#'] = '';
		}

		$equalizer_mid_change = $this->getCmd(null, 'equalizer_mid_change');
		if (!empty($equalizer_mid_change)) {
			$replace['#equalizer_mid_change#'] = $equalizer_mid_change->toHtml();
		} else {
			$replace['#equalizer_mid_change#'] = '';
		}

		$equalizer_high_change = $this->getCmd(null, 'equalizer_high_change');
		if (!empty($equalizer_high_change)) {
			$replace['#equalizer_high_change#'] = $equalizer_high_change->toHtml();
		} else {
			$replace['#equalizer_high_change#'] = '';
		}

		$volume_change = $this->getCmd(null, 'volume_change');
		if (!empty($volume_change)) {
			$replace['#volume_change#'] = $volume_change->toHtml();
		} else {
			$replace['#volume_change#'] = '';
		}

		$img = '/plugins/' . __CLASS__ . '/ressources/input/' . $replace['#input#'] . '.png';
		if (file_exists(__DIR__ . '/../../../..' . $img)) {
			$replace['#input_icon#'] = $img;
		} else {
			$replace['#input_icon#'] = '/plugins/' . __CLASS__ . '/plugin_info/' . __CLASS__ . '.png';
		}
		/* ------------ N'ajouter plus de code apres ici------------ */

		return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, __CLASS__, __CLASS__)));
	}

	public function getImage() {
		$type = $this->getConfiguration('model_name');
		if(isset($type)) {
			$url = "plugins/" . __CLASS__ . "/core/img/" . $type .".jpg";
			if (file_exists($url)) {
				return $url;
			}
		}
		return parent::getImage();
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
		$port = config::byKey('socket.port', __CLASS__);
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or log::add(__CLASS__, 'error', 'Création du deamon_info refusée');
		if (!socket_connect($sock, "127.0.0.1", $port)) {
			log::add(__CLASS__, 'error', 'Connexion impossible pour deamon_info');
			$return['state'] = 'ko';
			$return['log'] = "Connexion impossible pour deamon_info";
		} else if (!socket_write($sock, "test")) {
			log::add(__CLASS__, 'error', 'Envoie du test en echec deamon_info');
			$return['state'] = 'ko';
			$return['log'] = 'Envoie du test en echec deamon_info';
		} else {
			$cron = cron::byClassAndFunction(__CLASS__, 'socket_start');
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
		$cron = cron::byClassAndFunction(__CLASS__, 'socket_start');
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
		$cron = cron::byClassAndFunction(__CLASS__, 'socket_start');
		if (!is_object($cron)) {
			throw new Exception(__('Tache cron introuvable', __FILE__));
		}
		YamahaMusiccast::socket_stop();
		$cron->halt();
	}

	public static function socket_start() {
		$port = config::byKey('socket.port', __CLASS__);
		log::add(__CLASS__, 'debug', 'Lancement d’un socket sur le port ' . $port);
		$socket = new YamahaMusiccastSocket("0.0.0.0", $port);
		$socket->run();
		YamahaMusiccast::callYamahaMusiccast();
	}

	public static function socket_stop() {
		$port = config::byKey('socket.port', __CLASS__);
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or log::add(__CLASS__, 'error', 'Création du socket_stop refusée');
		socket_connect($sock, "127.0.0.1", $port) or log::add(__CLASS__, 'error', 'Connexion impossible pour socket_stop');
		socket_write($sock, "stop");
		//socket_close($sock);
	}

	public static function cron5() {
		log::add(__CLASS__, 'debug', 'Appel du Cron5');
		YamahaMusiccast::callYamahaMusiccast();
	}

	public static function searchAndSaveDeviceList() {
		$return = array();
		$ipList = YamahaMusiccast::searchDeviceIpList();
		foreach ($ipList as $ip) {
			array_push($return, YamahaMusiccast::saveDeviceIp($ip));
		}
		return $return;
	}

	public static function saveDeviceIp($ip) {
		$deviceZoneList = array();
		$getNetworkStatus = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getNetworkStatus");
		if ($getNetworkStatus === false) {
			throw new Exception(__('L’appareil avec ip ' . $this->getLogicalId() . ' n’est pas joingnable ou n’existant !'), __FILE__);
		}
		$getDeviceInfo = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getDeviceInfo");

		$getFeatures = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getFeatures");
		$getLocationInfo = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getLocationInfo");
		
		if (isset($getFeatures) && isset($getLocationInfo)) {
			$musiccastId = $getLocationInfo->id;
			$musiccastName = $getLocationInfo->name;
			//YamahaMusiccast::setConfiguration('musiccastId', $musiccastId);
			//YamahaMusiccast::setConfiguration('musiccastName', $musiccastName);
			$musiccastZoneList = $getLocationInfo->zone_list;
			$fonc_list_features = $getFeatures->system->func_list;
			foreach ($getFeatures->zone as $zone) {
				$zoneName = $zone->id;
				$logicalId = $ip . ':' . $zoneName;
				$device = YamahaMusiccast::byLogicalId($logicalId, __CLASS__);
				if (!is_object($device)) {
					$device = new YamahaMusiccast();
					$device->setEqType_name(__CLASS__);
				}
				$device->setName($logicalId);
				$device->setLogicalId($logicalId);
				$device->setCategory('multimedia', 1);
				if($musiccastZoneList->$zoneName) {
					$device->setIsVisible(1);
					$device->setIsEnable(1);
				} else {
					$device->setIsVisible(0);
					$device->setIsEnable(0);
				}
				$device->setConfiguration('zone', $zoneName);
				$device->setConfiguration('ip', $ip);
				$device->save();

				$getStatusZone = YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zoneName/getStatus");

				$deviceDir = __DIR__ . '/../../../../plugins/' . __CLASS__ . '/ressources/' . $device->getId() . '/';
				if (!file_exists($deviceDir)) {
					mkdir($deviceDir, 0700);
				}

				if(in_array("wired_lan", $fonc_list_features)) {
					$device->createCmd('set_wired_lan', 'action', 'other')->save();
				}
				if(in_array("wireless_lan", $fonc_list_features)) {
					$device->createCmd('set_wirless_lan', 'action', 'other')->save();
				}
				if(in_array("wireless_direct", $fonc_list_features)) {
					$device->createCmd('set_wirless_direct', 'action', 'other')->save();
				}
				if(in_array("extend_1_band", $fonc_list_features)) {
				}
				if(in_array("dfs_option", $fonc_list_features)) {
				}
				if(in_array("network_standby_auto", $fonc_list_features)) {
				}
				if(in_array("network_standby", $fonc_list_features)) {

				}
				if(in_array("bluetooth_standby", $fonc_list_features)) {
					$device->createCmd('bluetooth_standby_state')->save();
					$device->createCmd('disconnect_bluetooth_device', 'action', 'other')->save();
					$device->createCmd('connect_bluetooth_device', 'action', 'other')->save();
					$device->createCmd('update_bluetooth_device_list', 'action', 'other')->save();

				}
				if(in_array("bluetooth_tx_setting", $fonc_list_features)) {
					$device->createCmd('bluetooth_tx_setting_state')->save();
					$device->createCmd('set_bluetooth_tx_setting', 'action', 'other')->save();

				}
				if(in_array("auto_power_standby", $fonc_list_features)) {
					$device->createCmd('auto_power_standby_state')->save();
					$device->createCmd('auto_power_standby_on', 'action', 'other')->save();
					$device->createCmd('auto_power_standby_off', 'action', 'other')->save();

				}
				if(in_array("ir_sensor", $fonc_list_features)) {
					$device->createCmd('ir_sensor_state')->save();
					$device->createCmd('ir_sensor_on', 'action', 'other')->save();
					$device->createCmd('ir_sensor_off', 'action', 'other')->save();

				}
				if(in_array("speaker_a", $fonc_list_features)) {
					$device->createCmd('speaker_a_state')->save();
					$device->createCmd('speaker_a_on', 'action', 'other')->save();
					$device->createCmd('speaker_a_off', 'action', 'other')->save();
				}
				if(in_array("dimmer", $fonc_list_features)) {
					$device->createCmd('dimmer_state')->save();
					$device->createCmd('dimmer', 'action', 'other')->save();
				}
				if(in_array("speaker_b", $fonc_list_features)) {
					$device->createCmd('speaker_b_state')->save();
					$device->createCmd('speaker_b_on', 'action', 'other')->save();
					$device->createCmd('speaker_b_off', 'action', 'other')->save();
				}
				if(in_array("zone_b_volume_sync", $fonc_list_features)) {
					$device->createCmd('zone_b_volume_sync_state')->save();
					$device->createCmd('zone_b_volume_sync_on', 'action', 'other')->save();
					$device->createCmd('zone_b_volume_sync_off', 'action', 'other')->save();
				}
				if(in_array("headphone", $fonc_list_features)) {
					$device->createCmd('headphone_state')->save();
				}
				if(in_array("hdmi_out_1", $fonc_list_features)) {
					$device->createCmd('hdmi_out_1_state')->save();
					$device->createCmd('hdmi_out_1_on', 'action', 'other')->save();
					$device->createCmd('hdmi_out_1_off', 'action', 'other')->save();
				}
				if(in_array("hdmi_out_2", $fonc_list_features)) {
					$device->createCmd('hdmi_out_2_state')->save();
					$device->createCmd('hdmi_out_2_on', 'action', 'other')->save();
					$device->createCmd('hdmi_out_2_off', 'action', 'other')->save();
				}
				if(in_array("hdmi_out_3", $fonc_list_features)) {
					$device->createCmd('hdmi_out_3_state')->save();
					$device->createCmd('hdmi_out_3_on', 'action', 'other')->save();
					$device->createCmd('hdmi_out_3_off', 'action', 'other')->save();
				}
				if(in_array("airplay", $fonc_list_features)) {
					$device->createCmd('set_air_play_pin', 'action', 'other')->save();
				}
				if(in_array("stereo_pair", $fonc_list_features)) {

				}
				if(in_array("speaker_settings", $fonc_list_features)) {

				}
				if(in_array("disklavier_settings", $fonc_list_features)) {

				}
				if(in_array("background_download", $fonc_list_features)) {

				}
				if(in_array("remote_info", $fonc_list_features)) {

				}
				if(in_array("network_reboot", $fonc_list_features)) {

				}
				if(in_array("system_reboot", $fonc_list_features)) {

				}
				if(in_array("auto_play", $fonc_list_features)) {
					$device->createCmd('auto_play_state')->save();
					$device->createCmd('auto_play_on', 'action', 'other')->save();
					$device->createCmd('auto_play_off', 'action', 'other')->save();
				}
				if(in_array("speaker_pattern", $fonc_list_features)) {
					$device->createCmd('speaker_pattern_state_state')->save();
				}
				if(in_array("party_mode", $fonc_list_features)) {
					$device->createCmd('party_mode_state')->save();
				}
				$fonc_list_zone = $zone->func_list;
				if(in_array("power", $fonc_list_zone)) {
					$device->createCmd('power_state')->save();
					$device->createCmd('power_on', 'action', 'other', false, 'ENERGY_ON')->save();
					$device->createCmd('power_off', 'action', 'other', false, 'ENERGY_OFF')->save();
				}
				if(in_array("sleep", $fonc_list_zone)) {
					
				}
				if(in_array("volume", $fonc_list_zone)) {
					$config_volume_change['minValue'] = 0;
					if (!empty($getStatusZone->max_volume)) {
						$config_volume_change['maxValue'] = $getStatusZone->max_volume;
					}
					$volume_state = $device->createCmd('volume_state');
					$volume_state->save();
					$volume = $device->createCmd('volume_change', 'action', 'slider', false, 'SET_VOLUME', $config_volume_change)->setValue($volume_state->getId())->save();
					$device->createCmd('max_volume')->save();
				}
				if(in_array("mute", $fonc_list_zone)) {
					$device->createCmd('mute_on', 'action', 'other')->save();
					$device->createCmd('mute_off', 'action', 'other')->save();
					$device->createCmd('mute_state')->save();
				}
				if(in_array("sound_program", $fonc_list_zone)) {
					$sound_program_state = $device->createCmd('sound_program_state');
					$sound_program_state->save();
					$sound_program_change = $device->createCmd('sound_program_change', 'action', 'select', false , null, $config_sound_program_change)->setValue($sound_program_state->getId())->save();
				}
				if(in_array("surround_3d", $fonc_list_zone)) {
					
				}
				if(in_array("direct", $fonc_list_zone)) {
					
				}
				if(in_array("pure_direct", $fonc_list_zone)) {
					
				}
				if(in_array("enhancer", $fonc_list_zone)) {
					
				}
				if(in_array("tone_control", $fonc_list_zone)) {
					
				}
				if(in_array("equalizer", $fonc_list_zone)) {
					$device->createCmd('equalizer_mode')->save();
					$config_volume_change['minValue'] = -10;
					$config_volume_change['maxValue'] = 10;
					$equalizer_low = $device->createCmd('equalizer_low');
					$equalizer_low->save();
					$device->createCmd('equalizer_low_change', 'action', 'slider', false, null, $config_volume_change)->setValue($equalizer_low->getId())->save();
					$equalizer_mid = $device->createCmd('equalizer_mid');
					$equalizer_mid->save();
					$device->createCmd('equalizer_mid_change', 'action', 'slider', false, null, $config_volume_change)->setValue($equalizer_mid->getId())->save();
					$equalizer_high = $device->createCmd('equalizer_high');
					$equalizer_high->save();
					$device->createCmd('equalizer_high_change', 'action', 'slider', false, null, $config_volume_change)->setValue($equalizer_high->getId())->save();
				}
				if(in_array("balance", $fonc_list_zone)) {
					
				}
				if(in_array("dialogue_level", $fonc_list_zone)) {
					
				}
				if(in_array("dialogue_lift", $fonc_list_zone)) {
					
				}
				if(in_array("bass_extension", $fonc_list_zone)) {
					
				}
				if(in_array("clear_voice", $fonc_list_zone)) {
					
				}
				if(in_array("signal_info", $fonc_list_zone)) {
					
				}
				if(in_array("subwoofer_volume", $fonc_list_zone)) {
					
				}
				if(in_array("prepare_input_change", $fonc_list_zone)) {
					
				}
				if(in_array("link_control", $fonc_list_zone)) {
					
				}
				if(in_array("link_audio_delay", $fonc_list_zone)) {
					
				}
				if(in_array("link_audio_quality", $fonc_list_zone)) {
					
				}
				if(in_array("scene", $fonc_list_zone)) {
					
				}
				if(in_array("contents_display", $fonc_list_zone)) {
					
				}
				if(in_array("cursor", $fonc_list_zone)) {
					
				}
				if(in_array("menu", $fonc_list_zone)) {
					
				}
				if(in_array("actual_volume", $fonc_list_zone)) {
					
				}
				if(in_array("audio_select", $fonc_list_zone)) {
					
				}
				if(in_array("surr_decoder_type", $fonc_list_zone)) {
					
				}
				if (!empty($getFeatures->tuner)) {
					$tuner = $getFeatures->tuner;
					if(!empty($tuner->func_list)){
						$fonc_list_tuner = $tuner->func_list;
						if(in_array("am", $fonc_list_tuner)) {

						}
						if(in_array("fm", $fonc_list_tuner)) {

						}
						if(in_array("rds", $fonc_list_tuner)) {

						}
						if(in_array("dab", $fonc_list_tuner)) {

						}
						if(in_array("hd_radio", $fonc_list_tuner)) {

						}
						if(in_array("fm_auto_preset", $fonc_list_tuner)) {

						}
						if(in_array("dab_initial_scan", $fonc_list_tuner)) {

						}
						if(in_array("dab_tune_aid", $fonc_list_tuner)) {

						}
					}
				}
				if (!empty($getFeatures->netusb)) {
					$netusb = $getFeatures->netusb;
					if(!empty($netusb->func_list)){
						$fonc_list_netusb = $netusb->func_list;
						if(in_array("recent_info", $fonc_list_netusb)) {

						}
						if(in_array("play_queue", $fonc_list_netusb)) {

						}
						if(in_array("mc_playlist", $fonc_list_netusb)) {

						}
						if(in_array("streaming_service_use", $fonc_list_netusb)) {

						}
					}
				}
				$cmdInput = $device->createCmd('input');
				$cmdInput->save();
				$input_change_string = "";
				$device->createCmd('input_change', 'action', 'select', false , null)->setValue($cmdInput->getId())->save();

				$device->createCmd('audio_error')->save();
				$device->createCmd('audio_format')->save();
				$device->createCmd('audio_fs')->save();
				
				$device->createCmd('netusb_recall_recent_list')->save();
				$device->createCmd('netusb_recall_preset_list')->save();


				$device->createCmd('netusb_playback_play', 'action', 'other', '<i class="fas fa-play"></i>', 'MEDIA_RESUME')->save();
				$device->createCmd('netusb_playback_stop', 'action', 'other', '<i class="fas fa-stop"></i>', 'MEDIA_STOP')->save();
				$device->createCmd('netusb_playback_pause', 'action', 'other', '<i class="fas fa-pause"></i>', 'MEDIA_PAUSE')->save();
				$device->createCmd('netusb_playback_play_pause', 'action', 'other', '<i class="fas fa-play"></i><i class="fas fa-pause"></i>')->save();
				$device->createCmd('netusb_playback_previous', 'action', 'other', '<i class="fas fa-step-backward"></i>', 'MEDIA_PREVIOUS')->save();
				$device->createCmd('netusb_playback_next', 'action', 'other', '<i class="fas fa-step-forward"></i>', 'MEDIA_NEXT')->save();
				$device->createCmd('netusb_playback_fast_reverse_start', 'action', 'other')->save();
				$device->createCmd('netusb_playback_fast_reverse_end', 'action', 'other')->save();
				$device->createCmd('netusb_playback_fast_forward_start', 'action', 'other')->save();
				$device->createCmd('netusb_playback_fast_forward_end', 'action', 'other')->save();

				$device->createCmd('netusb_shuffle_off', 'action', 'other')->save();
				$device->createCmd('netusb_shuffle_on', 'action', 'other')->save();
				$device->createCmd('netusb_shuffle_songs', 'action', 'other')->save();
				$device->createCmd('netusb_shuffle_albums', 'action', 'other')->save();
				$device->createCmd('netusb_repeat_off', 'action', 'other')->save();
				$device->createCmd('netusb_repeat_one', 'action', 'other')->save();
				$device->createCmd('netusb_repeat_all', 'action', 'other')->save();

				$device->createCmd('netusb_input')->save();
				$device->createCmd('netusb_play_queue_type')->save();
				$device->createCmd('netusb_playback')->save();
				$device->createCmd('netusb_repeat')->save();
				$device->createCmd('netusb_shuffle')->save();
				$device->createCmd('netusb_play_time')->save();
				$device->createCmd('netusb_total_time')->save();
				$device->createCmd('netusb_artist')->save();
				$device->createCmd('netusb_album')->save();
				$device->createCmd('netusb_track')->save();
				$device->createCmd('netusb_albumart_url')->save();
				$device->createCmd('netusb_albumart_id')->save();
				$device->createCmd('netusb_usb_devicetype')->save();
				$device->createCmd('netusb_usb_auto_stopped')->save();
				$device->createCmd('netusb_attribute')->save();
				$device->createCmd('netusb_repeat_available')->save();
				$device->createCmd('netusb_shuffle_available')->save();
				if($zoneName === 'main') {
					$device->setName($getNetworkStatus->network_name);
				} else {
					$device->setName($getNetworkStatus->network_name . " (" . $zoneName . ")");
				}
				array_push($deviceZoneList, [
					"name" => $getNetworkStatus->network_name ,
					"zone" => $zoneName
				]);
				$device->setConfiguration('model_name', $getDeviceInfo->model_name);
				$device->save();
				YamahaMusiccast::callZoneGetStatus($device, $zoneName);
				YamahaMusiccast::callGetPresetInfoNetusb($device);
				YamahaMusiccast::callGetNetusbRecentInfo($device);
				YamahaMusiccast::callSystemNameText($device);
			}
		}
		return $deviceZoneList;
	}

	public static function searchDeviceIpList() {
		log::add(__CLASS__, 'debug', 'searchDeviceList');
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
		log::add(__CLASS__, 'debug', print_r($return, true));
		return $return;
	}

	public static function callYamahaMusiccast() {
		$devices = self::byType(__CLASS__);
		$date = date("Y-m-d H:i:s");
		foreach ($devices as $device) {
			if ($device->getIsEnable() == 0) {
				continue;
			}
			$lastCallAPI = $device->getStatus('lastCallAPI');
			$deltaSeconds = strtotime($date) - strtotime($lastCallAPI);
			if ($deltaSeconds > (4.5 * 60)) {
				$result = YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/getDeviceInfo");
				log::add(__CLASS__, 'debug', 'Mise à jour ' . $device->getName());
			}
		}
	}

	public static function traitement_message($host, $port, $json) {
		//log::add(__CLASS__, 'debug', 'Traitement  : ' . $host . ':' . $port . ' → ' . $json);
		$result = json_decode($json);
		$eqLogicByIPList = array();
		$eqLogicList = self::byType(__CLASS__);
		foreach ($eqLogicList as $eqLogic) {
			$ip = $eqLogic->getConfiguration('ip');
			$zone = $eqLogic->getConfiguration('zone');
			if ($ip === $host) {
				array_push($eqLogicByIPList, $eqLogic);
				$device_id = $result->device_id;
				if (!empty($result->system)) {
					$system = $result->system;
					if (!empty($system->bluetooth_info_updated)) {
						$bluetooth_info_updated = $system->bluetooth_info_updated;
						YamahaMusiccast::callBluetoothInfo($eqLogic);
					}
					if (!empty($system->func_status_updated)) {
						$func_status_updated = $system->func_status_updated;
						YamahaMusiccast::callGetFuncStatus($eqLogic);
					}
					if (!empty($system->location_info_updated)) {
						YamahaMusiccast::callGetLocationInfo($eqLogic);
					}
					if (!empty($system->name_text_updated)) {
						YamahaMusiccast::callSystemNameText($eqLogic);
					}
					if (!empty($system->speaker_settings_updated)) {
						$speaker_settings_updated = $system->speaker_settings_updated;
						log::add(__CLASS__, 'info', 'TODO: $speaker_settings_updated - Reserved ' . print_r($speaker_settings_updated, true));
					}
					if (!empty($system->stereo_pair_info_updated)) {
						$stereo_pair_info_updated = $system->stereo_pair_info_updated;
						log::add(__CLASS__, 'info', 'TODO: $stereo_pair_info_updated - Reserved ' . print_r($stereo_pair_info_updated, true));
					}
					if (!empty($system->tag_updated)) {
						$tag_updated = $system->tag_updated;
						log::add(__CLASS__, 'info', 'TODO: $tag_updated - Reserved ' . print_r($tag_updated, true));
					}
				}
				if (!empty($result->$zone)) {
					YamahaMusiccast::callZone($eqLogic, $zone, $result->$zone);
				}
				if (!empty($result->tuner)) {
					$tuner = $result->tuner;
					if (!empty($tuner->play_info_updated)) {
						$play_info_updated = $tuner->play_info_updated;
						log::add(__CLASS__, 'info', 'TODO: Mise à jour du isPlayInfoUpdated Main - Note: If so, pull renewed info using /tuner/getPlayInf' . print_r($play_info_updated, true));
					}
					if (!empty($tuner->preset_info_updated)) {
						$preset_info_updated = $tuner->preset_info_updated;
						log::add(__CLASS__, 'info', 'TODO: Mise à jour du isPresetInfoUpdated Main - Note: If so, pull renewed info using /tuner/getPresetInfo' . print_r($preset_info_updated, true));
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
						log::add(__CLASS__, 'info', 'TODO: Mise à jour du $play_error ' . print_r($play_error, true));
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
						log::add(__CLASS__, 'info', 'TODO: Mise à jour du $multiple_play_errors ' . print_r($multiple_play_errors, true));
					}
					if (!empty($netusb->play_message)) {
						$play_message = $netusb->play_message;
						log::add(__CLASS__, 'info', 'TODO: Playback related message ' . print_r($play_message, true));
					}
					if (!empty($netusb->account_updated)) {
						YamahaMusiccast::callGetNetusbAccountStatus($eqLogic);
					}
					if (!empty($netusb->play_time)) {
						$eqLogic->checkAndUpdateCmd('netusb_play_time', $netusb->play_time);
					}
					if (!empty($netusb->preset_info_updated)) {
						$preset_info_updated = $netusb->preset_info_updated;
						YamahaMusiccast::callGetPresetInfoNetusb($eqLogic);
					}
					if (!empty($netusb->recent_info_updated)) {
						YamahaMusiccast::callGetNetusbRecentInfo($eqLogic);
					}
					if (!empty($netusb->preset_control)) {
						$preset_control = $netusb->preset_control;
						switch ($preset_control->type) {
							case "recall":
								switch ($preset_control->result) {
									case "success":
										break;
									case "not_found":
										log::add(__CLASS__, 'warning', 'Le Favoris n°' . $preset_control->num . ' est non disponible.');
										break;
									default:
										log::add(__CLASS__, 'warning', 'TODO:  Ajouter le `result` de `preset_control=recall`. ' . print_r($preset_control, true));
										break;
								}
							break;
							case "store":
								switch ($preset_control->result) {
									case "success":
										break;
									default:
										log::add(__CLASS__, 'warning', 'TODO:  Ajouter le `result` de `preset_control=store`. ' . print_r($preset_control, true));
										break;
								}
							break;
							default:
								log::add(__CLASS__, 'warning', 'TODO:  Ajouter le `type` de `preset_control`. ' . print_r($preset_control, true));
								break;
						}
					}
					if (!empty($netusb->trial_status)) {
						$trial_status = $netusb->trial_status;
						log::add(__CLASS__, 'info', 'TODO:  Trial status of a Device. ' . print_r($trial_status, true));
					}
					if (!empty($netusb->trial_time_left)) {
						$trial_time_left = $netusb->trial_time_left;
						log::add(__CLASS__, 'info', 'TODO:  Remaining time of a trial. ' . print_r($trial_time_left, true));
					}
					if (!empty($netusb->play_info_updated)) {
						YamahaMusiccast::callNetusbGetPlayInfo($eqLogic);
					}
					if (!empty($netusb->list_info_updated)) {
						YamahaMusiccast::callGetNetusbListInfo($eqLogic);
					}
				}
				if (!empty($result->cd)) {
					$cd = $result->cd;
					log::add(__CLASS__, 'info', 'TODO: CD. ' . print_r($cd, true));
				}
				if (!empty($result->dist)) {
					$dist = $result->dist;
					log::add(__CLASS__, 'info', 'TODO: $dist. ' . print_r($dist, true));
				}
				if (!empty($result->clock)) {
					$clock = $result->clock;
					if (!empty($clock->settings_updated)) {
						$settings_updated = $clock->settings_updated;
						log::add(__CLASS__, 'info', 'TODO: isSettingsUpdated ' . print_r($settings_updated, true));
					}
				}
				//$eqLogic->refreshWidget();
			}
		}
		if (empty($eqLogicByIPList)) {
			log::add(__CLASS__, 'info', 'L’appareil ' . $host . ' n’existe plus');
		}
		//log::add(__CLASS__, 'debug', '$device_id' . $device_id . '       ' . print_r($result, true));
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
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/$zoneName/getSignalInfo");
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

	static function callBluetoothInfo($eqLogic) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/system/getBluetoothInfo");
		if (!empty($result->bluetooth_standby)) {
			$eqLogic->checkAndUpdateCmd('bluetooth_standby_state', $result->bluetooth_standby);
		}
		if (!empty($result->bluetooth_tx_setting)) {
			$eqLogic->checkAndUpdateCmd('bluetooth_tx_setting_state', $result->bluetooth_tx_setting);
		}
		if (!empty($result->bluetooth_device)) {
			log::add(__CLASS__, 'info', 'TODO:  Gestion des devices : $bluetooth_info_updated ' . print_r($result->bluetooth_device, true));
		}
	}

	static function callNetusbGetPlayInfo($eqLogic) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/netusb/getPlayInfo");
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
			$eqLogic->checkAndUpdateCmd('netusb_artist', str_replace("'", "’", $result->artist));
			// Lorsque la radio Web est allumé, seul `artist` est renseigné.
			$eqLogic->checkAndUpdateCmd('netusb_album', '');
			$eqLogic->checkAndUpdateCmd('netusb_track', '');
		}
		if (!empty($result->album)) {
			$eqLogic->checkAndUpdateCmd('netusb_album', str_replace("'", "’", $result->album));
		}
		if (!empty($result->track)) {
			$eqLogic->checkAndUpdateCmd('netusb_track', str_replace("'", "’", $result->track));
		}
		$fileAlbumARTUrl = '/plugins/' . __CLASS__ . '/ressources/' . $eqLogic->getId() . '/AlbumART.jpg';
		$fileAlbumART = __DIR__ . '/../../../..' . $fileAlbumARTUrl;
		if (!empty($result->albumart_url)) {
			$url = "http://" . $eqLogic->getConfiguration('ip') . $result->albumart_url;
			if(file_put_contents($fileAlbumART, file_get_contents($url))) {
				$eqLogic->checkAndUpdateCmd('netusb_albumart_url', $fileAlbumARTUrl . '?' . $result->albumart_id);
			} else {
				$eqLogic->checkAndUpdateCmd('netusb_albumart_url', '/plugins/' . __CLASS__ . '/plugin_info/' . __CLASS__ . '_icon.png');
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
		$getStatusZone = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/$zoneName/getStatus");
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
		if (!empty($getStatusZone->equalizer)) {
			$equalizer = $getStatusZone->equalizer;
			if (!empty($equalizer->mode)) {
				$eqLogic->checkAndUpdateCmd('equalizer_mode', $equalizer->mode);
			}
			if (!empty($equalizer->low)) {
				$eqLogic->checkAndUpdateCmd('equalizer_low', $equalizer->low);
			}
			if (!empty($equalizer->mid)) {
				$eqLogic->checkAndUpdateCmd('equalizer_mid', $equalizer->mid);
			}
			if (!empty($equalizer->high)) {
				$eqLogic->checkAndUpdateCmd('equalizer_high', $equalizer->high);
			}
		}
	}

	static function callGetPresetInfoNetusb($eqLogic) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/netusb/getPresetInfo");
		if (!empty($result->preset_info)) {
			$netusb_recall_preset_list = "";
			$int = 0;
			foreach ($result->preset_info as $preset_info) {
				++$int;
				$input = $preset_info->input;
				$text = $preset_info->text;
				$attribute = $preset_info->attribute;
				if ($input !== "unknown") {
					$netusb_recall_preset_list .= $int . "|". $input . "|" . $text . ";";
				}
			}
			if(!empty($netusb_recall_preset_list)) {
				$config_netusb_recall_preset['listValue'] = substr($netusb_recall_preset_list, 0, -1);
			} else {
				$config_netusb_recall_preset['listValue'] = $netusb_recall_preset_list;
			}
			$netusb_recall_preset = $eqLogic->createCmd('netusb_recall_preset', 'action', 'select', false , null, $config_netusb_recall_preset)
					->setValue(null)->save();
			$eqLogic->checkAndUpdateCmd('netusb_recall_preset_list',$config_netusb_recall_preset['listValue']);
		}
		if (!empty($result->func_list)) {
			// Returns a list of valid functions for Preset. (Recall/Store functions are always valid without specifically listed here)
			// Values: "clear" / "move"
			log::add(__CLASS__, 'debug', 'TODO: Gestion de func_list ' . print_r($result->func_list, true));
		}
	}

	static function callGetFuncStatus($eqLogic) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/system/getFuncStatus");
		if (!empty($result->auto_power_standby)) {
			$eqLogic->checkAndUpdateCmd('auto_power_standby_state', $result->auto_power_standby);
		}
		if (!empty($result->ir_sensor)) {
			$eqLogic->checkAndUpdateCmd('ir_sensor_state', $result->ir_sensor);
		}
		if (!empty($result->speaker_a)) {
			$eqLogic->checkAndUpdateCmd('speaker_a_state', $result->speaker_a);
		}
		if (!empty($result->speaker_b)) {
			$eqLogic->checkAndUpdateCmd('speaker_b_state', $result->speaker_b);
		}
		if (!empty($result->headphone)) {
			$eqLogic->checkAndUpdateCmd('headphone_state', $result->headphone);
		}
		if (!empty($result->dimmer)) {
			$eqLogic->checkAndUpdateCmd('dimmer_state', $result->dimmer);
		}
		if (!empty($result->zone_b_volume_sync)) {
			$eqLogic->checkAndUpdateCmd('zone_b_volume_sync_state', $result->zone_b_volume_sync);
		}
		if (!empty($result->hdmi_out_1)) {
			$eqLogic->checkAndUpdateCmd('hdmi_out_1_state', $result->hdmi_out_1);
		}
		if (!empty($result->hdmi_out_2)) {
			$eqLogic->checkAndUpdateCmd('hdmi_out_2_state', $result->hdmi_out_2);
		}
		if (!empty($result->hdmi_out_3)) {
			$eqLogic->checkAndUpdateCmd('hdmi_out_3_state', $result->hdmi_out_3);
		}
		if (!empty($result->auto_play)) {
			$eqLogic->checkAndUpdateCmd('auto_play_state', $result->auto_play);
		}
		if (!empty($result->speaker_pattern)) {
			$eqLogic->checkAndUpdateCmd('speaker_pattern_state', $result->speaker_pattern);
		}
		if (!empty($result->party_mode)) {
			$eqLogic->checkAndUpdateCmd('party_mode_state', $result->party_mode);
		}
	}

	static function callGetLocationInfo($eqLogic) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/system/getLocationInfo");
		if (!empty($result->id)) {
		}
		if (!empty($result->name)) {
		}
		if (!empty($result->zone_list)) {
			foreach ($result->zone_list as $zone) {
				
			}
		}
	}

	static function callGetNetusbListInfo($eqLogic) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/netusb/getListInfo?list_id=main&input=net_radio&index=0&size=8");
		if (!empty($result->service_list)) {
			log::add(__CLASS__, 'info', 'TODO: Gestion de la méthode netusb getListInfo - Warning:net_radio');
			foreach ($result->service_list as $service) {
				$id = $service->id;
				$registered = $service->registered;
				$login_status = $service->login_status;
				$username = $service->username;
				$type = $service->type;
				$trial_time_left = $service->trial_time_left;
			}
		}
	}

	static function callGetNetusbAccountStatus($eqLogic) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/netusb/getAccountStatus");
		if (!empty($result->service_list)) {
			log::add(__CLASS__, 'debug', 'TODO: Gestion de la méthode netusb getAccountStatus');
			foreach ($result->service_list as $service) {
				$id = $service->id;
				$registered = $service->registered;
				$login_status = $service->login_status;
				$username = $service->username;
				$type = $service->type;
				$trial_time_left = $service->trial_time_left;
			}
		}
	}

	static function callGetNetusbRecentInfo($eqLogic) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/netusb/getRecentInfo");
		if (!empty($result->recent_info)) {
			$recent_info_list = "";
			$int = 0;
			foreach ($result->recent_info as $recent_info) {
				++$int;
				$input = $recent_info->input;
				$text = $recent_info->text;
				$albumart_url = $recent_info->albumart_url;
				$play_count = $recent_info->play_count;
				$attribute = $recent_info->attribute;
				if ($input !== "unknown") {
					$recent_info_list .= $int . "|" . $input . "|" . $text. "|" . $albumart_url. "|" . $play_count . ";";
				}
			}
			if(!empty($recent_info_list)) {
				$config_netusb_recall_recent['listValue'] = substr($recent_info_list, 0, -1);
			} else {
				$config_netusb_recall_recent['listValue'] = $recent_info_list;
			}
			$netusb_recall_recent = $eqLogic->createCmd('netusb_recall_recent', 'action', 'select', false , null, $config_netusb_recall_recent)
					->setValue($eqLogic->getCmd(null, 'netusb_track')->getId())->save();
			$eqLogic->checkAndUpdateCmd('netusb_recall_recent_list',$config_netusb_recall_recent['listValue']);
		}
	}
	static function callSystemNameText($eqLogic) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/system/getNameText");
		log::add(__CLASS__, 'info', 'TODO: callSystemNameText ' . print_r($result, true));

		if (!empty($result->input_list)) {
			$input_change_string = "";
			foreach ($result->input_list as $input) {
				$input_change_string .= $input->id . "|".$input->text . ";";
			}
			$config_input_change['listValue'] = substr($input_change_string, 0, -1);
			
			$cmd = $eqLogic->getCmd(null, 'input_change');
			foreach ($config_input_change as $key => $value){
				$cmd->setConfiguration($key, $value);
			}
			$cmd->save();
			//$eqLogic->refreshWidget();
		}

		if (!empty($result->sound_program_list)) {
			$sound_program_list_string = "";
			foreach ($result->sound_program_list as $sound_program) {
				$sound_program_list_string .= $sound_program->id . "|".$sound_program->text . ";";
			}
			$config_sound_program_change['listValue'] = substr($sound_program_list_string, 0, -1);
		
			$cmd = $eqLogic->getCmd(null, 'sound_program_change');
			foreach ($config_sound_program_change as $key => $value){
				$cmd->setConfiguration($key, $value);
			}
			$cmd->save();
		}
	}

	static function CallAPI($method, $eqLogic, $path, $data = false) {
		$port = config::byKey('socket.port', __CLASS__);
		$name = config::byKey('socket.name', __CLASS__);
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
		$ip = null;
		if (is_string($eqLogic)) {
			$ip = $eqLogic;
		} else {
			$ip = $eqLogic->getConfiguration('ip');
		}
		$url = "http://" . $ip . $path;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = json_decode(curl_exec($curl));
		curl_close($curl);
		if (!is_string($eqLogic)) {
			$eqLogic->setStatus('lastCallAPI', date("Y-m-d H:i:s"));
		}
		$response_code = $result->response_code;
		$message = "KO";
		$logLevel = "error";
		switch ($response_code) {
			case 0:
				$message = "Successful request";
				$logLevel = false;
				break;
			case 1:
				$message = "Initializing";
				break;
			case 2:
				$message = "Internal Error";
				break;
			case 3:
				$message = "Invalid Request (A method did not exist, a method wasn’t appropriate etc)";
				break;
			case 4:
				$message = "Invalid Parameter (Out of range, invalid characters etc.)";
				break;
			case 5:
				$message = "Guarded (Unable to setup in current status etc.)";
				break;
			case 6:
				$message = "Time Out";
				break;
			case 99:
				$message = "Firmware Updating";
				break;
			case 100:
				$message = "Access Error";
				break;
			case 101:
				$message = "Other Errors";
				break;
			case 102:
				$message = "Wrong User Name";
				break;
			case 103:
				$message = "Wrong Password";
				break;
			case 104:
				$message = "Account Expired";
				break;
			case 105:
				$message = "Account Disconnected/Gone Off/Shut Down";
				break;
			case 106:
				$message = "Account Number Reached to the Limit";
				break;
			case 107:
				$message = "Server Maintenance";
				break;
			case 108:
				$message = "Invalid Account";
				break;
			case 109:
				$message = "License Error";
				break;
			case 110:
				$message = "Read Only Mode";
				break;
			case 111:
				$message = "Max Stations";
				break;
			case 112:
				$message = "Access Denied";
				break;
			case 113:
				$message = "There is a need to specify the additional destination Playlist";
				break;
			case 114:
				$message = "There is a need to create a new Playlist";
				break;
			case 115:
				$message = "Simultaneous logins has reached the upper limit";
				break;
			case 200:
				$message = "Linking in progress";
				break;
			case 201:
				$message = "Unlinking in prog";
				break;
			default :
				$message = "CallAPI - response_code not found : " . $response_code;
		}
		if ($logLevel) {
			log::add(__CLASS__, $logLevel, 'Resultat appel ' . $url . ' : ' . $response_code . ' - ' . $message);
		}
		return $result;
	}

	/*	 * **********************Getteur Setteur*************************** */
}
