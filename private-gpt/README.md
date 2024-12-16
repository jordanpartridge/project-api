# PrivateGPT Installation Guide

## Current Issues and Resolution Plan

### Environment Analysis
- **Project Location**: `/users/jordanpartridge/sites/project-api/private-gpt`
- **Current Python Environments**: Multiple conflicting environments (3.9 and 3.11)
- **Architecture**: M1/M2 Mac (ARM64) but system reporting as x86_64
- **Build System**: CMake with Metal support

### Identified Problems
1. **Python Environment Conflicts**
   - Multiple virtual environments present
   - Python version mismatches (3.9 vs 3.11)
   - Dependency conflicts between environments

2. **Architecture Mismatch**
   - System architecture reported as x86_64
   - Actual architecture is ARM64 (Apple Silicon)
   - Causing compilation and optimization issues

3. **llama-cpp-python Build Failures**
   - Missing C++ standard library headers
   - Architecture-specific compilation errors
   - CMake configuration issues with Metal support

4. **Metal GPU Support**
   - Requires specific compilation flags
   - Architecture detection issues
   - Performance optimization needed

### Research Findings

#### Known Issues
1. Metal GPU Support Issues
   - [Issue #756](https://github.com/abetlen/llama-cpp-python/issues/756) - GPU detection problems
   - [Issue #1349](https://github.com/zylon-ai/private-gpt/issues/1349) - API server issues on M1
   - Metal support requires specific environment setup

2. Architecture Detection
   - Apple Silicon requires explicit architecture flags
   - Performance impact when architecture is misdetected
   - Documented in [llama.cpp discussions](https://github.com/ggerganov/llama.cpp/discussions/4167)

3. Installation Solutions
   - [Official Metal GPU Documentation](https://llama-cpp-python.readthedocs.io/en/latest/install/macos/)
   - [Community M1/M2 Setup Guide](https://gist.github.com/Manoz/7113472524b611e9a5da714522b701ae)
   - [Performance Optimization Guide](https://github.com/ggerganov/llama.cpp/discussions/4167)

### Resolution Plan

1. **Environment Setup**
   ```bash
   # Remove existing virtual environments
   rm -rf private-gpt/venv privategpt_env
   
   # Create new environment with Python 3.11
   python3.11 -m venv privategpt_env
   ```

2. **Architecture and Metal Configuration**
   ```bash
   # Set correct architecture and Metal flags
   export CMAKE_ARGS="-DLLAMA_METAL=on"
   export ARCHFLAGS="-arch arm64"
   export LLAMA_METAL=1
   ```

3. **Dependencies Installation**
   ```bash
   # Activate new environment
   source privategpt_env/bin/activate
   
   # Install llama-cpp-python with Metal support
   pip install --no-cache-dir llama-cpp-python==0.1.77
   
   # Install other requirements
   pip install -r requirements.txt
   ```

### Required System Dependencies
- Python 3.11+
- CMake (latest version)
- Apple Clang (Command Line Tools)
- Metal SDK

### Performance Optimization
1. Metal GPU Usage
   - Verify Metal support is active
   - Monitor GPU utilization
   - Adjust n_gpu_layers parameter

2. Memory Management
   - Configure appropriate context size
   - Monitor RAM usage
   - Adjust batch size if needed

### Troubleshooting Guide
1. Metal Support Verification
   ```python
   import llama_cpp
   print(llama_cpp.__version__)
   print(llama_cpp.llama_backend_engine())  # Should show "metal"
   ```

2. Architecture Verification
   ```bash
   # Should show arm64
   uname -m
   ```

### References and Resources
1. Official Documentation:
   - [PrivateGPT Documentation](https://docs.privategpt.dev)
   - [llama-cpp-python macOS Guide](https://llama-cpp-python.readthedocs.io/en/latest/install/macos/)

2. Community Resources:
   - [M1/M2 Setup Guide](https://gist.github.com/Manoz/7113472524b611e9a5da714522b701ae)
   - [Metal Performance Guide](https://github.com/ggerganov/llama.cpp/discussions/4167)

3. Issue Trackers:
   - [llama-cpp-python Issues](https://github.com/abetlen/llama-cpp-python/issues)
   - [PrivateGPT Issues](https://github.com/zylon-ai/private-gpt/issues)

4. Additional Resources:
   - [Apple Silicon Transition Guide](https://support.apple.com/en-us/116943)
   - [Metal Performance Shaders Documentation](https://developer.apple.com/metal/Metal-Shading-Language-Specification.pdf)

## Contributing
Document any changes or improvements to this setup process by updating this README.

## License
This documentation is provided under the same license as PrivateGPT.

## Last Updated
December 15, 2024