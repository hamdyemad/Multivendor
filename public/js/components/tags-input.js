/**
 * Tags Input Component JavaScript
 * Provides functionality for the x-tags-input component
 */

class TagsInput {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            placeholder: options.placeholder || 'Type and press Enter...',
            language: options.language || 'en',
            allowDuplicates: options.allowDuplicates !== false,
            maxTags: options.maxTags || null,
            delimiter: options.delimiter || ','
        };
        
        this.tags = [];
        this.init();
    }
    
    init() {
        this.setupElements();
        this.loadExistingTags();
        this.bindEvents();
    }
    
    setupElements() {
        this.wrapper = $(this.container).closest('.tags-input-wrapper');
        this.input = $(this.container).find('.tags-input');
        this.hiddenInput = $(this.container).find('input[type="hidden"]');
        this.tagsDisplay = $(this.container).find('.tags-display');
        
        // Create elements if they don't exist
        if (this.tagsDisplay.length === 0) {
            this.tagsDisplay = $('<div class="tags-display"></div>');
            $(this.container).prepend(this.tagsDisplay);
        }
    }
    
    loadExistingTags() {
        const existingValue = this.hiddenInput.val();
        if (existingValue && existingValue.trim()) {
            this.tags = existingValue.split(this.options.delimiter)
                .map(tag => tag.trim())
                .filter(tag => tag.length > 0);
            this.renderTags();
        }
    }
    
    bindEvents() {
        // Handle input events
        this.input.on('keydown', (e) => {
            const value = this.input.val().trim();
            
            if (e.key === 'Enter' || e.key === this.options.delimiter) {
                e.preventDefault();
                if (value) {
                    this.addMultipleTags(value);
                    this.input.val('');
                }
            }
            // Backspace on empty input - remove last tag
            else if (e.key === 'Backspace' && !value && this.tags.length > 0) {
                this.removeTag(this.tags.length - 1);
            }
        });
        
        // Handle blur event
        this.input.on('blur', () => {
            const value = this.input.val().trim();
            if (value) {
                this.addMultipleTags(value);
                this.input.val('');
            }
        });
    }
    
    addTag(tagText) {
        if (!tagText || tagText.trim() === '') return false;
        
        tagText = tagText.trim();
        
        // Check for duplicates if not allowed
        if (!this.options.allowDuplicates && this.tags.includes(tagText)) {
            return false;
        }
        
        // Check max tags limit
        if (this.options.maxTags && this.tags.length >= this.options.maxTags) {
            return false;
        }
        
        this.tags.push(tagText);
        this.renderTags();
        this.updateHiddenInput();
        return true;
    }
    
    addMultipleTags(input) {
        if (!input) return;
        
        // Split by delimiter and process each tag
        const newTags = input.split(this.options.delimiter)
            .map(tag => tag.trim())
            .filter(tag => tag.length > 0);
        
        let added = 0;
        for (const tagText of newTags) {
            if (this.addTag(tagText)) {
                added++;
            }
        }
        
        return added;
    }
    
    removeTag(index) {
        if (index >= 0 && index < this.tags.length) {
            this.tags.splice(index, 1);
            this.renderTags();
            this.updateHiddenInput();
            return true;
        }
        return false;
    }
    
    renderTags() {
        this.tagsDisplay.empty();
        
        // Show or hide tags-display based on whether there are tags
        if (this.tags.length > 0) {
            this.tagsDisplay.removeClass('d-none');
        } else {
            this.tagsDisplay.addClass('d-none');
        }
        
        this.tags.forEach((tag, index) => {
            const isRtl = this.options.language === 'ar';
            const tagElement = $(`
                <div class="tag-item" data-index="${index}" ${isRtl ? 'dir="rtl"' : ''}>
                    <span class="tag-text" ${isRtl ? 'dir="rtl"' : ''}>${this.escapeHtml(tag)}</span>
                    <button type="button" class="tag-remove" title="Remove tag">×</button>
                </div>
            `);
            
            // Handle tag removal
            tagElement.find('.tag-remove').on('click', () => {
                this.removeTag(index);
            });
            
            this.tagsDisplay.append(tagElement);
        });
    }
    
    updateHiddenInput() {
        this.hiddenInput.val(this.tags.join(this.options.delimiter + ' '));
    }
    
    escapeHtml(text) {
        return $('<div>').text(text).html();
    }
    
    // Public methods
    getTags() {
        return [...this.tags];
    }
    
    setTags(tags) {
        this.tags = Array.isArray(tags) ? [...tags] : [];
        this.renderTags();
        this.updateHiddenInput();
    }
    
    clearTags() {
        this.tags = [];
        this.renderTags();
        this.updateHiddenInput();
    }
    
    addTagProgrammatically(tag) {
        return this.addTag(tag);
    }
    
    removeTagProgrammatically(tag) {
        const index = this.tags.indexOf(tag);
        if (index !== -1) {
            return this.removeTag(index);
        }
        return false;
    }
}

// Make TagsInput available globally
window.TagsInput = TagsInput;
