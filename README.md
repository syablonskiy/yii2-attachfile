# Yii2-attachfile
Данный модуль предназначен для приклепления файла(ов) к моделям ActiveRecord.
## Установка
1. Установите расширение с помощью Composer
выполните в терминале
`composer require syablonskiy/yii2-attachfile "^1"`
либо добавьте в секцию require в файле composer.json
`"syablonskiy/yii2-attachfile": "^1"`

2. Добавьте модуль в конфигурацию вашего web-приложения
```php
'modules' => [
        ...
        'attachfile' => [
            'class' => syablonskiy\attachfile\Module::className(),
            'storePath' => 'path/to/upload/folder', //default '@app/uploads'
            'rules' => [
                'extensions' => 'jpg, zip, mp4',
                'maxSize' => 1024*1024,
            ],
            'maxFiles' => 2, //default  '3'
            'tableName' => 'your_table_name', //default 'attachment'
        ],
        ...
]
```
3. Выполните миграцию
`php yii migrate --migrationPath=@syablonskiy/attachfile/migrations`

4. Прикрепите поведение к вашей ActiveRecord модели
```php
    public function behaviors()
    {
        return [
           \syablonskiy\attachfile\behaviors\AttachmentBehavior::className()
        ];
    }
```
5. Удаление неактуальных файлов
В конфигурацию консольного приложения добавьте
```php
'modules' => [
        ...
        'attachfile' => [
            'class' => syablonskiy\attachfile\Module::className(),
            'controllerNamespace' => 'syablonskiy\attachfile\commands',
        ],
        ...
]
```
И добавьте в планировщик Cron подобную строку
`*/30 * * * * cd /path/to/your/app && /usr/bin/php yii attachfile/cron/delete-files >/dev/null`
После этого каждые полчаса будут удаляться загруженные, но неприкрепленные к модели файлы.

## Использование
1. Для прикрепления файлов, к создаваемой модели, передайте в виджет текущий экземпляр [[yii\widgets\ActiveForm]]
```php
<?= \syablonskiy\attachfile\widgets\InputWidget::widget(['form' => $form]) ?>
```

2. Отображение прикреплённых файлов модели `$model`
```php
<?= \syablonskiy\attachfile\widgets\FilesListWidget::widget(
        [
            'model' => $model,
            'allowDeletion' => true //default 'false'
        ]
    ) ?>
```
Опция 'allowDeletion' добавляет возможность отмечать файлы для удаления