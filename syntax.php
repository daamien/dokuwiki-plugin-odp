<?php
/**
 * ODP Plugin: Exports to ODP
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Damien Clochard <damien@taadeem.net> 
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_odp extends DokuWiki_Syntax_Plugin {

    /**
     * return some info
     */
    function getInfo(){
        return confToHash(dirname(__FILE__).'/info.txt');
    }

    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'substition';
    }

    /**
     * What about paragraphs?
     */
    function getPType(){
        return 'normal';
    }

    /**
     * Where to sort in?
     */
    function getSort(){
        return 319; // Before image detection, which uses {{...}} and is 320
    }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~ODP~~',$mode,'plugin_odp');
        $this->Lexer->addSpecialPattern('{{odp>.+?}}',$mode,'plugin_odp');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler){
        // Export button
        if ($match == '~~ODP~~') { return array(); }
        // Extended info
        $match = substr($match,6,-2); //strip markup
        $extinfo = explode(':',$match);
        $info_type = $extinfo[0];
        if (count($extinfo) < 2) { // no value
            $info_value = '';
        } elseif (count($extinfo) == 2) {
            $info_value = $extinfo[1];
        } else { // value may contain colons
            $info_value = implode(array_slice($extinfo,1), ':');
        }
        return array($info_type, $info_value);
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {
        global $ID, $REV;
        if (!$data) { // Export button
            if($format != 'xhtml') return false;
            $renderer->doc .= '<a href="'.exportlink($ID, 'odp', ($REV != '' ? 'rev='.$REV : '')).'" title="'.$this->getLang('view').'">';
            $renderer->doc .= '<img src="'.DOKU_BASE.'lib/plugins/odp/odp.png" align="right" alt="'.$this->getLang('view').'" width="48" height="48" />';
            $renderer->doc .= '</a>';
            return true;
        } else { // Extended info
            list($info_type, $info_value) = $data;
            if ($info_type == "template") { // Template-based export
                $renderer->template = $info_value;
                p_set_metadata($ID, array("relation"=> array("odp"=>array("template"=>$info_value))));
            }
        }
        return false;
    }

}

//Setup VIM: ex: et ts=4 enc=utf-8 :
