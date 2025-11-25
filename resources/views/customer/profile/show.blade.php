<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Profile</h2>
    </x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Profile Information</h5>
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil me-1"></i>Edit Profile
                            </a>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Name:</div>
                            <div class="col-md-8">{{ $customer->name }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Email:</div>
                            <div class="col-md-8">
                                {{ $customer->email }}
                                @if($customer->email_verified_at)
                                    <span class="badge bg-success ms-2">Verified</span>
                                @else
                                    <span class="badge bg-warning ms-2">Unverified</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Phone:</div>
                            <div class="col-md-8">{{ $customer->phone ?? '-' }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Address:</div>
                            <div class="col-md-8">{{ $customer->address ?? '-' }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">City:</div>
                            <div class="col-md-8">{{ $customer->city ?? '-' }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Postal Code:</div>
                            <div class="col-md-8">{{ $customer->postal_code ?? '-' }}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 fw-bold">Member Since:</div>
                            <div class="col-md-8">{{ $customer->created_at->format('F d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
