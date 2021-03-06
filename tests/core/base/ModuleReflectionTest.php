<?php

namespace luyatests\core\base;

use Yii;
use luya\base\ModuleReflection;

class ModuleReflectionTest extends \luyatests\LuyaWebTestCase
{
    private function buildObject($module)
    {
        return new ModuleReflection(new \luya\web\Request(), new \luya\web\UrlManager(), ['module' => $module]);
    }

    /**
     * @expectedException Exception
     */
    public function testInitException()
    {
        return new ModuleReflection(new \luya\web\Request(), new \luya\web\UrlManager());
    }
    
    public function testShareObject()
    {
        //$ref = new ModuleReflection(Yii::$app->getModule('unitmodule'));
        $ref = $this->buildObject(Yii::$app->getModule('unitmodule'));
        $ref->defaultRoute('unit-test', 'index', ['x' => 'y']);
        $reflectionRequest = $ref->getRequestRoute();
        
        $url = $ref->getUrlRule();
        
        $this->assertEquals('unitmodule', $url['module']);
        $this->assertEquals('unitmodule/unit-test/index', $url['route']);
        $this->assertEquals('y', $url['params']['x']);
        
        $content = $ref->run();

        $this->assertEquals(4, count($content));

        $this->assertEquals('unit-test', $content['id']);
        $this->assertEquals('unitmodule', $content['module']);
        $this->assertEquals('@app/views/unitmodule/unit-test', $content['viewPath']);
        $this->assertEquals('@app/views/unitmodule/', $content['moduleLayoutViewPath']);
    }

    /*
    public function testModuleObject()
    {
        $ref = $this->buildObject(Yii::$app->getModule('unitmodule'));
        $ref->defaultRoute('unit-test-2', 'index', ['x' => 'y']);
        $reflectionRequest = $ref->getRequestRoute();
        $content = $ref->run();

        $this->assertEquals(5, count($content));

        $this->assertEquals('unit-test-2', $content['id']);
        $this->assertEquals('unitmodule', $content['module']);
        $this->assertNotEquals('@app/views/unitmodule/unit-test', $content['viewPath']);
        $this->assertNotEquals('@app/views/unitmodule/', $content['moduleLayoutViewPath']);
        $this->assertEquals('@unitmodule/views/', $content['moduleLayoutViewPath']);
    }
    */

    public function testModuleSuffix()
    {
        $ref = $this->buildObject(Yii::$app->getModule('urlmodule'));
        $ref->suffix = 'bar';
        $response = $ref->getRequestRoute();

        $this->assertArrayHasKey('route', $response);
        $this->assertArrayHasKey('args', $response);

        $this->assertEquals('bar/index', $response['route']);
        $this->assertEquals(0, count($response['args']));

        $url = $ref->getUrlRule();
        
        $this->assertEquals('urlmodule', $url['module']);
        $this->assertEquals('urlmodule/bar/index', $url['route']);
        $this->assertTrue(empty($url['params']));
        
        $controllerResponse = $ref->run();

        $this->assertEquals('bar', $controllerResponse);
    }

    /**
     * @expectedException Exception
     */
    public function testNotFoundControllerException()
    {
        $ref = $this->buildObject(Yii::$app->getModule('urlmodule'));
        $ref->defaultRoute('foo', 'index');
        $request = $ref->getRequestRoute();
        // throws:  Controller not found. The requested module reflection route 'foo/index' could not be found.
        $resposne = $ref->run();
    }
}
