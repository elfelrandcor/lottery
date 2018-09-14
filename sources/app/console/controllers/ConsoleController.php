<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace console\controllers;


use yii\console\Controller;

class ConsoleController extends Controller
{

    protected function stringRender($string) {
        $args = \func_get_args();
        array_shift($args);
        return $this->stdout($string . PHP_EOL, ...$args);
    }
}
