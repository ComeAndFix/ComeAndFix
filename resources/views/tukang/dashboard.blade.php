<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tukang Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome, {{ Auth::guard('tukang')->user()->name }}!</h3>
                    <p class="text-green-600 font-semibold">✅ Tukang Login Function is Complete</p>

                    <div class="mt-6 space-y-2">
                        <p><strong>Email:</strong> {{ Auth::guard('tukang')->user()->email }}</p>
                        <p><strong>Phone:</strong> {{ Auth::guard('tukang')->user()->phone }}</p>
                        <p><strong>Specializations:</strong> {{ implode(', ', Auth::guard('tukang')->user()->specializations ?? []) }}</p>
                        <p><strong>Experience:</strong> {{ Auth::guard('tukang')->user()->years_experience ?? 0 }} years</p>
                    </div>

                    <form method="POST" action="{{ route('tukang.logout') }}" class="mt-6">
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
