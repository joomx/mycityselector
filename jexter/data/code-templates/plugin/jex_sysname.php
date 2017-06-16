<?php
/**
 * {jex_name}
 * @author {jex_author}
 * @version 1.0.0
 */

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

JLoader::import('joomla.plugin.plugin');
// helper.php
// JLoader::import('plugins.system.{jex_sysname}.helper', JPATH_ROOT); // this is how you can include own additional scripts

/**
 * Class plgSystem_NEWPLUGIN_
 * @see https://docs.joomla.org/Plugin/Events
 */
class plg_JEX_GROUP__JEX_CLASSNAME_ extends JPlugin
{

     /**
     * @var JDatabaseDriver
     */
    //private $db = null;

    /**
     * Initialization
     */
    function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);
        //$this->db = JFactory::getDbo();
        // todo your code
    }


    // TODO remove events that you no need...


    /**
     * This event is triggered after the framework has loaded and the application initialise method has been called.
     * @see https://docs.joomla.org/Plugin/Events/System#onAfterInitialise
     */
    public function onAfterInitialise()
    {

    }


    /**
     * This event is triggered after the framework has loaded and initialised and the router has routed the client request...
     * @see https://docs.joomla.org/Plugin/Events/System#onAfterRoute
     */
    public function onAfterRoute()
    {

    }


    /**
     * This event is triggered after the framework has dispatched the application...
     * @see https://docs.joomla.org/Plugin/Events/System#onAfterDispatch
     */
    public function onAfterDispatch()
    {

    }


    /**
     * This event is triggered immediately before the framework has rendered the application.
     * @see https://docs.joomla.org/Plugin/Events/System#onBeforeRender
     */
    public function onBeforeRender()
    {

    }


    /**
     * This is the first stage in preparing content for output and is the most common point for content orientated plugins
     * to do their work. Since the article and related parameters are passed by reference,
     * event handlers can modify them prior to display.
     * @see https://docs.joomla.org/Plugin/Events/Content#onContentPrepare
     *
     * @param   string   $context  The context of the content being passed to the plugin.
     * @param   mixed    &$row     An object with a "text" property or the string to be cloaked.
     * @param   mixed    &$params  Additional parameters object.
     * @param   integer  $page     Optional page number. Defaults to zero.
     * @return  boolean	True on success.
     */
    public function onContentPrepare($context, &$row, &$params, $page)
    {
        return true;
    }


    /**
     * This event only exists in Joomla Joomla 3.x. This is a request for information that should be placed
     * between the content title and the content body. Although parameters are passed by reference,
     * this is not the event to modify article data. Use onPrepareContent for that purpose.
     * Note this event has special purpose in com_content for use in handling the introtext.
     * @see https://docs.joomla.org/Plugin/Events/Content#onContentAfterTitle
     *
     * @param   string   $context  The context of the content being passed to the plugin.
     * @param   mixed    &$row     An object with a "text" property or the string to be cloaked.
     * @param   mixed    &$params  Additional parameters object.
     * @param   integer  $page     Optional page number. Defaults to zero.
     * @return string Returned value from this event will be displayed in a placeholder.
     *      Most templates display this placeholder after the article separator.
     */
    public function onContentAfterTitle($context, &$row, &$params, $page)
    {
        //return "<div>OnContentAfterTitle</div>";
        return '';
    }


    /**
     * This is a request for information that should be placed immediately before the generated content.
     * For views that generate HTML, this might include the use of styles that are specified as part of the content
     * or related parameters. Although parameters are passed by reference, this is not the event to modify article data.
     * Use onPrepareContent for that purpose.
     * @see https://docs.joomla.org/Plugin/Events/Content#onContentBeforeDisplay
     *
     * @param   string   $context  The context of the content being passed to the plugin.
     * @param   mixed    &$row     An object with a "text" property or the string to be cloaked.
     * @param   mixed    &$params  Additional parameters object.
     * @param   integer  $page     Optional page number. Defaults to zero.
     * @return string Returned value from this event will be displayed in a placeholder.
     *      Most templates display this placeholder after the article separator.
     */
    public function onContentBeforeDisplay($context, &$row, &$params, $page)
    {
        //return "<div>OnContentBeforeDisplay</div>";
        return '';
    }


    /**
     * This is a request for information that should be placed immediately after the generated content.
     * For views that generate HTML, this might include the closure of styles that are specified as part of the content
     * or related parameters. Although parameters are passed by reference, this is not the event to modify article data.
     * Use onPrepareContent for that purpose.
     * @see https://docs.joomla.org/Plugin/Events/Content#onContentAfterDisplay
     *
     * @param   string   $context  The context of the content being passed to the plugin.
     * @param   mixed    &$row     An object with a "text" property or the string to be cloaked.
     * @param   mixed    &$params  Additional parameters object.
     * @param   integer  $page     Optional page number. Defaults to zero.
     * @return string Returned value from this event will be displayed in a placeholder.
     *      Most templates display this placeholder after the article separator.
     */
    public function onContentAfterDisplay($context, &$row, &$params, $page)
    {
        //return "<div>OnContentAfterDisplay</div>";
        return '';
    }

    // @see other same events on https://docs.joomla.org/Plugin/Events/Content


    /**
     * Event listener for content edit form
     * @param $form
     * @param $data
     * @return bool
     */
    function onContentPrepareForm($form, $data)
    {
        // for example, you can add your tab to Article Edit Form
        /*
        $app = JFactory::getApplication();
        switch ($app->input->get('option')) {
            case 'com_content':
                if ($app->isAdmin() && JFactory::getApplication()->getName() == 'administrator') {
                    $string = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>
                        <form>
                            <fields name="params" label="MyLabel">
                                <fieldset name="params" label="MyParams">
                                    <field
                                        name="field1"
                                        type="text"
                                        label="Bla bla bla"
                                        />
                                    <field
                                        name="field2"
                                        type="text"
                                        label="tututu"
                                    />
                                </fieldset>
                            </fields>
                        </form>';
                    $xml = simplexml_load_string($string);
                    $form->load($xml, true, false);
                }
                return true;
        }
        */
        return true;
    }


    /**
     * Trigger after render a page
     */
    public function onAfterRender()
    {
        if (JFactory::getApplication()->getName() == 'administrator') {
            // backend (admin panel)
            //$body = $this->getPageBody();

            // todo do some changes in page body

            //$this->setPageBody($body);
        } else {
            // frontend page
            if (!$this->editMode) { // don't work for Edit Mode
                //$body = $this->getPageBody();

                // todo do some changes in page body

                //$this->setPageBody($body);
            }
        }
        return true;
    }


    /**
     * Alias for APP->getBody();
     * @return string
     */
    private function getPageBody(){
        $app = JFactory::getApplication();
        if (!method_exists($app, 'getBody')) {  // if joomla 2.5
            return JResponse::getBody();
        }
        return $app->getBody();
    }


    /**
     * Alias for APP->setBody();
     */
    private function setPageBody($body){
        $app = JFactory::getApplication();
        if (!method_exists($app, 'setBody')) {
            JResponse::setBody($body);  // if joomla 2.5
        }
        $app->setBody($body);
    }


}
