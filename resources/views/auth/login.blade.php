<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-white">Masuk</h1>
        <p class="text-sm text-emerald-100/80 mt-1">Gunakan akun yang sudah disediakan untuk demo.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-sky-500/20 bg-sky-500/10 px-4 py-3 text-sm text-sky-100">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-100">
            {{ $errors->first() }}
        </div>
    @endif

   

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label class="text-emerald-100/90" for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@kampus.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ showPassword: false }">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-2">
                <input id="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       placeholder="••••••••"
                       :type="showPassword ? 'text' : 'password'"
                       class="block w-full border-white/15 bg-white/5 text-white placeholder:text-white/50 focus:border-emerald-400 focus:ring-emerald-400 rounded-xl shadow-sm pr-12" />

                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 inline-flex items-center justify-center text-white/60 hover:text-white transition"
                        @click="showPassword = !showPassword"
                        :aria-label="showPassword ? 'Sembunyikan password' : 'Lihat password'">
                    <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400 focus:ring-offset-0" name="remember">
                <span class="ms-2 text-sm text-emerald-100/80">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between gap-3 mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
