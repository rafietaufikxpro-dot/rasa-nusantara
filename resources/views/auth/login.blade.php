<x-guest-layout>
    <!-- Left Panel (Hidden on Mobile) -->
    <div class="hidden lg:flex lg:w-[42%] bg-accent flex-col justify-between p-12 text-white">
        <div class="text-2xl font-serif font-medium tracking-tight">Rasa Nusantara</div>
        <div>
            <h1 class="font-serif text-[30px] font-medium leading-tight tracking-tight mb-4">
                Kelola pesanan katering dari seluruh kota, dalam satu dashboard.
            </h1>
            <p class="text-accent-soft text-sm">Dipakai tim operasional di 50 kota.</p>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="w-full lg:w-[58%] flex flex-col justify-center items-center p-6 sm:p-12 bg-main relative">
        <div class="w-full max-w-[360px]">
            <!-- Mobile Logo -->
            <div class="lg:hidden mb-8 text-2xl font-serif font-medium text-accent">Rasa Nusantara</div>

            <div class="mb-8">
                <h2 class="font-serif text-[26px] font-medium text-ink">Masuk</h2>
                <p class="text-muted text-sm mt-1">Silakan masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-ink mb-1">Email</label>
                    <input id="email" class="block w-full border border-border rounded-md px-3 py-2 text-ink bg-main focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1 focus:ring-offset-main transition-colors" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-status-cancel-text text-sm" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-ink mb-1">Kata sandi</label>
                    <input id="password" class="block w-full border border-border rounded-md px-3 py-2 text-ink bg-main focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1 focus:ring-offset-main transition-colors" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-status-cancel-text text-sm" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-border text-accent shadow-sm focus:ring-accent focus:ring-offset-main bg-main" name="remember">
                        <span class="ms-2 text-sm text-muted">Ingat saya</span>
                    </label>

                    <a class="text-sm text-accent hover:text-ink transition-colors focus:outline-none focus:underline" href="#">
                        Lupa kata sandi?
                    </a>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-main focus:ring-accent transition-colors">
                        Masuk
                    </button>
                </div>
                
                <div class="mt-6 text-center text-sm text-muted">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-accent font-medium hover:underline focus:outline-none focus:underline">
                        Daftar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
