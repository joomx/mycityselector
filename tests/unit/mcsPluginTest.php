<?php
require_once JPATH_BASE . '/plugins/system/plgmycityselector/helpers/McsContentHelper.php';
require_once JPATH_BASE . '/plugins/system/plgmycityselector/helpers/McsData.php';
require_once JPATH_BASE . '/plugins/system/plgmycityselector/helpers/McsLog.php';
require_once JPATH_BASE . '/plugins/system/plgmycityselector/helpers/geo.php';

class mcsPluginTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    
    public function testContentHelper()
    {
        $this->assertEquals(true, class_exists('McsContentHelper'));

        $this->
        // простые метки
        $body = '<p>[city Омск]только</p> <p>Омск[/city]</p> <div>[mcs-10 проверка]</div>
                    <b>[city !Москва]все кроме москвы[/city]</b>';
        $tags = McsContentHelper::parseMcsTags($body);
        if (!empty($tags)) {
            if (count($tags) == 2) {
                out("успех\n", "cyan");

            } else {
                out("ОШИБКА (найдены не все метки)\n", "red");
            }
        } else {
            out("ОШИБКА (метки не найдены)\n", "red");
        }

    }
}