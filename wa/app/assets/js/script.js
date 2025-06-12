document.addEventListener('DOMContentLoaded', function() {
    // Toggle between login and signup forms
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding form
            forms.forEach(form => {
                form.classList.remove('active');
                if (form.id === `${tabName}-form`) {
                    form.classList.add('active');
                }
            });
        });
    });
	
	
	// Add this to ensure signup form is shown when hash is present
document.addEventListener('DOMContentLoaded', function() {
    // Existing code...
    
    // Auto-scroll to signup form if hash exists
    if (window.location.hash === '#signup-form') {
        const signupTab = document.querySelector('.auth-tab[data-tab="signup"]');
        if (signupTab) {
            signupTab.click();
            document.getElementById('signup-form').scrollIntoView();
        }
    }
});
    
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Form validation
    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            const password = document.getElementById('signup-password').value;
            const confirmPassword = document.getElementById('signup-confirm-password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
            }
        });
    }
    
    // Check for hash in URL to show specific form
    if (window.location.hash === '#signup-form') {
        document.querySelector('.auth-tab[data-tab="signup"]').click();
    }
});