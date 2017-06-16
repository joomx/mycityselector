<?php
/**
 * {mod_newmod}
 * @author {author}
 * @version 1.0.0
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


class _NEWMODCLASS_Module
{

    /**
     * @var JRegistry|null Объект параметров модуля
     */
    private $params = null;

    private $db = null;


    public function __construct()
    {
        $this->bd = JFactory::getDbo();
        $this->tmplPath = __DIR__ . '/tmpl';

        // $items = $this->bd->setQuery("SELECT ...")->loadResult();

        // PUT YOUR CODE THERE

    }


    /**
     * Connect jQuery
     */
    public function addJQuery()
    {
        if (JHtml::isRegistered('jquery.framework')) {
            JHtml::_('jquery.framework');
        } else {
            JFactory::getDocument()->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js');
            JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict();');
        }
    }


    /**
     * Alias of JDocument::addScript
     * @example Call in module template $this->addScript("path");
     */
    public function addScript($url, $type = "text/javascript", $defer = false, $async = false)
    {
        JFactory::getDocument()->addScript($url, $type, $defer, $async);
    }


    /**
     * Alias of JDocument::addStyleSheet
     * @example Call in module template $this->addStyle("path");
     */
    public function addStyle($url, $type = 'text/css', $media = null, $attribs = array())
    {
        JFactory::getDocument()->addStyleSheet($url, $type, $media, $attribs);
    }


    /**
     * Returns module parameter by name
     * @param String $param
     * @param String $default
     * @return mixed
     */
    public function get($param, $default='')
    {
        if (isset($MCS_BUFFER[$param])) {
            $default = $MCS_BUFFER[$param];
        } else {
            if (is_object($this->params)) {
                $default = $this->params->get($param, $default);
            }
        }
        return $default;
    }


}
// END CLASS

// Start module
new MyCitySelectorModule();