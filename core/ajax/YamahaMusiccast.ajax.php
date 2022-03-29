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

/**
 * Fichier appelé lorsque le plugin effectue une requête Ajax
 */
try {
	// Ajoute le fichier du core qui se charge d'inclure tous les fichiers nécessaires
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

	// Ajoute le fichier de gestion des authentifications
	include_file('core', 'authentification', 'php');

	// Test si l'utilisateur est connecté
	if (!isConnect('admin')) {
		// Lève un exception si l'utilisateur n'est pas connecté avec les bons droits
		throw new \Exception(__('401 - Accès non autorisé', __FILE__));
	}

	// Initialise la gestion des requêtes Ajax
	ajax::init();
	$action = init('action');
	switch($action) {
		case 'linked':
			$id = init('id');
			$ipList = init('ipList');
			if(!empty($ipList)) {
				$ipClientList = explode(",", $ipList);
			} else {
				$ipClientList = array();
			}
			$yamahaMusiccast = YamahaMusiccast::byId($id);
			$yamahaMusiccastIp = $yamahaMusiccast->getConfiguration("ip");
			$yamahaMusiccastZone = $yamahaMusiccast->getConfiguration("zone");
			log::add("YamahaMusiccast", 'info', 'Continuer la méthode Ajax linked ' . $yamahaMusiccastIp . ':' . $yamahaMusiccastZone . ' - ' . $ipList);
			$yamahaMusiccastRole = $yamahaMusiccast->getCmd('info', 'role')->execCmd();
			$groupId;
			$zoneRemote;
			$linkedAdd;
			$linkedRemove;
			log::add("YamahaMusiccast", 'info', 'Role :' . $yamahaMusiccastRole);
			$yamahaMusiccastClientListString = $yamahaMusiccast->getCmd('info', 'client_list')->execCmd();
			if($yamahaMusiccastRole === 'server') {
				$groupId = $yamahaMusiccast->getCmd('info', 'group_id')->execCmd();
				$zoneRemoteVerif = $yamahaMusiccast->getCmd('info', 'server_zone')->execCmd();
				if($zoneRemoteVerif !== $yamahaMusiccastZone) {
					ajax::error('L’appareil ne peux pas diffuser plusieurs flux.' . $zoneRemoteVerif .'!=='. $yamahaMusiccastZone, __FILE__);
				}
				$linkedAdd = array();
				$yamahaMusiccastClientList = explode(";", $yamahaMusiccastClientListString);
				if(empty($ipClientList)) {
					if(!empty($yamahaMusiccastClientListString)) {
						$linkedRemove = $yamahaMusiccastClientList;
						log::add("YamahaMusiccast", 'info', 'On remove tout:' . print_r($yamahaMusiccastClientList, true));
					} else {
						$linkedRemove = array();
					}
				} else {
					$linkedRemove = array();
					log::add("YamahaMusiccast", 'info', 'Test de :' . print_r($ipClientList, true) . ' => ' . print_r($yamahaMusiccastClientList, true));
					if(!empty($ipClientList)){
						foreach ($ipClientList as $ipClient) {
							if(!in_array($ipClient, $yamahaMusiccastClientList)) {
								array_push($linkedAdd, $ipClient);
							}
						}
					}
					if(!empty($yamahaMusiccastClientList)){
						foreach ($yamahaMusiccastClientList as $ipClient) {
							if(!in_array($ipClient, $ipClientList)) {
								array_push($linkedRemove, $ipClient);
							}
						}
					}
				}
				if(!empty($linkedRemove)){
					foreach ($linkedRemove as $ipClient) {
						if(!empty($ipClient)){
							log::add("YamahaMusiccast", 'info', 'Remove de:"' . $ipClient . '"');
							$zoneRemoteList = array(YamahaMusiccast::main); //TODO changer avec la vrai valeur
							$device = YamahaMusiccast::byIP($ipClient, YamahaMusiccast::main);
							//TODO: Gérer les multi-clients.
							$cmd = $device->getCmd('action', 'setClientInfo');
							$cmd->execCmd(array('groupId' => "", 'zoneRemote' => $zoneRemoteList));
						}
					}
					$cmd = $yamahaMusiccast->getCmd('action', 'setServerInfo');
					$cmd->execCmd(array('ipClientList' => $linkedRemove, 'groupId' => $groupId, 'zone' => $yamahaMusiccastZone, 'action' => 'remove'));
				}
				
			} else {
				$groupId = YamahaMusiccastCmd::generateGroupId();
				$zoneRemote = YamahaMusiccast::main;
				$linkedAdd = $ipClientList;
				$linkedRemove = array();
			}

			foreach ($linkedAdd as $ipClient) {
				log::add("YamahaMusiccast", 'info', 'Ajout de:' . $ipClient);
				$zoneRemoteList = array(YamahaMusiccast::main); //TODO changer avec la vrai valeur
				$device = YamahaMusiccast::byIP($ipClient, YamahaMusiccast::main);
				//TODO: Gérer les multi-clients.
				$cmd=$device->getCmd('action', 'setClientInfo');
				$cmd->execCmd(array('groupId' => $groupId, 'zoneRemote' => $zoneRemoteList));
			}

			if(empty($ipClientList)) {
				$cmdSetServerInfo = $yamahaMusiccast->getCmd('action', 'setServerInfo');
				$cmdSetServerInfo->execCmd(array('ipClientList' => $ipClientList, 'groupId' => '00000000000000000000000000000000'));
				$cmd = $yamahaMusiccast->getCmd('action', 'stopDistribution');
			} else {
				$cmdSetServerInfo = $yamahaMusiccast->getCmd('action', 'setServerInfo');
				$cmdSetServerInfo->execCmd(array('ipClientList' => $ipClientList, 'groupId' => $groupId, 'zone' => $yamahaMusiccastZone, 'action' => 'add'));
				$cmd = $yamahaMusiccast->getCmd('action', 'startDistribution');
			}
			$cmd->execCmd();
			YamahaMusiccast::checkDistributionAll();
			ajax::success();
			break;
		case 'searchMusiccast':
			$return = YamahaMusiccast::searchAndSaveDeviceList();
			$nb = count($return);
			if($nb === 0) {
				ajax::error(__('La recherche automatique n’a pas trouvé d’appareil compatible.', __FILE__). ' ' . __('Pour plus d’information consulter la ', __FILE__) . ' <a href="https://lucguinchard.github.io/plugin-Yamaha-musiccast/#faq">' . __('FAQ', __FILE__) . '</a>');
			} else {
				$deviceList = "";
				foreach ($return as $device){
					$deviceList .= $device . ', ';
				}
				ajax::success('La recherche a trouvé ' . $nb . ' zone(s) compatible(s) : ' . substr($deviceList, 0, -2) . '.', __FILE__);
			}
			break;
		case 'searchDirlist':
			$id = init('id');
			$yamahaMusiccast = YamahaMusiccast::byId($id);
			$device[YamahaMusiccast::main] = $yamahaMusiccast;
			$type = init('type');
			$index = init('index');
			$input = init('input');
			$zone = $yamahaMusiccast->getConfiguration('zone');
			$power_state_cmd = $yamahaMusiccast->getCmd(null, 'power_state');
			if($power_state_cmd->execCmd() !== 'on') {
				$yamahaMusiccast->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setPower?power=on");
				log::add("YamahaMusiccast", 'info', 'Allumer l’appareil');
				sleep(1);
			}
			$inputCmd = $yamahaMusiccast->getCmd('info', 'input');
			if($inputCmd->execCmd() !== $input) {
				$yamahaMusiccast->callAPIGET(YamahaMusiccast::url_v1 . "$zone/setInput?input=$input");
				log::add("YamahaMusiccast", 'info', 'Change Input');
			}
			if($type != null) {
				switch($type) {
					case 'select':
						YamahaMusiccast::callListControlSelect($device, $index);
						break;
					case 'return':
						YamahaMusiccast::callListControlReturn($device);
						break;
					// ALL
					case 'main':
					// Pandora
					case 'auto_complete':
					case 'search_artist':
					case 'search_track':
						$text = init('text');
						YamahaMusiccast::callSearchString($device, $type, $text, $index);
						break;
				}
			}
			$netusbListInfo = YamahaMusiccast::callGetNetusbListInfo($device, $input);
			ajax::success($netusbListInfo);
			break;
		case 'playDirlist':
			$id = init('id');
			$yamahaMusiccast = YamahaMusiccast::byId($id);
			$device[YamahaMusiccast::main] = $yamahaMusiccast;
			$input = init('input');
			$index = init('index');
			if($index != null) {
				YamahaMusiccast::callListControlPlay($device, $index);
			}
			ajax::success("OK");
			break;
		case 'searchPlaylist':
			$id = init('id');
			$yamahaMusiccast = YamahaMusiccast::byId($id);
			$device[YamahaMusiccast::main] = $yamahaMusiccast;
			$input = init('input');
			if(empty($input)) {
				$input = "server";
			}
			$index = init('index');
			ajax::success("Cette partie affichera les playlists enregistrées.");
			break;
	}

	// Lève une exception si la requête n'a pas été traitée avec succès (Appel de la fonction ajax::success());
	throw new \Exception(__('Aucune méthode correspondante à : ', __FILE__) . $action);
	/* **********Catch exeption*************** */
} catch (\Exception $e) {
	// Affiche l'exception levé à l'utilisateur
	ajax::error(displayExeption($e), $e->getCode());
}
