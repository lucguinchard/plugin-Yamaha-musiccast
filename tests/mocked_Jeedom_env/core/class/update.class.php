<?php

require_once('../../mocked_core.php');

/**
 * Mock de la classe udpate
 */
class update
{
    /**
     * @var mixed Résultat fournit par la méthode byId
     */
    public static $byIdResult = null;

    /**
     * @var mixed Résultat fournit par la méthode byLogicielId
     */
    public static $byLogicalIdResult = null;

    /**
     * Mock de la méthode permettant d'obtenir un objet par son identifiant
     *
     * @param mixed $id Identifiant de l'objet
     *
     * @return mixed Objet demandé défini par static::$byIdResult
     */
    public static function byId($id)
    {
        return static::$byIdResult;
    }

    /**
     * Mock de la méthode permettant d'obtenir un objet logique par son identifiant
     *
     * @param mixed $id Identifiant de l'objet
     *
     * @return mixed Objet demandé défini par static::$byLogicielIdResult
     */
    public static function byLogicalId($id)
    {
        $result = null;
        if (is_array(static::$byLogicalIdResult)) {
            $result = static::$byLogicalIdResult[$id];
        } else {
            $result = static::$byLogicalIdResult;
        }
        return $result;
    }
}

