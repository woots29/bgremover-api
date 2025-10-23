from fastapi import FastAPI, UploadFile, File, Form
from fastapi.responses import StreamingResponse, JSONResponse
from rembg import remove
from io import BytesIO
from PIL import Image
import uvicorn

app = FastAPI(title="Background Remover API", version="1.1.0")

@app.post("/remove-background")
async def remove_background(
    file: UploadFile = File(...),
    alpha_matting: bool = Form(False),
    fg_threshold: int = Form(240),
    bg_threshold: int = Form(10),
    erode_size: int = Form(10)
):
    """
    Removes background from an uploaded image with optional tuning parameters.
    """
    try:
        input_bytes = await file.read()
        img = Image.open(BytesIO(input_bytes))

        output_img = remove(
            img,
            alpha_matting=alpha_matting,
            alpha_matting_foreground_threshold=fg_threshold,
            alpha_matting_background_threshold=bg_threshold,
            alpha_matting_erode_size=erode_size
        )

        buffer = BytesIO()
        output_img.save(buffer, format="PNG")
        buffer.seek(0)

        return StreamingResponse(buffer, media_type="image/png")
    except Exception as e:
        return JSONResponse(status_code=500, content={"error": str(e)})

if __name__ == "__main__":
    uvicorn.run("bg_api:app", host="0.0.0.0", port=8000)
