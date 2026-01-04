const fs = require('fs');
const path = require('path');
const ffmpeg = require('fluent-ffmpeg');
const NodeID3 = require('node-id3');

class MusicService {
  constructor() {
    this.musicDirectory = path.join(__dirname, '..', 'music');
    this.ensureMusicDirectory();
  }

  ensureMusicDirectory() {
    if (!fs.existsSync(this.musicDirectory)) {
      fs.mkdirSync(this.musicDirectory);
    }
  }

  // List available music files
  listMusicFiles() {
    return fs.readdirSync(this.musicDirectory)
      .filter(file => 
        ['.mp3', '.wav', '.flac'].includes(path.extname(file).toLowerCase())
      );
  }

  // Get music file metadata
  getMusicMetadata(filename) {
    const filepath = path.join(this.musicDirectory, filename);
    try {
      const tags = NodeID3.read(filepath);
      return {
        filename,
        title: tags.title || filename,
        artist: tags.artist || 'Unknown Artist',
        album: tags.album || 'Unknown Album',
        duration: this.getMusicDuration(filepath)
      };
    } catch (error) {
      console.error(`Error reading metadata for ${filename}:`, error);
      return { filename };
    }
  }

  // Get music file duration
  getMusicDuration(filepath) {
    return new Promise((resolve, reject) => {
      ffmpeg.ffprobe(filepath, (err, metadata) => {
        if (err) {
          reject(err);
        } else {
          resolve(metadata.format.duration);
        }
      });
    });
  }

  // Mix two audio files
  mixAudioFiles(file1, file2, outputFilename) {
    return new Promise((resolve, reject) => {
      const input1 = path.join(this.musicDirectory, file1);
      const input2 = path.join(this.musicDirectory, file2);
      const output = path.join(this.musicDirectory, outputFilename);

      ffmpeg()
        .input(input1)
        .input(input2)
        .complexFilter([
          // Basic crossfade effect
          '[0:a][1:a]acrossfade=d=5'
        ])
        .output(output)
        .on('end', () => resolve(outputFilename))
        .on('error', (err) => reject(err))
        .run();
    });
  }

  // Analyze BPM and key of a track
  analyzeTrack(filename) {
    return new Promise((resolve, reject) => {
      const filepath = path.join(this.musicDirectory, filename);
      
      // Placeholder for advanced audio analysis
      ffmpeg.ffprobe(filepath, (err, metadata) => {
        if (err) {
          reject(err);
        } else {
          resolve({
            filename,
            bpm: Math.floor(Math.random() * 200), // Placeholder BPM
            key: ['C', 'D', 'E', 'F', 'G', 'A', 'B'][Math.floor(Math.random() * 7)],
            duration: metadata.format.duration
          });
        }
      });
    });
  }

  // Upload music file
  uploadMusicFile(file, originalFilename) {
    const targetPath = path.join(this.musicDirectory, originalFilename);
    
    // Move uploaded file to music directory
    fs.copyFileSync(file.path, targetPath);
    
    return originalFilename;
  }
}

module.exports = new MusicService();
