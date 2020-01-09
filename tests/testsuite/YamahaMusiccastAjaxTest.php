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

use PHPUnit\Framework\TestCase;

require_once('../../core/php/core.inc.php');

class YamahaMusiccastAjaxTest extends TestCase
{
    /**
     * Appelée avant chaque test
     */
    protected function setUp()
    {
        MockedActions::clear();
    }

    /**
     * Appelée après chaque test
     */
    protected function tearDown()
    {

    }

    /**
     * Obtenir le rendu du fichier à tester
     */
    public function getTestRender()
    {
        ob_start();
        require_once('core/ajax/YamahaMusiccast.ajax.php');
        return ob_get_clean();
    }

    public function testWithoutUserConnected()
    {
        JeedomVars::$isConnected = false;

        $result = $this->getTestRender();
        $actions = MockedActions::get();

        $this->assertEquals('', $result);

        $this->assertCount(2, $actions);
        $this->assertEquals('include_file', $actions[0]['action']);
        $this->assertEquals('authentification', $actions[0]['content']['name']);
        $this->assertEquals('ajax_error', $actions[1]['action']);
        $this->assertEquals('401 - Accès non autorisé', $actions[1]['content']['msg']->getMessage());
    }

    public function testAnswerWithoutRequest()
    {
        JeedomVars::$isConnected = true;
        JeedomVars::$initAnswers['action'] = 'action';

        $result = $this->getTestRender();
        $actions = MockedActions::get();

        $this->assertEquals('', $result);

        $this->assertCount(3, $actions);
        $this->assertEquals('include_file', $actions[0]['action']);
        $this->assertEquals('authentification', $actions[0]['content']['name']);
        $this->assertEquals('ajax_init', $actions[1]['action']);
        $this->assertEquals('ajax_error', $actions[2]['action']);
        $this->assertEquals('Aucune méthode correspondante à : action', $actions[2]['content']['msg']->getMessage());
    }
}
