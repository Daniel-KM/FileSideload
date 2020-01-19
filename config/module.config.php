<?php
namespace FileSideload;

return [
    'service_manager' => [
        // Override the default local store in order to hard-link files if possible, else copy them as usual.
        'Omeka\File\Store\Local' => Service\File\Store\LocalFactory::class,
    ],
    'media_ingesters' => [
        'factories' => [
            'sideload' => Service\MediaIngesterSideloadFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            Form\ConfigForm::class => Service\Form\ConfigFormFactory::class,
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'csv_import' => [
        'media_ingester_adapter' => [
            'sideload' => CSVImport\SideloadMediaIngesterAdapter::class,
        ],
    ],
];
