<?php
require_once 'src/Conversion.php';
require_once 'src/File.php';
/**
 * Class Shell
 *
 * @version 1.0
 * @package csvimportexport
 * @author Giuseppe Scarfò <giuseppe.scarfo@giuseppescarfo.it>
 */
class Shell
{
    /**
     * constant
     */
    const SHORTOPTION  = "v::";
    const LONGOPTION  = array(
        "transform:",
        "type:",
        "file:",
        "outfolder:",
        "filename:",
        "help::",
        "no-serialize"
        );

    const HELP = <<<TEXT

TEXT;


    /**
     * exec
     *
     * Run shell script.
     * @throws ShellInvalidException
     */
    public static function exec()
    {
        $conv = null;
        $args = getopt(self::SHORTOPTION,self::LONGOPTION);

        if(isset($args['help'])) {
            echo self::HELP;
            return;
        }

        if(isset($args['transform'])) {
            switch ($args['transform']) {
                case "csvto":
                    if (!isset($args['type'])) {
                       throw new ShellInvalidException(
                           'You must insert the type (array or json) for this transform'
                       );
                    }
                    $file = new File($args['file'],File::SHELL);
                    if ($args['type'] == "json") {
                        if ($file !== false) {
                            $conv = Conversion::csvToJson($file->read());
                            if (
                                Conversion::toFileShell(
                                    $args['outfolder'],
                                    $file->getFileNameWithoutPath(),
                                    $conv,
                                    'json',
                                    (isset($args['filename']) ? $args['filename'] : NULL )
                                )
                            ) {
                                echo "Operazione completata. Il tuo file si trova nella cartella {$args['outfolder']}\n";
                            } else {
                                echo "Non è stato possibile creare il fine nella cartella {$args['outfolder']}\n";
                            }
                        }
                    } else if ($args['type'] == "array") {
                        if ($file !== false) {
                            $conv = Conversion::csvToArray($file->read());
                            if (
                                Conversion::toFileShell(
                                    $args['outfolder'],
                                    $file->getFileNameWithoutPath(),
                                    $conv,
                                    'php',
                                    (isset($args['filename']) ? $args['filename'] : NULL )
                                )
                            ) {
                                echo
                                "Operazione completata. Il tuo file si trova nella cartella {$args['outfolder']}\n";
                            } else {
                                echo "Non è stato possibile creare il fine nella cartella {$args['outfolder']}\n";
                            }
                        }
                    } else {
                        echo "Error on format\n";
                    }
                    break;
                case "tocsv":
                    $file = new File($args['file'],File::SHELL);
                    $serialize = true;
                    if(isset($args['no-serialize'])){
                        $serialize = false;
                    }
                    if ($file !== false) {
                        $path = isset($args['outfolder']) ? $args['outfolder'] : NULL;
                        if ($file->isValidExtensionFile(array('php'))) {
                            Conversion::arrayToCSV(
                                $file->read(),
                                (isset($args['filename']) ? $args['filename'] :$file->getFileNameWithoutPath())
                                ,File::SHELL,
                                $path,
                                $serialize

                            );
                        } else if($file->isValidExtensionFile(array('json'))) {
                            if(
                                Conversion::jsonToCsv(
                                    $file->read(),
                                    (isset($args['filename']) ? $args['filename'] :$file->getFileNameWithoutPath())
                                    ,File::SHELL,
                                    $path,
                                    $serialize
                                )
                            ) {
                                if($path == NULL) {
                                    echo "Operazione completata. Il tuo file si trova nella cartella corrente.\n";
                                } else {
                                    echo
                                    "Operazione completata. Il file si trova nella cartella {$args['outfolder']}\n";
                                }

                            } else {
                                echo "Non è stato possibile creare il file.\n";
                            }
                        } else {
                            throw new ShellInvalidException("{$args['file']} isn't a php file or json file\n");
                        }
                    }
                    break;
                default:
                    throw new ShellInvalidException("Invalid params\n");
            }
        } else {
            throw new ShellInvalidException("You must insert a --transform parameter\n");
        }
    }
}

try{
Shell::exec();
} catch (Exception $e) {
    echo $e->getMessage();
}
