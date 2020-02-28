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

		foreach ($this->getCmd('action') as $cmd) {
			$replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
			if(!empty($cmd->getDisplay('icon'))) {
				$replace['#' . $cmd->getLogicalId() . '_icon#'] = $cmd->getDisplay('icon');
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
		$getNetworkStatus = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getNetworkStatus");
		if ($getNetworkStatus === false) {
			throw new Exception(__('L’appareil avec ip ' . $this->getLogicalId() . ' n’est pas joingnable ou n’existant !'), __FILE__);
		}
		$getDeviceInfo = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getDeviceInfo");

		$getFeatures = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getFeatures");
		if ($getFeatures !== false) {
			$fonc_list_features = $getFeatures->system->func_list;
			foreach ($getFeatures->zone as $zone) {
				$zoneName = $zone->id;
				array_push($deviceZoneList, $zoneName);
				$logicalId = $ip . ':' . $zoneName;
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
					$device->createCmd('auto_power_standby_on', 'action', 'other')->save();
					$device->createCmd('auto_power_standby_off', 'action', 'other')->save();

				}
				if(in_array("ir_sensor", $fonc_list_features)) {
					$device->createCmd('ir_sensor_on', 'action', 'other')->save();
					$device->createCmd('ir_sensor_off', 'action', 'other')->save();

				}
				if(in_array("speaker_a", $fonc_list_features)) {
					$device->createCmd('speaker_a_on', 'action', 'other')->save();
					$device->createCmd('speaker_a_off', 'action', 'other')->save();
				}
				if(in_array("dimmer", $fonc_list_features)) {
					$device->createCmd('dimmer', 'action', 'other')->save();
				}
				if(in_array("speaker_b", $fonc_list_features)) {
					$device->createCmd('speaker_b_on', 'action', 'other')->save();
					$device->createCmd('speaker_b_off', 'action', 'other')->save();
				}
				if(in_array("zone_b_volume_sync", $fonc_list_features)) {
					$device->createCmd('zone_b_volume_sync_on', 'action', 'other')->save();
					$device->createCmd('zone_b_volume_sync_off', 'action', 'other')->save();
				}
				if(in_array("headphone", $fonc_list_features)) {

				}
				if(in_array("hdmi_out_1", $fonc_list_features)) {
					$device->createCmd('hdmi_out_1_on', 'action', 'other')->save();
					$device->createCmd('hdmi_out_1_off', 'action', 'other')->save();
				}
				if(in_array("hdmi_out_2", $fonc_list_features)) {
					$device->createCmd('hdmi_out_2_on', 'action', 'other')->save();
					$device->createCmd('hdmi_out_2_off', 'action', 'other')->save();
				}
				if(in_array("hdmi_out_3", $fonc_list_features)) {
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
					$device->createCmd('auto_play_on', 'action', 'other')->save();
					$device->createCmd('auto_play_off', 'action', 'other')->save();
				}
				if(in_array("speaker_pattern", $fonc_list_features)) {

				}
				if(in_array("party_mode", $fonc_list_features)) {

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
					$config_volume_change['maxValue'] = 50;// TODO:Utilisation de max_volume
					$device->createCmd('volume_change', 'action', 'slider', false, 'SET_VOLUME', $config_volume_change)->save();
					$device->createCmd('volume_change_step', 'action', 'other', false, 'SET_VOLUME')->save();
					$device->createCmd('volume_state')->save();
					$device->createCmd('max_volume')->save();
				}
				$getNameText = YamahaMusiccast::CallAPI("GET", $ip, "/YamahaExtendedControl/v1/system/getNameText");
				if(in_array("mute", $fonc_list_zone)) {
					$device->createCmd('mute_on', 'action', 'other')->save();
					$device->createCmd('mute_off', 'action', 'other')->save();
					$device->createCmd('mute_state')->save();
				}
				if(in_array("sound_program", $fonc_list_zone)) {
					$sound_program_list_string = "";
					if (!empty($getNameText->sound_program_list)) {
						$sound_program_list = $getNameText->sound_program_list;
						foreach ($sound_program_list as $sound_program) {
							$sound_program_list_string .= $sound_program->id . "|".$sound_program->text . ";";
						}
					}
					
					$config_sound_program_change['listValue'] = substr($sound_program_list_string, 0, -1);
					$device->createCmd('sound_program_change', 'action', 'select', false , null, $config_sound_program_change)->save();
					$device->createCmd('sound_program_state')->save();
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
				$device->createCmd('input')->save();
				$input_change_string = "";
				if (!empty($getNameText->input_list)) {
					$input_list = $getNameText->input_list;
					foreach ($input_list as $input) {
						$input_change_string .= $input->id . "|".$input->text . ";";
					}
				}
				$config_input_change['listValue'] = substr($input_change_string, 0, -1);
				$device->createCmd('input_change', 'action', 'select', false , null, $config_input_change)->save();

				$device->createCmd('audio_error')->save();
				$device->createCmd('audio_format')->save();
				$device->createCmd('audio_fs')->save();


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
				$device->setConfiguration('model_name', $getDeviceInfo->model_name);
				$device->save();
				YamahaMusiccast::callZoneGetStatus($device, $zoneName);
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
		$eqLogicByIPList = array();
		$eqLogicList = self::byType('YamahaMusiccast');
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
						YamahaMusiccast::callBluetoothInfo($eqLogic, $bluetooth_info_updated);
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
					if (!empty($system->tag_updated)) {
						$tag_updated = $system->tag_updated;
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
		if (empty($eqLogicByIPList)) {
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

	static function callBluetoothInfo($eqLogic, $bluetooth_info_updated) {
		$result = YamahaMusiccast::CallAPI("GET", $eqLogic, "/YamahaExtendedControl/v1/system/getBluetoothInfo");
		if (!empty($result->bluetooth_standby)) {
			$eqLogic->checkAndUpdateCmd('bluetooth_standby_state', $result->bluetooth_standby);
		}
		if (!empty($result->bluetooth_tx_setting)) {
			$eqLogic->checkAndUpdateCmd('bluetooth_tx_setting_state', $result->bluetooth_tx_setting);
		}
		if (!empty($result->bluetooth_device)) {
			log::add('YamahaMusiccast', 'info', 'TODO:  Gestion des devices : $bluetooth_info_updated ' . print_r($result->bluetooth_device, true));
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
			$url = "http://" . $eqLogic->getConfiguration('ip') . $result->albumart_url;
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
			log::add('YamahaMusiccast', $logLevel, 'Resultat appel ' . $url . ' : ' . $response_code . ' - ' . $message);
		}
		return $result;
	}

	/*	 * **********************Getteur Setteur*************************** */
}
