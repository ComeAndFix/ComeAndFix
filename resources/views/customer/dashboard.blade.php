<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome, {{ Auth::guard('customer')->user()->name }}!</h3>
                    <p class="text-green-600 font-semibold">✅ Customer Login Function is Complete</p>

                    <div class="mt-6 space-y-2">
                        <p><strong>Email:</strong> {{ Auth::guard('customer')->user()->email }}</p>
                        <p><strong>Phone:</strong> {{ Auth::guard('customer')->user()->phone ?? 'Not provided' }}</p>
                        <p><strong>City:</strong> {{ Auth::guard('customer')->user()->city ?? 'Not provided' }}</p>
                    </div>

                    <form method="POST" action="{{ route('customer.logout') }}" class="mt-6">
                        @csrf
                        <x-primary-button>
                            {{ __('Logout') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
