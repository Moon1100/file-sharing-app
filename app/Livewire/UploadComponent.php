<?php

namespace App\Livewire;

use App\Models\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UploadComponent extends Component
{
    use WithFileUploads;

    public $files = [];
    public $uploadedFile = null;
    public $pinCode = '';
    public $remainingTime = 0;
    public $downloadCount = 0;
    public $maxDownloads = 2;
    public $isUploaded = false;
    public $uploaderToken = '';

    protected $listeners = ['updateCountdown'];

    public function mount()
    {
        $this->uploaderToken = Str::random(32);
    }

    public function updatedFiles()
    {
        // Add some debugging
        \Log::info('Files updated:', ['count' => count($this->files)]);
        
        if (!empty($this->files)) {
            \Log::info('Files received:', [
                'count' => count($this->files),
                'first_file_name' => $this->files[0]->getClientOriginalName() ?? 'No name',
                'first_file_size' => $this->files[0]->getSize() ?? 'No size'
            ]);
        }
    }

    public function testFileUpload()
    {
        \Log::info('Test upload called', ['files_count' => count($this->files)]);
        
        if (!empty($this->files)) {
            foreach ($this->files as $index => $file) {
                \Log::info("File {$index}:", [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ]);
            }
        }
    }

    public function testButton()
    {
        \Log::info('Test button clicked!');
        session()->flash('message', 'Test button works!');
    }

    public function doUpload()
    {
        \Log::info('doUpload method called');
        $this->upload();
    }

    public function upload()
    {
        \Log::info('Upload method called');
        
        // Check if files are selected
        if (empty($this->files)) {
            \Log::warning('No files selected');
            session()->flash('error', 'Please select files to upload');
            return;
        }

        \Log::info('Files found:', ['count' => count($this->files)]);

        // Validate files
        try {
            $this->validate([
                'files' => 'required|array|min:1',
                'files.*' => 'required|file|max:10240', // 10MB max per file
            ]);
            \Log::info('Files validated successfully');
        } catch (\Exception $e) {
            \Log::error('Validation failed:', ['error' => $e->getMessage()]);
            session()->flash('error', 'File validation failed: ' . $e->getMessage());
            return;
        }

        try {
            // Generate unique 6-digit PIN
            do {
                $this->pinCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (File::where('pin_code', $this->pinCode)->exists());
            
            \Log::info('PIN generated:', ['pin' => $this->pinCode]);
            
            // Set expiration time (3 minutes from now)
            $expiresAt = Carbon::now()->addMinutes(3);

            // Create a descriptive filename
            $fileCount = count($this->files);
            $originalName = $fileCount === 1 
                ? $this->files[0]->getClientOriginalName()
                : "quickdrop_files_{$fileCount}_files.zip";
            
            // Create file record
            $file = File::create([
                'uploader_token' => $this->uploaderToken,
                'original_name' => $originalName,
                'compressed_path' => 'temp', // Will be updated after compression
                'pin_code' => $this->pinCode,
                'downloads' => 0,
                'max_downloads' => $this->maxDownloads,
                'expires_at' => $expiresAt,
                'is_premium' => false,
            ]);

            \Log::info('File record created:', ['file_id' => $file->id]);

            // Store files and compress them
            $compressedPath = $this->compressAndStoreFiles($file->id);
            
            \Log::info('Files compressed and stored:', ['path' => $compressedPath]);
            
            // Update file record with actual path
            $file->update(['compressed_path' => $compressedPath]);

            $this->isUploaded = true;
            $this->remainingTime = $expiresAt->diffInSeconds(now());
            
            // Log success
            \Log::info('Upload successful:', [
                'pin_code' => $this->pinCode,
                'file_id' => $file->id,
                'compressed_path' => $compressedPath
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    private function compressAndStoreFiles($fileId)
    {
        if (!empty($this->files)) {
            $fileCount = count($this->files);
            
            if ($fileCount === 1) {
                // For single files, store directly without compression
                $file = $this->files[0];
                $originalName = $file->getClientOriginalName();
                $path = $file->storeAs("files/{$fileId}", $originalName, 'public');
                
                \Log::info('Single file stored directly', [
                    'file_path' => $path,
                    'file_name' => $originalName,
                    'file_size' => $file->getSize()
                ]);
                
                return $path;
            } else {
                // For multiple files, store them in a folder
                $folderPath = "files/{$fileId}/";
                Storage::disk('public')->makeDirectory($folderPath);
                
                $storedFiles = [];
                foreach ($this->files as $index => $file) {
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->storeAs($folderPath, $fileName, 'public');
                    $storedFiles[] = $fileName;
                }
                
                \Log::info('Multiple files stored in folder', [
                    'folder_path' => $folderPath,
                    'files_count' => count($this->files),
                    'file_names' => $storedFiles
                ]);
                
                return $folderPath;
            }
        }
        
        return "files/{$fileId}/file";
    }

    public function updateCountdown()
    {
        if ($this->isUploaded && $this->remainingTime > 0) {
            $this->remainingTime--;
            
            if ($this->remainingTime <= 0) {
                $this->isUploaded = false;
                session()->flash('warning', 'File has expired!');
            }
        }
    }

    public function upgradeRetention()
    {
        $this->dispatch('openUpgradeModal', fileId: $this->uploadedFile->id);
    }

    public function render()
    {
        return view('livewire.upload-component');
    }
}
