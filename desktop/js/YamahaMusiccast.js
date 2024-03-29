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

$('.eqLogicAttr[data-l1key=configuration][data-l2key=model_name]').on('change', function () {
	var url = 'plugins/YamahaMusiccast/core/img/' + $(this).value() + '.png';
	$('#img_device_not_found a').attr('href', 'https://github.com/lucguinchard/plugin-Yamaha-musiccast/issues/new?assignees=&labels=type%3AEnhancement&template=LOGO_DEVICE_EMPTY.md&title=L%E2%80%99image+de+mon+appareil+%60' + $(this).value() + '%60+n%E2%80%99existe+pas.');
	$.get(url, function(data){
		$('#img_device').attr('src', url);
		$('#img_device_not_found').css('display', 'none');
	}).fail(function() {
		$('#img_device').attr('src', '/plugins/YamahaMusiccast/plugin_info/YamahaMusiccast_icon.png');
		$('#img_device_not_found').css('display', 'block');
	});
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
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 200px;" placeholder="{{Nom}}">';
	tr += '</td>';
	tr += '<td>';
	tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
	tr += '</td>';
	tr += '<td>';
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

	let type = init(_cmd.configuration.type);
	let table;
	if (type === 'netusb') {
		table = "#table_netusb";
	} else if(type === 'tuner') {
		table = "#table_tuner";
	} else if(type === 'cd') {
		table = "#table_cd";
	} else if(type === 'clock') {
		table = "#table_clock";
	} else if(type === 'bluetooth') {
		table = "#table_bluetooth";
	} else if(type === 'distribution') {
		table = "#table_distribution";
	} else {
		table = "#table_system";
	}

	$(table + ' tbody').append(tr);
	$(table + ' tbody tr:last').setValues(_cmd, '.cmdAttr');

	if (isset(_cmd.type)) {
		$(table + ' tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
	}
	jeedom.cmd.changeType($(table + ' tbody tr:last'), init(_cmd.subType));
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
			jeedomUtils.loadPage(url);
			if (data.state !== 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			} else {
				$('#div_alert').showAlert({message: data.result, level: 'success'});
			}
		}
	});
});
