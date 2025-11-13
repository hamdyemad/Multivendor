/**
 * Tags Input Component
 * Reusable component for creating tag-style inputs
 */

class TagsInput {
    constructor(container, options = {}) {
        this.container = $(container);
        this.options = {
            placeholder: options.placeholder || 'Type and press Enter...',
            rtlPlaceholder: options.rtlPlaceholder || 'اكتب واضغط Enter...',
            language: options.language || 'en',
            allowDuplicates: options.allowDuplicates !== false,
            maxTags: options.maxTags || null,
            delimiter: options.delimiter || ',',
            ...options
        };
        
        this.tags = [];
        this.init();
    }

    init() {
        this.createStructure();
        this.bindEvents();
        this.loadExistingTags();
        console.log('✅ Tags Input Component initialized');
    }

    createStructure() {
        const isRtl = this.options.language === 'ar';
        const placeholder = isRtl ? this.options.rtlPlaceholder : this.options.placeholder;
        
        const html = `
            <div class="tags-input-container" data-language="${this.options.language}">
                <div class="tags-display"></div>
                <input type="text" class="tags-input" placeholder="${placeholder}" ${isRtl ? 'dir="rtl"' : ''}>
            </div>
        `;
        
        this.container.html(html);
        this.tagsDisplay = this.container.find('.tags-display');
        this.input = this.container.find('.tags-input');
        this.hiddenInput = this.container.find('input[type="hidden"]').first();
        
        // If no hidden input exists, create one
        if (this.hiddenInput.length === 0) {
            this.hiddenInput = $('<input type="hidden">');
            this.container.append(this.hiddenInput);
        }
    }

    bindEvents() {
        // Handle input events
        this.input.on('keydown', (e) => {
            const value = this.input.val().trim();
            
            // Enter key or comma
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
        
        // Handle blur event (when user clicks away)
        this.input.on('blur', () => {
            const value = this.input.val().trim();
            if (value) {
                this.addMultipleTags(value);
                this.input.val('');
            }
        });
    }

    loadExistingTags() {
        const existingValue = this.hiddenInput.val();
        if (existingValue && existingValue.trim()) {
            this.tags = existingValue.split(this.options.delimiter).map(tag => tag.trim()).filter(tag => tag.length > 0);
            this.renderTags();
        }
    }

    addTag(tagText) {
        if (!tagText) return;
        
        // Check for duplicates if not allowed
        if (!this.options.allowDuplicates && this.tags.includes(tagText)) {
            return;
        }
        
        // Check max tags limit
        if (this.options.maxTags && this.tags.length >= this.options.maxTags) {
            return;
        }
        
        this.tags.push(tagText);
        this.renderTags();
        this.updateHiddenInput();
        this.triggerChange();
    }

    addMultipleTags(input) {
        if (!input) return;
        
        // Split by comma and process each tag
        const newTags = input.split(this.options.delimiter)
            .map(tag => tag.trim())
            .filter(tag => tag.length > 0);
        
        let addedCount = 0;
        
        for (const tagText of newTags) {
            // Check max tags limit
            if (this.options.maxTags && this.tags.length >= this.options.maxTags) {
                break;
            }
            
            // Check for duplicates if not allowed
            if (!this.options.allowDuplicates && this.tags.includes(tagText)) {
                continue;
            }
            
            this.tags.push(tagText);
            addedCount++;
        }
        
        if (addedCount > 0) {
            this.renderTags();
            this.updateHiddenInput();
            this.triggerChange();
        }
    }

    removeTag(index) {
        if (index >= 0 && index < this.tags.length) {
            this.tags.splice(index, 1);
            this.renderTags();
            this.updateHiddenInput();
            this.triggerChange();
        }
    }

    renderTags() {
        this.tagsDisplay.empty();
        
        this.tags.forEach((tag, index) => {
            const isRtl = this.options.language === 'ar';
            const tagElement = $(`
                <div class="tag-item" data-index="${index}">
                    <span class="tag-text" ${isRtl ? 'dir="rtl"' : ''}>${this.escapeHtml(tag)}</span>
                    <button type="button" class="tag-remove" title="${isRtl ? 'إزالة' : 'Remove'}">×</button>
                </div>
            `);
            
            // Add click handler for remove button
            tagElement.find('.tag-remove').on('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.removeTag(index);
            });
            
            this.tagsDisplay.append(tagElement);
        });
    }

    updateHiddenInput() {
        this.hiddenInput.val(this.tags.join(`${this.options.delimiter} `));
    }

    triggerChange() {
        this.hiddenInput.trigger('change');
        this.container.trigger('tags:changed', [this.tags]);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Public methods
    getTags() {
        return [...this.tags];
    }

    setTags(tags) {
        this.tags = Array.isArray(tags) ? [...tags] : [];
        this.renderTags();
        this.updateHiddenInput();
        this.triggerChange();
    }

    addTagProgrammatically(tag) {
        this.addTag(tag);
    }

    clearTags() {
        this.tags = [];
        this.renderTags();
        this.updateHiddenInput();
        this.triggerChange();
    }

    destroy() {
        this.container.off();
        this.container.empty();
    }
}

// jQuery plugin wrapper
$.fn.tagsInput = function(options) {
    return this.each(function() {
        const $this = $(this);
        let instance = $this.data('tagsInput');
        
        if (!instance) {
            instance = new TagsInput(this, options);
            $this.data('tagsInput', instance);
        }
        
        return instance;
    });
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TagsInput;
}

// Global access
window.TagsInput = TagsInput;
