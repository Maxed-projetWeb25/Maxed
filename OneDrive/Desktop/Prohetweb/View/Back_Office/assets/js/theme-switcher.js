// Theme switching functionality
function switchTheme(theme) {
    // Remove any existing theme classes
    document.body.classList.remove('theme-dark', 'theme-blue', 'theme-bordered', 'theme-semi-dark');
    
    // Update HTML data attribute for theme
    document.documentElement.setAttribute('data-bs-theme', theme + '-theme');
    
    // Add the selected theme class if not default
    if (theme !== 'default') {
        document.body.classList.add('theme-' + theme);
    }
    
    // Save theme preference
    localStorage.setItem('preferred-theme', theme);
    
    // Update sidebar and header colors
    updateLayoutColors(theme);
    
    // Dispatch custom event for other components
    document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
}

function updateLayoutColors(theme) {
    const sidebar = document.querySelector('.sidebar-wrapper');
    const header = document.querySelector('.top-header');
    
    // Remove existing theme classes
    sidebar?.classList.remove('bg-dark', 'bg-light', 'bg-primary', 'bg-bordered', 'bg-semi-dark');
    header?.classList.remove('bg-dark', 'bg-light', 'bg-primary', 'bg-bordered', 'bg-semi-dark');
    
    // Add new theme classes
    switch(theme) {
        case 'dark':
            sidebar?.classList.add('bg-dark');
            header?.classList.add('bg-dark');
            break;
        case 'blue':
            sidebar?.classList.add('bg-primary');
            header?.classList.add('bg-primary');
            break;
        case 'bordered':
            sidebar?.classList.add('bg-bordered');
            header?.classList.add('bg-bordered');
            break;
        case 'semi-dark':
            sidebar?.classList.add('bg-semi-dark');
            header?.classList.add('bg-semi-dark');
            break;
        default:
            sidebar?.classList.add('bg-light');
            header?.classList.add('bg-light');
    }
}

function updateActiveTheme() {
    // Update dropdown items
    document.querySelectorAll('.theme-option').forEach(item => {
        const itemTheme = item.getAttribute('data-theme');
        item.classList.toggle('active', itemTheme === currentTheme);
        
        // Add checkmark icon to active theme
        if (itemTheme === currentTheme) {
            const icon = item.querySelector('.material-icons-outlined');
            if (icon) {
                icon.textContent = 'check';
            }
        } else {
            const icon = item.querySelector('.material-icons-outlined');
            if (icon) {
                // Restore original icon
                const originalIcon = getThemeIcon(itemTheme);
                icon.textContent = originalIcon;
            }
        }
    });

    // Update customize button text
    const customizeBtn = document.getElementById('themeCustomizeBtn');
    if (customizeBtn) {
        const themeName = currentTheme.charAt(0).toUpperCase() + currentTheme.slice(1);
        customizeBtn.innerHTML = `<i class="material-icons-outlined">palette</i> ${themeName} Theme`;
    }
}

function getThemeIcon(theme) {
    const icons = {
        'default': 'brightness_medium',
        'dark': 'dark_mode',
        'blue': 'water',
        'bordered': 'border_style',
        'semi-dark': 'contrast'
    };
    return icons[theme] || 'palette';
}

// Load saved theme preference
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('preferred-theme') || 'blue';
    switchTheme(savedTheme);

    // Add click listeners to theme options
    document.querySelectorAll('.btn-check').forEach(radio => {
        radio.addEventListener('change', function() {
            const themeId = this.id;
            let themeName = '';
            
            switch(themeId) {
                case 'BlueTheme':
                    themeName = 'blue';
                    break;
                case 'LightTheme':
                    themeName = 'default';
                    break;
                case 'DarkTheme':
                    themeName = 'dark';
                    break;
                case 'SemiDarkTheme':
                    themeName = 'semi-dark';
                    break;
                case 'BorderedTheme':
                    themeName = 'bordered';
                    break;
            }
            
            if (themeName) {
                switchTheme(themeName);
            }
        });
    });

    // Update DataTables styling when theme changes
    document.addEventListener('themeChanged', function(e) {
        const tables = $.fn.dataTable.tables({ visible: true, api: true });
        tables.draw();
    });
});

// Export for use in other scripts
window.themeManager = {
    switchTheme,
    getCurrentTheme: () => currentTheme,
    THEMES
}; 