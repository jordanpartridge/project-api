FROM node:16-alpine

# Install system dependencies for node-gyp and audio processing
RUN apk add --no-cache \
    python3 \
    make \
    g++ \
    ffmpeg

# Create app directory
WORKDIR /usr/src/app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the application
COPY . .

# Expose port for backend
EXPOSE 5000

# Start the server
CMD ["npm", "run", "dev"]
