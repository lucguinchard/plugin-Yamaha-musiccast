<?php

require_once('../../mocked_core.php');

/**
 * Mock de la classe scenario
 */
class scenario
{
    /**
     * @var array Liste des scénarios de Jeedom
     */
    public static $scenariosList;

    /**
     * Initialise les scénarios de Jeedom
     */
    public static function init()
    {
        static::$scenariosList = array(
            new scenarioItem(1, 'First scenario', 'none', 1, true),
            new scenarioItem(2, 'Second scenario', 'realtime', 1, true),
            new scenarioItem(3, 'First scenario', 'none', 0, true),
            new scenarioItem(4, 'First scenario', 'none', 1, false)
        );
    }

    /**
     * Obtenir la liste des scénarios
     *
     * @return array Liste des scénarios
     */
    public static function all()
    {
        return static::$scenariosList;
    }

    /**
     * Obtenir un scénario à partir de son identifiant
     *
     * @param mixed $scenarioId Identifiant du scénario
     *
     * @return scenarioItem Objet du scénario
     */
    public static function byId($scenarioId)
    {
        return static::$scenariosList[$scenarioId - 1];
    }
}

/**
 * Mock d'un scenario
 */
class scenarioItem
{
    /**
     * @var integer Identifiant du scénario
     */
    public $id;
    /**
     * @var string Nom du scénario
     */
    public $name;
    public $logmode;
    public $syncmode;
    public $enabled;
    public static $enabledScenario = null;
    public static $lastLaunch = null;
    public static $isRunning = null;

    /**
     * Constructeur d'un scenario
     *
     * @param null $id Identifiant
     * @param null $name Nom
     * @param null $logmode Mode des logs
     * @param null $syncmode Logs synchrone
     * @param null $enabled Etat du scénario
     */
    public function __construct($id = null, $name = null, $logmode = null, $syncmode = null, $enabled = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->logmode = $logmode;
        $this->syncmode = $syncmode;
        $this->enabled = $enabled;
    }

    /**
     * Obtenir l'identifiant du scénario
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obtenir le nom du plugin
     *
     * @return string Nom du plugin
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Obtenir une information sur la configuration du scénario
     * Répond à logmode et syncmod
     *
     * @param $config Configuration demandée
     * @return mixed Information sur la configuration
     */
    public function getConfiguration($config)
    {
        switch ($config) {
            case 'logmode':
                return $this->logmode;
                break;
            case 'syncmode':
                return $this->syncmode;
                break;
        }
        return false;
    }

    /**
     * Renvoie l'état d'activation du scénario
     * scenarioItem::$enabledScenario permet de forcer la réponse pour tous les scénarios
     *
     * @return int Etat de l'activation
     */
    public function getIsActive()
    {
        $result = 0;
        if (scenarioItem::$enabledScenario != null) {
            if (scenarioItem::$enabledScenario) {
                $result = 1;
            }
        } else {
            if ($this->enabled) {
                $result = 1;
            }
        }
        return $result;
    }

    /**
     * Renvoie la date de dernier lancement du scénario
     * scenarioItem::$lastLaunch permet de forcer la réponse pour tous les scénarios
     *
     * @return string Date du jour ou scenarioItem::$lastLaunch
     */
    public function getLastLaunch()
    {
        $result = "";
        if (scenarioItem::$lastLaunch != null) {
            $result = static::$lastLaunch->format('Y-m-d H:i:s');
        } else {
            $today = new \DateTime('now');
            $result = $today->format('Y-m-d H:i:s');
        }
        return $result;
    }

    /**
     * Renvoie l'état de fonctionnement du scénario
     * scenarioItem::$isRunning permet de forcer la réponse pour tous les scénarios
     *
     * @return bool Etat
     */
    public function running()
    {
        $result = true;
        if (scenarioItem::$isRunning != null) {
            $result = scenarioItem::$isRunning;
        }
        return $result;
    }

    /**
     * Modifie la configuration d'un scénario
     *
     * @param $config Configuration à modifier
     * @param $value Valeur de cette configuration
     */
    public function setConfiguration($config, $value)
    {
        MockedActions::add('set_configuration', array('config' => $config, 'value' => $value));
    }

    /**
     * Sauvegarde un scénario
     */
    public function save()
    {
        MockedActions::add('save');
    }

    /**
     * Supprime un scénario
     */
    public function remove()
    {
        MockedActions::add('remove');
    }
}
