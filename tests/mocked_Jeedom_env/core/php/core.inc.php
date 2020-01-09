<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Fichier permetant d'inclure l'ensemble des fichiers de l'environnement en mock
 */

require_once(dirname(__FILE__) . '/../class/ajax.class.php');
require_once(dirname(__FILE__) . '/../class/config.class.php');
require_once(dirname(__FILE__) . '/../class/jeedom.class.php');
require_once(dirname(__FILE__) . '/../class/log.class.php');
require_once(dirname(__FILE__) . '/../class/plugin.class.php');
require_once(dirname(__FILE__) . '/../class/scenario.class.php');
require_once(dirname(__FILE__) . '/../class/system.class.php');
require_once(dirname(__FILE__) . '/../class/update.class.php');
require_once(dirname(__FILE__) . '/../class/DB.class.php');
require_once(dirname(__FILE__) . '/../../mocked_core.php');
