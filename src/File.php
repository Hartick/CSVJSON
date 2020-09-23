<?php
/**
 * Class File
 *
 * @version 1.0
 * @package csvimportexport
 * @author Giuseppe Scarfò <giuseppe.scarfo@giuseppescarfo.it> 
 */

class File
{
    /** constant */
    const BROWSER = 1;
    const SHELL = 0;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var int $type
     */
    private $type;

    /**
     * @var string $extension
     */
    private $extension;

    /**
     * File constructor.
     * @param string $name
     * @param int $type
     * @param string $extension
     */
    public function __construct (string $name, int $type = self::BROWSER, string $extension = null)
    {
        $this -> name = $name;
        $this -> type = $type;
        $this -> extension = $extension;
    }

    /**
     *
     * fileExists
     *
     * Verify the temporary file exists in temp folder
     * @param  $extension
     * @throws Exception
     * @return $this|bool
     */
    public function fileExists($extension = null)
    {
        $file_tmp = tempnam(sys_get_temp_dir(), $this->name);
        if(file_exists($file_tmp)){
            if($extension != null) {
                if(!self::isValidExtensionFile($extension)) {
                    if(count($extension) > 1) {
                        throw new Exception("The file that you chosen isn't allowed file");
                    } else {
                        throw new Exception("The file that you chosen isn't a $extension[0] file");
                    }
                }
            }
            $this->name = $file_tmp;
        } else {
            return false;
        }
        return $this;
    }

    /**
     * isValidExtensionFile
     *
     * Verify is valid extention file.
     * @param array $extensions
     * @return bool
     */
    public function isValidExtensionFile(array $extensions)
    {
        $fileinfo = pathinfo($this->name);
        foreach ($extensions as $extension) {
            if($fileinfo['extension'] == $extension) {
                $this->extension = $fileinfo ['extension'];
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }


    /**
     * getFileNameWithoutPath
     *
     * @return bool|string
     */
    public function getFileNameWithoutPath()
    {
        return substr($this->name, strrpos('\\' || '/',$this->name));
    }

    /**
     * read
     *
     * @return bool|null|string
     * @throws ShellInvalidException
     */
    public function read()
    {
        if($this->type == self::SHELL) {
            $strShell = "cat $this->name";
            $content = shell_exec($strShell);
            if($content === NULL) {
                throw new ShellInvalidException("$this->name is not a file or not exist");
            }
            return $content;
        } else {
            return file_get_contents($this->name);
        }
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this -> name;
    }


    public function getPathTempFile()
    {
        return  tempnam(sys_get_temp_dir(), $this->name);
    }
}
