<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function download($pinCode)
    {
        \Log::info('FileController download called', ['pin_code' => $pinCode]);
        $file = File::where('pin_code', $pinCode)->first();
        if (!$file) {
            \Log::warning('File not found in controller', ['pin_code' => $pinCode]);
            return response()->json(['error' => 'File not found'], 404);
        }
        \Log::info('File found in controller', [
            'file_id' => $file->id,
            'compressed_path' => $file->compressed_path
        ]);
        if ($file->isExpired()) {
            \Log::warning('File expired in controller', ['file_id' => $file->id]);
            return response()->json(['error' => 'File has expired'], 410);
        }
        if ($file->isDownloadLimitReached()) {
            \Log::warning('Download limit reached in controller', ['file_id' => $file->id]);
            return response()->json(['error' => 'Download limit reached'], 429);
        }
        $file->increment('downloads');
        
        $filePath = Storage::disk('public')->path($file->compressed_path);
        \Log::info('File path', ['path' => $filePath, 'exists' => file_exists($filePath)]);
        
        // Check if it's a folder (ends with /)
        if (substr($file->compressed_path, -1) === '/') {
            // It's a folder, create ZIP on-the-fly
            $zipPath = $this->createZipFromFolder($file->compressed_path, $file->id);
            $fileName = $file->original_name;
            $mimeType = 'application/zip';
        } else {
            // It's a single file
            if (!file_exists($filePath)) {
                \Log::error('File not found on server', ['path' => $filePath]);
                return response()->json(['error' => 'File not found on server'], 404);
            }
            $zipPath = $filePath;
            $fileName = $file->original_name;
            $mimeType = mime_content_type($filePath);
        }
        
        \Log::info('Downloading file', [
            'file_id' => $file->id, 
            'original_name' => $fileName,
            'mime_type' => $mimeType
        ]);
        
        return response()->download($zipPath, $fileName, [
            'Content-Type' => $mimeType
        ]);
    }
    
    private function createZipFromFolder($folderPath, $fileId)
    {
        $zipPath = "temp/zip_{$fileId}_" . time() . ".zip";
        Storage::disk('public')->makeDirectory('temp');
        
        $zip = new \ZipArchive();
        $zipFileName = Storage::disk('public')->path($zipPath);
        
        if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            $folderFullPath = Storage::disk('public')->path($folderPath);
            $files = Storage::disk('public')->files($folderPath);
            
            foreach ($files as $file) {
                $filePath = Storage::disk('public')->path($file);
                $fileName = basename($file);
                $zip->addFile($filePath, $fileName);
            }
            
            $zip->close();
            
            \Log::info('ZIP created from folder', [
                'folder_path' => $folderPath,
                'zip_path' => $zipPath,
                'files_count' => count($files)
            ]);
            
            return $zipFileName;
        } else {
            \Log::error('Failed to create ZIP from folder', ['folder_path' => $folderPath]);
            throw new \Exception('Failed to create ZIP from folder');
        }
    }

    public function delete($id)
    {
        $file = File::findOrFail($id);

        // Delete physical file
        if (Storage::disk('public')->exists($file->compressed_path)) {
            Storage::disk('public')->delete($file->compressed_path);
        }

        // Delete file record
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }
}
