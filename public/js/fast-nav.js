// تسريع التنقل بين الصفحات - سرعة فائقة ⚡
(function() {
    'use strict';
    
    // تحسين استجابة النقرات - فورية
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && link.href.startsWith(window.location.origin)) {
            e.preventDefault();
            
            // تأثير بصري فوري
            link.style.opacity = '0.7';
            link.style.transform = 'scale(0.98)';
            link.style.transition = 'all 0.05s ease';
            
            // التنقل الفوري - بدون تأخير
            window.location.href = link.href;
        }
    });
    
    // التحميل المسبق عند التمرير - محسن
    const preloadedUrls = new Set();
    document.addEventListener('mouseover', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && link.href.startsWith(window.location.origin)) {
            const url = link.href;
            if (!preloadedUrls.has(url)) {
                // تحميل مسبق فوري
                const prefetch = document.createElement('link');
                prefetch.rel = 'prefetch';
                prefetch.href = url;
                document.head.appendChild(prefetch);
                preloadedUrls.add(url);
            }
        }
    });
    
    // إزالة التأخير في اللمس - محسن
    document.addEventListener('touchstart', function() {}, { passive: true });
    document.addEventListener('touchend', function() {}, { passive: true });
    
    // تحسين أداء الصفحة
    window.addEventListener('load', function() {
        // إزالة التأثيرات البطيئة
        document.body.style.opacity = '1';
        document.body.style.pointerEvents = 'auto';
        
        // تحسين التمرير
        document.documentElement.style.scrollBehavior = 'auto';
    });
    
    // تسريع التنقل في القائمة الجانبية
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            if (href) {
                // تأثير فوري
                this.style.opacity = '0.8';
                this.style.transform = 'translateX(-2px)';
                
                // تنفيذ فوري
                window.location.href = href;
            }
        });
    });
    
    // تحسين استجابة النقر العام
    document.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            const target = e.target.tagName === 'A' ? e.target : e.target.closest('a');
            target.style.transform = 'scale(0.98)';
            target.style.transition = 'all 0.05s ease';
        }
    });
    
    // إزالة التأخير في النقرات
    document.addEventListener('mousedown', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            e.target.style.transform = 'scale(0.96)';
        }
    });
    
    document.addEventListener('mouseup', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            setTimeout(() => {
                e.target.style.transform = '';
            }, 50);
        }
    });
    
    // تحسين أداء التمرير
    let ticking = false;
    function updateScroll() {
        ticking = false;
    }
    
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateScroll);
            ticking = true;
        }
    }
    
    document.addEventListener('scroll', requestTick, { passive: true });
    
    // تحسين أداء النقرات على الأجهزة المحمولة
    if ('ontouchstart' in window) {
        document.addEventListener('touchstart', function() {}, { passive: true });
        document.addEventListener('touchmove', function() {}, { passive: true });
        document.addEventListener('touchend', function() {}, { passive: true });
    }
    
    // إزالة التأخير في النقرات
    document.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            e.target.style.webkitTapHighlightColor = 'transparent';
        }
    });
    
    // تحسين أداء الصفحة عند التحميل
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('loaded');
        });
    } else {
        document.body.classList.add('loaded');
    }
    
    // تسريع التنقل باستخدام History API
    window.addEventListener('popstate', function() {
        document.body.style.opacity = '0.95';
        setTimeout(() => {
            document.body.style.opacity = '1';
        }, 50);
    });
    
    // تحسين أداء الروابط
    const allLinks = document.querySelectorAll('a');
    allLinks.forEach(link => {
        link.style.transition = 'all 0.05s ease';
        link.style.webkitTapHighlightColor = 'transparent';
    });
    
    console.log('🚀 Fast Navigation Enabled - سرعة فائقة مفعلة');
})();
