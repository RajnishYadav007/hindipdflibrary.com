<?php

use App\Models\Referral;
use App\Models\User;
use App\Services\Contributor\ContributorRewardService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $ref = '';

    public function mount(): void
    {
        $this->ref = trim((string) request()->query('ref', ''));
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'ref' => ['nullable', 'string', 'max:16'],
        ]);

        $refCode = strtoupper(trim((string) ($validated['ref'] ?? '')));
        unset($validated['ref']);

        $referrer = ! empty($refCode) ? User::where('referral_code', $refCode)->first() : null;
        if ($referrer) {
            $validated['referred_by'] = $referrer->id;
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Ensure user has their own referral code
        app(ContributorRewardService::class)->generateReferralCode($user);

        // Record pending referral if referred by another legitimate contributor
        if ($referrer && $referrer->id !== $user->id) {
            Referral::firstOrCreate(
                ['referred_id' => $user->id],
                [
                    'referrer_id' => $referrer->id,
                    'status' => 'pending',
                ]
            );
        }

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Optional Referral Code -->
        <div class="mt-4">
            <x-input-label for="ref" :value="__('आमंत्रण कोड / Referral Code (वैकल्पिक)')" />
            <x-text-input wire:model="ref" id="ref" class="block mt-1 w-full uppercase tracking-wider font-mono text-sm"
                            type="text"
                            name="ref"
                            placeholder="उदा. HPLABC12" autocomplete="off" />
            <x-input-error :messages="$errors->get('ref')" class="mt-2" />
            @if(!empty($ref))
                <p class="mt-1 text-xs text-emerald-600 font-medium">✓ आमंत्रण कोड संलग्न है</p>
            @endif
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
