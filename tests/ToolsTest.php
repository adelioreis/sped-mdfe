<?php

namespace Tests\NFePHP\MDFe;

/**
 * Class ToolsMDFeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */


class ToolsTest extends PHPUnit_Framework_TestCase
{
    public $mdfe;
    
    public function testeInstanciar()
    {
        $configJson = dirname(__FILE__) . '/fixtures/config/fakeconfig.json';
        //$this->mdfe = new Tools($configJson);
    }
}
