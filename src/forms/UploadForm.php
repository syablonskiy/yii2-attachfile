<?php
namespace syablonskiy\attachfile\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use syablonskiy\attachfile\ModuleTrait;
use syablonskiy\attachfile\models\Attachment;

class UploadForm extends Model
{
    use ModuleTrait;
    /**
     * @var UploadedFile
     */
    public $file;

    public function rules() {
        return [
            ArrayHelper::merge(
                ['file', 'file', 'skipOnEmpty' => true],
                $this->getModule()->rules
            )
        ];
    }

    public function upload() {
        if ($this->validate()) {

            $fileHash = md5(uniqid(rand(), true)) . time();
            $newFileName = $fileHash . '.' . $this->file->extension;

            $attachment = new Attachment();
            $attachment->name = $this->file->baseName;
            $attachment->hash = $fileHash;
            $attachment->size = $this->file->size;
            $attachment->type = $this->file->extension;
            $attachment->mime = $this->file->type;
            if (!$attachment->save()) {
                throw new Exception("Attachment model doesn't save");
            }

            $fileDirPath = $this->getModule()->getFilesDirPath($fileHash) . DIRECTORY_SEPARATOR . $newFileName;

            if (!$this->file->saveAs($fileDirPath)){
                throw new Exception("File doesn't save");
            }

            return [
                'id' => $attachment->id,
                'name' => $attachment->name,
            ];
        } else {
            return false;
        }
    }
}
