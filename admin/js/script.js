jQuery(document).ready(function($) {
    // Color picker initialization
    $('.color-field').wpColorPicker();

    // Handle icon type radio selection (works for both menu items and featured icon)
    $(document).on('change', '.icon-type-radio', function() {
        const container = $(this).closest('.field-custom, .featured-icon-wrapper');
        const uploadSection = container.find('.icon-upload-section');
        const dashiconSection = container.find('.dashicon-selection-section');
        const menuItemId = container.closest('.menu-item').find('.menu-item-data-db-id').val();
        
        if ($(this).val() === 'upload') {
            uploadSection.slideDown(300);
            dashiconSection.slideUp(300);
            if (menuItemId) {
                container.find(`input[name="menu-item-icon-type[${menuItemId}]"]`).val('upload');
            }
        } else {
            uploadSection.slideUp(300);
            dashiconSection.slideDown(300);
            if (menuItemId) {
                container.find(`input[name="menu-item-icon-type[${menuItemId}]"]`).val('dashicon');
            }
        }
    });

    // Media uploader for icons
    function initializeMediaUploader(buttonSelector) {
        $(document).on('click', buttonSelector, function(e) {
            e.preventDefault();
            
            const button = $(this);
            const inputField = button.prev('input');
            const container = button.closest('.field-custom, .featured-icon-wrapper');
            const previewContainer = container.find('.icon-preview');
            const menuItemId = container.closest('.menu-item').find('.menu-item-data-db-id').val();
            
            // Create or reuse media frame
            let custom_uploader = button.data('media-frame');
            
            if (!custom_uploader) {
                custom_uploader = wp.media({
                    title: 'Select or Upload Icon',
                    library: { type: ['image', 'image/svg+xml'] },
                    button: { text: 'Use this icon' },
                    multiple: false
                });
    
                // When an image is selected
                custom_uploader.on('select', function() {
                    const attachment = custom_uploader.state().get('selection').first().toJSON();
                    inputField.val(attachment.url);
                    
                    // Update preview
                    previewContainer.html(`<img src="${attachment.url}" style="max-width: 40px; height: auto;">`);
                
                    // Find or create the icon type input
                    if (menuItemId) {
                        let iconTypeInput = container.find(`input[name="menu-item-icon-type[${menuItemId}]"]`);
                        if (iconTypeInput.length === 0) {
                            iconTypeInput = $(`<input type="hidden" name="menu-item-icon-type[${menuItemId}]" value="upload">`);
                            container.append(iconTypeInput);
                        } else {
                            iconTypeInput.val('upload');
                        }
                
                        // Force the radio button selection
                        container.find('input[type="radio"][value="upload"]').prop('checked', true);
                        container.find('.dashicon-selection-section').hide();
                        container.find('.icon-upload-section').show();
                    }
                });
    
                button.data('media-frame', custom_uploader);
            }
    
            custom_uploader.open();
        });
    }

    // Dashicon selection for featured icon
    $(document).on('click', '.dashicon-option', function() {
        const container = $(this).closest('.field-custom, .featured-icon-wrapper');
        const selectedIcon = $(this).data('icon');
        const menuItemId = container.closest('.menu-item').find('.menu-item-data-db-id').val();
        
        // Update hidden input
        container.find('.selected-dashicon').val(selectedIcon);
        
        // Update visual selection
        container.find('.dashicon-option').removeClass('selected');
        $(this).addClass('selected');

        // Select dashicon radio button
        const radioButton = container.find('input[value="dashicon"]');
        radioButton.prop('checked', true).trigger('change');

        // Update icon type if it's a menu item
        if (menuItemId) {
            container.find(`input[name="menu-item-icon-type[${menuItemId}]"]`).val('dashicon');
        }

        // Clear upload preview and input
        container.find('.icon-preview').empty();
        container.find('.edit-menu-item-icon, input[name*="distm_featured_icon"]').val('');
    });

    // Handle form submission for featured icon
    $('.tm-wrap form').on('submit', function() {
        $('.featured-icon-wrapper').each(function() {
            const iconType = $(this).find('.icon-type-radio:checked').val();
            const dashiconValue = $(this).find('.selected-dashicon').val();
            
            // Update the hidden fields
            $('input[name="distm_settings[distm_featured_icon_type]"]').val(iconType);
            if (iconType === 'dashicon' && dashiconValue) {
                $('input[name="distm_settings[distm_featured_dashicon]"]').val(dashiconValue);
            }
        });
    });

    // Initialize media uploader for both menu items and featured icon
    initializeMediaUploader('.upload-icon-button, .tm-upload-button');

    // Dashicon search functionality
    $(document).on('input', '.dashicon-search', function() {
        const searchTerm = $(this).val().toLowerCase();
        const container = $(this).closest('.field-custom, .featured-icon-wrapper');
        const icons = container.find('.dashicon-option');
        
        icons.each(function() {
            const iconName = $(this).data('icon').toLowerCase();
            $(this).toggle(iconName.includes(searchTerm));
        });
    });

    // Enhanced form submission for menu items
    $('#update-nav-menu').on('submit', function() {
        $('.menu-item').each(function() {
            const container = $(this).find('.field-custom');
            const menuItemId = $(this).find('.menu-item-data-db-id').val();
            
            if (container.length && menuItemId) {
                const iconUrl = container.find('.edit-menu-item-icon').val();
                const iconType = iconUrl ? 'upload' : 'dashicon';
                
                // Ensure we have an icon type input
                let iconTypeInput = container.find(`input[name="menu-item-icon-type[${menuItemId}]"]`);
                if (!iconTypeInput.length) {
                    container.append(`<input type="hidden" name="menu-item-icon-type[${menuItemId}]" value="${iconType}">`);
                } else {
                    iconTypeInput.val(iconType);
                }
                
                // If it's a dashicon type, ensure we have a dashicon value
                if (iconType === 'dashicon') {
                    const dashicon = container.find('.selected-dashicon').val() || 'menu';
                    container.find(`input[name="menu-item-dashicon[${menuItemId}]"]`).val(dashicon);
                }
                
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    console.log('Menu Item ' + menuItemId + ' submission data:', {
                        iconType: iconType,
                        iconUrl: iconUrl,
                        dashicon: container.find('.selected-dashicon').val()
                    });
                }
            }
        });
    });

    // Mobile menu functionality
    const targetElement = document.querySelector('.tm-scrolling');

    function setOpacity(opacity) {
        if (targetElement) {
            targetElement.style.opacity = opacity;
        }
    }

    let scrollTimeout = null;

    if (targetElement) {
        window.addEventListener('scroll', () => {
            setOpacity(0.2);
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                setOpacity(1);
            }, 150);
        });
    }

    // Addon menu toggle
    const icon = document.querySelector('.tm-featured');
    const menuPopup = document.querySelector('.tm-addon-menu-wrapper');
    const featuredBg = document.querySelector('.tm-featured-bg');

    if (icon && menuPopup) {
        icon.addEventListener('click', function() {
            const isShown = menuPopup.classList.contains('show');
            if (!isShown) {
                menuPopup.classList.add('show');
                menuPopup.style.display = 'flex';
                menuPopup.style.animation = 'slideIn 0.5s forwards';
                icon.classList.add('active');
                if (featuredBg) featuredBg.classList.add('expanded');
            } else {
                menuPopup.style.animation = 'slideOut 0.5s forwards';
                icon.classList.remove('active');
                if (featuredBg) featuredBg.classList.remove('expanded');
                setTimeout(() => {
                    menuPopup.classList.remove('show');
                    menuPopup.style.display = 'none';
                }, 500);
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!icon.contains(event.target) && !menuPopup.contains(event.target) && menuPopup.classList.contains('show')) {
                menuPopup.style.animation = 'slideOut 0.5s forwards';
                icon.classList.remove('active');
                if (featuredBg) featuredBg.classList.remove('expanded');
                setTimeout(() => {
                    menuPopup.classList.remove('show');
                    menuPopup.style.display = 'none';
                }, 500);
            }
        });
    }

    // Page loader
    const links = document.querySelectorAll('.the-menu a');
    links.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') {
                e.preventDefault();
                return;
            }

            var pageLoader = document.getElementById('tm-pageLoader');
            if (pageLoader) {
                pageLoader.style.display = 'block';
                setTimeout(function() {
                    window.location.href = href;
                }, 100);
            }
            e.preventDefault();
        });
    });

    // Initialize icon type states
    function initializeIconTypes() {
        $('.field-custom').each(function() {
            const container = $(this);
            const uploadSection = container.find('.icon-upload-section');
            const dashiconSection = container.find('.dashicon-selection-section');
            const iconUrl = container.find('.edit-menu-item-icon').val();
            
            // If there's an icon URL, show upload section, otherwise show dashicon section
            if (iconUrl) {
                container.find('input[value="upload"]').prop('checked', true);
                uploadSection.show();
                dashiconSection.hide();
            } else {
                container.find('input[value="dashicon"]').prop('checked', true);
                uploadSection.hide();
                dashiconSection.show();
            }
        });
    }

    // Call on document ready
    initializeIconTypes();

    // Update the icon type radio change handler
    $(document).on('change', '.icon-type-radio', function() {
        const container = $(this).closest('.field-custom, .featured-icon-wrapper');
        const uploadSection = container.find('.icon-upload-section');
        const dashiconSection = container.find('.dashicon-selection-section');
        const menuItemId = container.closest('.menu-item').find('.menu-item-data-db-id').val();
        const iconType = $(this).val();
        
        if (iconType === 'upload') {
            uploadSection.slideDown(300);
            dashiconSection.slideUp(300);
            
            // Clear dashicon selection if switching to upload
            if (!container.find('.edit-menu-item-icon').val()) {
                container.find('input[value="dashicon"]').prop('checked', true).trigger('change');
            }
        } else {
            uploadSection.slideUp(300);
            dashiconSection.slideDown(300);
            
            // Ensure a dashicon is selected
            const selectedDashicon = container.find('.selected-dashicon').val() || 'menu';
            container.find('.selected-dashicon').val(selectedDashicon);
            container.find('.dashicon-option').removeClass('selected');
            container.find(`.dashicon-option[data-icon="${selectedDashicon}"]`).addClass('selected');
        }
    }); 

    // Add styles for the dashicon grid
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .dashicon-option {
                padding: 10px;
                font-size: 20px;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-block;
            }
            .dashicon-option:hover {
                background: #f0f0f0;
                transform: scale(1.2);
                border-radius: 10px;
            }
            .dashicon-option.selected {
                background: var(--tm-secondary-color, #2271b1);
                color: white;
                border-radius: 10px;
            }
            .icon-type-label {
                display: inline-block;
                margin-right: 15px;
            }
            .dashicon-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
                gap: 5px;
                padding: 10px;
                border-radius: 10px;
            }
            .dashicon-selection-section {
                background: #fff;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 15px;
            }
            .icon-preview {
                margin: 5px 0;
                min-height: 40px;
            }
            .dashicon-separator {
                grid-column: 1/-1;
                border-bottom: 1px solid #ddd;
                margin: 10px 0;
            }
        `)
        .appendTo('head');
});