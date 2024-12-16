#!/bin/bash

# Colors for pretty output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Starting PrivateGPT Installation for Apple Silicon...${NC}"

# Function to check command existence
check_command() {
    if ! command -v $1 &> /dev/null; then
        echo -e "${RED}❌ $1 is not installed. Installing...${NC}"
        return 1
    else
        echo -e "${GREEN}✓ $1 is installed${NC}"
        return 0
fi
}

# Check and install Homebrew if needed
if ! check_command "brew"; then
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
fi

# Install required packages
echo -e "${BLUE}📦 Installing required packages...${NC}"
brew install cmake python@3.11 git

# Set up Python environment variables
export PYTHON_CONFIGURE_OPTS="--enable-framework"
export PYTHON_BUILD_FLAGS="-j4"

# Set up environment variables for Metal support
export CMAKE_ARGS="-DLLAMA_METAL=ON"
export LLAMA_METAL=1
export FORCE_CMAKE=1

# Create Python virtual environment
echo -e "${BLUE}🐍 Setting up Python virtual environment...${NC}"
python3.11 -m venv privategpt_env
source privategpt_env/bin/activate

# Upgrade pip
echo -e "${BLUE}⬆️  Upgrading pip...${NC}"
pip install --upgrade pip

# Install LLAMA-cpp with Metal support
echo -e "${BLUE}🔧 Installing LLAMA-cpp with Metal support...${NC}"
CMAKE_ARGS="-DLLAMA_METAL=ON" pip install --force-reinstall llama-cpp-python==0.2.20

# Clone and install PrivateGPT requirements
echo -e "${BLUE}📥 Installing PrivateGPT requirements...${NC}"
pip install -r requirements.txt

# Verify GPU Support
echo -e "${BLUE}🖥  Verifying GPU Support...${NC}"
python3 verify_gpu.py

# Create configuration file
echo -e "${BLUE}⚙️  Setting up configuration...${NC}"
cat > .env << EOL
CUSTOM_MODEL_PATH=models
MODEL_TYPE=llama2
MODEL_PATH="models/llama-2-7b-chat.Q4_K_M.gguf"
EMBEDDING_MODEL_PATH="models/all-MiniLM-L6-v2"
EOL

echo -e "${GREEN}✨ Installation complete! A few notes:${NC}"
echo -e "1. Download your preferred GGUF model to the models directory"
echo -e "2. Adjust the .env file if using a different model"
echo -e "3. Run 'source privategpt_env/bin/activate' to activate the environment"
echo -e "4. Use 'python -m private_gpt' to start the server"

# Verify all installations
echo -e "${BLUE}🔍 Verifying installations...${NC}"
python3 -c "import llama_cpp; print(f'LLAMA-cpp version: {llama_cpp.__version__}')"

echo -e "${GREEN}🎉 Setup complete! Ready to roll!${NC}"