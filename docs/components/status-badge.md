# Status Badge Component

## Overview
A reusable Blade component for displaying consistent order status badges across the application.

## Location
`resources/views/components/status-badge.blade.php`

## Usage

### Basic Usage
```blade
<x-status-badge :status="$order->status" />
```

### With Expired Check
```blade
<x-status-badge :status="$order->status === 'pending' && $order->isExpired() ? 'expired' : $order->status" />
```

### With Size Variants
```blade
<!-- Small -->
<x-status-badge :status="$order->status" size="sm" />

<!-- Medium (default) -->
<x-status-badge :status="$order->status" size="md" />

<!-- Large -->
<x-status-badge :status="$order->status" size="lg" />
```

### With Additional Classes
```blade
<x-status-badge :status="$order->status" class="mt-2 ms-3" />
```

## Supported Statuses

| Status | Color | Background |
|--------|-------|------------|
| `pending` | Orange (#E65100) | Light Orange (#FFF3E0) |
| `accepted` | Blue (#1565C0) | Light Blue (#E3F2FD) |
| `on_progress` | Yellow (#F59E0B) | Light Yellow (#FFF9E6) |
| `completed` | Gray (#616161) | Light Gray (#F1F1F1) |
| `cancelled` | Red (#C62828) | Light Red (#FFEBEE) |
| `rejected` | Red (#C62828) | Light Red (#FFEBEE) |
| `expired` | Red (#C62828) | Light Red (#FFEBEE) |

## Size Variants

- **Small (`sm`)**: Padding 0.375rem 0.75rem, Font size 0.6875rem
- **Medium (`md`)**: Padding 0.5rem 1rem, Font size 0.75rem (default)
- **Large (`lg`)**: Padding 0.625rem 1.25rem, Font size 0.875rem

## CSS Styling
The component uses styles from `resources/css/components/order-list.css`

## Examples in Use

### Dashboard
```blade
<x-status-badge :status="$order->status === 'pending' && $order->isExpired() ? 'expired' : $order->status" />
```

### Order List
```blade
<x-status-badge :status="$order->status === 'pending' && $order->isExpired() ? 'expired' : $order->status" class="mt-2" />
```

### Chat Messages
```blade
<x-status-badge :status="$message->order->status" size="sm" />
```

## Migration Guide

### Before
```blade
<span class="status-badge {{ ($order->status === 'pending' && $order->isExpired()) ? 'rejected' : $order->status }}">
    {{ ($order->status === 'pending' && $order->isExpired()) ? 'Expired' : ucwords(str_replace('_', ' ', $order->status)) }}
</span>
```

### After
```blade
<x-status-badge :status="$order->status === 'pending' && $order->isExpired() ? 'expired' : $order->status" />
```

## Benefits

1. **Consistency**: Same look and feel across all pages
2. **Maintainability**: Single source of truth for status styling
3. **Flexibility**: Easy to add new statuses or modify existing ones
4. **Simplicity**: Clean, readable code
5. **Type Safety**: Automatic status mapping and normalization
