<?php

namespace Tests\CanvassPaint\Action;

use CanvassPaint\Action\RenderForm;
use CanvassPaint\PdoModel\Form;
use CanvassPaint\PdoModel\FormField;
use Implement\TestRenderFunction;
use Tests\TestCase;

class RenderFormTest extends TestCase
{
    public function test_render()
    {
//        Form::$table = 'forms';
//        FormField::$table = 'form_fields';

        new FormField(new \PDO('sqlite:memory:'));

        $action = new RenderForm(new TestRenderFunction());

        $json = $action->render(1);

        $data = json_decode($json, true);

        echo __FILE__ . ' on line ' . __LINE__;
        echo '<pre style="background: white; width: 1000px;">' . PHP_EOL;
        print_r($data);
        echo PHP_EOL . '</pre>' . PHP_EOL;
        exit;
    }
}
