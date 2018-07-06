<?php
namespace syablonskiy\attachfile\commands;

use yii\console\Controller;
use syablonskiy\attachfile\models\Attachment;

class CronController extends Controller
{
    public function actionDeleteFiles()
    {
        $filesQuery = Attachment::find()
            ->where(['itemId' => null])
            ->andWhere(['<', 'created_at', time() - $this->module->fileExpireTime]);

        foreach ($filesQuery->each() as $file) {
            $this->module->deleteFile($file);
        }
    }
}