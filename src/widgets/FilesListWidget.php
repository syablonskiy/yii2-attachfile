<?php
/**
 * Created by PhpStorm.
 * User: serega
 * Date: 25.06.18
 * Time: 11:09
 */
namespace syablonskiy\attachfile\widgets;

use yii\base\Widget;
use syablonskiy\attachfile\assets\FilesListWidgetAsset;
use syablonskiy\attachfile\ModuleTrait;

class FilesListWidget extends Widget
{
    use ModuleTrait;

    public $model;
    public $allowDeletion = false;

    public function init()
    {
        FilesListWidgetAsset::register($this->getView());
        parent::init();
    }

    public function run()
    {
        $files = $this->model->files;
        $moduleName = $this->getModule()->id;

        return $this->render('filesListWidget', [
            'files' => $files,
            'moduleName' => $moduleName,
            'allowDeletion' => $this->allowDeletion
        ]);
    }
}