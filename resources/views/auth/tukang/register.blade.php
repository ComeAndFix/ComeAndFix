<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Tukang Registration') }}
    </div>

    <form method="POST" action="{{ route('tukang.register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" required />
        </div>

        <div class="mt-4">
            <x-input-label for="address" :value="__('Address')" />
            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" />
        </div>

        <div class="mt-4">
            <x-input-label for="city" :value="__('City')" />
            <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" />
        </div>

        <div class="mt-4">
            <x-input-label for="specializations" :value="__('Specializations')" />
            <div class="mt-2 space-y-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="specializations[]" value="HVAC" class="rounded">
                    <span class="ml-2">HVAC</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="specializations[]" value="Electricity" class="rounded">
                    <span class="ml-2">Electricity</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="specializations[]" value="Plumbing" class="rounded">
                    <span class="ml-2">Plumbing</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="specializations[]" value="Carpentry" class="rounded">
                    <span class="ml-2">Carpentry</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="specializations[]" value="Painting" class="rounded">
                    <span class="ml-2">Painting</span>
                </label>
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="years_experience" :value="__('Years of Experience')" />
            <x-text-input id="years_experience" class="block mt-1 w-full" type="number" name="years_experience" min="0" />
        </div>

        <div class="mt-4">
            <x-input-label for="hourly_rate" :value="__('Hourly Rate (IDR)')" />
            <x-text-input id="hourly_rate" class="block mt-1 w-full" type="number" name="hourly_rate" step="0.01" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('tukang.login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
