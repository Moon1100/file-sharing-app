<div>
    @if (!$isRetrieved)
        <div class="space-y-6">
            <div class="space-y-4">
                <label for="pin-code" class="block text-sm font-medium text-gray-700 mb-2">
                    Enter 6-digit PIN
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="pin-code"
                        wire:model.live="pinCode"
                        maxlength="6"
                        class="w-full px-6 py-4 border border-gray-200 rounded-2xl shadow-sm focus-ring font-mono text-2xl text-center bg-white transition-all-smooth focus:border-blue-500 focus:shadow-lg"
                        placeholder="000000"
                    >
                    <div class="absolute inset-y-0 right-4 flex items-center">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                </div>
                <!-- Debug info -->
                @if(config('app.debug'))
                    <div class="text-xs text-gray-500">
                        PIN entered: "{{ $pinCode }}"
                    </div>
                @endif
            </div>

            @if ($error)
                <div class="animate-fade-in-up">
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-red-900 mb-1">Error</h3>
                                <p class="text-sm text-red-800">{{ $error }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Retrieve Button - Made more prominent -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl p-6 shadow-lg">
                <h3 class="text-white font-semibold text-lg mb-2">Ready to Retrieve?</h3>
                <p class="text-green-100 text-sm mb-4">Enter the PIN code above and click the button below to retrieve your files</p>
                <button 
                    wire:click="retrieve"
                    wire:loading.attr="disabled"
                    class="w-full bg-white text-green-600 px-8 py-4 rounded-xl font-bold text-lg transition-all-smooth hover:bg-green-50 focus-ring active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                >
                    <span wire:loading.remove class="flex items-center justify-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Retrieve Files
                    </span>
                    <span wire:loading class="flex items-center justify-center">
                        <div class="w-6 h-6 mr-3 border-2 border-green-600 border-t-transparent rounded-full animate-spin"></div>
                        Retrieving...
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
                        <h3 class="text-lg font-medium text-green-900 mb-3">File Found!</h3>
                        <div class="space-y-2 text-sm text-green-800">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">File:</span>
                                <span class="font-medium">{{ $file->original_name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Downloads:</span>
                                <span>{{ $file->downloads }}/{{ $file->max_downloads }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Expires:</span>
                                <span class="text-xs">{{ $file->expires_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button 
                    wire:click="download"
                    class="w-full bg-green-600 text-white px-6 py-3 rounded-full font-medium transition-all-smooth hover:bg-green-700 focus-ring active:scale-95"
                >
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Files
                    </span>
                </button>
                
                <button 
                    wire:click="resetRetrieval"
                    class="w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-full font-medium transition-all-smooth hover:bg-gray-200 focus-ring active:scale-95"
                >
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Retrieve Another File
                    </span>
                </button>
            </div>
        </div>
    @endif
</div>
