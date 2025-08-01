<?php

namespace App\Jobs;

use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupExpiredFiles implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find expired files
        $expiredFiles = File::where('expires_at', '<', Carbon::now())->get();

        foreach ($expiredFiles as $file) {
            // Delete physical file
            if (Storage::disk('public')->exists($file->compressed_path)) {
                Storage::disk('public')->delete($file->compressed_path);
            }

            // Delete file record
            $file->delete();
        }

        // Find files that have reached download limit
        $maxedFiles = File::whereRaw('downloads >= max_downloads')->get();

        foreach ($maxedFiles as $file) {
            // Delete physical file
            if (Storage::disk('public')->exists($file->compressed_path)) {
                Storage::disk('public')->delete($file->compressed_path);
            }

            // Delete file record
            $file->delete();
        }
    }
}
