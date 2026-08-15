<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: [
                            'Inter',
                            'ui-sans-serif',
                            'system-ui',
                            'sans-serif'
                        ],
                    },
                },
            },
        }
    </script>
</head>

<body class="min-h-screen bg-slate-50 font-sans text-slate-900">

    <div class="flex min-h-screen">

        {{-- LEFT SIDE --}}
        <div
            class="relative hidden overflow-hidden bg-slate-900 lg:flex lg:w-1/2"
        >

            {{-- Decorative Background --}}
            <div
                class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-blue-600/20 blur-3xl"
            ></div>

            <div
                class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl"
            ></div>

            <div
                class="relative z-10 flex w-full flex-col justify-between p-12 xl:p-16"
            >

                {{-- Logo --}}
                <div>
                    <a
                        href="{{ route('customer.home') }}"
                        class="inline-flex items-center gap-3"
                    >

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl font-bold text-slate-900 shadow-lg"
                        >
                            {{ strtoupper(substr(config('app.name'), 0, 1)) }}
                        </div>

                        <span class="text-xl font-bold text-white">
                            {{ config('app.name') }}
                        </span>

                    </a>
                </div>


                {{-- Content --}}
                <div class="max-w-lg">

                    <span
                        class="mb-5 inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-300"
                    >
                        Selamat datang kembali
                    </span>

                    <h1
                        class="text-4xl font-bold leading-tight text-white xl:text-5xl"
                    >
                        Belanja lebih mudah,
                        <span class="text-blue-400">
                            kapan saja.
                        </span>
                    </h1>

                    <p
                        class="mt-6 max-w-md text-base leading-7 text-slate-400"
                    >
                        Masuk ke akun Anda untuk melanjutkan pengalaman
                        berbelanja yang lebih mudah dan nyaman.
                    </p>

                </div>


                {{-- Footer --}}
                <div class="text-sm text-slate-500">
                    &copy; {{ date('Y') }}
                    {{ config('app.name') }}.
                    Semua hak dilindungi.
                </div>

            </div>

        </div>


        {{-- RIGHT SIDE --}}
        <div
            class="flex w-full items-center justify-center px-6 py-12 lg:w-1/2 lg:px-12"
        >

            <div class="w-full max-w-md">

                {{-- Mobile Logo --}}
                <div class="mb-10 text-center lg:hidden">

                    <a
                        href="{{ route('customer.home') }}"
                        class="inline-flex items-center gap-3"
                    >

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-xl font-bold text-white"
                        >
                            {{ strtoupper(substr(config('app.name'), 0, 1)) }}
                        </div>

                        <span class="text-xl font-bold text-slate-900">
                            {{ config('app.name') }}
                        </span>

                    </a>

                </div>


                {{-- Header --}}
                <div class="mb-8">

                    <h2
                        class="text-3xl font-bold tracking-tight text-slate-900"
                    >
                        Masuk ke akun
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Masukkan email dan password Anda untuk melanjutkan.
                    </p>

                </div>


                {{-- Success Message --}}
                @if (session('success'))

                    <div
                        class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700"
                    >

                        <svg
                            class="mt-0.5 h-5 w-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>

                        <span>
                            {{ session('success') }}
                        </span>

                    </div>

                @endif


                {{-- General Error --}}
                @if ($errors->any())

                    <div
                        class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4"
                    >

                        <div class="flex items-start gap-3">

                            <svg
                                class="mt-0.5 h-5 w-5 shrink-0 text-red-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>

                            <div class="text-sm text-red-700">

                                @foreach ($errors->all() as $error)

                                    <p>
                                        {{ $error }}
                                    </p>

                                @endforeach

                            </div>

                        </div>

                    </div>

                @endif


                {{-- Login Form --}}
                <form
                    action="{{ route('customer.login.process') }}"
                    method="POST"
                    class="space-y-5"
                >

                    @csrf


                    {{-- Email --}}
                    <div>
                        <label for="login" class="mb-2 block text-sm font-medium text-slate-700">
                            Email atau Nomor WhatsApp
                        </label>
                        <input id="login" type="text" name="login" value="{{ old('login') }}"
                            placeholder="Masukkan email atau nomor WhatsApp"
                            autocomplete="username" required autofocus
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 @error('login') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror">
                        @error('login')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Password --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <label
                                for="password"
                                class="block text-sm font-medium text-slate-700"
                            >
                                Password
                            </label>

                        </div>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Masukkan password Anda"
                            autocomplete="current-password"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                        >

                    </div>


                    {{-- Remember --}}
                    <div class="flex items-center">

                        <label class="flex cursor-pointer items-center gap-3">

                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                {{ old('remember') ? 'checked' : '' }}
                            >

                            <span class="text-sm text-slate-600">
                                Ingat saya
                            </span>

                        </label>

                    </div>


                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-900/10 active:scale-[0.99]"
                    >
                        Masuk
                    </button>

                </form>


                {{-- Register --}}
                <p class="mt-8 text-center text-sm text-slate-500">

                    Belum memiliki akun?

                    <a
                        href="{{ route('customer.register') }}"
                        class="font-semibold text-blue-600 transition hover:text-blue-700"
                    >
                        Daftar sekarang
                    </a>

                </p>


                {{-- Back Home --}}
                <div class="mt-6 text-center">

                    <a
                        href="{{ route('customer.home') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
                    >

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>

                        Kembali ke halaman utama

                    </a>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
```
