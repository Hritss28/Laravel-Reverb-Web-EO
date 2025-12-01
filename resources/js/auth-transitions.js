// Auth Page Transitions and Animations
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all animations and transitions
    initializePageAnimations();
    initializeFormInteractions();
    initializeTransitions();
    initializeInteractiveEffects();
    addTransitionOverlay();
});

function addTransitionOverlay() {
    // Create a reusable transition overlay
    const overlay = document.createElement('div');
    overlay.className = 'transition-loading-overlay';
    overlay.innerHTML = `
        <div class="transition-loading-content">
            <div class="mb-6">
                <div class="inline-block animate-spin rounded-full h-16 w-16 border-4 border-white border-t-transparent"></div>
            </div>
            <h3 class="text-2xl font-semibold mb-2">Memuat Halaman</h3>
            <p class="text-white/80 pulse-loading">Mohon tunggu sebentar...</p>
        </div>
    `;
    document.body.appendChild(overlay);
}

function initializePageAnimations() {
    // Enhanced staggered entrance animations
    const elementsToAnimate = [
        { selector: '.mx-auto.h-16.w-16', delay: 0, animation: 'animate-in' },
        { selector: 'h2.mt-6', delay: 200, animation: 'animate-in' },
        { selector: 'p.mt-2', delay: 400, animation: 'animate-in' },
        { selector: '.mt-8 form', delay: 600, animation: 'form-section-slide-in' },
        { selector: '.hidden.lg\\:block', delay: 300, animation: 'animate-in' }
    ];

    elementsToAnimate.forEach(({ selector, delay, animation }) => {
        const element = document.querySelector(selector);
        if (element) {
            element.style.opacity = '0';
            setTimeout(() => {
                element.classList.add(animation);
                element.style.opacity = '1';
            }, delay);
        }
    });

    // Staggered form elements animation
    const formElements = document.querySelectorAll('form > div');
    formElements.forEach((element, index) => {
        element.classList.add('form-element-stagger');
        setTimeout(() => {
            element.classList.add('animate');
        }, 800 + (index * 150));
    });
}

function initializeFormInteractions() {
    // Enhanced input animations
    const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
    
    inputs.forEach(input => {
        // Focus animations
        input.addEventListener('focus', function() {
            this.closest('.input-group').classList.add('focused');
            const icon = this.parentElement.querySelector('svg');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.color = 'rgb(99, 102, 241)'; // indigo-500
            }
        });

        // Blur animations
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.closest('.input-group').classList.remove('focused');
                const icon = this.parentElement.querySelector('svg');
                if (icon) {
                    icon.style.transform = 'scale(1)';
                    icon.style.color = 'rgb(156, 163, 175)'; // gray-400
                }
            }
        });

        // Typing animations
        input.addEventListener('input', function() {
            const icon = this.parentElement.querySelector('svg');
            if (icon && this.value) {
                icon.style.color = 'rgb(34, 197, 94)'; // green-500
            }
        });
    });

    // Password strength indicator
    const passwordInput = document.querySelector('input[name="password"]');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            showPasswordStrength(this.value);
        });
    }

    // Button animations
    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            createRippleEffect(e, this);
        });
    }
}

function initializeTransitions() {
    // Enhanced smooth transitions between login and register
    const authLinks = document.querySelectorAll('a[href*="login"], a[href*="register"]');
    
    authLinks.forEach(link => {
        // Add auth nav link styling
        link.classList.add('auth-nav-link');
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetUrl = this.href;
            const currentPage = window.location.pathname;
            
            // Only animate if switching between auth pages
            if ((currentPage.includes('login') && targetUrl.includes('register')) ||
                (currentPage.includes('register') && targetUrl.includes('login'))) {
                
                animatePageTransition(targetUrl);
            } else {
                window.location.href = targetUrl;
            }
        });
    });
}

function animatePageTransition(targetUrl) {
    const overlay = document.querySelector('.transition-loading-overlay');
    const mainContainer = document.querySelector('.min-h-full');
    
    // Update loading text based on destination
    const loadingText = overlay.querySelector('p');
    if (targetUrl.includes('login')) {
        loadingText.textContent = 'Menuju halaman masuk...';
    } else if (targetUrl.includes('register')) {
        loadingText.textContent = 'Menuju halaman daftar...';
    }
    
    // Start exit animation for current page
    if (mainContainer) {
        mainContainer.classList.add('page-transition-exit');
    }
    
    // Show overlay after a brief delay
    setTimeout(() => {
        overlay.classList.add('active');
    }, 200);
    
    // Navigate to new page
    setTimeout(() => {
        window.location.href = targetUrl;
    }, 800);
}

