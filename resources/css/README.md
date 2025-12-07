# CSS Architecture Documentation

## Overview
This project uses a modular CSS architecture to keep styles organized and maintainable.

## Directory Structure

```
resources/css/
├── app.css                      # Global variables, base styles, Tailwind directives
├── components/                  # Reusable component styles
│   ├── buttons.css             # Button styles (btn-orange, link-orange, etc.)
│   └── forms.css               # Form components (inputs, checkboxes, etc.)
└── auth/                        # Authentication page styles
    └── login.css               # Login page specific styles
```

## File Descriptions

### `app.css` (Global)
- Tailwind CSS directives
- CSS custom properties (variables)
- Global base styles
- Global typography classes

**Contains:**
- Brand colors (--brand-orange, --brand-dark, etc.)
- Base body styles
- Global font families

### `components/buttons.css` (Reusable)
- Button component styles
- Link styles

**Contains:**
- `.btn-orange` - Primary orange button
- `.link-orange` - Orange text links

### `components/forms.css` (Reusable)
- Form input styles
- Checkbox styles
- Input wrappers

**Contains:**
- `.custom-input` - Styled input fields
- `.custom-checkbox` - Styled checkboxes
- `.input-wrapper` - Input container with relative positioning
- `.password-toggle` - Password visibility toggle button
- `.remember-me-label` - Remember me checkbox label

### `auth/login.css` (Page-specific)
- Login page layout
- Login page components

**Contains:**
- `.login-container` - Main login page container
- `.login-image-section` - Left side workshop image
- `.login-form-section` - Right side form area
- `.brand-logo` - Logo component
- `.login-heading` - Large "LOGIN" heading
- `.welcome-text` - "WELCOME BACK!" text
- `.register-link` - Bottom register link
- Responsive styles for mobile

## How to Use

### For Existing Pages
Import the CSS files you need in your Blade layout:

```php
@vite([
    'resources/css/app.css',              // Always include (global styles)
    'resources/css/components/buttons.css', // If you use buttons
    'resources/css/components/forms.css',   // If you use forms
    'resources/css/auth/login.css',        // Page-specific styles
    'resources/js/app.js'
])
```

### For New Pages
1. Create a new CSS file in the appropriate directory:
   - `components/` for reusable components
   - `auth/`, `customer/`, `tukang/` for page-specific styles

2. Import it in your layout file using `@vite()`

3. Use the global CSS variables from `app.css`:
   ```css
   .my-element {
       color: var(--brand-orange);
       border-color: var(--border-gray);
   }
   ```

## Best Practices

1. **Global styles** → `app.css`
2. **Reusable components** → `components/`
3. **Page-specific styles** → Appropriate subdirectory
4. **Always use CSS variables** for colors and common values
5. **Keep files focused** - one purpose per file
6. **Name classes semantically** - describe what it is, not how it looks

## Benefits

✅ **Maintainable** - Easy to find and update styles
✅ **Scalable** - Add new pages without bloating existing files
✅ **Reusable** - Share components across pages
✅ **Performance** - Load only what you need
✅ **Team-friendly** - Reduce merge conflicts
✅ **Clear organization** - Know where everything belongs

## Example: Adding a New Page

To add a customer dashboard page:

1. Create `resources/css/customer/dashboard.css`
2. Add dashboard-specific styles
3. Import in your dashboard layout:
   ```php
   @vite([
       'resources/css/app.css',
       'resources/css/components/buttons.css',
       'resources/css/customer/dashboard.css',
       'resources/js/app.js'
   ])
   ```

## Migration Notes

The login page has been refactored from a single `app.css` file into:
- Global variables → `app.css`
- Buttons → `components/buttons.css`
- Forms → `components/forms.css`
- Login layout → `auth/login.css`

All functionality remains the same, but the code is now better organized!
