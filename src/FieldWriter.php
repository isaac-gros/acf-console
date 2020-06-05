<?php

namespace App\Console\Command;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class FieldWriter
{

    /**
     * Filesystem instance
     * @var Filesystem
     */
    private $filesystem;

    // Constructor
    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * Write content to a file. Create this file if it doesnt exists.
     * @param String $path : The file path
     * @param String $name : The name of the variable
     * @param mixed $value : The value of the variable
     * @return void
     */
    public function writeFieldToFile($path, $name, $value)
    {
        if(!$this->filesystem->exists($path)) {
            $this->createFile($path);
            $this->filesystem->appendToFile($path, '<?php');
        }
        $this->filesystem->appendToFile($path, "\r\n\r\n");
        $this->filesystem->appendToFile($path, '$' . $name . ' = ');
        $this->filesystem->appendToFile($path, var_export($value, true));
        $this->filesystem->appendToFile($path, ';');
    }

    /**
     * Create a file if it doesnt exist.
     * @param String $path : The file path
     */
    public function createFile($path)
    {
        $this->filesystem->touch($path);
    }
}