function createRippleEffect(event, button) {
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        pointer-events: none;
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
    `;
    
    button.style.position = 'relative';
    button.style.overflow = 'hidden';
    button.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

function showPasswordStrength(password) {
    let strength = 0;
    let feedback = [];
    
    // Check length
    if (password.length >= 8) strength++;
    else feedback.push('Minimal 8 karakter');
    
    // Check lowercase
    if (/[a-z]/.test(password)) strength++;
    else feedback.push('Huruf kecil');
    
    // Check uppercase
    if (/[A-Z]/.test(password)) strength++;
    else feedback.push('Huruf besar');
    
    // Check numbers
    if (/[0-9]/.test(password)) strength++;
    else feedback.push('Angka');
    
    // Check special characters
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    else feedback.push('Karakter khusus');
    
    updatePasswordStrengthUI(strength, feedback);
}

function updatePasswordStrengthUI(strength, feedback) {
    let strengthText = '';
    let strengthColor = '';
    let strengthWidth = '';
    
    switch (strength) {
        case 0:
        case 1:
            strengthText = 'Sangat Lemah';
            strengthColor = 'bg-red-500';
            strengthWidth = '20%';
            break;
        case 2:
            strengthText = 'Lemah';
            strengthColor = 'bg-orange-500';
            strengthWidth = '40%';
            break;
        case 3:
            strengthText = 'Sedang';
            strengthColor = 'bg-yellow-500';
            strengthWidth = '60%';
            break;
        case 4:
            strengthText = 'Kuat';
            strengthColor = 'bg-blue-500';
            strengthWidth = '80%';
            break;
        case 5:
            strengthText = 'Sangat Kuat';
            strengthColor = 'bg-green-500';
            strengthWidth = '100%';
            break;
    }
    
    // Create or update strength indicator
    const passwordInput = document.querySelector('input[name="password"]');
    let indicator = passwordInput.parentElement.parentElement.querySelector('.password-strength');
    
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'password-strength mt-2';
        passwordInput.parentElement.parentElement.appendChild(indicator);
    }
    
    indicator.innerHTML = `
        <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-medium text-gray-700">Kekuatan Password</span>
            <span class="text-xs ${strengthColor.replace('bg-', 'text-')}">${strengthText}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="${strengthColor} h-2 rounded-full transition-all duration-300" style="width: ${strengthWidth}"></div>
        </div>
        ${feedback.length > 0 ? `<div class="text-xs text-gray-500 mt-1">Perlu: ${feedback.join(', ')}</div>` : ''}
    `;
}

function initializeInteractiveEffects() {
    // Interactive card tilt effect
    const formContainer = document.querySelector('.mx-auto.w-full.max-w-sm');
    if (formContainer) {
        formContainer.classList.add('interactive-card');
        
        formContainer.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const tiltX = (y - centerY) / centerY * -8;
            const tiltY = (x - centerX) / centerX * 8;
            
            this.style.transform = `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) translateZ(0)`;
        });
        
        formContainer.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0)';
        });
    }
    
    // Magnetic effect for buttons and links
    const magneticElements = document.querySelectorAll('button, a.font-medium, a.auth-nav-link');
    magneticElements.forEach(element => {
        element.classList.add('magnetic-button');
        
        element.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            this.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px)`;
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = 'translate(0px, 0px)';
        });
    });
    
    // Enhanced floating elements
    const decorativeElements = document.querySelectorAll('.floating-elements');
    decorativeElements.forEach(element => {
        element.classList.add('enhanced-floating');
    });
}

// Add essential CSS animations that aren't in the main auth.css
const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .animate-in {
        animation: slideInUp 0.6s ease-out forwards;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .input-group {
        transition: all 0.3s ease;
    }
    
    .input-group.focused {
        transform: translateY(-2px);
    }
    
    .input-group input {
        transition: all 0.3s ease;
    }
    
    .input-group.focused input {
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.1);
    }
`;
document.head.appendChild(additionalStyles);

// Handle page visibility changes for smooth re-entry
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Page became visible, add subtle re-entry animation
        const mainContent = document.querySelector('.min-h-full');
        if (mainContent) {
            mainContent.classList.add('page-transition-enter');
        }
    }
});

// Handle browser back/forward navigation
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        // Page was loaded from cache, re-run entry animations
        initializePageAnimations();
    }
});
