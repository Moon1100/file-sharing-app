<?php

namespace App\Livewire;

use App\Models\File;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class RetrieveComponent extends Component
{
    public $pinCode = '';
    public $file = null;
    public $error = '';
    public $isRetrieved = false;

    public function updatedPinCode()
    {
        // Filter to only allow numbers and limit to 6 digits
        $this->pinCode = preg_replace('/[^0-9]/', '', $this->pinCode);
        $this->pinCode = substr($this->pinCode, 0, 6);
        
        \Log::info('PIN code updated', ['pin_code' => $this->pinCode]);
    }

    public function retrieve()
    {
        \Log::info('Retrieve method called', ['pin_code' => $this->pinCode]);
        
        $this->validate([
            'pinCode' => 'required|string|size:6',
        ]);

        try {
            $file = File::where('pin_code', $this->pinCode)->first();

            if (!$file) {
                \Log::warning('File not found for PIN', ['pin_code' => $this->pinCode]);
                $this->error = 'Invalid PIN code. Please check and try again.';
                return;
            }

            \Log::info('File found', [
                'file_id' => $file->id,
                'pin_code' => $file->pin_code,
                'expires_at' => $file->expires_at,
                'downloads' => $file->downloads,
                'max_downloads' => $file->max_downloads
            ]);

            if ($file->isExpired()) {
                \Log::warning('File expired', ['file_id' => $file->id]);
                $this->error = 'This file has expired and is no longer available.';
                return;
            }

            if ($file->isDownloadLimitReached()) {
                \Log::warning('Download limit reached', ['file_id' => $file->id]);
                $this->error = 'Download limit reached for this file.';
                return;
            }

            $this->file = $file;
            $this->isRetrieved = true;
            $this->error = '';

            \Log::info('File retrieved successfully', ['file_id' => $file->id]);

        } catch (\Exception $e) {
            \Log::error('Retrieve error', ['error' => $e->getMessage()]);
            $this->error = 'An error occurred while retrieving the file.';
        }
    }

    public function download()
    {
        \Log::info('Download method called', ['file_id' => $this->file->id ?? 'null']);
        
        if (!$this->file) {
            \Log::warning('No file to download');
            return;
        }

        // Increment download count
        $this->file->increment('downloads');

        \Log::info('Redirecting to download', ['pin_code' => $this->file->pin_code]);

        // Redirect to download route
        return redirect()->route('file.download', ['pinCode' => $this->file->pin_code]);
    }

    public function resetRetrieval()
    {
        $this->pinCode = '';
        $this->file = null;
        $this->error = '';
        $this->isRetrieved = false;
    }

    public function render()
    {
        return view('livewire.retrieve-component');
    }
}
