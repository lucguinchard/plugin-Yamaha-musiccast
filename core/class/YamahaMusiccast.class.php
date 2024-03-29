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
	const main = "main";
	const url_v1 = "/YamahaExtendedControl/v1/";
	const url_v1_system = YamahaMusiccast::url_v1 ."system/";
	const url_v1_netusb = YamahaMusiccast::url_v1 ."netusb/";
	const url_v1_tuner = YamahaMusiccast::url_v1 ."tuner/";
	const url_v1_dist = YamahaMusiccast::url_v1 ."dist/";


	/*	 * ***********************Methode static*************************** */

	public static function cron() {
		log::add(__CLASS__, 'debug', 'Appel du Cron');
		YamahaMusiccast::callYamahaMusiccast();
	}


	/*	 * *********************Méthodes d'instance************************* */

	public function preInsert() {
		
	}

	public function postInsert() {
		
	}

	public function createCmd($name, $type = 'info', $subtype = 'string', $icon = false, $generic_type = null, $configurationList = [], $displayList = [], $templateList = []) {
		$cmd = $this->getCmd(null, $name);
		if (!is_object($cmd)) {
			$cmd = new YamahaMusiccastCmd();
			$cmd->setLogicalId($name);
			$cmd->setName(__($name, __FILE__));
		}
		$cmd->setType($type);
		$cmd->setSubType($subtype);
		$cmd->setGeneric_type($generic_type);
		if ($icon) {
			$cmd->setDisplay('icon', $icon);
		}
		foreach ($configurationList as $key => $value) {
			$cmd->setConfiguration($key, $value);
		}
		foreach ($displayList as $key => $value) {
			$cmd->setDisplay($key, $value);
		}
		foreach ($templateList as $key => $value) {
			$cmd->setTemplate($key, $value);
		}
		$cmd->setEqLogic_id($this->getId());
		return $cmd;
	}

	public function preUpdate() {
		
	}

	public function postUpdate() {
		
	}

	public function preRemove() {
		rrmdir(__DIR__ . '/../../../../plugins/' . __CLASS__ . '/data/' . $this->getId());
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
			$replace['#' . $cmd->getLogicalId() . '_html#'] = $cmd->toHtml();
			if (!empty($cmd->getDisplay('icon'))) {
				$replace['#' . $cmd->getLogicalId() . '_icon#'] = $cmd->getDisplay('icon');
			} else {
				$replace['#' . $cmd->getLogicalId() . '_icon#'] = "<i class='icon divers-vlc1' title='Veuillez mettre une icon à l’action : " . $cmd->getLogicalId() . "'></i>";
			}
		}

		if (empty($replace['#sound_program_change_html#'])) {$replace['#sound_program_change_html#'] = "";}
		if (empty($replace['#input_change_select_html#'])) {$replace['#input_change_select_html#'] = "";}
		if (empty($replace['#netusb_recall_preset_html#'])) {$replace['#netusb_recall_preset_html#'] = "";}
		if (empty($replace['#equalizer_high_change_html#'])) {$replace['#equalizer_high_change_html#'] = "";}
		if (empty($replace['#equalizer_mid_change_html#'])) {$replace['#equalizer_mid_change_html#'] = "";}
		if (empty($replace['#equalizer_low_change_html#'])) {$replace['#equalizer_low_change_html#'] = "";}
		if (empty($replace['#volume_change_html#'])) {$replace['#volume_change_html#'] = "";}
		if (empty($replace['#link_control_list_html#'])) {$replace['#link_control_list_html#'] = "";}
		if (empty($replace['#link_audio_quality_list_html#'])) {$replace['#link_audio_quality_list_html#'] = "";}
		if (empty($replace['#link_audio_delay_list_html#'])) {$replace['#link_audio_delay_list_html#'] = "";}
		if (empty($replace['#surr_decoder_type_list_html#'])) {$replace['#surr_decoder_type_list_html#'] = "";}
		if (empty($replace['#system_reboot_html#'])) {$replace['#system_reboot_html#'] = "";}
		if (empty($replace['#network_reboot_html#'])) {$replace['#network_reboot_html#'] = "";}
		if (empty($replace['#scene_change_html#'])) {$replace['#scene_change_html#'] = "";}

		$netusb_recall_preset = $this->getCmd(null, 'netusb_recall_preset');
		if (!empty($netusb_recall_preset) && $netusb_recall_preset->getConfiguration('listValue', '') != '') {
			$replace['#netusb_recall_preset_change_list#'] = $netusb_recall_preset->getConfiguration('listValue', '');
		}

		$tuner_band = $this->getCmd(null, 'tuner_band');
		$replace['#is_tuner#'] = is_object($tuner_band);

		$netusb_recall_recent = $this->getCmd(null, 'netusb_recall_recent');
		if (!empty($netusb_recall_recent) && $netusb_recall_recent->getConfiguration('listValue', '') != '') {
			$replace['#netusb_recall_recent_change_list#'] = $netusb_recall_recent->getConfiguration('listValue', '');
		}

		$input_select = $this->getCmd(null, 'input_change');
		if (!empty($input_select) && $input_select->getConfiguration('listValue', '') != '') {
			$replace['#input_change_list#'] = $input_select->getConfiguration('listValue', '');
		}

		$img = '/plugins/' . __CLASS__ . '/data/input/' . $replace['#input#'] . '.png';
		if (file_exists(__DIR__ . '/../../../..' . $img)) {
			$replace['#input_icon#'] = $img;
		} else {
			$replace['#input_icon#'] = '/plugins/' . __CLASS__ . '/plugin_info/' . __CLASS__ . '.png';
		}
		/* ------------ N'ajouter plus de code apres ici------------ */
		$replace['#lastCallAPI#'] = $this->getStatus('lastCallAPI');
		return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, __CLASS__, __CLASS__)));
	}

	public function getImage() {
		$type = $this->getConfiguration('model_name');
		if (!empty($type)) {
			$url = "/plugins/" . __CLASS__ . "/core/img/" . $type . ".png";
			if (file_exists(__DIR__ . "/../../../../$url")) {
				return $url;
			}
		}
		return parent::getImage();
	}

	function callZoneGetSignalInfo() {
		$zone = $this->getConfiguration('zone');
		$result = $this->callAPIGET(YamahaMusiccast::url_v1 . "$zone/getSignalInfo");
		if (!empty($result->audio)) {
			$audio = $result->audio;
			if (!empty($audio->error)) {
				$this->checkAndUpdateZoneCmd('audio_error', $audio->error);
			}
			if (!empty($audio->format)) {
				$this->checkAndUpdateZoneCmd('audio_format', $audio->format);
			}
			if (!empty($audio->fs)) {
				$this->checkAndUpdateZoneCmd('audio_fs', $audio->fs);
			}
		}
	}

	function callZoneGetStatus() {
		$zone = $this->getConfiguration('zone');
		$result = $this->callAPIGET(YamahaMusiccast::url_v1 . "$zone/getStatus");
		if (!empty($result->power)) {
			$power_state = $this->getCmd('info', 'power_state');
			$refresh = false;
			if(is_object($power_state) && $power_state->execCmd() === 'unreachable') {
				$refresh = true;
			}
			$this->checkAndUpdateZoneCmd('power_state', $result->power);
			if($refresh) {
				self::checkDistributionAll();
			}
		}
		if (!empty($result->sleep)) {
			$this->checkAndUpdateZoneCmd('sleep', $result->sleep);
		}
		if (!empty($result->max_volume)) {
			$this->checkAndUpdateZoneCmd('max_volume', $result->max_volume);
		}
		if (!empty($result->volume)) {
			$this->checkAndUpdateZoneCmd('volume_state', $result->volume);
		}
		if (!empty($result->mute)) {
			$this->checkAndUpdateZoneCmd('mute_state', $result->mute);
		}
		if (!empty($result->input)) {
			$this->checkAndUpdateZoneCmd('input', $result->input);
		}
		if (!empty($result->input_text)) {
			$this->checkAndUpdateZoneCmd(null, $result->input_text, 'TODO : Gestion de input_text');
		}
		if (!empty($result->distribution_enable)) {
			$this->checkAndUpdateZoneCmd('distribution_enable', $result->distribution_enable);
		} else {
			$this->checkAndUpdateZoneCmd('distribution_enable', false);
		}
		if (!empty($result->sound_program)) {
			$this->checkAndUpdateZoneCmd('sound_program_state', $result->sound_program);
		}
		if (!empty($result->surr_decoder_type)) {
			$this->checkAndUpdateZoneCmd('surr_decoder_type', $result->surr_decoder_type);
		}
		if (!empty($result->surround_3d)) {
			$this->checkAndUpdateZoneCmd('surround_3d', $result->surround_3d);
		}
		if (!empty($result->direct)) {
			$this->checkAndUpdateZoneCmd('direct', $result->surround_3d);
		}
		if (!empty($result->pure_direct)) {
			$this->checkAndUpdateZoneCmd('pure_direct', $result->pure_direct);
		}
		if (!empty($result->enhancer)) {
			$this->checkAndUpdateZoneCmd('enhancer', $result->enhancer);
		}
		if (!empty($result->tone_control)) {
			$tone_control = $result->tone_control;
			if (!empty($tone_control->mode)) {
				$this->checkAndUpdateZoneCmd(null, $tone_control->mode, 'TODO : Gestion de tone_control_mode');
			}
			if (!empty($tone_control->bass)) {
				$this->checkAndUpdateZoneCmd(null, $tone_control->bass, 'TODO : Gestion de tone_control_bass');
			}
			if (!empty($tone_control->treble)) {
				$this->checkAndUpdateZoneCmd(null, $tone_control->treble, 'TODO : Gestion de tone_control_treble');
			}
		}
		if (!empty($result->equalizer)) {
			$equalizer = $result->equalizer;
			if (!empty($equalizer->mode)) {
				$this->checkAndUpdateZoneCmd('equalizer_mode', $equalizer->mode);
			}
			if (!empty($equalizer->low)) {
				$this->checkAndUpdateZoneCmd('equalizer_low', $equalizer->low);
			}
			if (!empty($equalizer->mid)) {
				$this->checkAndUpdateZoneCmd('equalizer_mid', $equalizer->mid);
			}
			if (!empty($equalizer->high)) {
				$this->checkAndUpdateZoneCmd('equalizer_high', $equalizer->high);
			}
		}
		if (!empty($result->balance)) {
			$this->checkAndUpdateZoneCmd('balance', $result->balance);
		}
		if (!empty($result->dialogue_level)) {
			$this->checkAndUpdateZoneCmd('dialogue_level', $result->dialogue_level);
		}
		if (!empty($result->dialogue_lift)) {
			$this->checkAndUpdateZoneCmd('dialogue_lift', $result->dialogue_lift);
		}
		if (!empty($result->clear_voice)) {
			$this->checkAndUpdateZoneCmd('clear_voice', $result->clear_voice);
		}
		if (!empty($result->subwoofer_volume)) {
			$this->checkAndUpdateZoneCmd('subwoofer_volume', $result->subwoofer_volume);
		}
		if (!empty($result->bass_extension)) {
			$this->checkAndUpdateZoneCmd('bass_extension', $result->bass_extension);
		}
		if (!empty($result->link_control)) {
			$this->checkAndUpdateZoneCmd('link_control', $result->link_control);
		}
		if (!empty($result->link_audio_delay)) {
			$this->checkAndUpdateZoneCmd('link_audio_delay', $result->link_audio_delay);
		}
		if (!empty($result->link_audio_quality)) {
			$this->checkAndUpdateZoneCmd('link_audio_quality', $result->link_audio_quality);
		}
		if (!empty($result->disable_flags)) {
			$this->checkAndUpdateZoneCmd('disable_flags', $result->disable_flags);
		}
		if (!empty($result->contents_display)) {
			$this->checkAndUpdateZoneCmd('contents_display', $result->contents_display);
		}
		if (!empty($result->actual_volume)) {
			$actualvolume = $result->actual_volume;
			if (!empty($actualvolume->mode)) {
				$this->checkAndUpdateZoneCmd('actual_volume_mode', $actualvolume->mode);
			}
			if (!empty($actualvolume->value)) {
				$this->checkAndUpdateZoneCmd('actual_volume_value', $actualvolume->value);
			}
			if (!empty($actualvolume->unit)) {
				$this->checkAndUpdateZoneCmd('actual_volume_unit', $actualvolume->unit);
			}
		}
		if (!empty($result->audio_select)) {
			$this->checkAndUpdateZoneCmd('audio_select', $result->audio_select);
		}
		if (!empty($result->party_enable)) {
			$this->checkAndUpdateZoneCmd('party_mode_state', $result->party_enable);
		}
		return $result;
	}

	function checkAndUpdateZoneCmd($cmd, $value, $debug = false) {
		if ($cmd !== false and $cmd !== null) {
			$this->checkAndUpdateCmd($cmd, str_replace("'", "’", $value));
		}
		if ($debug !== false) {
			log::add(__CLASS__, 'info', '[' . $this->getName() . '][' . $cmd . ']' . $debug . " → " . print_r($value, true));
		}
	}

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
		$port = intval(config::byKey('socket.port', __CLASS__));
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
	 * Stop le daemon
	 *
	 * @param Debug (par défault désactivé)
	 */
	public static function deamon_stop($_debug = false) {
		$cron = cron::byClassAndFunction(__CLASS__, 'socket_start');
		if (!is_object($cron)) {
			throw new Exception(__('Tache cron introuvable', __FILE__));
		}
		YamahaMusiccast::socket_stop();
		$cron->halt();
	}

	public static function socket_start() {
		$port = intval(config::byKey('socket.port', __CLASS__));
		log::add(__CLASS__, 'debug', 'Lancement d’un socket sur le port ' . $port);
		$socket = new YamahaMusiccastSocket("0.0.0.0", $port);
		$socket->run();
		YamahaMusiccast::callYamahaMusiccast();
	}

	public static function socket_stop() {
		$port = intval(config::byKey('socket.port', __CLASS__));
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or log::add(__CLASS__, 'error', 'Création du socket_stop refusée');
		socket_connect($sock, "127.0.0.1", intval($port)) or log::add(__CLASS__, 'error', 'Connexion impossible pour socket_stop');
		socket_write($sock, "stop");
		//socket_close($sock);
	}

	public static function searchAndSaveDeviceList() {
		$return = array();
		$ipList = YamahaMusiccast::searchDeviceIpList();
		foreach ($ipList as $ip) {
			$deviceIp = YamahaMusiccast::saveDeviceIp($ip);
			if($deviceIp !== null) {
				$return = array_merge($return, $deviceIp);
			}
		}
		return $return;
	}

	public static function createValueAndActionList($eqLogic, $fonc_list_zone, $fonc, $list) {
		if (in_array($fonc, $fonc_list_zone)) {
			$valueCmd = $eqLogic->createCmd($fonc);
			$valueCmd->save();
			if (!empty($list)) {
				$list_string = "";
				foreach ($list as $string) {
					$list_string .= $string . "|" . $string . ";";
				}
				$config_list['listValue'] = substr($list_string, 0, -1);
				$listCmd = $eqLogic->createCmd($fonc.'_list', 'action', 'select', false, null, $config_list);
				$listCmd->setValue($valueCmd->getId());
				$listCmd->save();
			}
		}
	}

	public static function byIP($_ip, $_zone = YamahaMusiccast::main) {
		$devices = self::byType(__CLASS__);
		foreach ($devices as $device) {
			if ($device->getConfiguration('ip') === $_ip && $device->getConfiguration('zone') === $_zone) {
				return $device;
			}
		}
	}

	public static function saveDeviceIp($ip) {
		$deviceZoneList = array();
		$device = array();
		log::add(__CLASS__, 'debug', 'Test de l’appareil avec ip ' . $ip . '.');
		$getNetworkStatus = YamahaMusiccast::callAPIGETIP($ip, YamahaMusiccast::url_v1_system . "getNetworkStatus");
		log::add(__CLASS__, 'debug', 'Resultat ' . print_r($getNetworkStatus, true) . '.');
		if ($getNetworkStatus === false || !empty($getNetworkStatus->response_code) || $getNetworkStatus->response_code !== 0) {
			log::add(__CLASS__, 'debug', 'L’appareil avec ip ' . $ip . ' n’est pas joignable ! ou n’est pas un appareil Musiccast.');
			return null;
		} else {
			$getDeviceInfo = YamahaMusiccast::callAPIGETIP($ip, YamahaMusiccast::url_v1_system . "getDeviceInfo");
			$getFeatures = YamahaMusiccast::callAPIGETIP($ip, YamahaMusiccast::url_v1_system . "getFeatures");
			$getLocationInfo = YamahaMusiccast::callAPIGETIP($ip, YamahaMusiccast::url_v1_system ."getLocationInfo");
			if (isset($getFeatures) && isset($getLocationInfo)) {
				$musiccastId = $getLocationInfo->id;
				$musiccastName = $getLocationInfo->name;
				$musiccastZoneList = $getLocationInfo->zone_list;
				$fonc_list_features = $getFeatures->system->func_list;
				foreach ($getFeatures->zone as $zone) {
					$zoneName = $zone->id;
					$logicalId = $ip . ':' . $zoneName;
					$eqLogic = YamahaMusiccast::byLogicalId($logicalId, __CLASS__);
					if (!is_object($eqLogic)) {
						$eqLogic = new YamahaMusiccast();
						$eqLogic->setEqType_name(__CLASS__);
					}
					$eqLogic->setName($logicalId);
					$eqLogic->setLogicalId($logicalId);
					$eqLogic->setCategory('multimedia', 1);
					if (!empty($musiccastZoneList->$zoneName)) {
						$eqLogic->setIsVisible(1);
						$eqLogic->setIsEnable(1);
					} else {
						$eqLogic->setIsVisible(0);
						$eqLogic->setIsEnable(0);
					}
					$eqLogic->setConfiguration('zone', $zoneName);
					$eqLogic->setConfiguration('ip', $ip);
					$eqLogic->save();

					$getStatusZone = $eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zoneName/getStatus");

					$deviceDir = __DIR__ . '/../../../../plugins/' . __CLASS__ . '/data/' . $eqLogic->getId() . '/';
					if (!file_exists($deviceDir)) {
						mkdir($deviceDir, 0700);
					}

					$configurationBluetooth['type'] = 'bluetooth';
					$configurationNetUsb['type'] = 'netusb';
					$configurationTuner['type'] = 'tuner';
					$configurationDistribution['type'] = 'distribution';

					$eqLogic->createCmd('linked_list', 'info', 'string', false, null, $configurationDistribution)->save();
					$eqLogic->createCmd('client_list', 'info', 'string', false, null, $configurationDistribution)->save();
					$eqLogic->createCmd('group_id', 'info', 'string', false, null, $configurationDistribution)->save();
					$eqLogic->createCmd('group_name', 'info', 'string', false, null, $configurationDistribution)->save();
					$eqLogic->createCmd('role', 'info', 'string', false, null, $configurationDistribution)->save();
					$eqLogic->createCmd('server_zone', 'info', 'string', false, null, $configurationDistribution)->save();

					$eqLogic->createCmd('setServerInfo', 'action', 'other', false, null, $configurationDistribution)->save();
					$eqLogic->createCmd('setClientInfo', 'action', 'other', false, null, $configurationDistribution)->save();
					$eqLogic->createCmd('startDistribution', 'action', 'other', false, null, $configurationDistribution)->save();
					$eqLogic->createCmd('stopDistribution', 'action', 'other', false, null, $configurationDistribution)->save();
					$displayList['title_placeholder'] = "Nom du nouveau groupe";
					$displayList['message_disable'] = "1";
					$eqLogic->createCmd('setGroupName', 'action', 'message', false, null, $configurationDistribution, $displayList)->save();

					if(!empty($getStatusZone->distribution_enable)) {
						$eqLogic->createCmd('distribution_enable', 'info', 'binary', false, null, $configurationDistribution)->save();
					}

					if (in_array("wired_lan", $fonc_list_features)) {
						$eqLogic->createCmd('set_wired_lan', 'action', 'other')->save();
					}
					if (in_array("wireless_lan", $fonc_list_features)) {
						$eqLogic->createCmd('set_wirless_lan', 'action', 'other')->save();
					}
					if (in_array("wireless_direct", $fonc_list_features)) {
						$eqLogic->createCmd('set_wirless_direct', 'action', 'other')->save();
					}
					if (in_array("extend_1_band", $fonc_list_features)) {

					}
					if (in_array("dfs_option", $fonc_list_features)) {

					}
					if (in_array("network_standby_auto", $fonc_list_features)) {

					}
					if (in_array("network_standby", $fonc_list_features)) {

					}
					if (in_array("bluetooth_standby", $fonc_list_features)) {
						$eqLogic->createCmd('bluetooth_standby_state', 'info', 'string', false, null, $configurationBluetooth)->save();
						$eqLogic->createCmd('disconnect_bluetooth_device', 'action', 'other', false, null, $configurationBluetooth)->save();
						$eqLogic->createCmd('connect_bluetooth_device', 'action', 'other', false, null, $configurationBluetooth)->save();
						$eqLogic->createCmd('update_bluetooth_device_list', 'action', 'other', false, null, $configurationBluetooth)->save();
					}
					if (in_array("bluetooth_tx_setting", $fonc_list_features)) {
						$eqLogic->createCmd('bluetooth_tx_setting_state', 'info', 'string', false, null, $configurationBluetooth)->save();
						$eqLogic->createCmd('set_bluetooth_tx_setting', 'action', 'other', false, null, $configurationBluetooth)->save();
					}
					if (in_array("auto_power_standby", $fonc_list_features)) {
						$eqLogic->createCmd('auto_power_standby_state')->save();
						$eqLogic->createCmd('auto_power_standby_on', 'action', 'other')->save();
						$eqLogic->createCmd('auto_power_standby_off', 'action', 'other')->save();
					}
					if (in_array("ir_sensor", $fonc_list_features)) {
						$eqLogic->createCmd('ir_sensor_state')->save();
						$eqLogic->createCmd('ir_sensor_on', 'action', 'other')->save();
						$eqLogic->createCmd('ir_sensor_off', 'action', 'other')->save();
					}
					if (in_array("speaker_a", $fonc_list_features)) {
						$eqLogic->createCmd('speaker_a_state')->save();
						$eqLogic->createCmd('speaker_a_on', 'action', 'other')->save();
						$eqLogic->createCmd('speaker_a_off', 'action', 'other')->save();
					}
					if (in_array("dimmer", $fonc_list_features)) {
						$eqLogic->createCmd('dimmer_state')->save();
						$eqLogic->createCmd('dimmer', 'action', 'other')->save();
					}
					if (in_array("speaker_b", $fonc_list_features)) {
						$eqLogic->createCmd('speaker_b_state')->save();
						$eqLogic->createCmd('speaker_b_on', 'action', 'other')->save();
						$eqLogic->createCmd('speaker_b_off', 'action', 'other')->save();
					}
					if (in_array("zone_b_volume_sync", $fonc_list_features)) {
						$eqLogic->createCmd('zone_b_volume_sync_state')->save();
						$eqLogic->createCmd('zone_b_volume_sync_on', 'action', 'other')->save();
						$eqLogic->createCmd('zone_b_volume_sync_off', 'action', 'other')->save();
					}
					if (in_array("headphone", $fonc_list_features)) {
						$eqLogic->createCmd('headphone_state')->save();
					}
					if (in_array("hdmi_out_1", $fonc_list_features)) {
						$eqLogic->createCmd('hdmi_out_1_state')->save();
						$eqLogic->createCmd('hdmi_out_1_on', 'action', 'other')->save();
						$eqLogic->createCmd('hdmi_out_1_off', 'action', 'other')->save();
					}
					if (in_array("hdmi_out_2", $fonc_list_features)) {
						$eqLogic->createCmd('hdmi_out_2_state')->save();
						$eqLogic->createCmd('hdmi_out_2_on', 'action', 'other')->save();
						$eqLogic->createCmd('hdmi_out_2_off', 'action', 'other')->save();
					}
					if (in_array("hdmi_out_3", $fonc_list_features)) {
						$eqLogic->createCmd('hdmi_out_3_state')->save();
						$eqLogic->createCmd('hdmi_out_3_on', 'action', 'other')->save();
						$eqLogic->createCmd('hdmi_out_3_off', 'action', 'other')->save();
					}
					if (in_array("airplay", $fonc_list_features)) {
						$eqLogic->createCmd('set_air_play_pin', 'action', 'other')->save();
					}
					if (in_array("stereo_pair", $fonc_list_features)) {

					}
					if (in_array("speaker_settings", $fonc_list_features)) {

					}
					if (in_array("disklavier_settings", $fonc_list_features)) {

					}
					if (in_array("background_download", $fonc_list_features)) {

					}
					if (in_array("getRemoteInfo", $fonc_list_features)) {
						/**
						 * TODO: requestNetworkReboot
						 * For retrieving remote monitor information.
						 */
					}
					if (in_array("network_reboot", $fonc_list_features)) {
						$eqLogic->createCmd('network_reboot', 'action', 'other')->save();
					}
					if (in_array("system_reboot", $fonc_list_features)) {
						$eqLogic->createCmd('system_reboot', 'action', 'other')->save();
					}
					if (in_array("auto_play", $fonc_list_features)) {
						$eqLogic->createCmd('auto_play_state')->save();
						$eqLogic->createCmd('auto_play_on', 'action', 'other')->save();
						$eqLogic->createCmd('auto_play_off', 'action', 'other')->save();
					}
					if (in_array("speaker_pattern", $fonc_list_features)) {
						$eqLogic->createCmd('speaker_pattern_state_state')->save();
					}
					if (in_array("party_mode", $fonc_list_features)) {
						/**
						 * TODO: setPartyMode
						 * For setting Party Mode.
						 */
						$eqLogic->createCmd('party_mode_on', 'action', 'other')->save();
						$eqLogic->createCmd('party_mode_off', 'action', 'other')->save();
						$eqLogic->createCmd('party_mode_state', 'info', 'binary')->save();
					}
					$fonc_list_zone = $zone->func_list;
					if (in_array("power", $fonc_list_zone)) {
						$eqLogic->createCmd('power_state')->save();
						$eqLogic->createCmd('power_on', 'action', 'other', false, 'ENERGY_ON')->save();
						$eqLogic->createCmd('power_off', 'action', 'other', false, 'ENERGY_OFF')->save();
					}
					if (in_array("sleep", $fonc_list_zone)) {
						$eqLogic->createCmd('sleep', 'info', 'numeric')->save();
					}
					if (in_array("volume", $fonc_list_zone)) {
						$config_volume_change['minValue'] = 0;
						if (!empty($getStatusZone->max_volume)) {
							$config_volume_change['maxValue'] = $getStatusZone->max_volume;
						}
						$volume_state = $eqLogic->createCmd('volume_state');
						$volume_state->save();
						$volume = $eqLogic->createCmd('volume_change', 'action', 'slider', false, 'SET_VOLUME', $config_volume_change)->setValue($volume_state->getId())->save();
						$eqLogic->createCmd('max_volume', 'info', 'numeric')->save();
					}
					if (in_array("mute", $fonc_list_zone)) {
						$eqLogic->createCmd('mute_on', 'action', 'other')->save();
						$eqLogic->createCmd('mute_off', 'action', 'other')->save();
						$eqLogic->createCmd('mute_state', 'info', 'binary')->save();
					}
					if (in_array("sound_program", $fonc_list_zone)) {
						$sound_program_state = $eqLogic->createCmd('sound_program_state');
						$sound_program_state->save();
						$sound_program_change = $eqLogic->createCmd('sound_program_change', 'action', 'select')->setValue($sound_program_state->getId())->save();
					}
					if (in_array("surround_3d", $fonc_list_zone)) {
						$eqLogic->createCmd('surround_3d', 'info', 'binary')->save();
					}
					if (in_array("direct", $fonc_list_zone)) {
						$eqLogic->createCmd('direct', 'info', 'binary')->save();
					}
					if (in_array("pure_direct", $fonc_list_zone)) {
						$eqLogic->createCmd('pure_direct', 'info', 'binary')->save();
					}
					if (in_array("enhancer", $fonc_list_zone)) {
						$eqLogic->createCmd('enhancer', 'info', 'binary')->save();
					}
					if (in_array("tone_control", $fonc_list_zone)) {
						$eqLogic->createCmd('tone_control_mode')->save();
						$eqLogic->createCmd('tone_control_base', 'info', 'numeric')->save();
						$eqLogic->createCmd('tone_control_treble', 'info', 'numeric')->save();
					}
					if (in_array("equalizer", $fonc_list_zone)) {
						$eqLogic->createCmd('equalizer_mode')->save();
						$config_volume_change['minValue'] = -10;
						$config_volume_change['maxValue'] = 10;
						$equalizer_low = $eqLogic->createCmd('equalizer_low', 'info', 'numeric');
						$equalizer_low->save();
						$eqLogic->createCmd('equalizer_low_change', 'action', 'slider', false, null, $config_volume_change)->setValue($equalizer_low->getId())->save();
						$equalizer_mid = $eqLogic->createCmd('equalizer_mid', 'info', 'numeric');
						$equalizer_mid->save();
						$eqLogic->createCmd('equalizer_mid_change', 'action', 'slider', false, null, $config_volume_change)->setValue($equalizer_mid->getId())->save();
						$equalizer_high = $eqLogic->createCmd('equalizer_high', 'info', 'numeric');
						$equalizer_high->save();
						$eqLogic->createCmd('equalizer_high_change', 'action', 'slider', false, null, $config_volume_change)->setValue($equalizer_high->getId())->save();
					}
					if (in_array("balance", $fonc_list_zone)) {
						$config_volume_change['minValue'] = -10;
						$config_volume_change['maxValue'] = 10;
						$balance = $eqLogic->createCmd('balance', 'info', 'numeric');
						$balance->save();
						$eqLogic->createCmd('balance_change', 'action', 'slider', false, null, $config_volume_change)->setValue($balance->getId())->save();
					}
					if (in_array("dialogue_level", $fonc_list_zone)) {
						$eqLogic->createCmd('dialogue_level', 'info', 'numeric')->save();
					}
					if (in_array("dialogue_lift", $fonc_list_zone)) {
						$eqLogic->createCmd('dialogue_lift', 'info', 'numeric')->save();
					}
					if (in_array("bass_extension", $fonc_list_zone)) {
						$eqLogic->createCmd('bass_extension', 'info', 'binary')->save();
					}
					if (in_array("clear_voice", $fonc_list_zone)) {
						$eqLogic->createCmd('clear_voice', 'info', 'binary')->save();
					}
					if (in_array("signal_info", $fonc_list_zone)) {

					}
					if (in_array("subwoofer_volume", $fonc_list_zone)) {
						$eqLogic->createCmd('subwoofer_volume', 'info', 'numeric')->save();
					}
					if (in_array("prepare_input_change", $fonc_list_zone)) {
						/**
						 * TODO: prepareInputChange
						 * Let a Device do necessary process before changing input in a specific zone. This is valid only
						 *	when “prepare_input_change” exists in “func_list” found in /system/getFuncStatus.
						 *	MusicCast CONTROLLER executes this API when an input icon is selected in a Room, right
						 *	before sending various APIs (of retrieving list information etc.) regarding selecting input
						 */

					}
					if(!empty($zone->link_control_list)) {
						YamahaMusiccast::createValueAndActionList($eqLogic, $fonc_list_zone, "link_control", $zone->link_control_list);
					}
					if(!empty($zone->link_audio_delay_list)) {
						YamahaMusiccast::createValueAndActionList($eqLogic, $fonc_list_zone, "link_audio_delay", $zone->link_audio_delay_list);
					}
					if(!empty($zone->link_audio_quality_list)) {
						YamahaMusiccast::createValueAndActionList($eqLogic, $fonc_list_zone, "link_audio_quality", $zone->link_audio_quality_list);
					}
					if (in_array("disable_flags", $fonc_list_zone)) {
						$eqLogic->createCmd('disable_flags', 'info', 'numeric')->save();
					}
					if (in_array("scene", $fonc_list_zone)) {
						if(!empty($zone->scene_num)) {
							$max_scene = $zone->scene_num;
							$config_scene_change['minValue'] = 1;
							$config_scene_change['maxValue'] = $max_scene;
							$scene = $eqLogic->createCmd('scene', 'info', 'numeric');
							$scene->save();
							$eqLogic->createCmd('scene_change', 'action', 'slider', false, null, $config_scene_change)->setValue($scene->getId())->save();
						}
					}
					if (in_array("contents_display", $fonc_list_zone)) {
						$eqLogic->createCmd('contents_display', 'info', 'binary')->save();
					}
					if (in_array("cursor", $fonc_list_zone)) {
						/**
						 * TODO: controlCursor
						 * Operate the cursor keys on the remote control.
						 */
					}
					if (in_array("menu", $fonc_list_zone)) {
						/**
						 * TODO: controlMenu
						 * Operate the cursor keys on the remote control.
						 */
					}
					if (in_array("actual_volume", $fonc_list_zone)) {
						$eqLogic->createCmd('actual_volume_mode', 'info', 'string')->save();
						$eqLogic->createCmd('actual_volume_value', 'info', 'numeric')->save();
						$eqLogic->createCmd('actual_volume_unit', 'info', 'string')->save();
						/**
						 * TODO: setActualVolume
						 * Set the volume of each Zone with the value to display.
						 */
					}
					if (in_array("audio_select", $fonc_list_zone)) {
						$eqLogic->createCmd('audio_select', 'info', 'string')->save();
						/**
						 * TODO: setAudioSelect
						 * Set the audio input selection.
						 * In the value of audio_select_list obtained by /system/getFeatures, it is possible to specify
						 * something other than unavailable. If current audio_select (gotten with getStatus) is unavailable,
						 * it can not be set.
						 */
					}

					if (!empty($zone->surr_decoder_type_list)) {
						YamahaMusiccast::createValueAndActionList($eqLogic, $fonc_list_zone, "surr_decoder_type", $zone->surr_decoder_type_list);
					}
					if (in_array("surr_decoder_type", $fonc_list_zone)) {
						/**
						 * TODO: setSurroundDecoderType
						 * Set the Sound Program : Decoder Type to be used with Surround Decoder.
						 */
					}
					if (!empty($getFeatures->tuner)) {
						$tuner = $getFeatures->tuner;
						$tuner_band = $eqLogic->createCmd('tuner_band', 'info', 'string', false, null, $configurationTuner);
						$tuner_band->save();
						$eqLogic->createCmd('tuner_auto_scan', 'info', 'binary', false, null, $configurationTuner)->save();
						$eqLogic->createCmd('tuner_auto_preset', 'info', 'binary', false, null, $configurationTuner)->save();
						$eqLogic->createCmd('tuner_set_frequency_up', 'action', 'other', false, null, $configurationTuner)->save();
						$eqLogic->createCmd('tuner_set_frequency_down', 'action', 'other', false, null, $configurationTuner)->save();
						$eqLogic->createCmd('tuner_set_frequency_cancel', 'action', 'other', false, null, $configurationTuner)->save();
						$eqLogic->createCmd('tuner_set_frequency_auto_up', 'action', 'other', false, null, $configurationTuner)->save();
						$eqLogic->createCmd('tuner_set_frequency_auto_down', 'action', 'other', false, null, $configurationTuner)->save();
						$tuner_set_frequency_direct_displayList['title_placeholder'] = "Fréquence (unité en kHz)";
						$tuner_set_frequency_direct_displayList['message_disable'] = "1";
						$eqLogic->createCmd('tuner_set_frequency_direct', 'action', 'message', false, null, $configurationTuner, $tuner_set_frequency_direct_displayList)->save();
						$eqLogic->createCmd('tuner_recall_preset', 'action', 'select', false, null, $configurationTuner)->save();
						$eqLogic->createCmd('tuner_recall_preset_list', 'info', 'string', false, null, $configurationTuner)->save();

						if (!empty($tuner->func_list)) {
							$fonc_list_tuner = $tuner->func_list;
							$band_list = "";
							$eqLogic->checkAndUpdateCmd('netusb_recall_recent_list', $config_surr_decoder_type['listValue']);
							if (in_array("am", $fonc_list_tuner)) {
								$band_list .= "am|am;";
								$eqLogic->createCmd('tuner_set_band_am', 'action', 'other', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_am_preset', 'info', 'binary', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_am_freq', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_am_tuned', 'info', 'numeric', false, null, $configurationTuner)->save();
							}
							if (in_array("fm", $fonc_list_tuner)) {
								$band_list .= "fm|fm;";
								$eqLogic->createCmd('tuner_set_band_fm', 'action', 'other', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_fm_preset', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_fm_freq', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_fm_tuned', 'info', 'binary', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_fm_audio_mode', 'info', 'numeric', false, null, $configurationTuner)->save();
							}
							if (in_array("rds", $fonc_list_tuner)) {
								$eqLogic->createCmd('tuner_rds_program_type', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_rds_program_service', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_rds_radio_text_a', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_rds_radio_text_b', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_set_frequency_tp_up', 'action', 'other', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_set_frequency_tp_down', 'action', 'other', false, null, $configurationTuner)->save();
							}
							if (in_array("dab", $fonc_list_tuner)) {
								$band_list .= "dab|dab;";
								$eqLogic->createCmd('tuner_set_band_dab', 'action', 'other', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_preset', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_id', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_status', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_freq', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_category', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_audio_mode', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_bit_rate', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_quality', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_tune_aid', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_off_air', 'info', 'binary', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_dab_plus', 'info', 'binary', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_program_type', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_ch_label', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_service_label', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_dls', 'info', 'string', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_ensemble_label', 'info', 'string', false, null, $configurationTuner)->save();
							}
							if (in_array("hd_radio", $fonc_list_tuner)) {
								//$eqLogic->createCmd('tuner_hd_radio')->save();
							}
							if (in_array("fm_auto_preset", $fonc_list_tuner)) {
								$eqLogic->createCmd('fm_auto_preset_start', 'action', 'other', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('fm_auto_preset_stop', 'action', 'other', false, null, $configurationTuner)->save();
							}
							if (in_array("dab_initial_scan", $fonc_list_tuner)) {
								$eqLogic->createCmd('tuner_dab_initial_scan_progress', 'info', 'numeric', false, null, $configurationTuner)->save();
								$eqLogic->createCmd('tuner_dab_total_station_num', 'info', 'numeric', false, null, $configurationTuner)->save();
							}
							if (in_array("dab_tune_aid", $fonc_list_tuner)) {
								$eqLogic->createCmd('tuner_dab_tune_aid_set', 'action', 'other', false, null, $configurationTuner)->save();
							}

							$eqLogic->createCmd('tuner_band_list', 'info', 'string', false, null, $configurationTuner)->save();
							if (!empty($band_list)) {
								$config_band_list['listValue'] = substr($band_list, 0, -1);
								$tuner_set_band = $eqLogic->createCmd('tuner_set_band', 'action', 'select', false, null, $config_band_list)
												->setValue($tuner_band->getId())->save();
								$eqLogic->checkAndUpdateCmd('tuner_band_list', $config_band_list['listValue']);
							}
						}
					}
					if (!empty($getFeatures->netusb)) {
						$netusb = $getFeatures->netusb;
						if (!empty($netusb->func_list)) {
							$fonc_list_netusb = $netusb->func_list;
							if (in_array("recent_info", $fonc_list_netusb)) {

							}
							if (in_array("play_queue", $fonc_list_netusb)) {

							}
							if (in_array("mc_playlist", $fonc_list_netusb)) {

							}
							if (in_array("streaming_service_use", $fonc_list_netusb)) {

							}
						}
						$cmdInput = $eqLogic->createCmd('input');
						$cmdInput->save();
						$input_change_string = "";
						$eqLogic->createCmd('input_change', 'action', 'select', false, null)->setValue($cmdInput->getId())->save();

						$eqLogic->createCmd('audio_error', 'info', 'numeric')->save();
						$eqLogic->createCmd('audio_format')->save();
						$eqLogic->createCmd('audio_fs')->save();

						$eqLogic->createCmd('netusb_recall_recent_list', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_recall_preset_list', 'info', 'string', false, null, $configurationNetUsb)->save();


						$eqLogic->createCmd('netusb_playback_play', 'action', 'other', '<i class="fas fa-play"></i>', 'MEDIA_RESUME', $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback_stop', 'action', 'other', '<i class="fas fa-stop"></i>', 'MEDIA_STOP', $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback_pause', 'action', 'other', '<i class="fas fa-pause"></i>', 'MEDIA_PAUSE', $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback_play_pause', 'action', 'other', '<i class="fas fa-play"></i><i class="fas fa-pause"></i>', null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback_previous', 'action', 'other', '<i class="fas fa-step-backward"></i>', 'MEDIA_PREVIOUS', $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback_next', 'action', 'other', '<i class="fas fa-step-forward"></i>', 'MEDIA_NEXT', $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback_fast_reverse_start', 'action', 'other', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback_fast_reverse_end', 'action', 'other', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback_fast_forward_start', 'action', 'other', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback_fast_forward_end', 'action', 'other', false, null, $configurationNetUsb)->save();

						$eqLogic->createCmd('netusb_shuffle_off', 'action', 'other', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_shuffle_on', 'action', 'other', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_shuffle_songs', 'action', 'other', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_shuffle_albums', 'action', 'other', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_repeat_off', 'action', 'other', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_repeat_one', 'action', 'other', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_repeat_all', 'action', 'other', false, null, $configurationNetUsb)->save();

						$eqLogic->createCmd('netusb_input', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_play_queue_type', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_playback', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_repeat', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_shuffle', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_play_time', 'info', 'numeric', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_total_time', 'info', 'numeric', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_artist', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_album', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_track', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_albumart_url', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_albumart_id', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_usb_devicetype', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_usb_auto_stopped', 'info', 'binary', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_attribute', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_repeat_available', 'info', 'string', false, null, $configurationNetUsb)->save();
						$eqLogic->createCmd('netusb_shuffle_available', 'info', 'string', false, null, $configurationNetUsb)->save();
					}
					if ($zoneName === YamahaMusiccast::main) {
						$nameEqLogic = $getNetworkStatus->network_name;
					} else {
						$nameEqLogic = $getNetworkStatus->network_name . " (" . $zoneName . ")";
					}
					$eqLogic->setName($nameEqLogic);
					$eqLogic->checkAndUpdateZoneCmd('group_name', $nameEqLogic);
					array_push($deviceZoneList, $eqLogic->getName());
					$eqLogic->setConfiguration('model_name', $getDeviceInfo->model_name);
					$eqLogic->callZoneGetStatus();
					$eqLogic->save();
					$device[$zoneName] = $eqLogic;
				}
			}
			YamahaMusiccast::callDistributionGetInfo($device);
			YamahaMusiccast::callNetusbGetPresetInfo($device);
			YamahaMusiccast::callNetusbGetRecentInfo($device);
			YamahaMusiccast::callSystemNameText($device);
			$cmd_tuner_band = $device[YamahaMusiccast::main]->getCmd(null, "tuner_band");
			if (is_object($cmd_tuner_band)) {
				YamahaMusiccast::callTunerGetPresetInfo($device);
				YamahaMusiccast::callTunerGetPlayInfo($device);
			}
			return $deviceZoneList;
		}
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
		$date = date("Y-m-d H:i:s");
		foreach ($eqlogicList = self::byType(__CLASS__) as $eqlogic) {
			if ($eqlogic->getIsEnable() == 0) {
				continue;
			}
			$power_state = $eqlogic->getCmd('info', 'power_state');
			if(is_object($power_state) && $power_state->execCmd() !== 'unreachable') {
				$zone = $eqlogic->getConfiguration('zone');
				if ($zone !== YamahaMusiccast::main) {
					continue;
				}
				$lastCallAPI = $eqlogic->getStatus('lastCallAPI');
				$deltaSeconds = strtotime($date) - strtotime($lastCallAPI);
				if ($deltaSeconds < (9 * 60)) {
					continue;
				}
			}
			if($eqlogic->callZoneGetStatus()) {
				log::add(__CLASS__, 'debug', '[' . $eqlogic->getName() . '] ' . 'Mise en place du dialogue pour 10 minutes.');
			}
		}
	}

	public static function traitement_message($host, $port, $json) {
		//log::add(__CLASS__, 'debug', 'Traitement  : ' . $host . ':' . $port . ' → ' . $json);
		$deviceList = array();
		foreach ($eqLogicList = self::byType(__CLASS__) as $eqLogic) {
			$ip = $eqLogic->getConfiguration('ip');
			$zone = $eqLogic->getConfiguration('zone');
			$deviceList[$ip][$zone] = $eqLogic;
		}
		$device = $deviceList[$host];
		if (empty($device)) {
			log::add(__CLASS__, 'info', 'L’appareil ' . $host . ' dialogue sur le port ' . $port . ' avec le message : ' . $json);
			return;
		}
		$result = json_decode($json);
		//$device_id = $result->device_id;
		if (!empty($result->system)) {
			$system = $result->system;
			if (!empty($system->bluetooth_info_updated)) {
				YamahaMusiccast::callBluetoothInfo($device);
			}
			if (!empty($system->func_status_updated)) {
				YamahaMusiccast::callGetFuncStatus($device);
			}
			if (!empty($system->location_info_updated)) {
				YamahaMusiccast::callGetLocationInfo($device);
			}
			if (!empty($system->name_text_updated)) {
				YamahaMusiccast::callSystemNameText($device);
			}
			if (!empty($system->speaker_settings_updated)) {
				log::add(__CLASS__, 'info', 'TODO: $speaker_settings_updated - Reserved ' . print_r($system->speaker_settings_updated, true));
			}
			if (!empty($system->stereo_pair_info_updated)) {
				log::add(__CLASS__, 'info', 'TODO: $stereo_pair_info_updated - Reserved ' . print_r($system->stereo_pair_info_updated, true));
			}
			if (!empty($system->tag_updated)) {
				log::add(__CLASS__, 'info', 'TODO: $tag_updated - Reserved ' . print_r($tag_updated = $system->tag_updated, true));
			}
		}
		foreach ($device as $eqLogic) {
			$zone = $eqLogic->getConfiguration('zone');
			if (!empty($result->$zone)) {
				$eqLogic->callZone($result->$zone);
			}
		}
		if (!empty($result->tuner)) {
			$tuner = $result->tuner;
			if (!empty($tuner->play_info_updated)) {
				YamahaMusiccast::callTunerGetPlayInfo($device);
			}
			if (!empty($tuner->preset_info_updated)) {
				YamahaMusiccast::callTunerGetPresetInfo($device);
			}
		}
		if (!empty($result->netusb)) {
			$netusb = $result->netusb;
			if (!empty($netusb->play_error)) {
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
				log::add(__CLASS__, 'info', 'TODO: Mise à jour du play_error ' . print_r($netusb->play_error, true));
			}
			if (!empty($netusb->multiple_play_errors)) {
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
				log::add(__CLASS__, 'info', 'TODO: Mise à jour du multiple_play_errors ' . print_r($netusb->multiple_play_errors, true));
			}
			if (!empty($netusb->play_message)) {
				log::add(__CLASS__, 'info', 'TODO: Playback related message ' . print_r($netusb->play_message, true));
			}
			if (!empty($netusb->account_updated)) {
				YamahaMusiccast::callGetNetusbAccountStatus($device);
			}
			if (!empty($netusb->play_time)) {
				foreach ($device as $eqLogic) {
					$eqLogic->checkAndUpdateCmd('netusb_play_time', $netusb->play_time);
				}
			}
			if (!empty($netusb->preset_info_updated)) {
				YamahaMusiccast::callNetusbGetPresetInfo($device);
			}
			if (!empty($netusb->recent_info_updated)) {
				YamahaMusiccast::callNetusbGetRecentInfo($device);
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
				YamahaMusiccast::callNetusbGetPlayInfo($device);
			}
			if (!empty($netusb->list_info_updated)) {
				YamahaMusiccast::callGetNetusbListInfo($device);
			}
		}
		if (!empty($result->cd)) {
			$cd = $result->cd;
			log::add(__CLASS__, 'info', 'TODO: CD. ' . print_r($cd, true));
		}
		if (!empty($result->dist)) {
			YamahaMusiccast::callDistributionGetInfo($device);
		}
		if (!empty($result->clock)) {
			$clock = $result->clock;
			if (!empty($clock->settings_updated)) {
				$settings_updated = $clock->settings_updated;
				log::add(__CLASS__, 'info', 'TODO: isSettingsUpdated ' . print_r($settings_updated, true));
			}
		}
	}

	public function callZone($zone) {
		if (!empty($zone->power)) {
			$refresh = false;
			$power_state = $this->getCmd('info', 'power_state');
			if(is_object($power_state) && $power_state->execCmd() === 'unreachable') {
				$refresh = true;
			}
			$this->checkAndUpdateCmd('power_state', $zone->power);
			if($refresh) {
				self::checkDistributionAll();
			}
		}
		if (!empty($zone->input)) {
			$this->checkAndUpdateCmd('input', $zone->input);
		}
		if (!empty($zone->volume)) {
			$this->checkAndUpdateCmd('volume_state', $zone->volume);
		}
		if (!empty($zone->mute)) {
			$this->checkAndUpdateCmd('mute_state', $zone->mute);
		}
		if (!empty($zone->status_updated)) {
			$this->callZoneGetStatus();
		}
		if (!empty($zone->signal_info_updated)) {
			$this->callZoneGetSignalInfo();
		}
	}

	public static function callTunerGetPlayInfo($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_tuner . "getPlayInfo");
		if (!empty($result->band)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_band', $result->band);
		}
		if (!empty($result->auto_scan)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_auto_scan', $result->auto_scan);
		}
		if (!empty($result->auto_preset)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_auto_preset', $result->auto_preset);
		}
		if (!empty($result->am)) {
			$am = $result->am;
			if (!empty($am->preset)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_am_preset', $am->preset);
			}
			if (!empty($am->freq)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_am_freq', $am->freq);
			}
			if (!empty($am->tuned)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_am_tuned', $am->tuned);
			}
		}
		if (!empty($result->fm)) {
			$fm = $result->fm;
			if (!empty($fm->preset)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_fm_preset', $fm->preset);
			}
			if (!empty($fm->freq)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_fm_freq', $fm->freq);
			}
			if (!empty($fm->tuned)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_fm_tuned', $fm->tuned);
			}
			if (!empty($fm->audio_mode)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_fm_audio_mode', $fm->audio_mode);
			}
		}
		if (!empty($result->rds)) {
			$rds = $result->rds;
			if (!empty($rds->program_type)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_rds_program_type', $rds->program_type);
			}
			if (!empty($rds->program_service)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_rds_program_service', $rds->program_service);
			}
			if (!empty($rds->radio_text_a)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_rds_radio_text_a', $rds->radio_text_a);
			}
			if (!empty($rds->radio_text_b)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_rds_radio_text_b', $rds->radio_text_b);
			}
		}
		if (!empty($result->dab)) {
			$dab = $result->dab;
			if (!empty($dab->preset)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_preset', $dab->preset);
			}
			if (!empty($dab->id)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_id', $dab->id);
			}
			if (!empty($dab->status)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_status', $dab->status);
			}
			if (!empty($dab->freq)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_freq', $dab->freq);
			}
			if (!empty($dab->category)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_category', $dab->category);
			}
			if (!empty($dab->audio_mode)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_audio_mode', $dab->audio_mode);
			}
			if (!empty($dab->bit_rate)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_bit_rate', $dab->bit_rate);
			}
			if (!empty($dab->quality)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_quality', $dab->quality);
			}
			if (!empty($dab->tune_aid)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_tune_aid', $dab->tune_aid);
			}
			if (!empty($dab->off_air)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_off_air', $dab->off_air);
			}
			if (!empty($dab->dab_plus)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_dab_plus', $dab->dab_plus);
			}
			if (!empty($dab->program_type)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_program_type', $dab->program_type);
			}
			if (!empty($dab->ch_label)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_ch_label', $dab->ch_label);
			}
			if (!empty($dab->service_label)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_service_label', $dab->service_label);
			}
			if (!empty($dab->dls)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_dls', $dab->dls);
			}
			if (!empty($dab->ensemble_label)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_ensemble_label', $dab->ensemble_label);
			}
			if (!empty($dab->initial_scan_progress)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_initial_scan_progress', $dab->initial_scan_progress);
			}
			if (!empty($dab->total_station_num)) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'tuner_dab_total_station_num', $dab->total_station_num);
			}
		}
		if (!empty($result->hd_radio)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, false/* 'tuner_hd_radio' */, $result->hd_radio, "tuner hd_radio");
		}
	}

	public static function callTunerGetPresetInfo($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_tuner . "getPresetInfo?band=fm", false);
		if($result->response_code === 4) {
			$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_tuner . "getPresetInfo?band=common");
		}
		if (!empty($result->preset_info)) {
			$preset_list = "";
			$int = 0;
			foreach ($result->preset_info as $preset_info) {
				++$int;
				$band = $preset_info->band;
				$number = $preset_info->number;
				$text = $preset_info->text;
				if ($band !== "unknown") {
					$preset_list .= $int . "|" . $band . "→" . $number . " - " . $text . ";";
				}
			}
			$config_tune_preset['type'] = 'tuner';
			if (!empty($preset_list)) {
				$config_tune_preset['listValue'] = substr($preset_list, 0, -1);
			} else {
				$config_tune_preset['listValue'] = $preset_list;
			}
			foreach ($device as $eqLogic) {
				$tuner_recall_preset = $eqLogic->createCmd('tuner_recall_preset', 'action', 'select', false, null, $config_tune_preset)
								->setValue(null)->save();
				$eqLogic->checkAndUpdateCmd('tuner_recall_preset_list', $config_tune_preset['listValue']);
			}
		}
	}

	public static function checkAndUpdateDeviceCmd($device, $cmd, $value, $debug = false) {
		foreach ($device as $eqLogic) {
			$eqLogic->checkAndUpdateZoneCmd($cmd, $value, $debug);
		}
	}

	public static function callBluetoothInfo($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_system . "getBluetoothInfo");
		if (!empty($result->bluetooth_standby)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'bluetooth_standby_state', $result->bluetooth_standby);
		}
		if (!empty($result->bluetooth_tx_setting)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'bluetooth_tx_setting_state', $result->bluetooth_tx_setting);
		}
		if (!empty($result->bluetooth_standby)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, false, $result->bluetooth_standby, 'Gestion des devices : $bluetooth_info_updated');
		}
	}

	public static function callNetusbGetPlayInfo($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_netusb . "getPlayInfo");
		if (!empty($result->input)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_input', $result->input);
			if (empty($result->albumart_url)) {
				//YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_albumart_url', '/plugins/' . __CLASS__ . '/plugin_info/' . __CLASS__ . '_icon.png');
			}
		}
		if (!empty($result->play_queue_type)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_play_queue_type', $result->play_queue_type);
		}
		if (!empty($result->playback)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_playback', $result->playback);
		}
		if (!empty($result->repeat)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_repeat', $result->repeat);
		}
		if (!empty($result->shuffle)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_shuffle', $result->shuffle);
		}
		if (!empty($result->play_time)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_play_time', $result->play_time);
		}
		if (!empty($result->total_time)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_total_time', $result->total_time);
		}
		YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_artist', $result->artist);
		YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_album', $result->album);
		YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_track', $result->track);
		if (!empty($result->albumart_url)) {
			foreach ($device as $eqLogic) {
				$fileAlbumARTUrl = '/plugins/' . __CLASS__ . '/data/' . $eqLogic->getId() . '/AlbumART.jpg';
				$fileAlbumART = __DIR__ . '/../../../..' . $fileAlbumARTUrl;
				$url = "http://" . $eqLogic->getConfiguration('ip') . $result->albumart_url;
				$netusb_albumart_url = null;
				if (file_put_contents($fileAlbumART, file_get_contents($url))) {
					$netusb_albumart_url = $fileAlbumARTUrl . '?' . $result->albumart_id;
				}
				$eqLogic->checkAndUpdateCmd('netusb_albumart_url', $netusb_albumart_url);
			}
		}
		if (!empty($result->albumart_id)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_albumart_id', $result->albumart_id);
		}
		if (!empty($result->usb_devicetype)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_usb_devicetype', $result->usb_devicetype);
		}
		if (!empty($result->usb_auto_stopped)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_usb_auto_stopped', $result->usb_auto_stopped);
		}
		if (!empty($result->usb_auto_stopped)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_usb_auto_stopped', $result->usb_auto_stopped);
		}
		if (!empty($result->attribute)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_attribute', $result->attribute);
		}
		if (!empty($result->repeat_available)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_repeat_available', $result->repeat_available);
		}
		if (!empty($result->shuffle_available)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'netusb_shuffle_available', $result->shuffle_available);
		}
	}

	public static function callNetusbGetPresetInfo($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_netusb . "getPresetInfo");
		if (!empty($result->preset_info)) {
			$netusb_recall_preset_list = "";
			$int = 0;
			foreach ($result->preset_info as $preset_info) {
				++$int;
				$input = $preset_info->input;
				$text = $preset_info->text;
				if ($input !== "unknown") {
					if (!empty($preset_info->attribute)) {
						$attribute = $preset_info->attribute;
					}
					$netusb_recall_preset_list .= $int . "|" . $input . "→" . $text . ";";
				}
			}
			$config_netusb_recall_preset['type'] = 'netusb';
			if (!empty($netusb_recall_preset_list)) {
				$config_netusb_recall_preset['listValue'] = substr($netusb_recall_preset_list, 0, -1);
			} else {
				$config_netusb_recall_preset['listValue'] = $netusb_recall_preset_list;
			}
			foreach ($device as $eqLogic) {
				$netusb_recall_preset = $eqLogic->createCmd('netusb_recall_preset', 'action', 'select', false, null, $config_netusb_recall_preset)
								->setValue(null)->save();
				$eqLogic->checkAndUpdateCmd('netusb_recall_preset_list', $config_netusb_recall_preset['listValue']);
			}
		}
		if (!empty($result->func_list)) {
			// Returns a list of valid functions for Preset. (Recall/Store functions are always valid without specifically listed here)
			// Values: "clear" / "move"
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, false, $result->func_list, "Gestion de getPresetInfo / func_list");
		}
	}

	public static function callGetFuncStatus($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_system . "getFuncStatus");
		if (!empty($result->auto_power_standby)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'auto_power_standby_state', $result->auto_power_standby);
		}
		if (!empty($result->ir_sensor)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'ir_sensor_state', $result->ir_sensor);
		}
		if (!empty($result->speaker_a)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'speaker_a_state', $result->speaker_a);
		}
		if (!empty($result->speaker_b)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'speaker_b_state', $result->speaker_b);
		}
		if (!empty($result->headphone)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'headphone_state', $result->headphone);
		}
		if (!empty($result->dimmer)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'dimmer_state', $result->dimmer);
		}
		if (!empty($result->zone_b_volume_sync)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'zone_b_volume_sync_state', $result->zone_b_volume_sync);
		}
		if (!empty($result->hdmi_out_1)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'hdmi_out_1_state', $result->hdmi_out_1);
		}
		if (!empty($result->hdmi_out_2)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'hdmi_out_2_state', $result->hdmi_out_2);
		}
		if (!empty($result->hdmi_out_3)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'hdmi_out_3_state', $result->hdmi_out_3);
		}
		if (!empty($result->auto_play)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'auto_play_state', $result->auto_play);
		}
		if (!empty($result->speaker_pattern)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'speaker_pattern_state', $result->speaker_pattern);
		}
		if (!empty($result->party_mode)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, 'party_mode_state', $result->party_mode);
		}
	}

	public static function callGetLocationInfo($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_system . "getLocationInfo");
		if (!empty($result->id)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, false, $result->id, "getLocationInfo id");
		}
		if (!empty($result->name)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, false, $result->name, "getLocationInfo name");
		}
		if (!empty($result->zone_list)) {
			foreach ($result->zone_list as $zone) {
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, false, $zone, "getLocationInfo Zone");
			}
		}
	}

	public static function callGetNetusbListInfo($device, $input = "net_radio") {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_netusb . "getListInfo?list_id=main&input=" . $input . "&index=0&size=8");
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
		return $result;
	}

	public static function callListControlReturn($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_netusb . "setListControl?list_id=&type=return");
		if (!empty($result->service_list)) {

		}
		return $result;
	}

	public static function callSearchString($device, $list_id, $string, $index) {
		$data = '{
			"list_id": "'.$list_id.'",
			"string":"'.$string.'",
			"index":"'.$index.'"
		}';
		$result = $device[YamahaMusiccast::main]->callAPIPOST(YamahaMusiccast::url_v1_netusb . "setSearchString", $data);
		return $result;
	}

	public static function callListControlSelect($device, $index = 0) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_netusb . "setListControl?list_id=&type=select&index=" . $index);
		if (!empty($result->service_list)) {

		}
		return $result;
	}

	public static function callListControlPlay($device, $index = 0) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_netusb . "setListControl?list_id=&type=play&index=" . $index);
		if (!empty($result->service_list)) {

		}
		return $result;
	}

	public static function callDistributionGetInfo($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_dist . "getDistributionInfo");
		if (!empty($result->status)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, false, $result->status);
			// working
		}
		$client_base_list = array();
		$client_ext_list = array();
		if (!empty($result->server_zone)) {
			$eqLogic = $device[$result->server_zone];
			$server_zone = $result->server_zone;
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, "server_zone", $server_zone);
			if (!empty($result->role) && !empty($result->group_id)) {
				$role = $result->role;
				$group_id = $result->group_id;
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, "role", $role);
				YamahaMusiccast::checkAndUpdateDeviceCmd($device, "group_id", $group_id);
				if ($group_id === "00000000000000000000000000000000") {
					$groupName = $eqLogic->getName();
				} else {
					switch ($role) {
						case "server" :
							if (!empty($result->client_list)) {
								$client_list = $result->client_list;
								foreach ($client_list as $client) {
									if (!empty($client->data_type)) {
										$data_type = $client->data_type;
										switch ($data_type) {
											case "base" :
												array_push($client_base_list, $client->ip_address);
												break;
											default:
												array_push($client_ext_list, $client->ip_address);
												break;
										}
									}
								}
							}
						case "client" :
							$groupName = str_replace("(Linked)", '<i class="fas fa-link"></i>', $result->group_name);
							break;
						case "none" :
						default :
							$groupName = $eqLogic->getName();
							break;
					}
				}
				YamahaMusiccast::checkAndUpdateDeviceCmd(array($eqLogic), "group_name", $groupName);
			}
		}
		YamahaMusiccast::checkAndUpdateDeviceCmd($device, "client_list", implode ( ";" , $client_base_list ));
		if (!empty($result->build_disable)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, false, $result->build_disable, "getDistributionInfo build_disable");
			$build_disable = $result->build_disable;
			if (!empty($build_disable->ip_address)) {
				
			}
			if (!empty($build_disable->data_type)) {
				
			}
		}
		if (!empty($result->audio_dropout)) {
			YamahaMusiccast::checkAndUpdateDeviceCmd($device, false, $result->audio_dropout, "getDistributionInfo audio_dropout");
		}
		self::checkDistributionAll();
	}

	public static function checkDistributionAll() {
		foreach ($eqLogicList = self::byType(__CLASS__) as $eqLogicCheckDistribution) {
			$eqLogicCheckDistribution->checkDistribution();
		}
	}

	public function checkDistribution() {
		log::add(__CLASS__, 'debug', '[' . $this->getName() . '] checkDistribution');
		$linked_list_string = "";
		$ip = $this->getConfiguration("ip");
		$zone = $this->getConfiguration("zone");
		$client_list = $this->getCmd('info', 'client_list');
		$client_list_base = array();
		if(is_object($client_list)) {
			$client_list_base_string = $client_list ->execCmd();
			if(!empty($client_list_base_string)) {
				$client_list_base = explode(",", $client_list_base_string);
			}
		}
		$eqLogicList = self::byType(__CLASS__);
		foreach ($eqLogicList as $eqLogicLink) {
			$ipLink = $eqLogicLink->getConfiguration("ip");
			$zoneLink = $eqLogicLink->getConfiguration("zone");
			if(!($ip === $ipLink && $zone === $zoneLink)) {
				$checked = "";
				$group_id = $eqLogicLink->getCmd('info', 'group_id');
				$power_state = $eqLogicLink->getCmd('info', 'power_state');
				if(is_object($power_state) && $power_state->execCmd() === 'unreachable') {
					$checked = "disabled|(Injoignable)";
				} else if(in_array($ipLink, $client_list_base)) {
					$checked = "checked";
				} else if(is_object($group_id) && $group_id->execCmd() !== '00000000000000000000000000000000') {
					$checked = "disabled|(Autre groupe)";
				}
				$linked_list_string .= $ipLink . "|" . $eqLogicLink->getName() . "|" . $checked . ";";
			}
		}
		if (!empty($linked_list_string)) {
			$linked_list_string = substr($linked_list_string, 0, -1);
		}
		$this->checkAndUpdateZoneCmd('linked_list', $linked_list_string);
		
	}

	public static function callGetNetusbAccountStatus($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_netusb . "getAccountStatus");
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

	public static function callNetusbGetRecentInfo($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_netusb . "getRecentInfo");
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
					$recent_info_list .= $int . "|" . $input . "→" . $text . "|" . $albumart_url . "|" . $play_count . ";";
				}
			}
			$config_netusb_recall_recent['type'] = 'netusb';
			if (!empty($recent_info_list)) {
				$config_netusb_recall_recent['listValue'] = substr($recent_info_list, 0, -1);
			} else {
				$config_netusb_recall_recent['listValue'] = $recent_info_list;
			}
			foreach ($device as $eqLogic) {
				$netusb_recall_recent = $eqLogic->createCmd('netusb_recall_recent', 'action', 'select', false, null, $config_netusb_recall_recent)
								->setValue($eqLogic->getCmd(null, 'netusb_track')->getId())->save();
				$eqLogic->checkAndUpdateCmd('netusb_recall_recent_list', $config_netusb_recall_recent['listValue']);
			}
		}
	}

	public static function callSystemNameText($device) {
		$result = $device[YamahaMusiccast::main]->callAPIGET(YamahaMusiccast::url_v1_system . "getNameText");
		if (!empty($result->input_list)) {
			$input_change_string = "";
			foreach ($result->input_list as $input) {
				$input_change_string .= $input->id . "|" . $input->text . ";";
			}
			$config_input_change['listValue'] = substr($input_change_string, 0, -1);
			foreach ($device as $eqLogic) {
				$cmd = $eqLogic->getCmd(null, 'input_change');
				if (is_object($cmd)) {
					foreach ($config_input_change as $key => $value) {
						$cmd->setConfiguration($key, $value);
					}
					$cmd->save();
				}
			}
		}

		if (!empty($result->sound_program_list)) {
			$sound_program_list_string = "";
			foreach ($result->sound_program_list as $sound_program) {
				$sound_program_list_string .= $sound_program->id . "|" . $sound_program->text . ";";
			}
			$config_sound_program_change['listValue'] = substr($sound_program_list_string, 0, -1);
			foreach ($device as $eqLogic) {
				$cmd = $eqLogic->getCmd(null, 'sound_program_change');
				if (is_object($cmd)) {
					foreach ($config_sound_program_change as $key => $value) {
						$cmd->setConfiguration($key, $value);
					}
					$cmd->save();
				}
			}
		}
	}

	public function callAPIGET($path, $data = false, $logLevel = "error") {
		return $this->callAPI("GET", $path, $data, $logLevel);
	}

	public function callAPIPOST($path, $data = false, $logLevel = "error") {
		return $this->callAPI("POST", $path, $data, $logLevel);
	}

	public function callAPI($method, $path, $data = false, $logLevel = "error") {
		$ip = $this->getConfiguration('ip');
		$result = self::callAPIIP($method, $ip, $path, $data, $logLevel);
		if(!$result) {
			$power_state = $this->getCmd('info', 'power_state');
			if(is_object($power_state)) {
				$power_state_string = $power_state->execCmd();
				$this->checkAndUpdateZoneCmd('power_state', "unreachable");
				if($power_state_string !== 'unreachable') {
					self::checkDistributionAll();
				}
			}
			log::add(__CLASS__, "info", '[' . $this->getName() . '] L’appareil ' . $ip . ' n’est pas joignable.');
		} else {
			$this->setStatus('lastCallAPI', date("Y-m-d H:i:s"));
		}
		return $result;
	}

	public static function callAPIGETIP($ip, $path, $data = false, $logLevel = "error") {
		return self::callAPIIP("GET",$ip,  $path, $data, $logLevel);
	}

	public static function callAPIPOSTIP($ip, $path, $data = false, $logLevel = "error") {
		return self::callAPIIP("POST",$ip,  $path, $data, $logLevel);
	}

	public static function callAPIIP($method, $ip, $path, $data = false, $logLevel = "error") {
		$port = intval(config::byKey('socket.port', __CLASS__));
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
		$url = "http://" . $ip . $path;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result_curl = curl_exec($curl);
		if(!$result_curl) {
			return false;
		}
		$result = json_decode($result_curl);
		curl_close($curl);
		if (!empty($result->response_code)) {
			$response_code = $result->response_code;
			$message = "KO";
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
					$message = "Invalid Parameter (Out of range, invalid characters etc.) " . print_r($data, true);
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
		}
		return $result;
	}

	/*	 * **********************Getteur Setteur*************************** */
}