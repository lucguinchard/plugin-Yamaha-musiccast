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
		switch ($this->getLogicalId()) {
			case "main_mute_on":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/main/setMute?enable=true");
				break;
			case "main_mute_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/main/setMute?enable=false");
				break;
			case "main_power_on":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/main/setPower?power=on");
				break;
			case "main_power_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/main/setPower?power=standby");
				break;
			case "main_power_toggle":
				log::add('YamahaMusiccast', 'info', 'TODO:main_power_Toggle');
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/main/setPower?power=toggle");
				break;
			case "main_volume_change":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/main/setVolume?volume=" . $_options['volume']);
				break;
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
			case "netusb_repeat_off":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setRepeat?mode=off");
				break;
			case "netusb_repeat_one":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setRepeat?mode=one");
				break;
			case "netusb_repeat_all":
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/netusb/setRepeat?mode=all");
				break;
			default :
				log::add('YamahaMusiccast', 'info', 'TODO:Créer la commande ' . $this->getLogicalId());
		}
	}

	/*	 * **********************Getteur Setteur*************************** */
}
