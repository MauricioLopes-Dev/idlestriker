<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Idle Strike</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cs-shell min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4 sm:p-6">
    <div class="w-full max-w-md cs-panel cs-panel-strong rounded-[2rem] p-8">
        <div class="mb-6">
            <span class="cs-pill">Access point</span>
            <h1 class="mt-4 text-2xl font-black text-white">Entrar no clube</h1>
            <p class="mt-2 text-sm text-slate-400">Use sua conta para manter o time e o progresso vinculados ao navegador.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-300">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-300" for="email">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="cs-input">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-300" for="password">Senha</label>
                <input id="password" name="password" type="password" required class="cs-input">
            </div>

            <div class="flex items-center justify-between text-sm text-slate-400">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-950">
                    Lembrar de mim
                </label>
                <a href="/" class="text-amber-400 hover:text-amber-300">Voltar ao jogo</a>
            </div>

            <button type="submit" class="cs-button w-full bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950">
                Entrar
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Ainda não tem conta?
            <a href="/register" class="font-semibold text-amber-400 hover:text-amber-300">Criar conta</a>
        </p>
    </div>
</body>
</html>
