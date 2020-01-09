<?php

/*
require_once('../../mocked_core.php');

/**
 * Mock de la classe system
 */

class system
{
    /**
     * @var string Réponse de la méthode getCmdSudo
     * Permet de bloquer l'exécution de la commande.
     */
    public static $cmdSudo = 'exit && ';

    /**
     * Renvoie la valeur stockée dans static::$cmdSudo
     *
     * @return string Commande sudo
     */
    public static function getCmdSudo()
    {
        MockedActions::add('get_cmd_sudo');
        return self::$cmdSudo;
    }
}

