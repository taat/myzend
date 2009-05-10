<?php

/**
 * TODO Description of class...
 *
 * @author TAAT
 * @version 1.0
 */
 class My_Application_Resource_Phptal extends Zend_Application_Resource_ResourceAbstract {

    // PROPERTIES
    /**
     * View
     * @access protected
     * @var string
     */
    protected $_view;

    // GETTERS
    // SETTERS
    // PRIVATE METHODS
    // PUBLIC METHODS

    /**
     * Initialize
     * @access public
     * @param
     * @return null
     */
    public function init() {
        $this->getView();
    }

    /**
     * Get Phptal view
     * @access public
     * @param
     * @return null
     */
    public function getView() {
        // setup PHPTAL
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

        $view = new ZendX_Phptal_View();

        $viewRenderer->setView($view)->init();

        $phptal = new PHPTAL;
        $view->setEngine($phptal);

        // set options from application config
        $class = new ReflectionClass('PHPTAL');
        foreach ($this->_options as $option=>$value) {
            $option = strtolower($option);
            $method = 'set' . $option;
            if ($option !== 'set' && $class->hasMethod($method)) {
                $phptal->$method($value);
            } else {
                trigger_error("Invalid PHPTAL resource option '{$option}' in application config.");
            }
        }

        // pre filter to support traditional Zend_View syntax in templates
        $phptal->setPreFilter(new ZendX_Phptal_Filter_ZfSyntax());

        $this->_view = $view;
        return $this->_view;
    }
}