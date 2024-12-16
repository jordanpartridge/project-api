# Apple Silicon PrivateGPT Installation Guide

## Prerequisites

### System Requirements
- Apple M1/M2/M3 Mac
- macOS Ventura or newer recommended
- 16GB RAM minimum (32GB recommended)
- 20GB free disk space

### Required Tools
- Homebrew
- Python 3.11+
- Command Line Tools
- CMake
- Metal SDK (included with macOS)

## Step-by-Step Installation

### 1. Prepare Environment
```bash
# Install Homebrew if not present
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install Python 3.11 and CMake
brew install python@3.11 cmake

# Verify Command Line Tools
xcode-select --install
```

### 2. Clone Repository
```bash
git clone https://github.com/zylon-ai/private-gpt.git
cd private-gpt
```

### 3. Setup Python Environment
```bash
# Create virtual environment
python3.11 -m venv privategpt_env

# Activate environment
source privategpt_env/bin/activate

# Set architecture flags
export CMAKE_ARGS="-DLLAMA_METAL=on"
export ARCHFLAGS="-arch arm64"
export LLAMA_METAL=1
```

### 4. Install Dependencies
```bash
# Install llama-cpp-python with Metal support
pip install --no-cache-dir llama-cpp-python==0.1.77

# Install other requirements
pip install -r requirements.txt
```

### 5. Verify Installation
```python
# Run Python and test:
import llama_cpp
print(llama_cpp.__version__)
print(llama_cpp.llama_backend_engine())  # Should show "metal"
```

## Common Issues and Solutions

### 1. Architecture Mismatch
**Problem**: System reporting x86_64 instead of arm64
**Solution**: 
```bash
export ARCHFLAGS="-arch arm64"
pip install --no-cache-dir --force-reinstall llama-cpp-python
```

### 2. Metal Support Issues
**Problem**: GPU not detected
**Solution**:
1. Verify Metal support:
```bash
system_profiler SPDisplaysDataType | grep Metal
```
2. Force Metal compilation:
```bash
CMAKE_ARGS="-DLLAMA_METAL=on" pip install --force-reinstall --no-cache-dir llama-cpp-python
```

### 3. Compilation Errors
**Problem**: Missing C++ headers
**Solution**:
```bash
xcode-select --install
softwareupdate --all --install --force
```

## Performance Optimization

### 1. Memory Usage
```python
# In your configuration:
n_ctx = 2048  # Adjust based on available RAM
n_gpu_layers = 32  # Adjust based on performance
```

### 2. Batch Size Tuning
```python
# Recommended settings:
batch_size = 512
context_length = 2048
```

### 3. Metal Performance
- Enable GPU acceleration
- Monitor GPU usage with Activity Monitor
- Adjust `n_gpu_layers` based on performance

## Troubleshooting Guide

### 1. Verify Installation
```bash
# Check Python version
python --version

# Check pip packages
pip list | grep llama

# Verify Metal support
system_profiler SPDisplaysDataType
```

### 2. Common Error Messages
1. "vector file not found":
   - Reinstall Command Line Tools
   - Update CMake

2. "Metal framework not found":
   - Update macOS
   - Reinstall Xcode tools

3. "Segmentation fault":
   - Reduce batch size
   - Check memory usage

## Maintenance

### 1. Regular Updates
```bash
# Update dependencies
pip install --upgrade llama-cpp-python
pip install --upgrade -r requirements.txt
```

### 2. Clean Installation
```bash
# If needed, clean everything:
pip uninstall llama-cpp-python
rm -rf privategpt_env
# Then follow installation steps again
```

## Contact Support
- GitHub Issues: [PrivateGPT Issues](https://github.com/zylon-ai/private-gpt/issues)
- Documentation: [Official Docs](https://docs.privategpt.dev)

## Notes
- Keep system updated
- Monitor GPU performance
- Backup configurations
- Test with small documents first

## References
- [Official Documentation](https://docs.privategpt.dev)
- [llama-cpp-python](https://github.com/abetlen/llama-cpp-python)
- [Metal Performance Guide](https://developer.apple.com/metal/)