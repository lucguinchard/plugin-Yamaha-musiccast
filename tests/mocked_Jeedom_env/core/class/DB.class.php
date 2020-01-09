<?php

/**
 * Mocked des requêtes
 */
class MockedStatement
{
    /**
     * @var Requête SQL
     */
    private $query;
    /**
     * @var Données de la requête
     */
    private $data;

    /**
     * Constructeur
     *
     * @param $query Requête SQL
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Mock de l'exécution d'une requête
     *
     * @param array $data Données de la requête
     */
    public function execute($data = null)
    {
        $this->data = $data;
        MockedActions::add('query_execute', array('query' => $this->query, 'data' => $this->data));
    }

    /**
     * Obtenir les résultats d'une requête
     *
     * @param integer $fetchMethod Méthode de récupération (inutilisé)
     *
     * @return mixed Données définies par DB::setAnswer au format PDO
     */
    public function fetchAll($fetchMethod)
    {
        return DB::$answer;
    }
}

/**
 * Mock de la classe PDO
 */
class MockedPDO
{
    public function prepare($query)
    {
        return new MockedStatement($query);
    }
}

/**
 * Mock de la classe DB de Jeedom
 */
class DB
{
    /**
     * @var Connexion à la base de données
     */
    private static $connection = null;

    /**
     * @var Réponse à fournir à une requête
     */
    public static $answer;

    /**
     * Initialisation de la connexion
     */
    public static function init()
    {
        static::$connection = new MockedPDO();
    }

    /**
     * @return Connexion Obtenir l'objet de la connexion
     */
    public static function getConnection()
    {
        return static::$connection;
    }

    /**
     * @param mixed $answer Définir la réponse à une requête
     */
    public static function setAnswer($answer)
    {
        if ($answer !== null) {
            static::$answer = array($answer);
        } else {
            static::$answer = array();
        }
    }
}
