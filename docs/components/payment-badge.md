# Payment Badge Component

## Overview
A reusable Blade component for displaying consistent payment status badges across the application.

## Location
`resources/views/components/payment-badge.blade.php`

## Usage

### Basic Usage
```blade
<x-payment-badge :status="$order->payment_status" />
```

### With Size Variants
```blade
<!-- Small -->
<x-payment-badge :status="$order->payment_status" size="sm" />

<!-- Medium (default) -->
<x-payment-badge :status="$order->payment_status" size="md" />

<!-- Large -->
<x-payment-badge :status="$order->payment_status" size="lg" />
```

### With Additional Classes
```blade
<x-payment-badge :status="$order->payment_status" class="mt-2 ms-3" />
```

### With Conditional Display
```blade
@if($order->payment_status)
    <x-payment-badge :status="$order->payment_status" />
@endif
```

## Supported Statuses

| Status | Icon | Color | Background | Use Case |
|--------|------|-------|------------|----------|
| `paid` | ✓ (check-circle-fill) | Green (#2E7D32) | Light Green (#E8F5E9) | Payment completed |
| `unpaid` | ! (exclamation-circle-fill) | Orange (#E65100) | Light Orange (#FFF3E0) | Payment pending |
| `pending` | ⏱ (clock-fill) | Yellow (#F59E0B) | Light Yellow (#FFF9E6) | Payment processing |
| `failed` | ✗ (x-circle-fill) | Red (#C62828) | Light Red (#FFEBEE) | Payment failed |
| `refunded` | ↻ (arrow-counterclockwise) | Blue (#1565C0) | Light Blue (#E3F2FD) | Payment refunded |

## Size Variants

- **Small (`sm`)**: Padding 0.375rem 0.75rem, Font size 0.6875rem
- **Medium (`md`)**: Padding 0.5rem 1rem, Font size 0.75rem (default)
- **Large (`lg`)**: Padding 0.625rem 1.25rem, Font size 0.875rem

## CSS Styling
The component uses styles from `resources/css/components/order-list.css`

## Examples in Use

### Customer Dashboard
```blade
@if($order->payment_status)
    <x-payment-badge :status="$order->payment_status" />
@endif
```

### Tukang Jobs List
```blade
@if($job->payment_status)
    <x-payment-badge :status="$job->payment_status" size="sm" class="mt-2" />
@endif
```

### Order Details Page
```blade
<x-payment-badge :status="$order->payment_status" size="lg" />
```

## Migration Guide

### Before
```blade
@if($order->payment_status === 'paid')
    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 border border-success">Paid</span>
@elseif($order->payment_status === 'unpaid')
    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 border border-warning">Unpaid</span>
@endif
```

### After
```blade
@if($order->payment_status)
    <x-payment-badge :status="$order->payment_status" />
@endif
```

## Benefits

1. **Consistency**: Same look and feel across all pages
2. **Maintainability**: Single source of truth for payment status styling
3. **Flexibility**: Easy to add new payment statuses
4. **Simplicity**: Clean, readable code
5. **Automatic Formatting**: Handles capitalization automatically

## Combining with Status Badge

Payment badges work great alongside status badges:

```blade
<div class="order-badges">
    <x-status-badge :status="$order->status" />
    @if($order->payment_status)
        <x-payment-badge :status="$order->payment_status" />
    @endif
</div>
```

## Color Scheme

The payment badge colors are designed to be intuitive:
- **Green (Paid)**: Success, completed transaction
- **Orange (Unpaid)**: Warning, action needed
- **Yellow (Pending)**: In progress, processing
- **Red (Failed)**: Error, transaction failed
- **Blue (Refunded)**: Information, money returned
