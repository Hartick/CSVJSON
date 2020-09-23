<?php
require_once 'src/File.php';
require_once 'src/Conversion.php';

if(isset($_FILES['fileUpload'])) {
    $temp_file = $_FILES['fileUpload'];
    //var_dump($_FILES);
    if($temp_file['name'] == null ) {
        echo json_encode(array('error'=>'No files upload'));
        return;
    }
    $file = new File($temp_file['name']);
    try {
        $file->fileExists(array('php','json','csv'));
    } catch (Exception $e) {
        echo json_encode(array('error'=>$e->getMessage()));
        return;
    }

    $serialize = isset($_GET['s'])? false : true;
    $content =  file_get_contents($temp_file['tmp_name']);
    switch ($file->getExtension()) {
        case "json":
            echo json_encode(array('extension'=>'json',
                'csv'=>Conversion::jsonToCSV(
                    $content,
                    $file->getFileNameWithoutPath(),
                    null,
                    File::BROWSER,
                    $serialize
                ),
                'php'=>Conversion::toPHP(json_decode($content, true)))
            );
            break;
        case "php":
            echo json_encode(
                array('extension'=>'php',
                    'csv'=>Conversion::arrayToCSV(
                        $content,
                        $file->getFileNameWithoutPath(),
                        null,
                        FILE::BROWSER,
                        $serialize
                    ),
                    'json'=>Conversion::phpToJson($content)
                )
            );
            break;
        case "csv":
            echo json_encode(
                array('extension'=>'csv',
                    'php'=>Conversion::toPHP(
                        Conversion::csvToArray(
                            $content
                        )
                    ),
                    'json'=>Conversion::csvToJson($content)
                )
            );
            break;
    }

} else {
    echo json_encode(array('error'=>'No files upload'));
    return;
}
