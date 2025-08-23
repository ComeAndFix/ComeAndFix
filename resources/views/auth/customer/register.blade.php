<x-guest-layout>
    <h3 class="fw-bold text-center mb-4">{{ __('Customer Registration') }}</h3>

    <form method="POST" action="{{ route('customer.register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('Phone') }}</label>
            <input id="phone" class="form-control" type="text" name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Address -->
        <div class="mb-3">
            <label for="address" class="form-label">{{ __('Address') }}</label>
            <textarea id="address" name="address" class="form-control" required>{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <!-- City -->
        <div class="mb-3">
            <label for="city" class="form-label">{{ __('City') }}</label>
            <input id="city" class="form-control" type="text" name="city" :value="old('city')" required />
            <x-input-error :messages="$errors->get('city')" class="mt-2" />
        </div>

        <!-- Postal Code -->
        <div class="mb-3">
            <label for="postal_code" class="form-label">{{ __('Postal Code') }}</label>
            <input id="postal_code" class="form-control" type="text" name="postal_code" :value="old('postal_code')" required />
            <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                {{ __('Register') }}
            </button>
        </div>

        <div class="text-center mt-3">
            <a class="text-decoration-none" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
        </div>
    </form>
</x-guest-layout>
