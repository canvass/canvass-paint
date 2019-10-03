<?php

namespace Tests;

use CanvassPaint\PdoModel\Form;
use CanvassPaint\PdoModel\FormField;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $pdo = new \PDO(
            'mysql:host=127.0.0.1;dbname=canvass',
            'admin',
            'Sup3rS3cr3t!P4$s'
        );

        \Canvass\Forge::setFormClosure(static function () use ($pdo) {
            return new Form($pdo);
        });

        \Canvass\Forge::setFieldClosure(static function () use ($pdo) {
            return new FormField($pdo);
        });

        \Canvass\Forge::setLoggerClosure(static function(\Throwable $e) {
            echo PHP_EOL . __FILE__ . ' on line ' . __LINE__ . PHP_EOL;
            echo "{$e->getMessage()} in {$e->getFile()} on {$e->getLine()}";
            echo $e->getTraceAsString();
            exit;
        });
    }
}
