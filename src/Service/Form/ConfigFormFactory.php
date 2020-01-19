<?php
namespace FileSideload\Service\Form;

use FileSideload\Form\ConfigForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConfigFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $config = $services->get('Config');
        $basePath = $config['file_store']['local']['base_path'] ?: OMEKA_PATH . '/files';
        $originalFilesPath = $basePath . '/original';

        $form = new ConfigForm(null, $options);
        return $form
            ->setOriginalFilesPath($originalFilesPath);
    }
}
