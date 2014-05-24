<?php
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'admin.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_redirect extends DokuWiki_Admin_Plugin {

    var $ConfFile;  // path/to/redirect.conf

    function __construct() {
        // conf path option
        $confPath = array(
            'legacy'     => dirname(__FILE__).'/redirect.conf', // to be deprecated
            'default'    => DOKU_CONF.'redirect.conf',
            'local'      => DOKU_CONF.'redirect.local.conf',
        );

        // copy redirect.conf from legacy to local
        if (!file_exists($confPath['local']) && file_exists($confPath['legacy'])) {
            $map = io_readFile($confPath['legacy']);
            io_saveFile($confPath['local'], $map);
            io_saveFile($confPath['legacy'], ''); // legacy file is now blank
        }

        // set ConfFile
        $this->ConfFile = $confPath['local'];
    }

    /**
     * Access for managers allowed
     */
    function forAdminOnly(){
        return false;
    }

    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
        return 140;
    }

    /**
     * return prompt for admin menu
     */
    function getMenuText($language) {
        return $this->getLang('name');
    }

    /**
     * handle user request
     */
    function handle() {
        if($_POST['redirdata']){
            if(io_saveFile($this->ConfFile, cleanText($_POST['redirdata']))){
                msg($this->getLang('saved'),1);
            }
        }
    }

    /**
     * output appropriate html
     */
    function html() {
        global $lang;
        echo $this->locale_xhtml('intro');
        echo '<form action="" method="post" >';
        echo '<input type="hidden" name="do" value="admin" />';
        echo '<input type="hidden" name="page" value="redirect" />';
        echo '<textarea class="edit" rows="15" cols="80" style="height: 300px" name="redirdata">';
        echo formtext(io_readFile($this->ConfFile));
        echo '</textarea><br />';
        echo '<input type="submit" value="'.$lang['btn_save'].'" class="button" />';
        echo '</form>';
    }

}
//Setup VIM: ex: et ts=4 enc=utf-8 :
