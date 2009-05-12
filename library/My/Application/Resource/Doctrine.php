<?php

/**
 * Doctrine Resource Plugin for Zend Framework Application
 *
 * Installation:
 * 1. Create default directory structure for Doctrine:
 *  /application/data/fixtures/
 *  /application/models/
 *  /application/models/generated/
 *  /application/migrations/
 *  /application/data/sql/
 *  /application/schema/
 * 2. Add following lines to application.ini:
 * pluginPaths.My_Application_Resource = "My/Application/Resource"
 * resources.doctrine.db.adapter  = pgsql
 * resources.doctrine.db.host     = localhost
 * resources.doctrine.db.dbname   = test
 * resources.doctrine.db.username = test
 * resources.doctrine.db.password = test
 * resources.doctrine.session.handler = off
 * resources.doctrine.session.lifetime = 5
 * resources.doctrine.session.table = Session
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
    public function init()
    {
        return $this->getManager();
    }

    /**
     * Get Doctrine manager
     * @access public
     * @return object Doctrine_Manager
     */
    public function getManager()
    {
        $optionsd = $this->_options['db'];

        // Default Doctrine structure:
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

        Doctrine::loadModels($options['models_path']);

        // pass options from application.ini to the Doctrine manager

        $this->manager = Doctrine_Manager::connection(sprintf(
                                                        '%s://%s:%s@%s/%s',
                                                        strtolower($optionsd['adapter']),
                                                        $optionsd['username'],
                                                        $optionsd['password'],
                                                        $optionsd['host'],
                                                        $optionsd['dbname']
                                                        )
                                                );

        // set connection manager attributies here

        // $this->manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);
        // $this->manager->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL);
        // $this->manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        // $this->manager->setAttribute(Doctrine::ATTR_AUTO_FREE_QUERY_OBJECTS, true);
        // $this->manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);
        // $this->manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        // $this->manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);
        // $this->manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, false);

        // save Doctrine settings to registry
        Zend_Registry::set('doctrine_config', $options);

        // session hadler
        $optionss = $this->_options['session'];

        if (isset($optionss['handler']) && $optionss['handler']) {
            if (isset($optionss['table'])) {
                $table = $optionss['table'];
            }
            else {
                $table = 'Session';
            }
            try {
                $handler = new ZendX_Doctrine_Session();
                $handler->setTable($table);
                // session lifetime from options
                if (isset($optionss['lifetime']) && $optionss['lifetime']) {
                    $handler->setLifetime($optionss['lifetime']);
                }
                Zend_Session::setSaveHandler($handler);
                Zend_Session::start();
            } catch (Exception $e) {
                throw new Doctrine_Exception('Can\'t access Doctrine session table.');
            }
        }

        return $this->_manager;
    }
}