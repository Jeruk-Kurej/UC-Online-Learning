<x-app-layout bleed>
    @section('title', 'Log in')

    <div class="mx-auto w-full max-w-md px-4 py-12 sm:py-16">
        <h1 class="page-title">Log in</h1>
        <p class="page-lead">Use the account of your UC Online Learning profile.</p>

        <form method="POST" action="{{ route('login') }}" class="card mt-6 space-y-5">
            @csrf

            <x-forms.field look="login" name="email" label="Email">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="field-input @error('email') !border-red-700 @enderror">
            </x-forms.field>

            <x-forms.field look="login" name="password" label="Password">
                <input id="password" type="password" name="password" required autocomplete="current-password" class="field-input @error('password') !border-red-700 @enderror">
            </x-forms.field>

            <button type="submit" class="btn btn-primary w-full">Log in</button>

            <p class="text-center text-sm text-gray-600">or</p>

            <a href="{{ route('auth.google.redirect') }}" class="btn btn-secondary w-full">
                <i class="bi bi-google" aria-hidden="true"></i>
                Log in with Google
            </a>
        </form>

        <p class="mt-6 text-base text-gray-700">
            Only students and alumni in our records can log in. If you cannot, write to
            <a href="mailto:pmb.online@ciputra.ac.id" class="text-link">pmb.online@ciputra.ac.id</a>.
        </p>
    </div>
</x-app-layout>
