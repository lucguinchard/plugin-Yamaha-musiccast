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
				log::add('YamahaMusiccast', 'info', 'TODO:main_volume_change' . "/YamahaExtendedControl/v1/main/setVolume?volume=" . $_options['volume']);
				YamahaMusiccast::CallAPI("GET", $device, "/YamahaExtendedControl/v1/main/setVolume?volume=" . $_options['volume']);
				break;
		}
	}

	/*	 * **********************Getteur Setteur*************************** */
}
