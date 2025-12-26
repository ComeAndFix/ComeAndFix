@props(['status', 'size' => 'md'])

@php
    // Normalize status for display
    $displayStatus = $status;
    $statusClass = strtolower(str_replace(' ', '_', $status));
    
    // Map status to appropriate class
    $statusMap = [
        'pending' => 'pending',
        'accepted' => 'accepted',
        'on_progress' => 'on_progress',
        'completed' => 'completed',
        'cancelled' => 'cancelled',
        'rejected' => 'rejected',
        'expired' => 'rejected', // Use same style as rejected
    ];
    
    $badgeClass = $statusMap[$statusClass] ?? 'pending';
    
    // Size classes
    $sizeClasses = [
        'sm' => 'status-badge-sm',
        'md' => '',
        'lg' => 'status-badge-lg',
    ];
    
    $sizeClass = $sizeClasses[$size] ?? '';
@endphp

<span {{ $attributes->merge(['class' => "status-badge {$badgeClass} {$sizeClass}"]) }}>
    {{ ucwords(str_replace('_', ' ', $displayStatus)) }}
</span>
