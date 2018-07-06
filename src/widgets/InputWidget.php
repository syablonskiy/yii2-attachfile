<?php

namespace syablonskiy\attachfile\widgets;

use syablonskiy\attachfile\assets\InputWidgetAsset;
use syablonskiy\attachfile\forms\UploadForm;
use syablonskiy\attachfile\ModuleTrait;
use yii\base\Widget;

class InputWidget extends Widget
{

    use ModuleTrait;

    public $form;
    public $label;

    public function init()
    {
        parent::init();
        if ($this->label === null) {
            $this->label = '<a>Прикрепить файл <span class="glyphicon glyphicon-paperclip"></span></a>';
        }
    }


    public function run()
    {

        $uploadModel = new UploadForm();
        $module = $this->getModule();
        $moduleName = $module->id;
        $maxFiles = $module->maxFiles;

        return $this->render('inputWidget', [
            'uploadModel' => $uploadModel,
            'form' => $this->form,
            'moduleName' => $moduleName,
            'maxFiles' => $maxFiles,
            'label' => $this->label
        ]);
    }
}
