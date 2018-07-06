<?php

namespace syablonskiy\attachfile;

trait ModuleTrait
{
    /**
     * @var null|Module
     */
    private $module = null;

    /**
     * @return null|Module
     * @throws \Exception
     */
    public function getModule()
    {
        $this->module = \Yii::$app->getModule('attachfile');

        if (!$this->module) {
            throw new \Exception("yii2-attachfile module not found, may be you didn't add it to your config?");
        }

        return $this->module;
    }
}