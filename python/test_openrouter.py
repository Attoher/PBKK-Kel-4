# test_openrouter.py
import requests
from openai import OpenAI

API_KEY = "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"
BASE_URL = "https://openrouter.ai/api/v1"

try:
    client = OpenAI(
        base_url=BASE_URL,
        api_key=API_KEY,
    )
    
    # Test simple completion
    completion = client.chat.completions.create(
        extra_headers={
            "HTTP-Referer": "http://localhost:8000",
            "X-Title": "FormatCheck ITS",
        },
        model="tngtech/deepseek-r1t2-chimera:free",
        messages=[
            {"role": "user", "content": "Hello, test connection"}
        ],
        max_tokens=10
    )
    
    print("✅ OpenRouter connection successful!")
    print("Response:", completion.choices[0].message.content)
    
except Exception as e:
    print(f"❌ OpenRouter connection failed: {e}")