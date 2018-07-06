<?php

namespace syablonskiy\attachfile;

use Yii;
use yii\helpers\FileHelper;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'syablonskiy\attachfile\controllers';

    public $storePath = '@app/uploads';

    public $fileExpireTime = 1800;

    public $rules = [];

    public $maxFiles = 3;

    public $tableName = 'attachment';

    public function init()
    {
        parent::init();
        if (Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'syablonskiy\attachfile\commands';
        }
    }

    /**
     * Возвращает путь к папке-хранилищу файлов
     *
     * @return bool|string
     */
    public function getStorePath()
    {
        return Yii::getAlias($this->storePath);
    }

    /**
     * Служит для создания директории под файл во время его загрузки
     * и разрешении пути к тому же файлу при скачивании
     *
     * @param $fileHash
     * @return string
     */
    public function getFilesDirPath($fileHash)
    {
        $path = $this->getStorePath() . DIRECTORY_SEPARATOR . $this->getSubDirs($fileHash);

        FileHelper::createDirectory($path);

        return $path;
    }

    public function getSubDirs($fileHash, $depth = 3)
    {
        $depth = min($depth, 9);
        $path = '';

        for ($i = 0; $i < $depth; $i++) {
            $folder = substr($fileHash, $i * 3, 2);
            $path .= $folder;
            if ($i != $depth - 1) $path .= DIRECTORY_SEPARATOR;
        }

        return $path;
    }

    /**
     * Удаляет неактуальные файлы
     *
     * @param $fileModel \syablonskiy\attachfile\models\Attachment
     */
    public function deleteFile($fileModel)
    {
        $filePath = $this->getFilesDirPath($fileModel->hash) . DIRECTORY_SEPARATOR
            . $fileModel->hash . '.' . $fileModel->type;

        return file_exists($filePath) ? unlink($filePath) && $fileModel->delete() : $fileModel->delete();
    }
}