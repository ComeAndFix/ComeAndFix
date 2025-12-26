<x-app-layout>
    @vite(['resources/css/tukang/finance.css'])
    
    <div class="finance-container">
        <!-- Header -->
        <div class="finance-header">

            <h1 class="page-title">
                <i class="bi bi-wallet2 text-brand-orange me-3"></i>Financial Manager
            </h1>
            <p class="page-subtitle">Manage your wallet and track your income</p>
        </div>

        @if(session('success'))
            <div class="alert-message alert-success">
                <i class="bi bi-check-circle-fill alert-icon"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-message alert-error">
                <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        @endif

        <!-- Wallet Cards Section -->
        <div class="wallet-grid">
            <!-- Available Balance Card -->
            <div class="wallet-card primary">
                <div class="wallet-icon-wrapper">
                    <i class="bi bi-wallet2 wallet-icon"></i>
                </div>
                <p class="wallet-label">Available Balance</p>
                <h2 class="wallet-amount">Rp {{ number_format($walletBalance, 0, ',', '.') }}</h2>
                <button class="withdraw-button" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <i class="bi bi-cash-stack"></i>
                    Withdraw Funds
                </button>
            </div>

            <!-- Total Earnings Card -->
            <div class="wallet-card">
                <div class="wallet-icon-wrapper">
                    <i class="bi bi-graph-up wallet-icon"></i>
                </div>
                <p class="wallet-label">Total Earnings</p>
                <h2 class="wallet-amount">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</h2>
            </div>

            <!-- Total Withdrawn Card -->
            <div class="wallet-card">
                <div class="wallet-icon-wrapper">
                    <i class="bi bi-cash-coin wallet-icon"></i>
                </div>
                <p class="wallet-label">Total Withdrawn</p>
                <h2 class="wallet-amount">Rp {{ number_format($withdrawnAmount, 0, ',', '.') }}</h2>
            </div>
        </div>

        <!-- Monthly Income Breakdown -->
        <div class="breakdown-card">
            <div class="breakdown-header">
                <h2 class="breakdown-title">
                    <i class="bi bi-calendar-month"></i>
                    Monthly Income Breakdown
                </h2>
            </div>
            <div class="breakdown-body">
                @forelse($monthlyBreakdown as $month)
                    <div class="month-item">
                        <div class="month-content">
                            <div class="month-header">
                                <h3 class="month-name">{{ $month['month'] }}</h3>
                                <div class="month-summary">
                                    <h4 class="month-total">Rp {{ number_format($month['total'], 0, ',', '.') }}</h4>
                                    <p class="month-count">{{ $month['count'] }} completed job(s)</p>
                                </div>
                            </div>

                            @if($month['orders']->count() > 0)
                                <div class="orders-table-wrapper">
                                    <table class="orders-table">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Customer</th>
                                                <th>Service</th>
                                                <th>Date</th>
                                                <th>Earnings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($month['orders'] as $order)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('tukang.jobs.show', $order) }}" class="order-link">
                                                            #{{ $order->order_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $order->customer->name }}</td>
                                                    <td>{{ $order->service->name }}</td>
                                                    <td>{{ $order->updated_at->format('d M Y') }}</td>
                                                    <td class="order-earnings">
                                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-icon"></i>
                        <p class="empty-text">No completed orders yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div class="modal fade modal-overlay" id="withdrawModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('tukang.finance.withdraw') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Withdraw Funds</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="info-box">
                            <i class="bi bi-info-circle info-icon"></i>
                            <span>
                                Available Balance: <span class="info-balance">Rp {{ number_format($walletBalance, 0, ',', '.') }}</span>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="amount" class="form-label">Withdrawal Amount</label>
                            <input type="number" class="form-input" id="amount" name="amount" 
                                   min="10000" max="{{ $walletBalance }}" 
                                   placeholder="Enter amount (min. Rp 10.000)" required>
                            <p class="form-hint">Minimum withdrawal: Rp 10.000</p>
                        </div>
                        <div class="quick-amounts">
                            <button type="button" class="quick-amount-btn" onclick="document.getElementById('amount').value = Math.min(50000, {{ $walletBalance }})">Rp 50.000</button>
                            <button type="button" class="quick-amount-btn" onclick="document.getElementById('amount').value = Math.min(100000, {{ $walletBalance }})">Rp 100.000</button>
                            <button type="button" class="quick-amount-btn" onclick="document.getElementById('amount').value = {{ $walletBalance }}">Withdraw All</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-primary">Confirm Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
