<?php

/**
 * Mock de la classe log
 */
class log
{
    /**
     * Ajoute un message d'erreur dans les logs
     *
     * @param string $msg Message
     * @param string $channel Canal du log
     * @param string $msg Message du log
     */
    public static function add($plugin, $channel, $msg)
    {
        MockedActions::add('log_add', array('msg' => $msg, 'plugin' => $plugin, 'channel' => $channel));
    }
}
