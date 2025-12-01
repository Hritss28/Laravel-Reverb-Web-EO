// Auth Page Interactive Effects
document.addEventListener('DOMContentLoaded', function() {
    // Add animation classes on load
    const leftSide = document.querySelector('.animate-fadeInLeft');
    const rightSide = document.querySelector('.animate-fadeInRight');
    
    if (leftSide) {
        setTimeout(() => leftSide.classList.add('animate-fadeInLeft'), 100);
    }
    if (rightSide) {
        setTimeout(() => rightSide.classList.add('animate-fadeInRight'), 300);
    }

    // Form input focus effects
    const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
    
    inputs.forEach(input => {
        // Add focus effect
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-opacity-50');
            const icon = this.parentElement.querySelector('svg');
            if (icon) {
                icon.classList.add('text-indigo-500', 'scale-110');
                icon.classList.remove('text-gray-400');
            }
        });

        // Remove focus effect
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-opacity-50');
            const icon = this.parentElement.querySelector('svg');
            if (icon && this.value === '') {
                icon.classList.remove('text-indigo-500', 'scale-110');
                icon.classList.add('text-gray-400');
            }
        });

        // Typing effect
        input.addEventListener('input', function() {
            const icon = this.parentElement.querySelector('svg');
            if (icon && this.value !== '') {
                icon.classList.add('text-green-500');
                icon.classList.remove('text-gray-400', 'text-indigo-500');
            }
        });
    });

    // Button ripple effect
    const buttons = document.querySelectorAll('button[type="submit"]');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple-effect');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Form validation feedback
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                `;
                submitButton.disabled = true;
            }
        });
    }    // Password strength indicator - hanya untuk halaman register
    const passwordInput = document.querySelector('input[name="password"]');
    const isRegisterPage = window.location.pathname.includes('register');
    
    if (passwordInput && isRegisterPage) {
        passwordInput.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            showPasswordStrength(strength);
        });
    }

    function checkPasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        return strength;
    }    function showPasswordStrength(strength) {
        // Hanya tampilkan di halaman register
        if (!window.location.pathname.includes('register')) {
            return;
        }
        
        let strengthText = '';
        let strengthColor = '';
        
        switch (strength) {
            case 0:
            case 1:
                strengthText = 'Sangat Lemah';
                strengthColor = 'text-red-500';
                break;
            case 2:
                strengthText = 'Lemah';
                strengthColor = 'text-orange-500';
                break;
            case 3:
                strengthText = 'Sedang';
                strengthColor = 'text-yellow-500';
                break;
            case 4:
                strengthText = 'Kuat';
                strengthColor = 'text-blue-500';
                break;
            case 5:
                strengthText = 'Sangat Kuat';
                strengthColor = 'text-green-500';
                break;
        }

        // Create or update strength indicator
        const passwordInput = document.querySelector('input[name="password"]');
        let indicator = document.querySelector('.password-strength');
        if (!indicator && passwordInput) {
            indicator = document.createElement('div');
            indicator.className = 'password-strength text-xs mt-1';
            passwordInput.parentElement.parentElement.appendChild(indicator);
        }
        
        if (indicator) {
            indicator.innerHTML = `<span class="${strengthColor}">Kekuatan Password: ${strengthText}</span>`;
        }
    }

    // Floating animation for decorative elements
    const floatingElements = document.querySelectorAll('.floating-elements');
    floatingElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 2}s`;
    });

    // Parallax effect for decorative background
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        const decorativeElements = document.querySelectorAll('.parallax-element');
        decorativeElements.forEach(element => {
            element.style.transform = `translateY(${rate}px)`;
        });
    });

    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInputToggle = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (togglePassword && passwordInputToggle && eyeIcon) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInputToggle.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInputToggle.setAttribute('type', type);
            
            // Toggle eye icon
            if (type === 'text') {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });
    }

    // Enhanced form validation
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const emailInput = this.querySelector('input[name="email"]');
            const passwordInput = this.querySelector('input[name="password"]');
            let isValid = true;
            
            // Remove previous error states
            document.querySelectorAll('.input-error').forEach(el => el.remove());
            document.querySelectorAll('input').forEach(input => {
                input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            });
            
            // Validate email
            if (!emailInput.value || !emailInput.value.includes('@')) {
                showFieldError(emailInput, 'Email harus valid');
                isValid = false;
            }
            
            // Validate password
            if (!passwordInput.value || passwordInput.value.length < 3) {
                showFieldError(passwordInput, 'Password minimal 3 karakter');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    function showFieldError(input, message) {
        input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'input-error text-red-500 text-xs mt-1';
        errorDiv.textContent = message;
        
        input.parentElement.parentElement.appendChild(errorDiv);
    }
});

// CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        pointer-events: none;
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
