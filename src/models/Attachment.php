<?php

namespace syablonskiy\attachfile\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "attachment".
 *
 * @property int $id
 * @property string $name
 * @property string $model
 * @property int $itemId
 * @property string $hash
 * @property int $size
 * @property string $type
 * @property string $mime
 * @property int $created_at
 */
class Attachment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return \Yii::$app->getModule('attachfile')->tableName;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'hash', 'size', 'type', 'mime'], 'required'],
            [['itemId', 'size', 'created_at'], 'integer'],
            [['name', 'model', 'hash', 'type', 'mime'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'model' => 'Model',
            'itemId' => 'Item ID',
            'hash' => 'Hash',
            'size' => 'Size',
            'type' => 'Type',
            'mime' => 'Mime',
            'created_at' => 'Created At',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }
}
