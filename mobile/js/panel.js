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

function initYamahaMusiccastPanel(_object_id) {
    if(typeof jeedomUtils.setBackgroundImage == 'function'){
        jeedomUtils.setBackgroundImage('plugins/YamahaMusiccast/core/img/panel.jpg');
    }
    jeedom.object.all({
        onlyHasEqLogic : 'YamahaMusiccast',
        error: function (error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
            jeedomUtils.loadPanel(error.message);
        },
        success: function (objects) {
            var li = ' <ul data-role="listview">';
            li += '<li></span><a href="#" class="link" data-page="panel" data-plugin="YamahaMusiccast" data-title="Yamaha Musiccast" data-option=""><span><i class="fas fa-globe"></i></span>{{Tous}}</a></li>'
            for (var i in objects) {
                if (objects[i].isVisible == 1) {
                    var icon = '';
                    if (isset(objects[i].display) && isset(objects[i].display.icon)) {
                        icon = objects[i].display.icon;
                    }
                    li += '<li></span><a href="#" class="link" data-page="panel" data-plugin="YamahaMusiccast" data-title="' + icon.replace(/\"/g, "\'") + ' ' + objects[i].name + '" data-option="' + objects[i].id + '"><span>' + icon + '</span> ' + objects[i].name + '</a></li>';
                }
            }
            li += '</ul>';
            jeedomUtils.loadPanel(li);
        }
    });
    displayYamahaMusiccast(_object_id);

    $(window).on("resize", function (event) {
        jeedomUtils.setTileSize('.eqLogic');
        $('#div_displayEquipementYamahaMusiccast').packery({gutter : 0});
        $('#div_displayEquipementYamahaMusiccast').packery({gutter : 0});
    });
}

function displayYamahaMusiccast(_object_id) {
    $.showLoading();
    $.ajax({
        type: 'POST',
        url: 'plugins/YamahaMusiccast/core/ajax/YamahaMusiccast.ajax.php',
        data: {
            action: 'getYamahaMusiccast',
            object_id: _object_id,
            version: 'mobile'
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                jeedomUtils.showAlert({
                    message: data.result,
                    level: 'danger'
                })
                return;
            }
            $('#div_displayEquipementYamahaMusiccast').empty();
            for (var i in data.result.eqLogics) {
                $('#div_displayEquipementYamahaMusiccast').append(data.result.eqLogics[i]).trigger('create');
            }
            jeedomUtils.setTileSize('.eqLogic');
            $('.eqLogic-widget').addClass('displayObjectName');
            $('#div_displayEquipementYamahaMusiccast').packery({gutter : 0});
            $('#div_displayEquipementYamahaMusiccast').packery({gutter : 0});
            $.hideLoading();
        }
    });
}

initYamahaMusiccastPanel();