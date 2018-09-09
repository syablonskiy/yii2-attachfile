<?php

namespace syablonskiy\attachfile\behaviors;

use syablonskiy\attachfile\models\Attachment;
use syablonskiy\attachfile\ModuleTrait;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Created by PhpStorm.
 * User: serega
 * Date: 15.06.18
 * Time: 10:16
 */
class AttachmentBehavior extends Behavior
{
    use ModuleTrait;
    /**
     * @var string  id's приклепленных файлов, разделенные запятыми
     * соответствуют записям в таблице Attachment
     */
    public $attachList;
    public $detachList;

    /**
     * инициализирует переменную $attachment
     */
    public function init()
    {
        parent::init();
        $this->attachList = Yii::$app->request->post('list_of_files_for_attachment');
        $this->detachList = Yii::$app->request->post('files_for_deletion');
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'manageFiles',
            ActiveRecord::EVENT_AFTER_UPDATE => 'manageFiles',
            ActiveRecord::EVENT_AFTER_DELETE => 'detachFiles'
        ];
    }

    /**
     * Привязывает загруженные файлы к модели,
     * присваивая значения полям itemId и model в таблице attachment
     * @return void
     */
    public function manageFiles($event)
    {
        if ($this->detachList) {
            $this->changeFilesModelName($this->detachList, null);
        }
        if ($this->attachList !== null) {
            $filesIds = explode(",", $this->attachList);
            $this->changeFilesModelName($filesIds, $this->getClassName($this->owner));
        }

    }

    public function detachFiles($event)
    {
        $files = $this->getFiles()->all();
        foreach ($files as $file) {
            $this->getModule()->deleteFile($file);
        }
    }

    public function getFiles()
    {
        return $this->owner->hasMany(Attachment::className(), ['itemId' => 'id'])
            ->where([
                'model' => $this->getClassName($this->owner)
            ]);
    }

    /**
     * Возвращает короткое имя класса модели
     * @param $model
     * @return string
     *
     */
    protected function getClassName($model)
    {
        $reflection = new \ReflectionClass($model);
        return $reflection->getShortName();
    }

    protected function changeFilesModelName($filesIds, $modelName)
    {
        Attachment::updateAll(
            ['itemId' => $this->owner->id, 'model' => $modelName],
            ['in', 'id', $filesIds]
        );
    }
}