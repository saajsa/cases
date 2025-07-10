/**
 * Causelist JavaScript
 * Extracted from views/admin/hearings/causelist.php
 * Modular JavaScript for the court causelist functionality
 */

class CauselistManager {
    constructor(config) {
        this.config = {
            adminUrl: config.adminUrl,
            currentDate: config.currentDate,
            ...config
        };
        
        this.autoRefreshInterval = null;
        this.touchStartX = 0;
        this.touchEndX = 0;
        
        this.init();
    }
    
    init() {
        console.log('Causelist initialized');
        this.bindEvents();
        this.initializeAnimations();
        this.initializeKeyboardShortcuts();
        this.initializeTouchSupport();
        this.initializeAutoRefresh();
        this.highlightCurrentTimeSlot();
        this.addLoadingStyles();
        
        if (window.location.search.includes('debug=1')) {
            this.debugInfo();
        }
    }
    
    bindEvents() {
        // Quick date dropdown
        const quickDatesSelect = document.getElementById('quick-dates');
        if (quickDatesSelect) {
            quickDatesSelect.addEventListener('change', (e) => {
                const selectedDate = e.target.value;
                console.log('Quick date selected:', selectedDate);
                if (selectedDate) {
                    this.goToDate(selectedDate);
                }
            });
        }
        
        // Date selector and go-to-date button
        const dateSelector = document.getElementById('date-selector');
        const goToDateBtn = document.getElementById('go-to-date');
        
        if (goToDateBtn && dateSelector) {
            goToDateBtn.addEventListener('click', () => {
                const selectedDate = dateSelector.value;
                console.log('Go to date clicked:', selectedDate);
                if (selectedDate) {
                    this.setLoadingState(goToDateBtn, 'Loading...');
                    this.goToDate(selectedDate);
                }
            });
            
            dateSelector.addEventListener('keypress', (e) => {
                if (e.which === 13 || e.keyCode === 13) {
                    const selectedDate = dateSelector.value;
                    console.log('Enter key pressed on date:', selectedDate);
                    if (selectedDate) {
                        this.goToDate(selectedDate);
                    }
                    e.preventDefault();
                }
            });
        }
        
        // Navigation buttons
        const prevDayBtn = document.getElementById('prev-day');
        if (prevDayBtn) {
            prevDayBtn.addEventListener('click', () => {
                console.log('Previous day clicked');
                const currentDate = new Date(this.config.currentDate);
                currentDate.setDate(currentDate.getDate() - 1);
                const newDate = this.formatDate(currentDate);
                console.log('Previous date:', newDate);
                
                this.setLoadingState(prevDayBtn, 'Loading...');
                this.goToDate(newDate);
            });
        }
        
        const nextDayBtn = document.getElementById('next-day');
        if (nextDayBtn) {
            nextDayBtn.addEventListener('click', () => {
                console.log('Next day clicked');
                const currentDate = new Date(this.config.currentDate);
                currentDate.setDate(currentDate.getDate() + 1);
                const newDate = this.formatDate(currentDate);
                console.log('Next date:', newDate);
                
                this.setLoadingState(nextDayBtn, 'Loading...');
                this.goToDate(newDate);
            });
        }
        
        const todayBtn = document.getElementById('today');
        if (todayBtn) {
            todayBtn.addEventListener('click', () => {
                console.log('Today clicked');
                const today = this.formatDate(new Date());
                console.log('Today date:', today);
                
                this.setLoadingState(todayBtn, 'Loading...');
                this.goToDate(today);
            });
            
            // Update today button state
            const today = this.formatDate(new Date());
            if (this.config.currentDate === today) {
                todayBtn.classList.add('active');
            } else {
                todayBtn.classList.remove('active');
            }
        }
        
        // Print button
        const printBtn = document.getElementById('print-list');
        if (printBtn) {
            printBtn.addEventListener('click', () => this.handlePrint());
        }
        
        // Table row hover effects
        const tableRows = document.querySelectorAll('.causelist-table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#fafafa';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
        
        // Page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAutoRefresh();
            } else {
                this.startAutoRefresh();
            }
        });
        
        // Error handling
        window.addEventListener('error', (e) => this.handleError(e));
        window.addEventListener('beforeunload', () => this.cleanup());
    }
    
    goToDate(dateStr) {
        console.log('Navigating to date:', dateStr);
        window.location.href = this.config.adminUrl + 'cases/hearings/causelist?date=' + dateStr;
    }
    
    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }
    
    setLoadingState(button, text) {
        if (button) {
            button.textContent = text;
            button.disabled = true;
        }
    }
    
    handlePrint() {
        console.log('Print button clicked');
        
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        const printHeader = document.getElementById('print-header');
        const courtSections = document.querySelectorAll('.court-section');
        
        let printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Court Cause List - ${new Date().toLocaleDateString()}</title>
                <style>
                    /* Print-specific styles */
                    * {
                        box-sizing: border-box;
                        margin: 0;
                        padding: 0;
                    }
                    
                    body {
                        font-family: 'Arial', sans-serif;
                        background: #ffffff;
                        color: #000000;
                        font-size: 12px;
                        line-height: 1.4;
                        padding: 20px;
                    }
                    
                    .print-header {
                        text-align: center;
                        margin-bottom: 30px;
                        padding-bottom: 20px;
                        border-bottom: 2px solid #000000;
                    }
                    
                    .print-header h2 {
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    
                    .print-header h3 {
                        font-size: 16px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    
                    .print-header h4 {
                        font-size: 14px;
                        font-weight: normal;
                    }
                    
                    .court-section {
                        page-break-inside: avoid;
                        border: 2px solid #000000;
                        margin-bottom: 20px;
                        background: #ffffff;
                    }
                    
                    .court-header {
                        background: #f0f0f0;
                        color: #000000;
                        padding: 15px;
                        text-align: center;
                        border-bottom: 2px solid #000000;
                        font-weight: bold;
                        font-size: 14px;
                    }
                    
                    .judge-section {
                        border-bottom: 1px solid #cccccc;
                    }
                    
                    .judge-section:last-child {
                        border-bottom: none;
                    }
                    
                    .judge-header {
                        background: #f8f8f8;
                        padding: 12px 15px;
                        border-bottom: 1px solid #000000;
                    }
                    
                    .judge-name {
                        font-size: 13px;
                        font-weight: bold;
                        margin-bottom: 3px;
                    }
                    
                    .court-info {
                        font-size: 11px;
                        color: #666666;
                    }
                    
                    .causelist-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 0;
                    }
                    
                    .causelist-table th {
                        background: #f0f0f0;
                        border: 1px solid #000000;
                        padding: 8px 6px;
                        text-align: center;
                        font-weight: bold;
                        font-size: 11px;
                        text-transform: uppercase;
                    }
                    
                    .causelist-table td {
                        border: 1px solid #000000;
                        padding: 8px 6px;
                        vertical-align: top;
                        font-size: 11px;
                        line-height: 1.3;
                    }
                    
                    .causelist-table td:first-child {
                        text-align: center;
                        font-weight: bold;
                        width: 40px;
                    }
                    
                    .case-number {
                        font-weight: bold;
                        margin-bottom: 2px;
                    }
                    
                    .case-name {
                        font-weight: normal;
                        text-transform: uppercase;
                        font-size: 10px;
                        line-height: 1.2;
                    }
                    
                    .hearing-time {
                        font-weight: bold;
                        font-size: 12px;
                    }
                    
                    .hearing-purpose {
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    
                    .hearing-description {
                        font-size: 10px;
                        color: #666666;
                        margin-bottom: 5px;
                        line-height: 1.2;
                    }
                    
                    .follow-up-indicator {
                        font-size: 9px;
                        color: #666666;
                        font-style: italic;
                    }
                    
                    .status-badge {
                        display: inline-block;
                        padding: 2px 6px;
                        border: 1px solid #000000;
                        background: none;
                        color: #000000;
                        font-size: 9px;
                        font-weight: bold;
                        text-transform: uppercase;
                        float: right;
                        margin-top: 3px;
                    }
                    
                    .empty-state {
                        text-align: center;
                        padding: 40px 20px;
                        color: #666666;
                    }
                    
                    @page {
                        margin: 1inch;
                        size: A4;
                    }
                    
                    @media print {
                        body {
                            padding: 0;
                        }
                        
                        .court-section {
                            page-break-inside: avoid;
                            margin-bottom: 15px;
                        }
                        
                        .judge-section {
                            page-break-inside: avoid;
                        }
                    }
                </style>
            </head>
            <body>
        `;
        
        if (printHeader) {
            printContent += `<div class="print-header">${printHeader.innerHTML}</div>`;
        }
        
        courtSections.forEach(section => {
            printContent += `<div class="court-section">${section.innerHTML}</div>`;
        });
        
        printContent += `
            </body>
            </html>
        `;
        
        printWindow.document.write(printContent);
        printWindow.document.close();
        
        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.print();
                setTimeout(() => printWindow.close(), 1000);
            }, 500);
        };
    }
    
    initializeAnimations() {
        const courtSections = document.querySelectorAll('.court-section');
        courtSections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            
            setTimeout(() => {
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }
    
    initializeKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            if (document.activeElement.tagName !== 'INPUT' && 
                document.activeElement.tagName !== 'SELECT' && 
                document.activeElement.tagName !== 'TEXTAREA') {
                
                const prevDayBtn = document.getElementById('prev-day');
                const nextDayBtn = document.getElementById('next-day');
                const todayBtn = document.getElementById('today');
                const printBtn = document.getElementById('print-list');
                
                switch(e.key) {
                    case 'ArrowLeft':
                        console.log('Left arrow key pressed');
                        if (prevDayBtn) prevDayBtn.click();
                        e.preventDefault();
                        break;
                        
                    case 'ArrowRight':
                        console.log('Right arrow key pressed');
                        if (nextDayBtn) nextDayBtn.click();
                        e.preventDefault();
                        break;
                        
                    case 't':
                    case 'T':
                        console.log('T key pressed for today');
                        if (todayBtn) todayBtn.click();
                        e.preventDefault();
                        break;
                        
                    case 'p':
                    case 'P':
                        if (e.ctrlKey || e.metaKey) {
                            console.log('Ctrl+P pressed for print');
                            if (printBtn) {
                                printBtn.click();
                                e.preventDefault();
                            }
                        }
                        break;
                }
            }
        });
    }
    
    initializeTouchSupport() {
        document.addEventListener('touchstart', (e) => {
            this.touchStartX = e.changedTouches[0].screenX;
        });
        
        document.addEventListener('touchend', (e) => {
            this.touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe();
        });
    }
    
    handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = this.touchEndX - this.touchStartX;
        
        if (Math.abs(swipeDistance) > swipeThreshold) {
            const prevDayBtn = document.getElementById('prev-day');
            const nextDayBtn = document.getElementById('next-day');
            
            if (swipeDistance > 0) {
                console.log('Swipe right detected');
                if (prevDayBtn) prevDayBtn.click();
            } else {
                console.log('Swipe left detected');
                if (nextDayBtn) nextDayBtn.click();
            }
        }
    }
    
    initializeAutoRefresh() {
        this.startAutoRefresh();
    }
    
    startAutoRefresh() {
        const today = this.formatDate(new Date());
        
        if (this.config.currentDate === today) {
            console.log('Starting auto-refresh for today\'s cause list');
            this.autoRefreshInterval = setInterval(() => {
                if (!document.hidden && !document.activeElement.matches('input, select, textarea')) {
                    console.log('Auto-refreshing cause list...');
                    location.reload();
                }
            }, 30 * 60 * 1000); // 30 minutes
        }
    }
    
    stopAutoRefresh() {
        if (this.autoRefreshInterval) {
            console.log('Stopping auto-refresh');
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    }
    
    highlightCurrentTimeSlot() {
        const today = this.formatDate(new Date());
        
        if (this.config.currentDate === today) {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            const currentTimeMinutes = currentHour * 60 + currentMinute;
            
            const hearingTimes = document.querySelectorAll('.hearing-time');
            hearingTimes.forEach(timeElement => {
                const timeText = timeElement.textContent.trim();
                const timeMatch = timeText.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
                
                if (timeMatch) {
                    let hours = parseInt(timeMatch[1]);
                    const minutes = parseInt(timeMatch[2]);
                    const ampm = timeMatch[3].toUpperCase();
                    
                    if (ampm === 'PM' && hours !== 12) {
                        hours += 12;
                    } else if (ampm === 'AM' && hours === 12) {
                        hours = 0;
                    }
                    
                    const hearingTimeMinutes = hours * 60 + minutes;
                    const timeDiff = hearingTimeMinutes - currentTimeMinutes;
                    
                    if (timeDiff >= -15 && timeDiff <= 30) {
                        const row = timeElement.closest('tr');
                        if (row) {
                            row.style.backgroundColor = '#fff8e6';
                            row.style.borderLeft = '3px solid #cc8c1a';
                            row.style.boxShadow = '0 2px 4px rgba(204, 140, 26, 0.1)';
                        }
                    }
                }
            });
        }
        
        // Update highlighting every minute
        setTimeout(() => this.highlightCurrentTimeSlot(), 60000);
    }
    
    showLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(250, 250, 250, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            font-family: Inter, sans-serif;
        `;
        
        overlay.innerHTML = `
            <div style="text-align: center; color: #666666;">
                <div style="width: 40px; height: 40px; border: 3px solid #e1e1e1; border-top: 3px solid #1a1a1a; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 15px;"></div>
                <p style="margin: 0; font-weight: 500;">Loading cause list...</p>
            </div>
        `;
        
        document.body.appendChild(overlay);
    }
    
    addLoadingStyles() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            .printing .page-actions,
            .printing .date-navigation,
            .printing .debug-panel {
                display: none !important;
            }
            
            .printing .print-header {
                display: block !important;
            }
        `;
        document.head.appendChild(style);
        
        // Add loading state to navigation buttons
        const navButtons = document.querySelectorAll('.date-nav-btn, #go-to-date');
        navButtons.forEach(button => {
            button.addEventListener('click', () => {
                if (!button.id || button.id !== 'print-list') {
                    setTimeout(() => this.showLoadingOverlay(), 200);
                }
            });
        });
    }
    
    handleError(e) {
        console.error('Causelist error:', e.error);
        
        const navButtons = document.querySelectorAll('.date-nav-btn, #go-to-date');
        navButtons.forEach(button => {
            if (button.disabled) {
                button.disabled = false;
                if (button.id === 'prev-day') button.textContent = '← Previous';
                else if (button.id === 'next-day') button.textContent = 'Next →';
                else if (button.id === 'today') button.textContent = 'Today';
                else if (button.id === 'go-to-date') button.textContent = 'Go to Date';
            }
        });
        
        const overlay = document.getElementById('loading-overlay');
        if (overlay) overlay.remove();
    }
    
    cleanup() {
        this.stopAutoRefresh();
        
        const overlay = document.getElementById('loading-overlay');
        if (overlay) overlay.remove();
    }
    
    debugInfo() {
        console.log('=== CAUSELIST DEBUG INFO ===');
        console.log('Current date:', this.config.currentDate);
        console.log('Admin URL:', this.config.adminUrl);
        console.log('Today:', this.formatDate(new Date()));
        
        const elements = {
            quickDates: !!document.getElementById('quick-dates'),
            dateSelector: !!document.getElementById('date-selector'),
            goToDateBtn: !!document.getElementById('go-to-date'),
            prevDayBtn: !!document.getElementById('prev-day'),
            nextDayBtn: !!document.getElementById('next-day'),
            todayBtn: !!document.getElementById('today'),
            printBtn: !!document.getElementById('print-list')
        };
        
        console.log('Elements found:', elements);
        console.log('Court sections:', document.querySelectorAll('.court-section').length);
        console.log('Table rows:', document.querySelectorAll('.causelist-table tbody tr').length);
        console.log('============================');
    }
}

// Export for use in other modules
window.CauselistManager = CauselistManager;