function elementShowOrHide(element, show) {
	if (show) {
		element.show();
	} else {
		element.hide();
	}
}

function getInputIconMap() {
	const inputIconMap = new Map();
	/**
	* ['onelinedefile'] : permet d'afficher le titre en cours.
	* ['divInputPochette'] Permet d'afficher la pochette
	* ['nav_playlist']
	* ['divInputIcon'] Permet d'afficher l'icone en grand (quand il y a pas de pochette)
	* ['icon']
	*/
	inputIconMap.set('airplay', [true, false, false, true, '<span class="fa-stack fa-lg"><i class="fas fa-mobile-alt fa-stack-2x"></i><i class="fas fa-music fa-stack-1x" style="font-size:0.5em"></i></span>']);
	inputIconMap.set('cd', [true, false, false, true, '<i class="fas fa-compact-disc"></i>']);
	inputIconMap.set('tuner', [true, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('multi_ch', [true, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('phono', [false, false, false, true, '<i class="fas fa-record-vinyl"></i>']);
	inputIconMap.set('hdmi', [false, false, false, true, '<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi1', [false, false, false, true, '<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi2', [false, false, false, true, '<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi3', [false, false, false, true, '<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi4', [false, false, false, true, '<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi5', [false, false, false, true, '<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi6', [false, false, false, true, '<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi7', [false, false, false, true, '<i class="fas fa-desktop"></i>']);
	inputIconMap.set('hdmi8', [false, false, false, true, '<i class="fas fa-desktop"></i>']);
	inputIconMap.set('av1', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('av2', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('av3', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('av4', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('av5', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('av6', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('av7', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('v_aux', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('aux1', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('aux2', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('aux', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('audio1', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('audio2', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('audio3', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('audio4', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('audio5', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('audio_cd', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('audio', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('optical1', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('optical2', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('optical', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('coaxial1', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('coaxial2', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('coaxial', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('digital1', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('digital2', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('digital', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('line1', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('line2', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('line3', [false, false, false, true,  '<i class="fas fa-music"></i>']);
	inputIconMap.set('line_cd', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('analog', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('tv', [false, false, false, true, '<i class="icon techno-television4"></i>']);
	inputIconMap.set('bd_dvd', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('usb_dac', [true, false, false, true, '<i class="icon techno-memory"></i>']);
	inputIconMap.set('usb', [true, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('bluetooth', [true, false, true, true, '<i class="fab fa-bluetooth icon_blue"></i>']);
	inputIconMap.set('server', [true, false, false, true, '<i class="fas fa-hdd"></i>']);
	inputIconMap.set('net_radio', [true, true, true, false, '<i class="fas fa-music"></i>']);
	inputIconMap.set('mc_link', [true, true, false, false, '<i class="fas fa-link"></i>']);
	inputIconMap.set('main_sync', [false, false, false, true, '<i class="fas fa-music"></i>']);
	inputIconMap.set('none', [false, false, false, true, '<i class="fas fa-music"></i>']);
	return inputIconMap;
	/**
			cd / tuner / multi_ch / phono / hdmi1 / hdmi2 / hdmi3 / hdmi4 / hdmi5 / hdmi6 / hdmi7 /
	hdmi8 / hdmi / av1 / av2 / av3 / av4 / av5 / av6 / av7 / v_aux / aux1 / aux2 / aux / audio1 /
	audio2 / audio3 / audio4 / audio5 / audio_cd / audio / optical1 / optical2 / optical / coaxial1 /
	coaxial2 / coaxial / digital1 / digital2 / digital / line1 / line2 / line3 / line_cd / analog / tv /
	bd_dvd / usb_dac / usb / bluetooth / server / net_radio / napster / pandora / siriusxm /
	spotify / juke / airplay / radiko / qobuz / tidal / deezer / mc_link / main_sync / none
	 */
}