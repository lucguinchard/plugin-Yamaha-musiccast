function elementShowOrHide(element, show) {
	if (show) {
		element.show();
	} else {
		element.hide();
	}
}

function navigateButton(uid_value, id) {
	let play_stop = $("#" + uid_value + "_play_stop").val();
	if(play_stop === '') return;
	let input = $("#" + uid_value + "_input").val();
	if(input === '') return;

	let uid = $('.YamahaMusiccast[data-eqLogic_uid=' + uid_value +']');

	let class_play = uid.find('.play');
	let class_pause = uid.find('.pause');
	let class_stop = uid.find('.stop');
	let class_action_list = uid.find('.action_list');
	let class_dir_list = uid.find('.dir_list');
	let class_onelinedefile = uid.find('.one-line-defile');
	let class_divInputPochette = uid.find('.divInputPochette');
	let class_nav_playlist = uid.find('.nav_playlist');
	let class_divInputIcon = uid.find('.panelMusic .divInputIcon');
	let class_pochette_input = uid.find('.panelMusic .pochette_input');
	let inputIconMap = getInputIconMap();

	if(inputIconMap.has(input)) {
		let icon = inputIconMap.get(input);
		elementShowOrHide(class_onelinedefile, icon[0]);
		elementShowOrHide(class_divInputPochette, icon[1]);
		elementShowOrHide(class_divInputIcon, !icon[1]);
		elementShowOrHide(class_nav_playlist, icon[2] !== false);
		switch (icon[2]) {
			case 'stop':
				class_pause.css('display', 'none');
				class_action_list.css('display', 'none');
				if (play_stop === 'play') {
					class_play.css('display', 'none');
					class_stop.css('display', 'table-cell');
				} else if (play_stop === 'pause' || play_stop === 'stop') {
					class_stop.css('display', 'none');
					class_play.css('display', 'table-cell');
				}
			break;
			case 'play_pause':
				class_action_list.css('display', 'table-cell');
				class_stop.css('display', 'none');
				if (play_stop === 'play') {
					class_play.css('display', 'none');
					class_pause.css('display', 'table-cell');
				} else if (play_stop === 'pause' || play_stop === 'stop') {
					class_pause.css('display', 'none');
					class_play.css('display', 'table-cell');
				}
			break;
		}
		class_divInputIcon.empty().append(icon[4]).append("<span>" + input + "</span>");
	} else {
		class_onelinedefile.show();
		class_nav_playlist.show();
		let url = '/plugins/YamahaMusiccast/data/input/' + input + '.png';
		$.get(url, function(data){
			class_pochette_input.attr('src', url);
		}).fail(function() {
			class_pochette_input.attr('src', '/plugins/YamahaMusiccast/plugin_info/YamahaMusiccast_icon.png');
		});
		class_pochette_input.show();
		class_divInputPochette.show();
		class_divInputIcon.hide();
		class_stop.css('display', 'none');
		class_action_list.css('display', 'table-cell');
		if (play_stop === 'play') {
			class_play.css('display', 'none');
			class_pause.css('display', 'table-cell');
		} else if (play_stop === 'pause' || play_stop === 'stop') {
			class_pause.css('display', 'none');
			class_play.css('display', 'table-cell');
		}
	}
	elementShowOrHide(class_dir_list, isInputDirSearch(input));
	class_dir_list.attr("onclick", "searchDirlist" + uid_value + "('" + id + "', null, '" + input + "');");
}

function isInputDirSearch(input) {
	let inputDirSearchMap = [
		"usb",
		"server",
		"net_radio",
		"qobuz",
		"deezer",
		"tidal",
		"napster"
	];
	return inputDirSearchMap.includes(input);
}

function getInputIconMap() {
	let inputIconMap = new Map();
	/**
	* ['onelinedefile'] : permet d’afficher le titre en cours.
	* ['divInputPochette'] Permet d’afficher la pochette.
	* ['nav_playlist'] Permet de savoir qu’elle est le type de barre de navigation à afficher.
	* ['divInputIcon'] Permet d’afficher l’icone en grand (quand il y a pas de pochette).
	* ['icon']
	*/
	inputIconMap.set('airplay',		[true,	false,	"play_pause",	true,	'<span class="fa-stack fa-lg"><i class="fas fa-mobile-alt fa-stack-1x"></i><i class="fas fa-music fa-stack-1x" style="font-size:0.5em"></i></span>']);
	inputIconMap.set('cd',			[true,	false,	"play_pause",	true,	'<i class="fas fa-compact-disc"></i>']);
	inputIconMap.set('tuner',		[false,	false,	false,			true,	'<i class="fas fa-broadcast-tower"></i>']);
	inputIconMap.set('multi_ch',	[true,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('phono',		[false,	false,	false,			true,	'<i class="fas fa-record-vinyl"></i>']);
	inputIconMap.set('hdmi',		[false,	false,	false,			true,	'<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi1',		[false,	false,	false,			true,	'<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi2',		[false,	false,	false,			true,	'<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi3',		[false,	false,	false,			true,	'<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi4',		[false,	false,	false,			true,	'<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi5',		[false,	false,	false,			true,	'<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi6',		[false,	false,	false,			true,	'<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi7',		[false,	false,	false,			true,	'<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi8',		[false,	false,	false,			true,	'<i class="fas fa-desktop"></i>']);
	inputIconMap.set('av1',			[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('av2',			[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('av3',			[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('av4',			[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('av5',			[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('av6',			[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('av7',			[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('v_aux',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('aux1',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('aux2',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('aux',			[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('audio1',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('audio2',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('audio3',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('audio4',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('audio5',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('audio_cd',	[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('audio',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('optical1',	[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('optical2',	[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('optical',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('coaxial1',	[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('coaxial2',	[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('coaxial',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('digital1',	[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('digital2',	[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('digital',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('line1',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('line2',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('line3',		[false,	false,	false,			true,	 '<i class="fas fa-music"></i>']);
	inputIconMap.set('line_cd',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('analog',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('tv',			[false,	false,	false,			true,	'<i class="icon techno-television4"></i>']);
	inputIconMap.set('bd_dvd',		[false,	false,	"play_pause",	true,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('usb_dac',		[true,	false,	false,			true,	'<i class="icon techno-memory"></i>']);
	inputIconMap.set('usb',			[true,	false,	"play_pause",	true,	'<i class="icon techno-memory"></i>']);
	inputIconMap.set('bluetooth',	[true,	false,	"play_pause",	true,	'<i class="fab fa-bluetooth icon_blue"></i>']);
	inputIconMap.set('server',		[true,	true,	"play_pause",	true,	'<i class="fas fa-hdd"></i>']);
	inputIconMap.set('net_radio',	[true,	true,	"stop",			false,	'<i class="fas fa-music"></i>']);
	inputIconMap.set('mc_link',		[true,	false,	false,			false,	'<i class="fas fa-link"></i>']);
	inputIconMap.set('main_sync',	[false,	false,	false,			true,	'<i class="fas fa-link"></i>']);
	inputIconMap.set('none',		[false,	false,	false,			true,	'<i class="fas fa-music"></i>']);
	return inputIconMap;
}