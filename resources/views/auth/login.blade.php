<x-guest-layout>
    <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        
        <h2 class="text-center text-2xl font-bold text-gray-900 dark:text-gray-100">
            Selamat Datang Kembali
        </h2>
        <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-4 mb-8">
            Silakan masuk untuk melanjutkan.
        </p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div class="relative">
                <x-input-label for="email" :value="__('Email')" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto" /><br>
                <x-text-input id="email" name="email" type="email" class="peer block w-full" :value="old('email')" required autofocus autocomplete="username" placeholder=" " />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="relative">
                <x-input-label for="password" :value="__('Password')" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-800 px-2 peer-focus:px-2 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto" /><br>
                <x-text-input id="password" name="password" type="password" class="peer block w-full" required autocomplete="current-password" placeholder=" " />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingat saya') }}</span>
                </label>
            </div>

            <div>
                <x-primary-button class="w-full justify-center inline-flex items-center">
                    <span>{{ __('Log in') }}</span>
                    <svg class="ms-2 -me-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </x-primary-button>
            </div>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <!-- 
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white dark:bg-gray-800 px-2 text-gray-500 dark:text-gray-400">Atau masuk dengan</span>
                </div>
                -->
            </div>

            <!--
            <div class="grid grid-cols-1 gap-3">
                <a href="#" class="inline-flex w-full justify-center items-center rounded-md bg-white dark:bg-gray-700 px-4 py-2 text-gray-500 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <svg class="h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M15.841 10.221c0-.743-.065-1.455-.188-2.144H10.22v4.043h3.149c-.14 1.288-.86 2.385-1.926 3.129v2.623h3.385c1.982-1.82 3.123-4.524 3.123-7.651z"/>
                        <path d="M10.22 20c2.724 0 4.994-1.12 6.648-3.033l-3.385-2.623c-.905.61-2.07.97-3.263.97-2.512 0-4.638-1.693-5.397-3.958H1.365v2.712C3.045 17.618 6.36 20 10.22 20z"/>
                        <path d="M4.823 12.043c-.22-.643-.345-1.328-.345-2.043s.125-1.4.345-2.043V5.245H1.365C.503 6.945 0 8.52 0 10s.503 3.055 1.365 4.755l3.458-2.712z"/>
                        <path d="M10.22 3.97c1.472 0 2.805.508 3.845 1.49l2.99-2.99C15.21.95 12.94 0 10.22 0 6.36 0 3.045 2.382 1.365 5.245l3.458 2.712C5.582 5.663 7.708 3.97 10.22 3.97z"/>
                    </svg>
                    <span class="ms-3 text-sm font-semibold leading-6">Google</span>
                </a>
            </div>
        -->
        </form>
    </div>
</x-guest-layout>