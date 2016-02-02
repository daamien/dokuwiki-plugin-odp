<?php
/**
 * ODP Plugin: Exports to ODP
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     damien clochard <damien@taadeem.net>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

/**
 * Add the template as a page dependency for the caching system
 */
class action_plugin_odp extends DokuWiki_Action_Plugin {

    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('PARSER_CACHE_USE','BEFORE', $this, 'handle_cache_prepare');
    }

    function handle_cache_prepare(&$event, $param) {
        global $conf, $ID;
        $cache =& $event->data;
        // only the ODP rendering mode needs caching tweaks
        if ($cache->mode != "odp") return;
        $odt_meta = p_get_metadata($ID, 'relation odp');
        $template_name = $odt_meta["template"];
        if (!$template_name) {
            return;
        }
        $template_path = $conf['mediadir'].'/'.$this->getConf("tpl_dir")."/".$template_name;
        if (file_exists($template_path)) {
            $cache->depends['files'][] = $template_path;
        }
    }

}

//Setup VIM: ex: et ts=4 enc=utf-8 :
