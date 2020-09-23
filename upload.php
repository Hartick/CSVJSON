<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload file</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="http://momentjs.com/downloads/moment.min.js"></script>
</head>
<body>
<main class="container p-5">
    <div class="warning" id = 'errormessage'></div>
    <form class="starter-template" id ="upload" action="upload.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <input type="file" name="fileUpload" id="fileUpload"/>
            <button type="button" id="UploadFile" name="UploadFile" class="btn btn-info">Upload File</button>
            <input type="checkbox" id="serialize" name="serialize" value="serialize"> Do you want to serialize the results?<br>
        </div>
    </form>
    <div class="row">
        <div class="col-md-6 bg-dark text-white cont-1">
            <pre id="container-1">

            </pre><br>
            <button class="btn" id="download-1"><i class="fa fa-download"></i> Download</button>
        </div>
        <div class="col-md-6 bg-dark text-white cont-2">
            <pre id="container-2">

            </pre><br>
            <button class="btn" id="download-2"><i class="fa fa-download"></i> Download</button>
        </div>
    </div>
    <div id="go-top" onclick="topFunction()"></div>
</main>
</body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        $('#UploadFile').click(function(){
            $('#errormessage').empty();
            var s = 'false'
            if (serialize.checked == true){
                s = 'true';
            }
            var fileUpload = $('#fileUpload');
            var file = $(fileUpload).get(0).files[0],
                formData = new FormData();
            formData.append('fileUpload',file);
            $.ajax({
                url:'uploaderfiles.php?s='+s,
                data: formData,
                type: 'POST',
                contentType: false,
                cache: false,
                processData:false,
                success: function (data) {

                    var datadecoded = JSON.parse(data);
                    var date = new Date();

                    switch (datadecoded.extension) {
                        case ('json'):
                            $('.title1').empty();
                            $('#container-1').empty();
                            $('.cont-1').prepend('<h4 class="not-selectable title1">CSV<i class="copyable" onclick="copyToClipboard1()"></i></h4>');
                            if (datadecoded.csv != -1) {
                                $('#container-1').append(datadecoded.csv);
                            } else {
                                $('#container-1').append('JSON file not convertible in CSV');
                            }
                            $('.title2').empty();
                            $('#container-2').empty();
                            $('.cont-2').prepend('<h4 class="not-selectable title2">PHP<i class="copyable" onclick="copyToClipboard2()"></i></h4>');
                            var phpcode = datadecoded.php;
                            $('#container-2').append(phpcode.substr(6, phpcode.length-2));
                            document.getElementById("download-1").addEventListener("click", function(){
                                var filename = file.name.replace(/\.[^/.]+$/, "")+'-'+moment().format('l')+".csv";
                                var content = datadecoded.csv;
                                download(filename, datadecoded.csv);
                            }, false);

                            document.getElementById("download-2").addEventListener("click", function(){
                                var filename = file.name.replace(/\.[^/.]+$/, "")+'-'+moment().format('l')+".php";
                                var content = datadecoded.php;
                                download(filename, datadecoded.php);
                            }, false);

                            break;
                        case ('csv'):
                            $('.title1').empty();
                            $('#container-1').empty();
                            $('.cont-1').prepend('<h4 class="not-selectable title1">PHP<i class="copyable" onclick="copyToClipboard1()"></i></h4>');
                            var phpcode = datadecoded.php;
                            $('#container-1').append(phpcode.substr(6, phpcode.length-2));
                            $('.title2').empty();
                            $('#container-2').empty();
                            $('.cont-2').prepend('<h4 class="not-selectable 2">JSON<i class="copyable" onclick="copyToClipboard2()"></i></h4>');
                            $('#container-2').append(JSON.stringify(JSON.parse(datadecoded.json), null, "\t"));
                            document.getElementById("download-1").addEventListener("click", function(){
                                var filename = file.name.replace(/\.[^/.]+$/, "")+'-'+moment().format('l')+".php";
                                var content = datadecoded.php;
                                download(filename, datadecoded.php);
                            }, false);
                            document.getElementById("download-2").addEventListener("click", function(){
                                var filename = file.name.replace(/\.[^/.]+$/, "")+'-'+moment().format('l')+".json";
                                var content = datadecoded.json;
                                download(filename, datadecoded.json);
                            }, false);
                            break;
                        case ('php'):
                            console.log(datadecoded);
                            $('.title1').empty();
                            $('#container-1').empty();
                            $('.cont-1').prepend('<h4 class="not-selectable title1">JSON<i class="copyable" onclick="copyToClipboard1()"></i></h4>');
                            $('#container-1').append(JSON.stringify(JSON.parse(datadecoded.json), null, "\t"));
                            $('.title2').empty();
                            $('#container-2').empty();
                            $('.cont-2').prepend('<h4 class="not-selectable title2">CSV<i class="copyable" onclick="copyToClipboard2()"></i></h4>');
                            if (datadecoded.csv != -1) {
                                $('#container-2').append(datadecoded.csv);
                            } else {
                                $('#container-2').append('JSON file not convertible in CSV');
                            }
                            document.getElementById("download-1").addEventListener("click", function(){
                                var filename = file.name.replace(/\.[^/.]+$/, "")+'-'+moment().format('l')+".json";
                                var content = datadecoded.json;
                                download(filename, datadecoded.json);
                            }, false);
                            document.getElementById("download-2").addEventListener("click", function(){
                                var filename = file.name.replace(/\.[^/.]+$/, "")+'-'+moment().format('l')+".php";
                                var content = datadecoded.csv;
                                download(filename, datadecoded.csv);
                            }, false);
                            break;
                    }
                    if(datadecoded.error) {
                        $('#errormessage').append(datadecoded.error);
                    }
                }
            });
        });
    });
</script>
<script type="text/javascript">
    function download(filename, text) {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    }
    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("go-top").style.display = "block";
        } else {
            document.getElementById("go-top").style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }

    function copyToClipboard1() {
        var copyText = document.querySelector("#container-1");
        var textArea = document.createElement("textarea");
        textArea.value = copyText.textContent;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("Copy");
        textArea.remove();
    }
    function copyToClipboard2() {
        var copyText = document.querySelector("#container-2");
        var textArea = document.createElement("textarea");
        textArea.value = copyText.textContent;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("Copy");
        textArea.remove();
    }
</script>
</html>
