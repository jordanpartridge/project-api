#!/bin/bash

# Beast Monitoring Script
BEAST_IP="192.168.2.117"
LOG_FILE="/users/jordanpartridge/Sites/project-logs/beast-availability.log"
TIMESTAMP=$(date "+%Y-%m-%d %H:%M:%S")

# Ping test
ping -c 4 $BEAST_IP > /dev/null 2>&1

# Check ping result
if [ $? -eq 0 ]; then
    echo "$TIMESTAMP - Beast is UP" >> "$LOG_FILE"
else
    echo "$TIMESTAMP - Beast is DOWN - ALERT" >> "$LOG_FILE"
    # Optional: Send notification (e.g., email, slack message)
    # You could add a script to send alerts here
fi
