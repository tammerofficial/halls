// Ultra Fast Navigation - سرعة فائقة جداً 🚀
(function() {
    'use strict';
    
    // تفعيل السرعة الفائقة
    document.documentElement.classList.add('ultra-fast');
    
    // تحسين استجابة النقرات - فورية تماماً
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && link.href.startsWith(window.location.origin)) {
            e.preventDefault();
            e.stopPropagation();
            
            // تأثير بصري فوري
            link.style.opacity = '0.6';
            link.style.transform = 'scale(0.95)';
            
            // التنقل الفوري - بدون أي تأخير
            window.location.href = link.href;
        }
    }, { passive: false });
    
    // التحميل المسبق الفوري
    const preloadedUrls = new Set();
    document.addEventListener('mouseenter', function(e) {
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
    
    // تحسين أداء التمرير
    let scrollTicking = false;
    function updateScroll() {
        scrollTicking = false;
    }
    
    function requestScrollTick() {
        if (!scrollTicking) {
            requestAnimationFrame(updateScroll);
            scrollTicking = true;
        }
    }
    
    document.addEventListener('scroll', requestScrollTick, { passive: true });
    
    // تحسين أداء النقرات على الأجهزة المحمولة
    if ('ontouchstart' in window) {
        document.addEventListener('touchstart', function() {}, { passive: true });
        document.addEventListener('touchmove', function() {}, { passive: true });
        document.addEventListener('touchend', function() {}, { passive: true });
    }
    
    // إزالة التأخير في النقرات
    document.addEventListener('mousedown', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            e.target.style.transform = 'scale(0.94)';
        }
    });
    
    document.addEventListener('mouseup', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            setTimeout(() => {
                e.target.style.transform = '';
            }, 30);
        }
    });
    
    // تحسين أداء الصفحة
    window.addEventListener('load', function() {
        document.body.style.opacity = '1';
        document.body.style.pointerEvents = 'auto';
        document.documentElement.style.scrollBehavior = 'auto';
    });
    
    // تسريع التنقل في القائمة الجانبية
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const href = this.getAttribute('href');
            if (href) {
                this.style.opacity = '0.7';
                this.style.transform = 'translateX(-3px)';
                window.location.href = href;
            }
        });
    });
    
    // تحسين أداء الروابط
    const allLinks = document.querySelectorAll('a');
    allLinks.forEach(link => {
        link.style.transition = 'all 0.03s ease';
        link.style.webkitTapHighlightColor = 'transparent';
        link.style.touchAction = 'manipulation';
    });
    
    // تحسين أداء الأزرار
    const allButtons = document.querySelectorAll('button, .btn');
    allButtons.forEach(button => {
        button.style.transition = 'all 0.03s ease';
        button.style.touchAction = 'manipulation';
    });
    
    // تحسين أداء النماذج
    const allInputs = document.querySelectorAll('input, textarea, select');
    allInputs.forEach(input => {
        input.style.transition = 'border-color 0.03s ease';
    });
    
    // تحسين أداء الجداول
    const allTables = document.querySelectorAll('table');
    allTables.forEach(table => {
        table.style.contain = 'layout style';
    });
    
    // تحسين أداء البطاقات
    const allCards = document.querySelectorAll('.card');
    allCards.forEach(card => {
        card.style.contain = 'layout style';
        card.style.willChange = 'transform';
    });
    
    // تحسين أداء القوائم المنسدلة
    const allDropdowns = document.querySelectorAll('.dropdown-menu');
    allDropdowns.forEach(dropdown => {
        dropdown.style.contain = 'layout style';
        dropdown.style.transition = 'all 0.03s ease';
    });
    
    // تحسين أداء التبويبات
    const allTabs = document.querySelectorAll('.tab-content .tab-pane');
    allTabs.forEach(tab => {
        tab.style.contain = 'layout style';
    });
    
    // تحسين أداء الصور
    const allImages = document.querySelectorAll('img');
    allImages.forEach(img => {
        img.style.contain = 'layout style';
        img.loading = 'lazy';
    });
    
    // تحسين أداء الأيقونات
    const allIcons = document.querySelectorAll('.fas, .far, .fab');
    allIcons.forEach(icon => {
        icon.style.contain = 'layout style';
    });
    
    // تحسين أداء التنقل العام
    document.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            const target = e.target.tagName === 'A' ? e.target : e.target.closest('a');
            target.style.transform = 'scale(0.97)';
            target.style.transition = 'all 0.03s ease';
        }
    });
    
    // إزالة التأخير في النقرات
    document.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            e.target.style.webkitTapHighlightColor = 'transparent';
        }
    });
    
    // تحسين أداء الصفحة عند التحميل
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('ultra-loaded');
        });
    } else {
        document.body.classList.add('ultra-loaded');
    }
    
    // تسريع التنقل باستخدام History API
    window.addEventListener('popstate', function() {
        document.body.style.opacity = '0.97';
        setTimeout(() => {
            document.body.style.opacity = '1';
        }, 30);
    });
    
    // تحسين أداء التمرير
    document.documentElement.style.scrollBehavior = 'auto';
    
    // تحسين أداء الذاكرة
    if ('requestIdleCallback' in window) {
        requestIdleCallback(function() {
            // تنظيف الذاكرة
            if (window.gc) {
                window.gc();
            }
        });
    }
    
    console.log('🚀 Ultra Fast Navigation Enabled - سرعة فائقة جداً مفعلة');
})();
