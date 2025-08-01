@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 relative overflow-hidden">
    <!-- Background decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-50 rounded-full opacity-50 animate-pulse-slow"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-50 rounded-full opacity-30 animate-pulse-slow" style="animation-delay: 1s;"></div>
    </div>

    <!-- Header Section -->
    <div class="relative z-10 pt-16 pb-8 px-6">
        <div class="max-w-4xl mx-auto text-center animate-fade-in-up" style="animation-delay: 0.2s;">
            <h1 class="text-5xl md:text-6xl font-light text-gray-900 mb-6 tracking-tight">
                QuickDrop
            </h1>
            <p class="text-xl text-gray-600 font-light max-w-2xl mx-auto leading-relaxed">
                Share files instantly with a simple, secure, and elegant experience
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 px-6 pb-16">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                
            <!-- Upload Section -->
                <div class="animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div class="card-apple p-8 h-full">
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 animate-scale-in" style="animation-delay: 0.6s;">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-medium text-gray-900 mb-2">Upload Files</h2>
                            <p class="text-gray-500 text-sm">Drag & drop or select files to securely upload</p>
                        </div>
                        
                        <div class="animate-slide-in-bottom" style="animation-delay: 0.8s;">
                        @livewire('upload-component')
                        </div>
                    </div>
                </div>

                <!-- Retrieve Section -->
                <div class="animate-fade-in-up" style="animation-delay: 0.6s;">
                    <div class="card-apple p-8 h-full">
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-4 animate-scale-in" style="animation-delay: 0.8s;">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-medium text-gray-900 mb-2">Retrieve Files</h2>
                            <p class="text-gray-500 text-sm">Enter your code to retrieve your shared files</p>
            </div>

                        <div class="animate-slide-in-bottom" style="animation-delay: 1s;">
                        @livewire('retrieve-component')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="relative z-10 text-center py-8 animate-fade-in" style="animation-delay: 1.2s;">
        <p class="text-gray-400 text-sm font-light">
            © {{ date('Y') }} QuickDrop. Designed with simplicity in mind.
        </p>
    </div>
</div>

<script>
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all cards for scroll animations
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.card-apple');
        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(card);
        });
    });
</script>
@endsection
