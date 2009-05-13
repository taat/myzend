<?php

/**
 * PHPTAL View
 * Zend_Application Resource plugin
 *
 * @author TAAT
 * @version 1.1
 */
 class My_Application_Resource_Phptal extends Zend_Application_Resource_ResourceAbstract {

    // PROPERTIES
    /**
     * View
     * @access protected
     * @var Zend_View
     */
    protected $_view;

    /**
     * Initialize
     * @access public
     * @return null
     */
    public function init()
    {
        $this->getView();
    }

    /**
     * Get Phptal view
     * @access public
     * @return Zend_View
     */
    public function getView()
    {
        $view = new ZendX_Phptal_View();
        $phptal = $view->getEngine();

        // set options from application config
        $class = new ReflectionClass('PHPTAL');
        foreach ($this->_options as $option=>$value) {
            $option = strtolower($option);

            if ('zendsyntax' === $option) {
            // enable or disable Zend Syntax in the config
                if ($value) {
                    $phptal->setPreFilter(new ZendX_Phptal_Filter_ZfSyntax());
                }
            } else {
            // other phptal options
                $method = 'set' . $option;
                if ($option !== 'set' && $class->hasMethod($method)) {
                    $phptal->$method($value);
                } else {
                    throw new  ZendX_Phptal_Exception("Invalid PHPTAL resource option '{$option}' in application config.");
                }

            }

        }

        $this->_view = $view;
        return $this->_view;
    }
}