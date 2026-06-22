import sys
import json
import joblib
import warnings

# Suppress scikit-learn warnings about feature names
warnings.filterwarnings("ignore")

try:
    if len(sys.argv) < 5:
        raise ValueError("Missing input arguments. Required: temperature, humidity, wind_speed, cloud_cover")

    temp = float(sys.argv[1])
    hum = float(sys.argv[2])
    wind = float(sys.argv[3])
    cloud = float(sys.argv[4])

    # Load Model and Encoder
    model = joblib.load('../database/weather_model.pkl')
    le = joblib.load('../database/label_encoder.pkl')

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
