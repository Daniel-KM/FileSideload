<?php
namespace FileSideload;

return [
    'media_ingesters' => [
        'factories' => [
            'sideload' => Service\MediaIngesterSideloadFactory::class,
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'csv_import_media_ingester_adapter' => [
        'sideload' => CSVImport\SideloadMediaIngesterAdapter::class,
    ],
];
