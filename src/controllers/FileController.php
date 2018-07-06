<?php

namespace syablonskiy\attachfile\controllers;

use Yii;
use syablonskiy\attachfile\forms\UploadForm;
use syablonskiy\attachfile\models\Attachment;
use yii\web\UploadedFile;

class FileController extends \yii\web\Controller
{
    public function actionDownload($id)
    {
        $file = Attachment::findOne($id);
        $filePath = $this->module->getFilesDirPath($file->hash) . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;

        $response = Yii::$app->response;
        return $response->sendFile($filePath, "$file->name.$file->type");
    }

    public function actionUpload()
    {
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $model->upload();
        }
    }
}
