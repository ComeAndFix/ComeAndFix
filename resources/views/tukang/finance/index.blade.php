<x-app-layout>
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <a href="{{ route('tukang.dashboard') }}" class="btn btn-outline-secondary mb-2">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
                <h2 class="fw-bold mb-0">Financial Manager</h2>
                <p class="text-muted">Manage your wallet and track your income</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Wallet Section -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white border-0 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-wallet2 display-4 mb-3"></i>
                        <h2 class="fw-bold mb-1">Rp {{ number_format($walletBalance, 0, ',', '.') }}</h2>
                        <p class="mb-3">Available Balance</p>
                        <button class="btn btn-light w-100" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <i class="bi bi-cash-stack me-2"></i>Withdraw Funds
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up text-primary display-4 mb-3"></i>
                        <h2 class="fw-bold mb-1">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</h2>
                        <p class="text-muted mb-0">Total Earnings</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-coin text-warning display-4 mb-3"></i>
                        <h2 class="fw-bold mb-1">Rp {{ number_format($withdrawnAmount, 0, ',', '.') }}</h2>
                        <p class="text-muted mb-0">Total Withdrawn</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Income Breakdown -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-month me-2"></i>
                    Monthly Income Breakdown
                </h5>
            </div>
            <div class="card-body p-0">
                @forelse($monthlyBreakdown as $month)
                    <div class="border-bottom">
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">{{ $month['month'] }}</h6>
                                <div class="text-end">
                                    <h5 class="fw-bold text-success mb-0">Rp {{ number_format($month['total'], 0, ',', '.') }}</h5>
                                    <small class="text-muted">{{ $month['count'] }} completed job(s)</small>
                                </div>
                            </div>

                            @if($month['orders']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order #</th>
                                                <th>Customer</th>
                                                <th>Service</th>
                                                <th>Date</th>
                                                <th class="text-end">Earnings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($month['orders'] as $order)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('tukang.jobs.show', $order) }}" class="text-decoration-none">
                                                            #{{ $order->order_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $order->customer->name }}</td>
                                                    <td>{{ $order->service->name }}</td>
                                                    <td>{{ $order->updated_at->format('d M Y') }}</td>
                                                    <td class="text-end fw-bold text-success">
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
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="text-muted mt-3">No completed orders yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div class="modal fade" id="withdrawModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('tukang.finance.withdraw') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Withdraw Funds</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Available Balance: <strong>Rp {{ number_format($walletBalance, 0, ',', '.') }}</strong>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Withdrawal Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   min="10000" max="{{ $walletBalance }}" 
                                   placeholder="Enter amount (min. Rp 10.000)" required>
                            <div class="form-text">Minimum withdrawal: Rp 10.000</div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('amount').value = Math.min(50000, {{ $walletBalance }})">Rp 50.000</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('amount').value = Math.min(100000, {{ $walletBalance }})">Rp 100.000</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('amount').value = {{ $walletBalance }}">Withdraw All</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Confirm Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
