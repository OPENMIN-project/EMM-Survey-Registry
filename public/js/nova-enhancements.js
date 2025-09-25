/**
 * Nova Enhancements - Sticky Buttons and Keyboard Shortcuts
 */

console.log('Nova Enhancements Loaded');
(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', initializeNovaEnhancements);
    
    // Also initialize when Nova router changes (for SPA navigation)
    document.addEventListener('nova-ready', initializeNovaEnhancements);
    
    // Initialize when Vue router changes (for Nova SPA)
    if (window.Nova) {
        Nova.$on('router-ready', initializeNovaEnhancements);
    }

    function initializeNovaEnhancements() {
        makeButtonsSticky();
        Nova.$on('router-ready', makeButtonsSticky);
        window.navigation.onnavigate = makeButtonsSticky;
    }

    // Make form buttons sticky
    function makeButtonsSticky(counter = 0) {
        // Try multiple selectors for different Nova versions
        const retry = () => setTimeout(() => {
                makeButtonsSticky(counter + 1);
            }, 1000);
            
        const buttonsElement = document.querySelector('div.flex.items-center>button[type="submit"]')?.parentNode;
        
        if(!buttonsElement && counter < 10) {
            retry();
            return;
        }

        const formElement = buttonsElement?.parentNode;
        if(!formElement) {
            retry();
            return;
        }
        
        if (formElement && !formElement.classList.contains('nova-sticky-applied')) {
            formElement.classList.add('nova-sticky-applied');
            formElement.classList.add('relative');
            buttonsElement.classList.add('sticky', 'p-4', 'bg-40');
            buttonsElement.style.bottom = '0';
        }
    }
})(); 