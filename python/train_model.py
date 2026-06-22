import sys
import os
import json
import joblib
import pandas as pd
import numpy as np
import sqlite3
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score, confusion_matrix
import matplotlib
matplotlib.use('Agg')  # Use non-interactive backend for server environments
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.preprocessing import LabelEncoder

# Use script directory for resolving paths (not CWD)
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
DB_DIR = os.path.join(SCRIPT_DIR, '..', 'database')
ASSETS_DIR = os.path.join(SCRIPT_DIR, '..', 'assets', 'images')

# Configuration
DB_PATH = os.path.join(DB_DIR, 'weather.sqlite')

try:
    # Connect to Database
    conn = sqlite3.connect(DB_PATH)
    
    # Read Data
    query = "SELECT temperature, humidity, wind_speed, cloud_cover, weather_desc FROM weather_data"
    df = pd.read_sql(query, conn)
    
    if len(df) < 50:
        print(json.dumps({"status": "error", "message": "Dataset terlalu kecil untuk dilatih (minimal 50 data)."}))
        sys.exit(1)
        
    # Preprocessing
    X = df[['temperature', 'humidity', 'wind_speed', 'cloud_cover']]
    y = df['weather_desc']
    
    # Label Encoding
    le = LabelEncoder()
    y_encoded = le.fit_transform(y)
    
    # Save Label Encoder to database directory
    joblib.dump(le, os.path.join(DB_DIR, 'label_encoder.pkl'))
    
    # Train Test Split (80% training, 20% testing)
    X_train, X_test, y_train, y_test = train_test_split(X, y_encoded, test_size=0.2, random_state=42)
    
    # Training Model
    model = RandomForestClassifier(n_estimators=100, random_state=42)
    model.fit(X_train, y_train)
    
    # Save Model to database directory
    joblib.dump(model, os.path.join(DB_DIR, 'weather_model.pkl'))
    
    # Evaluation
    y_pred = model.predict(X_test)
    
    # use average='weighted' to handle multi-class
    accuracy = accuracy_score(y_test, y_pred)
    precision = precision_score(y_test, y_pred, average='weighted', zero_division=0)
    recall = recall_score(y_test, y_pred, average='weighted', zero_division=0)
    f1 = f1_score(y_test, y_pred, average='weighted', zero_division=0)
    
    # Save evaluation to DB
    cursor = conn.cursor()
    insert_eval = "INSERT INTO model_evaluation (accuracy, precision_score, recall_score, f1_score) VALUES (?, ?, ?, ?)"
    cursor.execute(insert_eval, (float(accuracy), float(precision), float(recall), float(f1)))
    conn.commit()
    
    # Generate Confusion Matrix
    cm = confusion_matrix(y_test, y_pred)
    plt.figure(figsize=(8, 6))
    sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', xticklabels=le.classes_, yticklabels=le.classes_)
    plt.ylabel('Actual')
    plt.xlabel('Predicted')
    plt.title('Confusion Matrix')
    
    # Ensure images dir exists
    if not os.path.exists(ASSETS_DIR):
        os.makedirs(ASSETS_DIR)
        
    plt.savefig(os.path.join(ASSETS_DIR, 'confusion_matrix.png'))
    plt.close()
    
    cursor.close()
    conn.close()
    
    result = {
        "status": "success",
        "message": "Model berhasil dilatih.",
        "metrics": {
            "accuracy": round(accuracy * 100, 2),
            "precision": round(precision * 100, 2),
            "recall": round(recall * 100, 2),
            "f1_score": round(f1 * 100, 2)
        }
    }
    print(json.dumps(result))

except Exception as e:
    print(json.dumps({"status": "error", "message": str(e)}))
