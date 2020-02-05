<?php

/**
 * Mock de la classe Ajax
 */
class ajax
{
    /**
     * Initialise la requête Ajax
     */
    public static function init()
    {
        MockedActions::add('ajax_init');
    }

    /**
     * Renvoie un message d'erreur
     *
     * @param string $msg Message
     * @param string $code Code de l'erreur
     */
    public static function error($msg, $code)
    {
        MockedActions::add('ajax_error', array('msg' => $msg, 'code' => $code));
    }

    /**
     * Renvoie une confirmation de la requête.
     */
    public static function success()
    {
        MockedActions::add('ajax_success');
    }

}