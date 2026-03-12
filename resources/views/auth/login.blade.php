<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - RepairBox</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="mt-4 text-3xl font-extrabold text-gray-900">RepairBox</h2>
            <p class="mt-2 text-sm text-gray-600">Mobile Shop Management System</p>
        </div>

        <div class="card">
            <div class="card-body">

                <form x-data="loginForm()" @submit.prevent="submit()" class="space-y-5">
                    <div x-show="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm" x-text="error"></div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" x-model="email" type="email" required autofocus class="form-input-custom" placeholder="admin@repairbox.com">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" x-model="password" type="password" required class="form-input-custom" placeholder="••••••••">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" x-model="remember" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            Remember me
                        </label>
                    </div>
                    <button type="submit" class="w-full btn-primary py-3 text-base" :disabled="loading">
                        <span x-show="loading" class="spinner mr-2"></span>Sign In
                    </button>
                </form>

                <div class="mt-4 text-center text-xs text-gray-400">
                    Demo: admin@repairbox.com / password
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function loginForm() {
    return {
        email: '', password: '', remember: false, loading: false, error: '',
        async submit() {
            this.error = ''; this.loading = true;
            try {
                const res = await fetch('/login', {
                    method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},
                    body: JSON.stringify({email:this.email,password:this.password,remember:this.remember})
                });
                const data = await res.json();
                if(data.success) { window.location.href = data.redirect || '/dashboard'; }
                else { this.error = data.message || 'Invalid credentials'; this.loading = false; }
            } catch(e) { this.error = 'Login failed. Please try again.'; this.loading = false; }
        }
    };
}
</script>
</body>
</html>
