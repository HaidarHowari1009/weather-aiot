import sys
import os
import json
import joblib
import warnings

# Suppress scikit-learn warnings about feature names
warnings.filterwarnings("ignore")

# Use script directory for resolving paths (not CWD)
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
DB_DIR = os.path.join(SCRIPT_DIR, '..', 'database')

try:
    if len(sys.argv) < 5:
        raise ValueError("Missing input arguments. Required: temperature, humidity, wind_speed, cloud_cover")

    temp = float(sys.argv[1])
    hum = float(sys.argv[2])
    wind = float(sys.argv[3])
    cloud = float(sys.argv[4])

    # Load Model and Encoder from database directory
    model_path = os.path.join(DB_DIR, 'weather_model.pkl')
    encoder_path = os.path.join(DB_DIR, 'label_encoder.pkl')

    model = joblib.load(model_path)
    le = joblib.load(encoder_path)

    # Predict
    input_data = [[temp, hum, wind, cloud]]
    pred_encoded = model.predict(input_data)[0]
    
    # Confidence
    probabilities = model.predict_proba(input_data)[0]
    confidence = max(probabilities) * 100

    # Decode
    pred_class = le.inverse_transform([pred_encoded])[0]

    result = {
        "status": "success",
        "prediction": str(pred_class),
        "confidence": round(confidence, 2)
    }
    print(json.dumps(result))

except Exception as e:
    print(json.dumps({"status": "error", "message": str(e)}))
