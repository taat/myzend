<?php
/**
 * PHPTAL Filter
 * Fixes Zend_View inline PHP to use with PHPTAL
 * @version 1.2
 */
class ZendX_Phptal_Filter_ZfSyntax implements PHPTAL_Filter {

    /**
     * String filtering method, returns filtered string
     *
     * @name filter
     * @access public
     * @param string $xhtml Input code
     * @return string Filtered code
     * @author TAAT
     * @author Romke van der Meulen (regex fixes)
     * @author Kornel Lesiński (http://bugs.php.net/bug.php?id=47796)
     * @todo Improve regex in preg_replace_callback
     */
    public function filter($xhtml)
    {
        /**
         * Callback helper function
         * @param array $matches array of regex matches
         * @return string
         */
        function replaces($matches) {
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
            return preg_replace($search, $replace, $matches[1]);
        }

        // finds PHP code block and performs _replace only inside
        $xhtml = preg_replace_callback('@(<\?(=|php)?\s(.*?)\s\?>)@s', 'replaces', $xhtml);

        return $xhtml;
    }
}
?>