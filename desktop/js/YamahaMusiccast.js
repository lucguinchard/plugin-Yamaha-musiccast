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


$("#table_cmd").sortable({
	axis: "y",
	cursor: "move",
	items: ".cmd",
	placeholder: "ui-state-highlight",
	tolerance: "intersect",
	forcePlaceholderSize: true
});

/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin-YamahaMusiccast
 */
function addCmdToTable(_cmd) {
	if (!isset(_cmd)) {
		var _cmd = {configuration: {}};
	}
	if (!isset(_cmd.configuration)) {
		_cmd.configuration = {};
	}
	var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
	tr += '<td>';
	tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
	tr += '</td>';
	tr += '<td>';
	tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
	tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
	tr += '</td>';
	tr += '<td>';
	if (is_numeric(_cmd.id)) {
		tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
		tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
	}
	tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
	tr += '</td>';
	tr += '</tr>';
	$('#table_cmd tbody').append(tr);
	$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
	if (isset(_cmd.type)) {
		$('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
	}
	jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}

$('.eqLogicAction[data-action=searchMusiccast]').on('click', function () {
	$.ajax({
		type: "POST",
		url: "plugins/YamahaMusiccast/core/ajax/YamahaMusiccast.ajax.php",
		data: {
			action: "searchMusiccast"
		},
		dataType: 'json',
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			if (data.state !== 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			} else {
				$('#div_alert').showAlert({message: data.result, level: 'success'});
			}
			modifyWithoutSave = false;
			var vars = getUrlVars();
			var url = 'index.php?';
			for (var i in vars) {
				if (i !== 'id' && i !== 'saveSuccessFull' && i !== 'removeSuccessFull') {
					url += i + '=' + vars[i].replace('#', '') + '&';
				}
			}
			url += 'id=' + data.id + '&saveSuccessFull=1';
			if (document.location.toString().match('#')) {
				url += '#' + document.location.toString().split('#')[1];
			}
			loadPage(url);
		}
	});
});
