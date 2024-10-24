-- Create database
CREATE DATABASE IF NOT EXISTS weather_monitoring;
USE weather_monitoring;

-- Table to store real-time weather data for different cities
CREATE TABLE IF NOT EXISTS weather_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(100) NOT NULL,
    main_condition VARCHAR(50),         -- Main weather condition (e.g., Rain, Clear)
    temp FLOAT,                         -- Current temperature in Celsius
    feels_like FLOAT,                   -- Perceived temperature in Celsius
    dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Timestamp of the weather update
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp when the data was added to the database
);

-- Table to store daily summaries (aggregated data)
CREATE TABLE IF NOT EXISTS daily_weather_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    summary_date DATE NOT NULL,            -- Date of the summary
    avg_temp FLOAT,                        -- Average temperature for the day
    max_temp FLOAT,                        -- Maximum temperature for the day
    min_temp FLOAT,                        -- Minimum temperature for the day
    dominant_condition VARCHAR(50),        -- Dominant weather condition (most frequent during the day)
    city_name VARCHAR(100) NOT NULL,       -- City name
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp when the summary was created
);

-- Indexes for performance optimization
CREATE INDEX idx_city_name ON weather_data (city_name);
CREATE INDEX idx_summary_date ON daily_weather_summary (summary_date);
