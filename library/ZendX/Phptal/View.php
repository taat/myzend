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
     * PHPTAL object
     * @access private
     * @var object PHPTAL
     */
    protected $_engine = null;

    /**
     * Options array
     * @access protected
     * @var string
     */
    protected $_options = array();

    /**
     * Gets the value of the private property 'options'
     *
     * @access public
     * @return string
     */
    public function getOptions() {
        return $this->_options;
    }


    /**
     * Plug in PHPTAL object into View
     *
     * @name setEngine
     * @access public
     * @param object PHPTAL $engine
     */
    public function setEngine(PHPTAL $engine) {

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
     * Display template
     *
     * @access protected
     */
    protected function _run(){
        $this->_engine->setTemplate(func_get_arg(0));
            try {
                echo $this->_engine->execute();
            } catch (ZendX_Phptal_Exception $e) {
                throw new Zend_View_Exception($e);
            }
        }
} // end Zend_View_Abstract

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