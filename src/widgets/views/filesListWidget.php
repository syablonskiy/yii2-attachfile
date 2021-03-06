<?php

use yii\helpers\Html;

/**
 * @var syablonskiy\attachfile\models\Attachment[] $files
 * @var $moduleName string
 */
?>
<div class="file-list">
    <div class="info-row">
        <div class="info-cell name">Имя файла</div>
        <div class="info-cell size">Размер</div>
        <div class="info-cell download">Скачать</div>
        <?php if ($allowDeletion):?>
        <div class="info-cell download">Удалить</div>
        <?php endif;?>
    </div>
    <?php foreach ($files as $file): ?>
        <div class="info-row">
            <div class="info-cell name"><?= $file->name . "." . $file->type ?></div>
            <div class="info-cell size"><?= round($file->size / 1024) . " kB" ?></div>
            <div class="info-cell download">
                <?= Html::a(
                    '<span class="glyphicon glyphicon-download-alt"></span>',
                    ["/$moduleName/file/download", 'id' => $file->id],
                    ['title' => 'Скачать файл']
                ) ?>
            </div>
            <?php if ($allowDeletion):?>
                <div class="info-cell">
                    <input type="checkbox" name="files_for_deletion[]" value="<?= $file->id ?>">
                </div>
            <?php endif;?>
        </div>
    <?php endforeach; ?>
</div>
