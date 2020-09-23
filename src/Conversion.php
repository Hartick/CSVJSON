<?php

require_once 'ShellInvalidException.php';

/**
 * Class Conversion
 *
 * @version 1.0
 * @package csvimportexport
 * @author Giuseppe Scarfò <giuseppe.scarfo@giuseppescarfo.it>
 */
class Conversion
{


    /**
     * csvToArray
     *
     * Convert csv file to array
     * @param string $file
     * @return array
     */
    public static function csvToArray(string $file)
    {
        $array = array();
        $rows = explode("\n",$file);
        foreach ($rows as $row) {
            if($row == "") {
                continue;
            }
            $array[] = str_getcsv($row);
        }
        return self::mountKey($array);
    }

    /**
     * csvToJson
     *
     * Convert csv file to json
     * @param string $file
     * @return false|string
     */
    public static function csvToJson(string $file)
    {
        return json_encode(self::csvToArray($file));
    }


    /**
     * arrayToCSV
     *
     * converts array To CSV
     * @param $array
     * @param int $type
     * @param string $filename
     * @param string $path
     * @param bool $serialize
     * @param string $delimiter
     * @param string $enclosure
     * @return string|bool
     */
    public static function arrayToCSV($array, string $filename, string $path = null, int $type = File::BROWSER,  bool $serialize = true, string $delimiter = ',', string $enclosure = '"')
    {
        //$array = json_decode(substr($array,strpos("=",$array)),true);

        if(!is_array($array)) {
            $array = json_decode(substr($array,15,-1),true);
        }

        if($type == File::SHELL){
            $filename.='.csv';
            $f = fopen($filename, 'w');
            $i = 0;
            foreach ($array as $element) {
                $i = 0;
                foreach ($element as $e) {
                    if (is_array($e)){
                        if ($serialize) {
                            $element[$i++] = serialize($e);
                        } else {
                            return -1;
                        }
                    }
                }
                if(!fputcsv($f, $element, $delimiter, $enclosure)) {
                    unlink($filename.'.csv');
                    return false;
                }
            }
            fclose($f);
            if($path !== NULL) {
                shell_exec("mv $filename $path");
            }
            return true;

        } else {
            $handle = fopen('temp.handle', 'w');
            $i = 0;
            foreach ($array as $element) {
                $i = 0;
                    foreach ($element as $e) {
                        if (is_array($e)){
                            if ($serialize) {
                                $temp = $element;
                                $temp[$i++] = serialize($e);
                                $element = $temp;
                            } else {
                                return -1;
                            }
                        }
                    }

                fputcsv($handle, $element, $delimiter, $enclosure);
            }
            $contents = file_get_contents('temp.handle');
            unlink('temp.handle');
            return $contents;
        }
    }


    /**
     * toFileShell
     *
     * Create a file in choose path.
     * @param string $path
     * @param string $name
     * @param mixed $element
     * @param string $extension
     * @param string $filename
     * @return bool
     */
    public static function toFileShell(string $path, string $name, $element, string $extension, string $filename = NULL)
    {
        $f = NULL;
        if($filename == NULL) {
            $filename = $name.'_'. date("D_M_d_Y_G_i");
        }
        $filename.= '.'.$extension;

        $f = fopen($filename, 'w');
        if($extension == 'php') {
            $content = self::toPHP($element);
        } else {
            $content = $element;
        }
        if (!fwrite($f,$content)) {
            return false;
        } else {
            shell_exec("mv $filename $path");
        }
        fclose($f);
        return true;
    }

    /**
     * toPHP
     *
     * @param array $elements
     * @return string
     */
    public static function toPHP(array $elements)
    {
        $str=  '<?php'. "\n" . '$array = '."\n".'['."\n";
        $i = 1;
        foreach ($elements as $element) {
            $str.= "\t";
            if($i == count($elements)) {
                $str.= json_encode($element)."\n";
            } else {
                $str.= json_encode($element).",\n";
            }
            $i++;
        }
        $str.= '];';

        return $str;
    }

    /**
     * jsonToCSV
     *
     * Converts json file to CSV
     * @param string $array
     * @param int $type
     * @param string $filename
     * @param string $path
     * @param bool $serialize
     * @param string $delimiter
     * @param string $enclosure
     * @return string|bool
     */
    public static function jsonToCSV(string $array, string $filename, string $path = NULL, int $type = File::BROWSER, bool $serialize = true, string $delimiter = ',', string $enclosure = '"')
    {
        $json_decoded = json_decode($array, true);
        return self::arrayToCSV($json_decoded, $filename, $path,$type,$serialize, $delimiter, $enclosure);
    }

    /**
     * phpToJson
     *
     * Convert a php file to json
     * @param $array
     * @return false|string
     */
    public static function phpToJson($array)
    {
        return $array = json_encode(json_decode(substr($array,15,-1),true));
    }


    /**
     * mountKey
     *
     * Mount array with key=>value
     * @param array $array
     * @return array
     */
    public static function mountKey(array $array)
    {
        $row = $array[0];
        unset($array[0]);
        $i = 0;
        $assocArray = array();
        $assocArray[] = $row;
        $temp = array();
        foreach($array as $arr) {
            $i = 0;
            foreach($arr as $a) {
                $temp[$row[$i++]] = $a;
            }
            $assocArray[] = $temp;
            $temp = array();
        }
         //var_dump($assocArray);
        return $assocArray;
    }
}
