<x-guest-layout>
    <!-- Left Panel (Hidden on Mobile) -->
    <div class="hidden lg:flex lg:w-[42%] bg-accent flex-col justify-between p-12 text-white">
        <div class="text-2xl font-serif font-medium tracking-tight">Rasa Nusantara</div>
        <div>
            <h1 class="font-serif text-[30px] font-medium leading-tight tracking-tight mb-4">
                Bergabung dengan tim operasional Nusantara.
            </h1>
            <p class="text-accent-soft text-sm">Daftar untuk mengelola pesanan katering harian.</p>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="w-full lg:w-[58%] flex flex-col justify-center items-center p-6 sm:p-12 bg-main relative">
        <div class="w-full max-w-[360px]">
            <!-- Mobile Logo -->
            <div class="lg:hidden mb-8 text-2xl font-serif font-medium text-accent">Rasa Nusantara</div>

            <div class="mb-8">
                <h2 class="font-serif text-[26px] font-medium text-ink">Buat akun</h2>
                <p class="text-muted text-sm mt-1">Isi formulir di bawah ini dengan lengkap.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-ink mb-1">Nama lengkap</label>
                    <input id="name" class="block w-full border border-border rounded-md px-3 py-2 text-ink bg-main focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1 focus:ring-offset-main transition-colors" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-status-cancel-text text-sm" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-ink mb-1">Email</label>
                    <input id="email" class="block w-full border border-border rounded-md px-3 py-2 text-ink bg-main focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1 focus:ring-offset-main transition-colors" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-status-cancel-text text-sm" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-ink mb-1">Kata sandi</label>
                    <input id="password" class="block w-full border border-border rounded-md px-3 py-2 text-ink bg-main focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1 focus:ring-offset-main transition-colors" type="password" name="password" required autocomplete="new-password" />
                    <p class="text-[12.5px] text-faint mt-1">Minimal 8 karakter.</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-status-cancel-text text-sm" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-ink mb-1">Konfirmasi kata sandi</label>
                    <input id="password_confirmation" class="block w-full border border-border rounded-md px-3 py-2 text-ink bg-main focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1 focus:ring-offset-main transition-colors" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-status-cancel-text text-sm" />
                </div>


                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-main focus:ring-accent transition-colors">
                        Daftar
                    </button>
                </div>
                
                <div class="mt-6 text-center text-sm text-muted">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-accent font-medium hover:underline focus:outline-none focus:underline">
                        Masuk
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
