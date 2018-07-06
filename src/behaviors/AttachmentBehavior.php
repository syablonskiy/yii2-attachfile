<?php

namespace syablonskiy\attachfile\behaviors;

use syablonskiy\attachfile\models\Attachment;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use syablonskiy\attachfile\ModuleTrait;

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
    public $attachment;

    /**
     * инициализирует переменную $attachment
     */
    public function init()
    {
        parent::init();
        $this->attachment = Yii::$app->request->post('list_of_files_for_attachment');
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'attachFiles',
            ActiveRecord::EVENT_AFTER_DELETE => 'detachFiles'
        ];
    }

    /**
     * Привязывает загруженные файлы к модели,
     * присваивая значения полям itemId и model в таблице attachment
     * @return void
     */
    public function attachFiles($event)
    {
        if ($this->attachment === null) {
            return;
        }
        $filesIds = explode(",", $this->attachment);
        Attachment::updateAll(
            ['itemId' => $this->owner->id, 'model' => $this->getClassName($this->owner)],
            ['in', 'id', $filesIds]
        );
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
}