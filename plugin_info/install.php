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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function YamahaMusiccast_install()
{
	YamahaMusiccast_update();
}

function YamahaMusiccast_update()
{
	$cron = cron::byClassAndFunction('YamahaMusiccast', 'socket_start');
	if (!is_object($cron)) {
		$cron = new cron();
		$cron->setClass('YamahaMusiccast');
		$cron->setFunction('socket_start');
		$cron->setEnable(1);
		$cron->setDeamon(1);
		$cron->setDeamonSleepTime(0);
		$cron->setTimeout(0);
		$cron->setSchedule('* * * * *');
		$cron->save();
	}
}


function YamahaMusiccast_remove()
{
	$cron = cron::byClassAndFunction('YamahaMusiccast', 'socket_start');
	if (is_object($cron)) {
		$cron->remove();
	}
}
