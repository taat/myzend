<?php
/**
 * PHPTAL Filter
 * Fixes Zend_View inline PHP to use with PHPTAL
 */
class ZendX_Phptal_Filter_ZfSyntax implements PHPTAL_Filter {
    protected $search = '';
    protected $replace = '';

    /**
     * Add ctx-> to this->
     * Used in preg_replace callback function
     *
     * @name _replace
     * @access private
     * @param string $str
     * @return string
     */
    private function _replace($str)
    {
            $search = array(
                    // helpers
                    '@\$this->([^\(\s;]+)\s?\((.*)\)@is',
                    // variables
                    '@\$this->([a-zA-z_0-9^\(]+)@is'
                    );
            $replace = array(
                     '$ctx->this->\\1(\\2)',
                     '$ctx->\\1'
                     );

        $str = preg_replace($search, $replace, $str);

        return $str;
    }

    /**
     * String filtering method, returns filtered string
     *
     * @name filter
     * @access public
     * @param string $xhtml
     * @return string
     */
    public function filter($xhtml)
    {
        // finds PHP code block and performs _replace only inside
        // Regex fixes by Romke van der Meulen (http://www.redgeonline.net)
        $xhtml = preg_replace('@(<\?(=|php)?\s(.*?)\s\?>)@es', '$this->_replace(\'\\1\')', $xhtml);
        return $xhtml;
    }
}
?>