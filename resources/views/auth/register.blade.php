<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta - Idle Strike</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cs-shell min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4 sm:p-6">
    <div class="w-full max-w-md cs-panel cs-panel-strong rounded-[2rem] p-8">
        <div class="mb-6">
            <span class="cs-pill">Registration</span>
            <h1 class="mt-4 text-2xl font-black text-white">Criar conta</h1>
            <p class="mt-2 text-sm text-slate-400">Seu novo time padrão já será criado automaticamente.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-300">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/register" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-300" for="name">Nome</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="cs-input">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-300" for="email">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="cs-input">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-300" for="password">Senha</label>
                <input id="password" name="password" type="password" required class="cs-input">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-300" for="password_confirmation">Confirmar senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="cs-input">
            </div>

            <button type="submit" class="cs-button w-full bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950">
                Criar conta
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Já possui conta?
            <a href="/login" class="font-semibold text-amber-400 hover:text-amber-300">Entrar</a>
        </p>
    </div>
</body>
</html>
