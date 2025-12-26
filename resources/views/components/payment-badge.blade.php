@props(['status', 'size' => 'md'])

@php
    // Normalize payment status
    $paymentStatus = strtolower($status);
    
    // Map payment status to appropriate class
    $statusMap = [
        'paid' => 'paid',
        'unpaid' => 'unpaid',
        'pending' => 'pending',
        'failed' => 'failed',
        'refunded' => 'refunded',
    ];
    
    $badgeClass = $statusMap[$paymentStatus] ?? 'unpaid';
    
    // Map payment status to icons
    $iconMap = [
        'paid' => 'bi-check-circle-fill',
        'unpaid' => 'bi-exclamation-circle-fill',
        'pending' => 'bi-clock-fill',
        'failed' => 'bi-x-circle-fill',
        'refunded' => 'bi-arrow-counterclockwise',
    ];
    
    $icon = $iconMap[$paymentStatus] ?? 'bi-exclamation-circle-fill';
    
    // Size classes
    $sizeClasses = [
        'sm' => 'payment-badge-sm',
        'md' => '',
        'lg' => 'payment-badge-lg',
    ];
    
    $sizeClass = $sizeClasses[$size] ?? '';
    
    // Display label
    $displayLabel = ucwords($status);
@endphp

<span {{ $attributes->merge(['class' => "payment-badge {$badgeClass} {$sizeClass}"]) }}>
    <i class="bi {{ $icon }} me-1"></i>{{ $displayLabel }}
</span>
