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
require_once 'YamahaMusiccast.class.php';

class YamahaMusiccastCmd extends cmd {
	/*	 * *************************Attributs****************************** */
	/*	 * ***********************Methode static*************************** */
	/*	 * *********************Methode d'instance************************* */
	/*
	 * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
	  public function dontRemoveCmd() {
	  return true;
	  }
	 */

	public function execute($_options = array()) {
		if ($this->getType() == 'info') {
			return;
		}
		$eqLogic = $this->getEqLogic();
		$zone = $eqLogic->getConfiguration('zone');
		switch ($this->getLogicalId()) {
			//*******************************************/
			//					SYSTEM					*/
			//*******************************************/
			case 'set_wired_lan' :
//				$data = '{"dhcp":false,"ip_address":"192.168.0.11","subnet_mask":"255.255.255.0","default_gateway":"192.168.0.1","dns_server_1":"192.168.0.1","dns_server_2":"0.0.0.0"}';
//				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setWiredLan", $data);
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page21');
				break;
			case 'set_wirless_lan' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page22');
				break;
			case 'set_wirless_direct' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page23');
				break;
			case 'set_ip_setting' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page23');
				break;
			case 'set_network_name' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page24');
				break;
			case 'set_air_play_pin' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page25');
				break;
			case 'set_mac_adress_filter' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page26');
				break;
			case 'set_bluetooth_tx_setting' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page29');
				break;
			case 'update_bluetooth_device_list' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page30');
				break;
			case 'connect_bluetooth_device' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page31');
				break;
			case 'disconnect_bluetooth_device' :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la command ' . $this->getLogicalId() . '  → Page31');
				break;
			case 'auto_power_standby_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setAutoPowerStandby?enable=true");
				break;
			case 'auto_power_standby_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setAutoPowerStandby?enable=false");
				break;
			case 'ir_sensor_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setIrSensor?enable=true");
				break;
			case 'ir_sensor_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setIrSensor?enable=false");
				break;
			case 'speaker_a_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setSpeakerA?enable=true");
				break;
			case 'speaker_a_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setSpeakerA?enable=false");
				break;
			case 'speaker_b_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setSpeakerB?enable=true");
				break;
			case 'speaker_b_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setSpeakerB?enable=false");
				break;
			case 'dimmer' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setDimmer?value=" . $_options['slider']);
				break;
			case 'zone_b_volume_sync_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setZoneBVolumeSync?enable=true");
				break;
			case 'zone_b_volume_sync_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setZoneBVolumeSync?enable=false");
				break;
			case 'hdmi_out_1_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setHdmiOut1?enable=true");
				break;
			case 'hdmi_out_1_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setHdmiOut1?enable=false");
				break;
			case 'hdmi_out_2_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setHdmiOut2?enable=true");
				break;
			case 'hdmi_out_2_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setHdmiOut2?enable=false");
				break;
			case 'hdmi_out_3_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setHdmiOut3?enable=true");
				break;
			case 'hdmi_out_3_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setHdmiOut3?enable=false");
				break;
			case 'set_name_text' :
				$data = '{"id":"' . $_options['id'] . '","text":"' . $_options['text'] . '"}';
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setNameText", $data);
				break;
			//getLocationInfo
			//getStereoPairInfo
			//sendIrCode
			//getRemoteInfo
			case 'network_reboot' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "requestNetworkReboot");
				break;
			case 'system_reboot' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "requestSystemReboot");
				break;
			//getAdvancedFeatures
			case 'auto_play_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setAutoPlay?enable=true");
				break;
			case 'auto_play_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setAutoPlay?enable=false");
				break;
			//setSpeakerPattern
			case 'party_mode_on' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setPartyMode?enable=true");
				break;
			case 'party_mode_off' :
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_system . "setPartyMode?enable=false");
				break;
			//getSoundProgramList
			//*******************************************/
			//					Zone					*/
			//*******************************************/
			case "power_on":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPower?power=on");
				break;
			case "power_off":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPower?power=standby");
				break;
			case "power_toggle":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPower?power=toggle");
				break;
			//setSleep
			case "volume_change":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setVolume?volume=" . $_options['slider']);
				break;
			case "volume_change_step":
				//volume = up or down
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setVolume?volume=" . $_options['volume'] . "&step=" . $_options['step']);
				break;
			case "mute_on":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setMute?enable=true");
				break;
			case "mute_off":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setMute?enable=false");
				break;
			case "input_change":
				if(!empty($_options['select'])) {
					$power_state_cmd = $eqLogic->getCmd(null, 'power_state');
					if($power_state_cmd->execCmd() !== 'on') {
						$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPower?power=on");
					}
					$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setInput?input=" . $_options['select']);
				}
				break;
			case "input_change_mode":
				/**
				 * Specifies select mode. If no parameter is specified, actions of input
				  change depend on a Device’s specification
				  Value: "autoplay_disabled" (Restricts Auto Play of Net/USB
				  related Inputs). Available on and after API Version
				 */
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setInput?input=" . $_options['select'] . "&mode=" . $_options['mode']);
				break;
			case "sound_program_change":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setSoundProgram?program=" . $_options['select']);
				break;
			case "link_control_list":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setLinkControl?control=" . $_options['select']);
				break;
			case "link_audio_delay_list":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setLinkAudioDelay?delay=" . $_options['select']);
				break;
			case "link_audio_quality_list":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setLinkAudioQuality?mode=" . $_options['select']);
				break;
			case "3d_surround_on":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/set3dSurround?enable=true");
				break;
			case "3d_surround_off":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/set3dSurround?enable=false");
				break;
			case "direct_on":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setDirect?enable=true");
				break;
			case "direct_off":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setDirect?enable=false");
				break;
			case "pure_direct_on":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPureDirect?enable=true");
				break;
			case "pure_direct_off":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPureDirect?enable=false");
				break;
			case "enchancer_on":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setEnhancer?enable=true");
				break;
			case "enchancer_off":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setEnhancer?enable=false");
				break;
			//setToneControl
			//setEqualizer
			case "equalizer_low_change":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setEqualizer?mode=manual&low=" . $_options['slider']);
				break;
			case "equalizer_mid_change":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setEqualizer?mode=manual&mid=" . $_options['slider']);
				break;
			case "equalizer_high_change":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setEqualizer?mode=manual&high=" . $_options['slider']);
				break;
			//setBalance
			//setDialogueLevel
			//setDialogueLift
			//setClearVoice
			//setSubwooferVolume
			//setBassExtension
			//getSignalInfo
			//prepareInputChange
			//recallScene
			case "scene_change":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/recallScene?num=" . $_options['slider']);
				break;
			//setContentsDisplay
			//controlCursor
			//controlMenu
			//setActualVolume
			//setAudioSelect
			//setSurroundDecoderType
			//*******************************************/
			//					TUNER					*/
			//*******************************************/
			case "tuner_set_band":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setBand?band=" . $_options['band']);
				break;
			case "tuner_set_band_am":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setBand?band=am");
				break;
			case "tuner_set_band_fm":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setBand?band=fm");
				break;
			case "tuner_set_band_dab":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setBand?band=dab");
				break;
			case "tuner_set_frequency_up":
				$tuner_band = $eqLogic->getCmd(null, 'tuner_band')->execCmd();
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setFreq?band=" . $tuner_band . "&tuning=up");
				break;
			case "tuner_set_frequency_down":
				$tuner_band = $eqLogic->getCmd(null, 'tuner_band')->execCmd();
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setFreq?band=" . $tuner_band . "&tuning=down");
				break;
			case "tuner_set_frequency_cancel":
				$tuner_band = $eqLogic->getCmd(null, 'tuner_band')->execCmd();
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setFreq?band=" . $tuner_band . "&tuning=cancel");
				break;
			case "tuner_set_frequency_auto_up":
				$tuner_band = $eqLogic->getCmd(null, 'tuner_band')->execCmd();
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setFreq?band=" . $tuner_band . "&tuning=auto_up");
				break;
			case "tuner_set_frequency_auto_down":
				$tuner_band = $eqLogic->getCmd(null, 'tuner_band')->execCmd();
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setFreq?band=" . $tuner_band . "&tuning=auto_down");
				break;
			case "tuner_set_frequency_tp_up":
				$tuner_band = $eqLogic->getCmd(null, 'tuner_band')->execCmd();
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setFreq?band=" . $tuner_band . "&tuning=tp_up");
				break;
			case "tuner_set_frequency_tp_down":
				$tuner_band = $eqLogic->getCmd(null, 'tuner_band')->execCmd();
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setFreq?band=" . $tuner_band . "&tuning=tp_down");
				break;
			case "tuner_set_frequency_direct":
				if(!empty($_options['title'])) {
					$data = $_options['title'];
					$tuner_band = $eqLogic->getCmd(null, 'tuner_band')->execCmd();
					$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "setFreq?band=" . $tuner_band . "&tuning=direct&num=" . $data);
				}
				break;
			case 'tuner_recall_preset':
				if(!empty($_options['select'])) {
					$power_state_cmd = $eqLogic->getCmd(null, 'power_state');
					if($power_state_cmd->execCmd() !== 'on') {
						$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPower?power=on");
					}
					if(!empty($_options['tuner_band'])) {
						$tuner_band = $_options['tuner_band'];
					} else {
						$tuner_band = $eqLogic->getCmd(null, 'tuner_band')->execCmd();
					}
					$eqLogic->callAPIGET(YamahaMusiccast::url_v1_tuner . "recallPreset?zone=" . $zone . "&band=" . $tuner_band . "&num=" . $_options['select']);
				}
				break;
			//switchPreset
			//storePreset
			//clearPreset
			//startAutoPreset
			//cancelAutoPreset
			//movePreset
			//startDabInitialScan
			//cancelDabInitialScan
			//setDabTuneAid
			//setDabService
			//*******************************************/
			//				Network/USB					*/
			//*******************************************/
			case "netusb_playback_play":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=play");
				break;
			case "netusb_playback_stop":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=stop");
				break;
			case "netusb_playback_pause":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=pause");
				break;
			case "netusb_playback_play_pause":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=play_pause");
				break;
			case "netusb_playback_previous":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=previous");
				break;
			case "netusb_playback_next":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=next");
				break;
			case "netusb_playback_fast_reverse_start":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=fast_reverse_start");
				break;
			case "netusb_playback_fast_reverse_end":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=fast_reverse_end");
				break;
			case "netusb_playback_fast_forward_start":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=fast_forward_start");
				break;
			case "netusb_playback_fast_forward_end":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=fast_forward_end");
				break;
			//setPlayPosition
			case "netusb_play_position_change":
				//For setting track play position. This API is available only when input is Server.
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayPosition?position=" . $_options['position']);
				break;
			case "netusb_repeat_off":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setRepeat?mode=off");
				break;
			case "netusb_repeat_one":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setRepeat?mode=one");
				break;
			case "netusb_repeat_all":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setRepeat?mode=all");
				break;
			case "netusb_shuffle_off":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setShuffle?mode=off");
				break;
			case "netusb_shuffle_on":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setShuffle?mode=on");
				break;
			case "netusb_shuffle_songs":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setShuffle?mode=songs");
				break;
			case "netusb_shuffle_albums":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setShuffle?mode=albums");
				break;
			case "netusb_toggle_repeat":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "toggleRepeat");
				break;
			case "netusb_toggle_shuffle":
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "toggleShuffle");
				break;
			//getListInfo
			//setListControl
			//setSearchString
			case 'netusb_recall_preset':
				if(!empty($_options['select'])) {
					$power_state_cmd = $eqLogic->getCmd(null, 'power_state');
					if($power_state_cmd->execCmd() !== 'on') {
						$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPower?power=on");
					}
					$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "recallPreset?zone=" . $zone . "&num=" . $_options['select']);
				}
				break;
			//storePreset
			//clearPreset
			//movePreset
			//getSettings
			//setQuality
			//getRecentInfo
			case 'netusb_recall_recent':
				if(!empty($_options['select'])) {
					$power_state_cmd = $eqLogic->getCmd(null, 'power_state');
					if($power_state_cmd->execCmd() !== 'on') {
						$eqLogic->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPower?power=on");
					}
					$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "recallRecentItem?zone=" . $zone . "&num=" . $_options['select']);
					$eqLogic->callAPIGET(YamahaMusiccast::url_v1_netusb . "setPlayback?playback=play");
				}
				break;
			//clearRecentInfo
			//managePlay
			//manageList
			//getPlayDescription
			//setListSortOption
			//getAccountStatus
			//getServiceInfo
			//*******************************************/
			//					CD						*/
			//*******************************************/
			//getPlayInfo
			//setPlayback
			//toggleTray
			//setRepeat
			//setShuffle
			//toggleRepeat
			//toggleShuffle
			//*******************************************/
			//					Clock					*/
			//*******************************************/
			//getSettings
			//setAutoSync
			//setDateAndTime
			//setClockFormat
			//setAlarmSettings
			//*******************************************/
			//					Distribution					*/
			//*******************************************/
			case "setServerInfo":
				$groupId = "";
				if(!empty($_options['groupId'])) {
					$groupId = $_options['groupId'];
				}
				$zone = YamahaMusiccast::main;
				if(!empty($_options['zone'])) {
					$zone = $_options['zone'];
				}
				$action = "add";
				if(!empty($_options['action'])) {
					$action = $_options['action'];
				}
				$ipClientList = array();
				if(!empty($_options['ipClientList'])) {
					$ipClientList = $_options['ipClientList'];
				}
				$data = '{
					"group_id": "'.$groupId.'",
					"zone":"'.$zone.'",
					"type":"'.$action.'",
					"client_list":["'.implode('","', $ipClientList).'"]
					}';
				$result = $eqLogic->callAPIPOST(YamahaMusiccast::url_v1_dist . "setServerInfo", $data);
				break;
			case "setClientInfo":
				$groupId = "";
				if(!empty($_options['groupId'])) {
					$groupId = $_options['groupId'];
				}
				$zoneRemoteList = array();
				if(!empty($_options['zoneRemote'])) {
					$zoneRemoteList = $_options['zoneRemote'];
				} else {
					array_push($zoneRemoteList, YamahaMusiccast::main);
				}
				$data = '{
					"group_id": "'.$groupId.'",
					"zone":["'.implode('","', $zoneRemoteList).'"]
				}';
				$result = $eqLogic->callAPIPOST(YamahaMusiccast::url_v1_dist . "setClientInfo", $data);
				break;
			case "startDistribution":
				$num = 0;
				if(!empty($_options['num'])) {
					$num = $_options['num'];
				}
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_dist . "startDistribution?num=".$num);
				break;
			case "stopDistribution":
				$num = 0;
				if(!empty($_options['num'])) {
					$num = $_options['num'];
				}
				$eqLogic->callAPIGET(YamahaMusiccast::url_v1_dist . "stopDistribution?num=".$num);
				break;
			case "setGroupName":
				if(!empty($_options['title'])) {
					$data = '{"name":"' . $_options['title'] . '"}';
					$eqLogic->callAPIPOST(YamahaMusiccast::url_v1_dist . "setGroupName", $data);
				}
				break;
			default :
				log::add("YamahaMusiccast", 'info', 'TODO: Créer la commande ' . $this->getLogicalId() . ' - ' . print_r($_options, true));
		}
	}

	public static function generateGroupId($length = 32) {
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;

	}

	/*	 * **********************Getteur Setteur*************************** */
}