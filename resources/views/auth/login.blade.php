<x-guest-layout>


    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="username"
                   class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all"
                   placeholder="name@example.com">
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900 transition-colors">
                        Forgot?
                    </a>
                @endif
            </div>
            <input id="password"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password"
                     class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all"
                   placeholder="Enter your password">
            @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input id="remember_me"
                   type="checkbox"
                   name="remember"
                   class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900">
            <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
        </div>

        <button type="submit"
                class="w-full py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
            Sign in
        </button>
    </form>

</x-guest-layout>
