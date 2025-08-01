<div>
    @if (!$isUploaded)
        <div class="space-y-6">
            <!-- Debug information -->
            @if(config('app.debug'))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm">
                    <p><strong>Debug Info:</strong></p>
                    <p>Files count: {{ count($files) }}</p>
                    @if(!empty($files))
                        <p>First file: {{ $files[0]->getClientOriginalName() }}</p>
                        <p>File size: {{ number_format($files[0]->getSize() / 1024, 1) }} KB</p>
                    @endif
                    <div class="space-y-2 mt-2">
                        <button wire:click="testFileUpload" class="px-3 py-1 bg-blue-500 text-white rounded text-xs">
                            Test File Upload
                        </button>
                        <button wire:click="testButton" class="px-3 py-1 bg-green-500 text-white rounded text-xs">
                            Test Button
                        </button>
                    </div>
                </div>
            @endif

            <!-- Simple file input for testing -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-medium text-blue-900 mb-2">Simple File Upload Test</h3>
                <input 
                    type="file" 
                    wire:model="files" 
                    multiple 
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                >
                <p class="text-xs text-blue-600 mt-1">This is a simple test to see if file upload works</p>
            </div>

            <div class="border-2 border-dashed rounded-2xl p-8 text-center transition-all-smooth min-h-[200px] flex items-center justify-center border-gray-200 hover:border-gray-300">
                <div class="space-y-4">
                    <div class="relative">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="text-gray-600">
                            <label for="file-upload" class="cursor-pointer font-medium text-blue-600 hover:text-blue-500 transition-colors focus-ring">
                                <span>Choose files</span>
                                <input 
                                    id="file-upload" 
                                    wire:model="files"
                                    type="file" 
                                    multiple 
                                    class="sr-only"
                                    accept="*/*"
                                >
                            </label>
                            <span class="text-gray-500"> or drag and drop</span>
                        </div>
                        <p class="text-xs text-gray-400">Up to 10MB per file</p>
                    </div>
                </div>
            </div>

            @if (!empty($files))
                <div class="animate-fade-in-up">
                    <div class="bg-gray-50 rounded-2xl p-6">
                        <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Selected Files ({{ count($files) }})
                        </h4>
                        <div class="space-y-2">
                            @foreach ($files as $index => $file)
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-700 font-medium">{{ $file->getClientOriginalName() }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ number_format($file->getSize() / 1024, 1) }} KB</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <!-- Show when no files are selected -->
                <div class="text-center text-gray-500 text-sm">
                    No files selected yet
                </div>
            @endif

            <!-- Upload Button - Made more prominent -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 shadow-lg">
                <h3 class="text-white font-semibold text-lg mb-2">Ready to Upload?</h3>
                <p class="text-blue-100 text-sm mb-4">Click the button below to upload your files and get a PIN code</p>
                <button 
                    wire:click="doUpload"
                    wire:loading.attr="disabled"
                    onclick="console.log('Upload button clicked'); try { console.log('Button click successful'); } catch(e) { console.error('Button click error:', e); }"
                    class="w-full bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-lg transition-all-smooth hover:bg-blue-50 focus-ring active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                >
                    <span wire:loading.remove class="flex items-center justify-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload Files & Get PIN
                    </span>
                    <span wire:loading class="flex items-center justify-center">
                        <div class="w-6 h-6 mr-3 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                        Uploading...
                    </span>
                </button>
            </div>
        </div>
    @else
        <div class="space-y-6 animate-fade-in-up">
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-green-900 mb-3">Upload Successful!</h3>
                        <div class="space-y-2 text-sm text-green-800">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">PIN Code:</span>
                                <span class="font-mono text-lg bg-white px-3 py-1 rounded-lg border">{{ $pinCode }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Downloads:</span>
                                <span>{{ $downloadCount }}/{{ $maxDownloads }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Time Remaining:</span>
                                <span x-data="{ time: {{ $remainingTime }} }" x-text="Math.floor(time / 60) + ':' + (time % 60).toString().padStart(2, '0')" class="font-mono"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button 
                wire:click="upgradeRetention"
                class="w-full bg-amber-600 text-white px-6 py-3 rounded-full font-medium transition-all-smooth hover:bg-amber-700 focus-ring active:scale-95"
            >
                <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Extend Retention
                </span>
            </button>
        </div>
    @endif

    <script>
        // Debug Livewire
        console.log('Upload component loaded');
        
        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            console.log('Livewire initialized');
        });
        
        // Listen for errors
        document.addEventListener('livewire:error', (event) => {
            console.error('Livewire error:', event.detail);
        });
        
        // Countdown timer with smooth updates
        if (document.querySelector('[x-data]')) {
            Alpine.data('countdown', () => ({
                init() {
                    this.$watch('time', (value) => {
                        if (value <= 0) {
                            @this.call('updateCountdown');
                        }
                    });
                    
                    setInterval(() => {
                        if (this.time > 0) {
                            this.time--;
                        }
                    }, 1000);
                }
            }));
        }
    </script>
</div>
