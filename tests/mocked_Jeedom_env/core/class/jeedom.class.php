<?php

/**
 * Mock de la classe Jeedom
 */
class jeedom
{
    /**
     * @var bool Réponse pour la commande jeedom::isCapable
     */
    public static $isCapableAnswer = false;

    /**
     * @var string Nom du matériel reconnu par Jeedom
     */
    public static $hardwareName;

    /**
     * Obtenir le nom du matériel.
     *
     * @return string Valeur de jeedom::$hardwareName
     */
    public static function getHardwareName()
    {
        return jeedom::$hardwareName;
    }

    /**
     * Test si Jeedom peut exécutée une commande.
     *
     * @param string $str Nom de la commande à utiliser (inutilisé)
     *
     * @return bool Valeur de jeedom::$isCapableAnswer
     */
    public static function isCapable($str)
    {
        return self::$isCapableAnswer;
    }
}
