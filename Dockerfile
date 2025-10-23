FROM python:3.11-slim

# OS dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libgl1 \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Copy requirements
COPY requirements.txt .

# Install Python dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Copy API code
COPY bg_api.py .

# Expose port
EXPOSE 8000

# Start API server
CMD ["python", "bg_api.py"]
