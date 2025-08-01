import './bootstrap';
import Alpine from 'alpinejs';
import * as fflate from 'fflate';

// Make fflate available globally
window.fflate = fflate;

// Start Alpine.js
Alpine.start();

// Enhanced animations and interactions
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Add loading states to buttons
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.disabled) {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            }
        });
    });
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.card-apple');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.01)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Add focus effects to inputs
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = '';
        });
    });
    
    // Parallax effect for background elements
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.absolute');
        
        parallaxElements.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
    
    // Add reveal animations on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe elements for reveal animations
    const revealElements = document.querySelectorAll('.card-apple, .animate-fade-in-up');
    revealElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        revealObserver.observe(element);
    });
});

// Enhanced file upload interactions
document.addEventListener('DOMContentLoaded', function() {
    const dropZones = document.querySelectorAll('[x-on:dragover]');
    
    dropZones.forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.transform = 'scale(1.02)';
            this.style.borderColor = '#3B82F6';
            this.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
        });
        
        zone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.transform = '';
            this.style.borderColor = '';
            this.style.backgroundColor = '';
        });
        
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.transform = '';
            this.style.borderColor = '';
            this.style.backgroundColor = '';
        });
    });
});

// Smooth loading states
window.addEventListener('load', function() {
    document.body.classList.add('loaded');
    
    // Add loading animation to the page
    const loader = document.createElement('div');
    loader.className = 'fixed inset-0 bg-white z-50 flex items-center justify-center';
    loader.innerHTML = `
        <div class="flex flex-col items-center space-y-4">
            <div class="w-12 h-12 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
            <p class="text-gray-600 font-light">Loading QuickDrop...</p>
        </div>
    `;
    
    document.body.appendChild(loader);
    
    setTimeout(() => {
        loader.style.opacity = '0';
        loader.style.transition = 'opacity 0.5s ease-out';
        setTimeout(() => {
            loader.remove();
        }, 500);
    }, 1000);
});
