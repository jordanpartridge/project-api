import llama_cpp
from ctypes import *

def check_metal_support():
    try:
        if hasattr(llama_cpp, "llama_backend_engine"):
            print("Backend engine:", llama_cpp.llama_backend_engine())
            return True
        print("Metal support checking...")
        lib = llama_cpp._lib
        metal_supported = hasattr(lib, "ggml_metal_init")
        print("Metal support available:", metal_supported)
        return metal_supported
    except Exception as e:
        print("Error checking Metal support:", e)
        return False

print("\nChecking Metal GPU Support...")
metal_enabled = check_metal_support()
print("Configuration complete. Metal support status:", "✓" if metal_enabled else "✗")
