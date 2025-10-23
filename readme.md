# 🧠 Background Remover API (Docker + FastAPI + rembg)

A lightweight background removal API built with **FastAPI** and **rembg**, containerized with Docker for easy deployment.  
It can remove image backgrounds and return a transparent PNG — ideal for ID generators, photo editors, or automated systems.

---

## 🚀 Features

- Fast background removal using `rembg` (U²Net deep learning model)
- REST API for easy integration
- Dockerized — portable and self-contained
- Accepts file uploads and returns processed image
- Optional tuning parameters (thresholds, alpha matting, etc.)
- Example PHP client with upload + preview UI

---

## 🧩 Requirements

- Docker (latest version)
- Port `8322` available on your server (can be customized)

---

## 🛠️ Build and Run

### 1️⃣ Clone the repository
```bash
git clone https://github.com/woots29/bgremover-api.git
cd bgremover-api
```

### 2️⃣ Build the Docker image
```bash
docker build -t bgremover-api .
```

### 3️⃣ Run the container
```bash
docker run -d --name bgremover-api --restart always -p 8322:8000 bgremover-api
```

This will start the API server in the background and make it available at:

👉 **http://localhost:8322**

If deployed remotely:
👉 **http://your-server-ip:8322**

---

## 📡 API Endpoint

### `POST /remove-background`

**Request**
- Accepts: `multipart/form-data`
- Parameter: `file` (image to process)

**Example using `curl`:**
```bash
curl -X POST -F "file=@sample.jpg" http://localhost:8322/remove-background -o output.png
```

**Response**
- Returns the processed image as `image/png` with transparent background.

---

## 💻 Example: PHP Test Page

The repo includes a test PHP file: `html_api/index.php`

It allows you to:
- Upload an image
- Adjust background removal parameters
- Preview result on a color background
- Download the processed image

**Usage:**
1. Place `html_api/bgrem.php` in your PHP server root.
2. Edit this line in the PHP file to match your API:
   ```php
   $api_url = "http://localhost:8322/remove-background";
   ```
3. Open it in your browser and test the removal.

---

## 🧰 API Parameters (optional)

You can extend the API to accept optional tuning parameters:
| Parameter | Type | Description |
|------------|------|-------------|
| `alpha_matting` | bool | Enable alpha matting refinement |
| `fg_threshold` | int | Foreground confidence threshold (default 240) |
| `bg_threshold` | int | Background confidence threshold (default 10) |
| `erode_size` | int | Matte erosion size |
| `post_process` | bool | Clean edges and smooth result |

These can be sent along with the upload in a JSON body or form data.

---

## 🧾 Logs & Monitoring

Check logs in real time:
```bash
docker logs -f bgremover-api
```

Restart manually:
```bash
docker restart bgremover-api
```

Stop and remove:
```bash
docker stop bgremover-api && docker rm bgremover-api
```

---

## 🐳 Quick Run Command

For copy-paste convenience:

```bash
docker run -d --name bgremover-api --restart always -p 8322:8000 bgremover-api
```
---
## 💻 Sample Real-World Code: ID Card Printing with TCPDF & ImageMagick

```php

$student_picture = $row['student_picture']; //mysql blob data

if (!empty($_POST['adjustments'])) { // Imagemagick adjustment

	$enhance = isset($_POST['adjustments']['enhance']) ? $_POST['adjustments']['enhance'] : 0;
	$autolevel = isset($_POST['adjustments']['autolevel']) ? $_POST['adjustments']['autolevel'] : 0;
	$sharpen = isset($_POST['adjustments']['sharpen']) ? $_POST['adjustments']['sharpen'] : 0;
	$gamma = isset($_POST['adjustments']['gamma']) ? $_POST['adjustments']['gamma'] : 1;
	$contrast = isset($_POST['adjustments']['contrast']) ? $_POST['adjustments']['contrast'] : 50;
	$brightness = isset($_POST['adjustments']['brightness']) ? $_POST['adjustments']['brightness'] : 50;
	$saturation = isset($_POST['adjustments']['saturation']) ? $_POST['adjustments']['saturation'] : 50;
	$image = new Imagick();
	
	$image->readImageBlob($student_picture);

	// Apply adjustments based on received values
	if ($enhance == 1) {
		$image->enhanceImage();
	}

	if ($autolevel == 1) {
		$image->autoLevelImage();
	}

	if ($sharpen == 1) {
		$image->adaptiveSharpenImage(2, 1);
	}

	// Adjust gamma (range [-50, 50] from slider range [0, 100])
	$gammavalue = $gamma / 100;
	$image->gammaImage($gammavalue) ;
	
	// Adjust contrast (range [-50, 50] from slider range [0, 100])
	$contrastValue = $contrast - 50;
	$image->brightnessContrastImage(0, $contrastValue);

	// Adjust brightness (range [-50, 50] from slider range [0, 100])
	$brightnessValue = $brightness - 50;
	$image->brightnessContrastImage($brightnessValue, 0);

	// Adjust saturation
	$image->modulateImage(100, $saturation + 50, 100); 
		
	$student_picture = $image->getImagesBlob();
}

try {
    $student_picture = removeBackground($student_picture);
} catch (Exception $e) {
    // If the API fails, just log and continue with the original image
    error_log("BG Remover failed: " . $e->getMessage());
}
//place the transparent student picture on an area
$pdf->Image('@'.$student_picture, -5, 8, 45, '', 'PNG', '', '', true, 300, '', false, false, '', false, false, false);

```
---
## 🧑‍💻 Author
Developed by **woots29**  
Based on [`rembg`](https://github.com/danielgatis/rembg) and [`FastAPI`](https://fastapi.tiangolo.com/).

---

## 🧩 License
MIT License © 2025 — Free to use and modify.
