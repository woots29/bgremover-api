<?php
require_once "bgrem.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $imageBlob = file_get_contents($_FILES['file']['tmp_name']);

        $params = [
            'alpha_matting' => $_POST['alpha_matting'],
            'fg_threshold'  => $_POST['fg_threshold'],
            'bg_threshold'  => $_POST['bg_threshold'],
            'erode_size'    => $_POST['erode_size']
        ];

        $result = removeBackground($imageBlob, "http://[IP_HOST]:8322/remove-background", $params);

        header("Content-Type: image/png");
        echo $result;
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Background Remover Tester</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .preview { margin-top: 10px; max-width: 400px; display:block; }
        .slider-group { margin: 10px 0; }
        .result { margin-top: 20px; }
        .sl-value { font-weight: bold; }
        #resultContainer {
            display: inline-block;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #ffffff;
        }
        #bgColorLabel { font-weight: bold; }
    </style>
</head>
<body>

<h2>Background Remover Tester</h2>

<form id="bgForm" enctype="multipart/form-data">
    <label>Choose Image:</label><br>
    <input type="file" name="file" id="fileInput" accept="image/*" required><br>
    <img id="previewImg" class="preview" style="display:none;"/><br><br>

    <label><input type="checkbox" name="alpha_matting" id="alphaMatting" value="true" checked> Enable Alpha Matting</label><br><br>

    <div class="slider-group">
        <label>Foreground Threshold: <span id="fgValue" class="sl-value">240</span></label><br>
        <input type="range" name="fg_threshold" id="fgSlider" min="0" max="255" value="240">
    </div>

    <div class="slider-group">
        <label>Background Threshold: <span id="bgValue" class="sl-value">10</span></label><br>
        <input type="range" name="bg_threshold" id="bgSlider" min="0" max="255" value="10">
    </div>

    <div class="slider-group">
        <label>Erode Size: <span id="erodeValue" class="sl-value">10</span></label><br>
        <input type="range" name="erode_size" id="erodeSlider" min="0" max="50" value="10">
    </div>

    <div class="slider-group">
        <label id="bgColorLabel">Result Background Color:</label><br>
        <input type="color" id="bgColorPicker" value="#ffffff">
    </div>

    <button type="submit">Remove Background</button>
</form>

<div class="result" id="resultSection" style="display:none;">
    <h3>Result</h3>
    <div id="resultContainer">
        <img id="resultImg" class="preview">
    </div><br>
    <a id="downloadLink" download="output.png">Download Result</a>
</div>

<script>
$(function() {
    // Update live slider values
    $('#fgSlider').on('input', function() { $('#fgValue').text(this.value); });
    $('#bgSlider').on('input', function() { $('#bgValue').text(this.value); });
    $('#erodeSlider').on('input', function() { $('#erodeValue').text(this.value); });

    // Preview uploaded file
    $('#fileInput').on('change', function() {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Change result background color live
    $('#bgColorPicker').on('input', function() {
        $('#resultContainer').css('background-color', this.value);
    });

    // AJAX submit
    $('#bgForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        if (!$('#alphaMatting').is(':checked')) {
            formData.set('alpha_matting', 'false');
        }

        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: { responseType: 'blob' },
            success: function(data) {
                let imgURL = URL.createObjectURL(data);
                $('#resultImg').attr('src', imgURL);
                $('#downloadLink').attr('href', imgURL);
                $('#resultSection').show();
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseText);
            }
        });
    });
});
</script>

</body>
</html>
