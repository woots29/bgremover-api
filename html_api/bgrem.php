<?php
/**
 * removeBackground
 * Sends a binary image blob to the background remover API and returns the processed blob.
 * 
 * @param string $imageBlob - binary image data (JPEG/PNG)
 * @param string $apiUrl - URL of the bgremover API
 * @return string - processed image blob (PNG)
 * @throws Exception on failure
 */
function removeBackground($imageBlob, $apiUrl = "http://[IP_HOST]:8322/remove-background") {
    // Create a temporary file
    $tmpFile = tempnam(sys_get_temp_dir(), 'bg_');
    file_put_contents($tmpFile, $imageBlob);

    // Prepare cURL
    $ch = curl_init();
    $cfile = new CURLFile($tmpFile, 'image/jpeg', 'image.jpg'); // adjust MIME if needed
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $cfile]);

    // Execute
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        unlink($tmpFile);
        curl_close($ch);
        throw new Exception("Curl Error: " . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    unlink($tmpFile);

    if ($httpCode !== 200) {
        throw new Exception("Background remover API returned HTTP code $httpCode");
    }

    return $response; // PNG blob
}
