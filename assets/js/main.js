/**
 * TRAVELMATE - Minimal Vanilla UI Interactions
 */

document.addEventListener('DOMContentLoaded', function () {
    // Mobile navigation menu toggle
    const toggleBtn = document.querySelector('.mobile-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (toggleBtn && navMenu) {
        toggleBtn.addEventListener('click', function () {
            navMenu.classList.toggle('open');
            const expanded = navMenu.classList.contains('open');
            toggleBtn.setAttribute('aria-expanded', expanded);
        });
    }

    // Auto-fade flash messages after 6 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                if (alert.parentElement) {
                    alert.remove();
                }
            }, 500);
        }, 6000);
    });

    // Confirmation on destructive actions
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const message = btn.getAttribute('data-confirm') || 'Are you sure you want to proceed with this action?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Image file upload live preview (for admin forms)
    const imageInput = document.getElementById('place_image');
    const previewWrap = document.getElementById('preview_wrap');
    const previewImg = document.getElementById('image_preview');

    if (imageInput && previewImg) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    if (previewWrap) {
                        previewWrap.style.display = 'flex';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
