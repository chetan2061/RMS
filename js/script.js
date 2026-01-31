/* 
   Cups and Mugs - Interactive Effects 
   Simple logic to enhance UX without changing core functionality.
*/

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Navbar Scroll Effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // 2. Smooth Fade-In on Scroll (Intersection Observer)
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target); // Only animate once
            }
        });
    }, observerOptions);

    // Target elements to animate
    const animatedElements = document.querySelectorAll('.menu-item, .hero, .stat-box, .card');
    animatedElements.forEach(el => {
        el.classList.add('fade-in-section');
        observer.observe(el);
    });

    // 3. Button Click Ripple Effect (Visual Feedback)
    const buttons = document.querySelectorAll('button, .btn-add');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Check if it's a form submit to not block it, just animate
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
        });
    });

});
