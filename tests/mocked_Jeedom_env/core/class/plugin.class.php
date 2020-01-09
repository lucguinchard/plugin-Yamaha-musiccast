<?php

/**
 * Mock de la classe config
 */
class plugin
{
    private static $pluginsIds = array('thetemplate' => 0, 'IOptimize' => 1, 'supa_plugin' => 2);

    /**
     * Renvoie la liste des plugins
     *
     * @return array Liste des plugins
     */
    public static function listPlugin()
    {
        $result = array();
        array_push($result, new pluginItem('thetemplate', 'TheTemplate', true));
        array_push($result, new pluginItem('IOptimize', 'IOptimize', false));
        array_push($result, new pluginItem('supa_plugin', 'A superb plugin', true));
        return $result;
    }

    /**
     * Obtenir un plugin à partir de son identifiant
     *
     * @param string $id Identifiant du plugin
     *
     * @return pluginItem Objet du plugin
     */
    public static function byId($id)
    {
        $result = null;
        if (array_key_exists($id, static::$pluginsIds)) {
            $result = static::listPlugin()[static::$pluginsIds[$id]];
        } else {
            // Renvoie toujours un plugin valide
            $result = new pluginItem($id);
        }
        return $result;
    }
}

/**
 * Mock de l'objet d'un plugin
 */
class pluginItem
{
    /**
     * @var string Identifnat du plugin
     */
    public $id;
    /**
     * @var string Nom du plugin
     */
    public $name;
    /**
     * @var bool Etat du plugin
     */
    public $enabled;

    /**
     * @var string Chemin du répertoire des plugins
     */
    public static $basePluginPath = 'MockedPlugins';

    /**
     * Constructeur
     *
     * @param null $id Identifiant du plugin
     * @param null $name Nom du plugin
     * @param null $enabled Etat du plugin
     */
    public function __construct($id = null, $name = null, $enabled = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->enabled = $enabled;
    }

    /**
     * Obtenir l'identifiant du plugin
     *
     * @return string Identifiant du plugin
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
     * Obtenir l'état du plugin
     *
     * @return int Etat du plugin
     */
    public function isActive()
    {
        $result = 0;
        if ($this->enabled) {
            $result = 1;
        }
        return $result;
    }

    /**
     * Obtenir le chemin du fichier d'information du plugin
     *
     * @return string Chemin du fichier d'information
     */
    public function getFilePath()
    {
        return static::$basePluginPath . '/' . $this->id . '/plugin_info/info.json';
    }
}