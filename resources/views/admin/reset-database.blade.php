<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔥 Reset Database
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Warning Banner --}}
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <div class="flex items-center mb-2">
                            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <h3 class="font-bold text-lg">⚠️ DANGER ZONE</h3>
                        </div>
                        <p class="font-semibold">This action is IRREVERSIBLE and will permanently delete ALL data!</p>
                    </div>

                    {{-- What Will Happen --}}
                    <div class="mb-6">
                        <h3 class="font-bold text-lg mb-3">What will happen:</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <span class="text-red-500 mr-2">❌</span>
                                <span><strong>ALL businesses</strong> will be permanently deleted</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-red-500 mr-2">❌</span>
                                <span><strong>ALL products & services</strong> will be permanently deleted</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-red-500 mr-2">❌</span>
                                <span><strong>ALL users</strong> (including YOU) will be permanently deleted</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✅</span>
                                <span><strong>1 fresh admin account</strong> will be created</span>
                            </li>
                        </ul>
                    </div>

                    {{-- New Admin Credentials --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="font-bold text-lg mb-2">New Admin Credentials:</h3>
                        <div class="font-mono text-sm">
                            <p><strong>Email:</strong> admin@uco.com</p>
                            <p><strong>Password:</strong> password</p>
                        </div>
                    </div>

                    {{-- Confirmation Form --}}
                    <form method="POST" action="{{ route('admin.reset-database.execute') }}" 
                          onsubmit="return confirm('⚠️ ARE YOU ABSOLUTELY SURE?\n\nThis will DELETE EVERYTHING and you will be logged out.\n\nType YES in the next prompt to confirm.');">
                        @csrf
                        
                        <div class="flex gap-4">
                            <button type="submit" 
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                🔥 RESET DATABASE NOW
                            </button>
                            
                            <a href="{{ route('users.index') }}" 
                               class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg text-center transition-colors">
                                ← Cancel & Go Back
                            </a>
                        </div>
                    </form>

                    {{-- Instructions --}}
                    <div class="mt-6 text-sm text-gray-600 bg-gray-50 p-4 rounded">
                        <p class="font-semibold mb-2">⚠️ IMPORTANT:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>You will be <strong>immediately logged out</strong></li>
                            <li>Login again with: <code class="bg-gray-200 px-1">admin@uco.com</code> / <code class="bg-gray-200 px-1">password</code></li>
                            <li><strong>DELETE THIS ROUTE</strong> after use for security!</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
