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

include_file('core', 'authentification', 'php');

if (!isConnect()) {
	include_file('desktop', '404', 'php');
	die();
}
?>
<form class="form-horizontal">
	<fieldset>
		<div class="container">
			<h3><i class="fas fa-exchange-alt"></i> {{Socket}}</h3>
			<div class="form-group">
				<div>{{Les appareils Musiccast dialogue avec ce plugin à l’aide d’un socket php ouvert par le démon sur la machine Jeedom.}}</div>
			</div>
			<div class="form-group">
				<label class="col-lg-2 control-label">{{Numéro de port :}}</label>
				<div class="col-lg-4">
					<input type="number" class="configKey form-control" data-l1key="socket.port" value="" placeholder="9999"/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-2 control-label">{{Nom :}}</label>
				<div class="col-lg-4">
					<input type="text" class="configKey form-control" data-l1key="socket.name" value="" placeholder="jeedom_musiccast"/>
				</div>
			</div>
			<br/>
			<h3><i class="fas fa-coffee"></i> {{Offrez-un café}}</h3>
			<div class="form-group">
				<div>
					{{Ce plugin est entièrement gratuit et indépendant, Mais si cela vous enchante j’accepte les}} <a href="https://www.paypal.com/paypalme/lucguinchard" title="{{thé/café/bière}}" target="_blank"><i class="fas fa-coffee"></i></a>.
				</div>
			</div>
		</div>
	</fieldset>
	<br/>
	<br/>
</form>
