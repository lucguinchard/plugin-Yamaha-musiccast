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
		$device = $this->getEqLogic();
		$zone = $device->getConfiguration('zone');
		switch ($this->getLogicalId()) {
			case 'set_wired_lan' :
//				$data = '{"dhcp":false,"ip_address":"192.168.0.11","subnet_mask":"255.255.255.0","default_gateway":"192.168.0.1","dns_server_1":"192.168.0.1","dns_server_2":"0.0.0.0"}';
//				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setWiredLan", $data);
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
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setAutoPowerStandby?enable=true");
				break;
			case 'auto_power_standby_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setAutoPowerStandby?enable=false");
				break;
			case 'ir_sensor_on' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setIrSensor?enable=true");
				break;
			case 'ir_sensor_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setIrSensor?enable=false");
				break;
			case 'speaker_a_on' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setSpeakerA?enable=true");
				break;
			case 'speaker_a_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setSpeakerA?enable=false");
				break;
			case 'speaker_b_on' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setSpeakerB?enable=true");
				break;
			case 'speaker_b_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setSpeakerB?enable=false");
				break;
			case 'dimmer' :
				/**
				 * Setting Dimmer. Specifies -1 in case of auto setting.
				  Specifies 0 or more than 0 in case of manual setting.
				  Auto setting is available only when -1 is exists in vale range under
				  /system/getFeatures.
				  Value Range: calculated by minimum/maximum/step values gotten
				  via /system/getFeatures
				 */
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setDimmer?value=" . $_options['slider']);
				break;
			case 'zone_b_volume_sync_on' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setZoneBVolumeSync?enable=true");
				break;
			case 'zone_b_volume_sync_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setZoneBVolumeSync?enable=false");
				break;
			case 'hdmi_out_1_on' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setHdmiOut1?enable=true");
				break;
			case 'hdmi_out_1_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setHdmiOut1?enable=false");
				break;
			case 'hdmi_out_2_on' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setHdmiOut2?enable=true");
				break;
			case 'hdmi_out_2_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setHdmiOut2?enable=false");
				break;
			case 'hdmi_out_3_on' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setHdmiOut3?enable=true");
				break;
			case 'hdmi_out_3_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setHdmiOut3?enable=false");
				break;
			case 'set_name_text' :
				$data = '{"id":"' . $_options['id'] . '","text":"' . $_options['text'] . '"}';
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setNameText", $data);
				break;
			//getLocationInfo
			//getStereoPairInfo
			//sendIrCode
			//getRemoteInfo
			//requestNetworkReboot
			//requestSystemReboot
			//getAdvancedFeatures
			case 'auto_play_on' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setAutoPlay?enable=true");
				break;
			case 'auto_play_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setAutoPlay?enable=false");
				break;
			//setSpeakerPattern
			case 'party_mode_on' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setPartyMode?enable=true");
				break;
			case 'party_mode_off' :
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/system/setPartyMode?enable=false");
				break;
			//getSoundProgramList
			case "power_on":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setPower?power=on");
				break;
			case "power_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setPower?power=standby");
				break;
			case "power_toggle":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setPower?power=toggle");
				break;
			//setSleep
			case "volume_change":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setVolume?volume=" . $_options['slider']);
				break;
			case "volume_change_step":
				//volume = up or down
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setVolume?volume=" . $_options['volume'] . "&step=" . $_options['step']);
				break;
			case "mute_on":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setMute?enable=true");
				break;
			case "mute_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setMute?enable=false");
				break;
			case "input_change":
				if(!empty($_options['select'])) {
					$power_state_cmd = $device->getCmd(null, 'power_state');
					if($power_state_cmd->execCmd() !== 'on') {
						YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setPower?power=on");
					}
					YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setInput?input=" . $_options['select']);
				}
				break;
			case "input_change_mode":
				/**
				 * Specifies select mode. If no parameter is specified, actions of input
				  change depend on a Device’s specification
				  Value: "autoplay_disabled" (Restricts Auto Play of Net/USB
				  related Inputs). Available on and after API Version
				 */
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setInput?input=" . $_options['select'] . "&mode=" . $_options['mode']);
				break;
			case "sound_program_change":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setSoundProgram?program=" . $_options['select']);
				break;
			case "3d_surround_on":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/set3dSurround?enable=true");
				break;
			case "3d_surround_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/set3dSurround?enable=false");
				break;
			case "direct_on":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setDirect?enable=true");
				break;
			case "direct_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setDirect?enable=false");
				break;
			case "pure_direct_on":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setPureDirect?enable=true");
				break;
			case "pure_direct_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setPureDirect?enable=false");
				break;
			case "enchancer_on":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setEnhancer?enable=true");
				break;
			case "enchancer_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setEnhancer?enable=false");
				break;
			//setToneControl
			//setEqualizer
			case "equalizer_low_change":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setEqualizer?mode=manual&low=" . $_options['slider']);
				break;
			case "equalizer_mid_change":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setEqualizer?mode=manual&mid=" . $_options['slider']);
				break;
			case "equalizer_high_change":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setEqualizer?mode=manual&high=" . $_options['slider']);
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
			//setContentsDisplay
			//controlCursor
			//controlMenu
			//setActualVolume
			//setAudioSelect
			//setSurroundDecoderType
			//						TUNER
			//getPresetInfo
			//getPlayInfo
			//setBand
			//setFreq
			//case 'recallPreset':
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
			//						Network/USB
			//getPresetInfo
			//getPlayInfo
			case "netusb_playback_play":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=play");
				break;
			case "netusb_playback_stop":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=stop");
				break;
			case "netusb_playback_pause":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=pause");
				break;
			case "netusb_playback_play_pause":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=play_pause");
				break;
			case "netusb_playback_previous":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=previous");
				break;
			case "netusb_playback_next":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=next");
				break;
			case "netusb_playback_fast_reverse_start":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=fast_reverse_start");
				break;
			case "netusb_playback_fast_reverse_end":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=fast_reverse_end");
				break;
			case "netusb_playback_fast_forward_start":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=fast_forward_start");
				break;
			case "netusb_playback_fast_forward_end":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=fast_forward_end");
				break;
			//setPlayPosition
			case "netusb_play_position_change":
				//For setting track play position. This API is available only when input is Server.
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayPosition?position=" . $_options['position']);
				break;
			case "netusb_repeat_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setRepeat?mode=off");
				break;
			case "netusb_repeat_one":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setRepeat?mode=one");
				break;
			case "netusb_repeat_all":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setRepeat?mode=all");
				break;
			case "netusb_shuffle_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setShuffle?mode=off");
				break;
			case "netusb_shuffle_on":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setShuffle?mode=on");
				break;
			case "netusb_shuffle_songs":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setShuffle?mode=songs");
				break;
			case "netusb_shuffle_albums":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setShuffle?mode=albums");
				break;
			case "netusb_toggle_repeat":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/toggleRepeat");
				break;
			case "netusb_toggle_shuffle":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/toggleShuffle");
				break;
			//getListInfo
			//setListControl
			//setSearchString
			case 'netusb_recall_preset':
				if(!empty($_options['select'])) {
					$power_state_cmd = $device->getCmd(null, 'power_state');
					if($power_state_cmd->execCmd() !== 'on') {
						YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setPower?power=on");
					}
					YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/recallPreset?zone=" . $zone . "&num=" . $_options['select']);
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
					$power_state_cmd = $device->getCmd(null, 'power_state');
					if($power_state_cmd->execCmd() !== 'on') {
						YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/$zone/setPower?power=on");
					}
					YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/recallRecentItem?zone=" . $zone . "&num=" . $_options['select']);
					YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setPlayback?playback=play");
				}
				break;
			//clearRecentInfo
			//managePlay
			//manageList
			//getPlayDescription
			//setListSortOption
			//getAccountStatus
			//getServiceInfo
			//						CD
			//getPlayInfo
			//setPlayback
			//toggleTray
			//setRepeat
			//setShuffle
			//toggleRepeat
			//toggleShuffle
			//						Clock
			//getSettings
			//setAutoSync
			//setDateAndTime
			//setClockFormat
			//setAlarmSettings
			default :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la commande ' . $this->getLogicalId());
		}
	}

	/*	 * **********************Getteur Setteur*************************** */
}
