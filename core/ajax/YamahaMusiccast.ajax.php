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
		case 'searchMusiccast':
			$return = YamahaMusiccast::searchAndSaveDeviceList();
			$nb = count($return);
			if($nb === 0) {
				ajax::error(__('La recherche automatique n’a pas trouvé d’appareil compatible.'). ' ' . __('Pour plus d’information consulter la ') . ' <a href="https://lucguinchard.github.io/plugin-Yamaha-musiccast/fr_FR/#tocAnchor-1-5">' . __('FAQ') . '</a>');
			} else {
				$deviceList = "";
				foreach ($return as $device){
					foreach ($device as $zone){
						$deviceList .= $zone['name'] . '-' . $zone['zone'] . ', ';
					}
				}
				ajax::error('La recherche a trouvé ' . $nb . ' zone(s) compatible(s) : ' . substr($deviceList, 0, -2) . '');
			}
			break;
		case 'saveIP':
			$ip = init('ip');
			$return = YamahaMusiccast::saveDeviceIp($ip);
			$nb = count($return);
			if($nb === 0) {
				ajax::error(__('La recherche n’a pas trouvé d’appareil compatible pour : ' . $ip));
			} else {
				$deviceList = "";
				foreach ($return as $zone){
					$deviceList .= $zone['name'] . '-' . $zone['zone'] . ', ';
				}
				ajax::error('La recherche a trouvé un appareil compatible avec la(es) zone(s) suivante(s) : ' . substr($deviceList, 0, -2));
			}
			break;
	}

	// Lève une exception si la requête n'a pas été traitée avec succès (Appel de la fonction ajax::success());
	throw new \Exception(__('Aucune méthode correspondante à : ', __FILE__) . $action);
	/* **********Catch exeption*************** */
} catch (\Exception $e) {
	// Affiche l'exception levé à l'utilisateur
	ajax::error(displayExeption($e), $e->getCode());
}
