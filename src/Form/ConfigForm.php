<?php
namespace FileSideload\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Validator\Callback;

class ConfigForm extends Form
{
    /**
     * @var string
     */
    protected $originalFilesPath;

    public function init()
    {
        $this->add([
            'type' => 'text',
            'name' => 'directory',
            'options' => [
                'label' => 'Sideload directory', // @translate
                'info' => 'Enter the absolute path to the directory where files to be sideloaded will be added. The directory can be anywhere on your server.', // @translate
            ],
            'attributes' => [
                'required' => true,
                'id' => 'directory',
            ],
        ]);
        $this->add([
            'type' => 'checkbox',
            'name' => 'delete_file',
            'options' => [
                'label' => 'Delete sideloaded file?', // @translate
                'info' => 'Do you want to delete a file from the sideload directory after it has been sideloaded? If so, the directory must be server-writable.', // @translate
                'use_hidden_element' => true,
                'checked_value' => 'yes',
                'unchecked_value' => 'no',
            ],
            'attributes' => [
                'id' => 'delete-file',
            ],
        ]);
        $this->add([
            'type' => Element\Radio::class,
            'name' => 'filesideload_mode',
            'options' => [
                'label' => 'Import mode', // @translate
                'value_options' => [
                    'copy' => 'Copy', // @translate
                    'hardlink_copy' => 'Hard link (or copy if unsupported)', // @translate
                    'hardlink' => 'Hard link (or fail if unsupported)', // @translate
                ],
                'info' => 'Hard-link a file is quicker and more space efficient if supported by the server.', // @translate
                // TODO Give a link to the documentation to explain hard-link and how to check devices.
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => 'filesideload-mode',
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'directory',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'Callback',
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => 'The provided sideload directory is not a directory or does not have sufficient permissions.', // @translate
                        ],
                        'callback' => [$this, 'directoryIsValid']
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name' => 'filesideload_mode',
            'required' => true,
            'validators' => [
                [
                    'name' => 'Callback',
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => 'Hard-linking between the provided directory and the Omeka files/original directory is not supported. See readme for more informations.', // @translate
                        ],
                        'callback' => [$this, 'supportHardlink'],
                    ],
                ],
            ],
        ]);
    }

    public function directoryIsValid($dir, $context)
    {
        $dir = new \SplFileInfo($dir);
        $valid = $dir->isDir() && $dir->isExecutable() && $dir->isReadable();
        if (isset($context['delete_file']) && 'yes' === $context['delete_file']) {
            $valid = $valid && $dir->isWritable();
        }
        return $valid;
    }

    public function supportHardlink($mode, $context)
    {
        // Check only if admin chooses to hard-link only.
        if ($mode !== 'hardlink') {
            return true;
        }

        $directory = $context['directory'];
        if (!$directory || !$this->directoryIsValid($directory, $context)) {
            return false;
        }

        $sourcePath = $directory;

        $originalPath = $this->getOriginalFilesPath();
        $destinationFilepath = $originalPath . '/test_hardlink.txt';

        $sourceFilepath = $sourcePath . '/test_hardlink.txt';
        $sourceExists = file_exists($sourceFilepath);
        if ($sourceExists) {
            if (!is_readable($sourceFilepath)) {
                return false;
            }
        } else {
            if (!is_writeable($sourcePath)) {
                return false;
            }
            $result = file_put_contents($sourceFilepath, sprintf('Test hard-linking from "%s" to "%s".', $sourceFilepath, $destinationFilepath));
            if ($result === false) {
                return false;
            }
        }

        $result = @link($sourceFilepath, $destinationFilepath);

        if (!$sourceExists) {
            @unlink($sourceFilepath);
        }

        if (!$result) {
            return false;
        }

        @unlink($destinationFilepath);
        return true;
    }

    public function setOriginalFilesPath($originalFilesPath)
    {
        $this->originalFilesPath = $originalFilesPath;
        return $this;
    }

    public function getOriginalFilesPath()
    {
        return $this->originalFilesPath;
    }
}
