<?php
namespace FileSideload\Service;

use FileSideload\Media\Ingester\Sideload;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MediaIngesterSideloadFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $settings = $services->get('Omeka\Settings');
        return new Sideload(
            $settings->get('file_sideload_directory'),
            $settings->get('file_sideload_delete_file') === 'yes',
            $settings->get('file_sideload_mode'),
            $services->get('Omeka\File\TempFileFactory'),
            $services->get('Omeka\File\Validator')
        );
    }
}
