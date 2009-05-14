<?
/**
 * Extended Zend_View to support PHPTAL
 */
class ZendX_Phptal_View extends Zend_View_Abstract
    {

        /**
         * Constructor
         * @access public
         */
        public function __construct () {
            $this->_engine = new PHPTAL;
            $this->_engine->set('this', $this);
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $viewRenderer->setView($this);
        }

    /**
     * Script file name to execute
     *
     * @var string
     */
    private $_file = null;

    /**
     * Filters
     * @access private
     * @var array
     */
    private $_filter = array();


    /**
     * PHPTAL object
     * @access private
     * @var object PHPTAL
     */
    protected $_engine = null;

    /**
     * Use default Zend router view script by default
     * @access private
     * @var string
     */
    private $_useDefaultScript = true;

    /**
     * Sets the value of the private property 'useDefautScript'
     *
     * @access public
     * @param bool $useDefautScript Property value
     * @return ZendX_Phptal_View
     */
    public function setUseDefautScript($useDefautScript)
    {
        $this->_useDefautScript = $useDefautScript;
        return $this;
    }

    /**
     * Gets the value of the private property 'useDefaultScript'
     *
     * @access public
     * @return bool
     */
    public function getUseDefaultScript() {
        return $this->_useDefaultScript;
    }

    /**
     * Set phptal template
     * @access public
     * @param string $filename Template source filename
     * @return ZendX_Phptal_View
     */
    public function setTemplate($filename) {
        $this->_engine->setTemplate($filename);
        $this->_useDefaultScript = false;
        return $this;
    }

    /**
     * Get Template filename
     * @access public
     * @return string Template filename
     */
    public function getTemplate() {
        return $this->_engine->getTemplate();
    }

    /**
     * Set template source
     * @access public
     * @param string $xhtml
     * @return ZendX_Phptal_View
     */
    public function setSource($xhtml)
    {
        $this->_engine->setSource($xhtml);
        $this->_useDefaultScript = false;
        return $this;
    }

    /**
     * Add source resolver to Phptal
     * @access public
     * @param PHPTAL_SourceResolver $resolver
     * @return ZendX_Phptal_View
     */
     public function addSourceResolver(PHPTAL_SourceResolver $resolver)
    {
        $this->_engine->addSourceResolver($resolver);
        return $this;
    }

    /**
     * Get PHPTAL object from View
     *
     * @name getEngine
     * @access public
     */
    public function getEngine()
    {
        return $this->_engine;
    }

    /**
     * Set PHPTAL variables
     *
     * @access public
     * @param string $key variable name
     * @param string $value variable value
     */
    public function __set($key, $value)
    {
        $this->_engine->set($key, $value);
    }

    /**
     * Get PHPTAL Variable Value
     *
     * @access public
     * @param string $key variable name
     * @return mixed variable value
     */
    public function __get($key)
        {
            if (isset($this->_engine->$key)) {
                return $this->_engine->$key;
            } else {
               trigger_error("Undefined PHPTAL template variable: '{$key}'");
            }
        }

    /**
     * Check if PHPTAL variable is set
     *
     * @access public
     * @param string $key variable name
     */
    public function __isset($key)
        {
            return isset($this->_engine->$key);
        }

    /**
     * Unset PHPTAL variable
     *
     * @access public
     * @param string $key variable name
     */
    public function __unset($key)
        {
            if (isset($this->_engine->$key)) {
                unset($this->_engine->$key);
            }
        }

    /**
     * Clone PHPTAL object
     *
     * @access public
     */
    public function __clone()
    {
        $this->_engine = clone $this->_engine;
    }

    /**
    * Processes a view script and returns the output.
    *
    * @param string $name The script script name to process.
    * @return string The script output.
    */
    public function render($name)
    {
        $this->_file = $this->_script($name);

        if ($this->_useDefaultScript) {
            $this->_engine->setTemplate($this->_file);
        }
        try {
            $code =  $this->_engine->execute();
        } catch (ZendX_Phptal_Exception $e) {
            throw new Zend_View_Exception($e);
        }
        $this->_run($this->_file);

        // Zend filters should be applied here
        return $code;
    }

    /**
     * Display template
     * Required by Zend_View_Abstract
     * @access protected
     */
    protected function _run(){}

}

/**
* helper: custom PHPTAL modifier
*
* You may want to rename this function
* to phptal_tales_this if you want to use
* this:helperName() instead helper:helperName()
*
* @name phptal_tales_helper
* @param string $src
* @param bool $nothrow
*/
function phptal_tales_helper($src, $nothrow) {
    return phptal_tales('php:this->' . $src);
}