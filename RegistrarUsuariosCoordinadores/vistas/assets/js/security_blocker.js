/**
 * security_blocker.js
 * 
 * This file provides functionality to block various browser actions
 * for security purposes such as:
 * - Right-click context menu
 * - Copy/paste operations (Ctrl+C, Ctrl+V)
 * - Text selection
 * - Viewing source code (Ctrl+U)
 * - Print (Ctrl+P)
 * - Save page (Ctrl+S)
 */

(function() {
    'use strict';
    
    // Block right-click context menu
    document.addEventListener('contextmenu', function(event) {
        event.preventDefault();
        return false;
    }, true);
    
    // Block keyboard shortcuts
    document.addEventListener('keydown', function(event) {
        // Get key and whether ctrl or meta key is pressed
        const key = event.key.toLowerCase();
        const isCtrlOrMeta = event.ctrlKey || event.metaKey;
        
        // Block Ctrl+C (copy)
        if (isCtrlOrMeta && key === 'c') {
            event.preventDefault();
            return false;
        }
        
        // Block Ctrl+V (paste)
        if (isCtrlOrMeta && key === 'v') {
            event.preventDefault();
            return false;
        }
        
        // Block Ctrl+X (cut)
        if (isCtrlOrMeta && key === 'x') {
            event.preventDefault();
            return false;
        }
        
        // Block Ctrl+U (view source)
        if (isCtrlOrMeta && key === 'u') {
            event.preventDefault();
            return false;
        }
        
        // Block Ctrl+P (print)
        if (isCtrlOrMeta && key === 'p') {
            event.preventDefault();
            return false;
        }
        
        // Block Ctrl+S (save)
        if (isCtrlOrMeta && key === 's') {
            event.preventDefault();
            return false;
        }
        
        // Block F12 key (developer tools)
        if (event.key === 'F12') {
            event.preventDefault();
            return false;
        }
        
        // Block Alt+F4
        if (event.altKey && key === 'f4') {
            event.preventDefault();
            return false;
        }
    }, true);
    
    // Block text selection
    document.addEventListener('selectstart', function(event) {
        event.preventDefault();
        return false;
    }, true);
    
    // Block drag and drop
    document.addEventListener('dragstart', function(event) {
        event.preventDefault();
        return false;
    }, true);
    
    // Block copy event
    document.addEventListener('copy', function(event) {
        event.preventDefault();
        return false;
    }, true);
    
    // Block cut event
    document.addEventListener('cut', function(event) {
        event.preventDefault();
        return false;
    }, true);
    
    // Block paste event
    document.addEventListener('paste', function(event) {
        event.preventDefault();
        return false;
    }, true);
    
    // Initialize security measures on page load
    window.addEventListener('DOMContentLoaded', function() {
        console.log('Security measures initialized');
    });
})();