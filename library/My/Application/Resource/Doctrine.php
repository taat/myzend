<?php

/**
 * Doctrine Resource Plugin for Zend Framework Application
 *
 * Installation:
 * 1. Create default directory structure for Doctrine:
 * /application/data/fixtures/
 * /application/models/
 * /application/models/generated/
 * /application/migrations/
 * /application/data/sql/
 * /application/schema/
 * 2. Add following lines to application.ini:
 * pluginPaths.My_Application_Resource = "My/Application/Resource"
 * resources.doctrine.adapter  = pgsql
 * resources.doctrine.host     = localhost
 * resources.doctrine.dbname   = test
 * resources.doctrine.username = test
 * resources.doctrine.password = test
 * ; these are default, neded only if folders are not in default structure:
 * ; resources.doctrine.data_fixtures_path = APPLICATION_PATH "/data/fixtures/"
 * ; resources.doctrine.models_path = APPLICATION_PATH  "/models/"
 * ; resources.doctrine.generated_models_path = APPLICATION_PATH "/models/generated/"
 * ; resources.doctrine.migrations_path = APPLICATION_PATH "/migrations/"
 * ; resources.doctrine.sql_path = APPLICATION_PATH "/data/sql/"
 * ; resources.doctrine.yaml_schema_path = APPLICATION_PATH "/schema/"
 *
 * Doctrine command line interpreter file:
 * /scripts/doctrine-cli.sh
 * ///////////////////////////////////
 * #!/usr/bin/env php
 * <?php
 *
 * // use development database settings
 * define('APPLICATION_ENV', 'development');
 *
 * // almmost like index.php, but without running
 * require_once dirname(dirname(__FILE__)) . '/www/application.php';
 *
 * // run Doctrine CLI
 * $cli = new Doctrine_Cli(Zend_Registry::get('doctrine_config'));
 * $cli->run($_SERVER['argv']);
 * ////////////////////////////////////
 *
 * @author TAAT
 * @version 1.0
 */
 class My_Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract {

    // PROPERTIES
    /**
     * Doctrine manager instance
     * @access protected
     * @var string
     */
    protected $_manager;

    // GETTERS
    // SETTERS
    // PRIVATE METHODS
    // PUBLIC METHODS

    /**
     * Initialize, start
     * @access public
     * @return null
     */
    public function init() {
        return $this->getManager();
    }

    /**
     * Get Doctrine manager
     * @access public
     * @return object Doctrine_Manager
     */
    public function getManager() {

        // fallback autoloader is_a required since Doctrine uses model with no namepace
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);

        // pass options from application.ini to the Doctrine manager
        $options = $this->_options;
        $this->manager = Doctrine_Manager::connection(sprintf(
                                                        '%s://%s:%s@%s/%s',
                                                        strtolower($options['adapter']),
                                                        $options['username'],
                                                        $options['password'],
                                                        $options['host'],
                                                        $options['dbname']
                                                        )
                                                );

        // set connection manager attributies here

        // $manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);
        // $manager->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL);
        // $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        // $manager->setAttribute(Doctrine::ATTR_AUTO_FREE_QUERY_OBJECTS, true);
        // $manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);
        // $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        // $manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);
        // $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, false);

        // default doctrine structure:
            // /application/data/fixtures/
            // /application/models/
            // /application/models/generated/
            // /application/migrations/
            // /application/data/sql/
            // /application/schema/

        // apply default paths if not set in application.ini
        $defaults = array(
                          'data_fixtures_path' => APPLICATION_PATH . '/data/fixtures/',
                          'models_path' => APPLICATION_PATH . '/models/',
                          'generated_models_path' => APPLICATION_PATH . '/models/generated/',
                          'migrations_path' => APPLICATION_PATH . '/migrations/',
                          'sql_path' => APPLICATION_PATH . '/data/sql/',
                          'yaml_schema_path' => APPLICATION_PATH . '/schema/'
                          );

        foreach ($defaults as $key=>$path) {
            if (!isset($options[$key])) {
                $options[$key] = $path;
            }
        }

        // add models and generated models path to the include_path
        set_include_path(implode(PATH_SEPARATOR, array(
                                                       $options['models_path'],
                                                       $options['generated_models_path'],
                                                       get_include_path(),
                                                       )));
        // save doctrine settings to registry
        Zend_Registry::set('doctrine_config', $options);

        return $this->_manager;
    }
}