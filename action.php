<?php
/**
 * Redirect plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_redirect extends DokuWiki_Action_Plugin {

    var $ConfFile;  // path/to/redirect.conf

    function __construct() {
        // conf path option
        $confPath = array(
            0 => dirname(__FILE__).'/redirect.conf',
            1 => DOKU_CONF.'redirect.conf',
        );

        // set ConfFile
        switch ($this->getConf('conf_path')) {
            case 1:
                $this->ConfFile = $confPath[1];
                break;
            default:
                if(defined('DOKU_FARMDIR')) {
                    $this->ConfFile = $confPath[1]; // store in each animal's conf directory
                } else {
                    $this->ConfFile = $confPath[0];
                }
        }
    }

    /**
     * register the eventhandlers
     */
    function register(&$controller){
        $controller->register_hook('DOKUWIKI_STARTED',
                                   'AFTER',
                                   $this,
                                   'handle_start',
                                   array());
    }

    /**
     * handle event
     */
    function handle_start(&$event, $param){
        global $ID, $ACT;

        if($ACT != 'show') return;

        $redirects = confToHash($this->ConfFile);
        if($redirects[$ID]){
            if(preg_match('/^https?:\/\//',$redirects[$ID])){
                send_redirect($redirects[$ID]);
            }else{
                if($this->getConf('showmsg')){
                    msg(sprintf($this->getLang('redirected'),hsc($ID)));
                }
                $link = explode('#', $redirects[$ID], 2);
                send_redirect(wl($link[0] ,'',true) . '#' . rawurlencode($link[1]));
            }
            exit;
        }
    }


}

