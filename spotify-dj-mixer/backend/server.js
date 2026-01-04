const express = require('express');
const cors = require('cors');
const http = require('http');
const socketIo = require('socket.io');
const multer = require('multer');
const path = require('path');

const musicService = require('./services/musicService');

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
  cors: {
    origin: "*", // More permissive CORS for development
    methods: ["GET", "POST"]
  }
});

// Multer for file uploads
const upload = multer({ 
  dest: path.join(__dirname, 'music'),
  limits: { fileSize: 50 * 1024 * 1024 } // 50MB file size limit
});

// Middleware
app.use(cors({
  origin: '*', // Adjust in production
  methods: ['GET', 'POST', 'PUT', 'DELETE'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));
app.use(express.json());

// Serve static files for music
app.use('/music', express.static(path.join(__dirname, 'music')));

// Enhanced UI Routes
app.get('/', (req, res) => {
  res.json({
    message: 'Spotify DJ Mixer Backend',
    endpoints: {
      listMusic: '/api/music',
      uploadMusic: '/api/music/upload',
      mixTracks: '/api/music/mix',
      analyzeTrack: '/api/music/:filename/analyze'
    }
  });
});

// Music file routes with improved error handling
app.get('/api/music', (req, res) => {
  try {
    const musicFiles = musicService.listMusicFiles();
    res.json({
      total: musicFiles.length,
      files: musicFiles
    });
  } catch (error) {
    res.status(500).json({ 
      error: 'Failed to list music files',
      details: error.message 
    });
  }
});

app.get('/api/music/:filename/metadata', (req, res) => {
  try {
    const metadata = musicService.getMusicMetadata(req.params.filename);
    res.json(metadata);
  } catch (error) {
    res.status(500).json({ 
      error: 'Failed to get music metadata',
      details: error.message 
    });
  }
});

app.post('/api/music/upload', upload.single('music'), (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({ 
        error: 'No file uploaded',
        hint: 'Ensure file is sent with key "music"' 
      });
    }
    
    const filename = musicService.uploadMusicFile(req.file, req.file.originalname);
    res.json({ 
      message: 'File uploaded successfully',
      filename 
    });
  } catch (error) {
    res.status(500).json({ 
      error: 'File upload failed',
      details: error.message 
    });
  }
});

app.post('/api/music/mix', async (req, res) => {
  try {
    const { file1, file2, outputFilename } = req.body;
    
    if (!file1 || !file2) {
      return res.status(400).json({ 
        error: 'Missing input files',
        hint: 'Provide file1 and file2 for mixing' 
      });
    }
    
    const mixedFile = await musicService.mixAudioFiles(file1, file2, outputFilename);
    res.json({ 
      message: 'Files mixed successfully',
      mixedFile 
    });
  } catch (error) {
    res.status(500).json({ 
      error: 'Music mixing failed',
      details: error.message 
    });
  }
});

app.get('/api/music/:filename/analyze', async (req, res) => {
  try {
    const analysis = await musicService.analyzeTrack(req.params.filename);
    res.json({
      message: 'Track analyzed successfully',
      analysis
    });
  } catch (error) {
    res.status(500).json({ 
      error: 'Track analysis failed',
      details: error.message 
    });
  }
});

// Enhanced Socket.IO for real-time updates
io.on('connection', (socket) => {
  console.log('New DJ client connected');
  
  socket.on('start-mix', (mixDetails) => {
    console.log('Mix started:', mixDetails);
    io.emit('mix-started', {
      ...mixDetails,
      timestamp: new Date().toISOString()
    });
  });
  
  socket.on('disconnect', () => {
    console.log('DJ client disconnected');
  });
});

// Dynamic port configuration
const PORT = process.env.PORT || 5001;
server.listen(PORT, '0.0.0.0', () => {
  console.log(`Server running on port ${PORT}`);
});

module.exports = { app, server, io };
