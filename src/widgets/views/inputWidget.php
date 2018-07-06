<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $uploadModel syablonskiy\attachfile\forms\UploadForm
 * @var $moduleName string
 * @var $maxFiles int
 * @var $label string
 */

?>

    <div class="attach-widget"
         data-maxFiles="<?= $maxFiles ?>" data-modulename="<?= $moduleName ?>">
        <?= Html::hiddenInput('list_of_files_for_attachment') ?>

        <?php
        echo $form->field($uploadModel, 'file')
            ->fileInput(['style' => 'display:none'])
            ->label($label)
        ?>


        <div class="progress" style="height:3px;display:none">
            Загрузка файла
            <div class="progress-bar" role="progressbar" style="width: 0" aria-valuenow="0" aria-valuemin="0"
                 aria-valuemax="100"></div>
        </div>
        <div class="uploads"></div>
    </div>

<?php
$script = <<<JS

//on case if form sends or uploads through ajax
$('body').off('change', '#uploadform-file');
$('body').off('click', '.tempFile');
$('body').on('submit', 'form', function () {    
    var input = $(this).find('input[name=list_of_files_for_attachment]');
    if (input !== undefined) {
        setTimeout(function () {
            input.val('');
        }, 100);
    }
});

var widget = $('.attach-widget');
var maxFiles = widget.data('maxfiles');
var moduleName = widget.data('modulename');
var label = widget.find('label');
var attachment = widget.find('input[name=list_of_files_for_attachment]');
var uploadingFlag = false;

$('body').on('afterValidateAttribute', function(event, attribute, messages){
    console.log(attribute);
    if (attribute.id === 'uploadform-file') {
        if (messages.length === 0) {
            console.log('file is valid');
            sendFile();
        } else {
            //очищаем инпут от некорректного файла, иначе форма не отправится
            reset($('#uploadform-file'));
            console.log('file is not valid');
        }
    }
});

//удаление файла из прикрепленных
$('body').on('click', '.tempFile', function () {
    var element = $(this);
    var currentId = element.attr('data-id');
    var currentValue = attachment.val();
    var files = currentValue.split(',');
    if (files.length === maxFiles) {
        label.show();
    }

    var position = files.indexOf(currentId);
    if (position !== -1) {
        files = removeElement(files, position);
        attachment.val(files.join(','));
        element.remove();
    }

});

function removeElement(arr, index) {
    delete arr[index];
    return arr.filter(function (item) {
        return item !== undefined;
    });
}

function sendFile() {
    if (!uploadingFlag) {
        
        var inputElement = $('#uploadform-file');
        if (!inputElement.get(0).files.length) return false;
        
        uploadingFlag = true;
        
        var progress = widget.find('.progress');
        var bar = progress.find('.progress-bar');
        var uploads = widget.find('.uploads');

        var inputFile = inputElement.get(0).files[0];
        var formData = new FormData();
        formData.append('UploadForm[file]', inputFile);
        
        //очистка формы
        reset(inputElement);

        $.ajax({
            url: "/" + moduleName + "/file/upload",
            method: "POST",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                var percentVal = '0%';
                progress.show();
                bar.width(percentVal);
            },
            success: function (data) {
                progress.hide(500);
                bar.width('0%');
                if (data) {
                    uploads.append('<p class="tempFile" data-id="' + data.id + '">' +
                        '<span class="text-warning">' + data.name + '</span> загружен' +
                        '<span class="glyphicon glyphicon-remove" title="Удалить файл"></span></p>');
                    console.log(data);

                    var currentValue = attachment.val();
                    if (currentValue === "") {
                        var files = [];
                    } else {
                        var files = currentValue.split(',');
                    }
                    files.push(data.id);
                    if (files.length === maxFiles) {
                        label.hide();
                    }
                    attachment.val(files.join(','));
                    uploadingFlag = false;
                }
            },
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(ev) {
                    var percent = (ev.loaded / ev.total) * 100 + '%';
                    bar.width(percent);
                }, false);
                return xhr;
            },
            error: function (jqXHR, exception) {
                uploadingFlag = false;
                progress.hide(500);
            }
        });
        return false;
    }
}

function reset(e) {
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();
}
JS;

$this->registerJs($script);

?>