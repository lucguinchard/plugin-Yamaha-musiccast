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
	tr += '<td class="hidden-xs">';
	tr += '<span class="cmdAttr" data-l1key="id"></span>';
	tr += '</td>';
	tr += '<td>';
	tr += '<div class="input-group">';
	tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">';
	tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>';
	tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>';
	tr += '</div>';
	tr += '<td>';;
	tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
	tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
	tr += '</td>';
	tr += '<td>';
	tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> ';
	tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> ';
	tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label> ';
	tr += '<div style="margin-top:7px;">';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
	tr += '</div>';
	tr += '<td>';;
	tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>';
	tr += '</td>';
	tr += '<td>';
	  if (is_numeric(_cmd.id)) {
		tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure" title="{{Configuration avancée}}"><i class="fas fa-cogs"></i></a> ';
		tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
	  }
	tr += '</td>';
	tr += '<td>';
	tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i>';
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
