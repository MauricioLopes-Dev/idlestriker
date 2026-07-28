<!DOCTYPE html>
<html lang="pt-BR" class="bg-slate-950 text-slate-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Idle-Strike Launcher</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-sans cs-shell">
  <div class="relative isolate min-h-screen overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(255,106,0,0.16),_transparent_35%)]"></div>
    <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
      <nav class="mb-8 flex items-center justify-between rounded-full border border-white/10 bg-slate-900/70 px-4 py-3 backdrop-blur">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-full border border-amber-500/40 bg-amber-500/10 font-black text-amber-300">IS</div>
          <div>
            <p class="text-[10px] uppercase tracking-[0.35em] text-slate-500">Idle-Strike</p>
            <p class="text-sm font-semibold text-white">Launcher</p>
          </div>
        </div>
        <div class="flex items-center gap-3 text-sm text-slate-400">
          <span class="rounded-full border border-white/10 bg-slate-950/70 px-3 py-1">Status: online</span>
          <span class="rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-amber-300">Modo competitivo</span>
        </div>
      </nav>

      <main class="grid flex-1 gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="cs-panel cs-panel-strong cs-glow rounded-[2rem] p-8 sm:p-10 lg:p-12">
          <span class="cs-pill">⚡ Launcher principal</span>
          <h1 class="mt-6 text-4xl font-black leading-tight text-white sm:text-5xl">Entre no ambiente de gestão do seu clube e entre no próximo round.</h1>
          <p class="mt-5 max-w-2xl text-base leading-8 text-slate-300">
            Uma experiência profissional, limpa e imersiva para organizar seu elenco, controlar o cofre e abrir o jogo com uma interface pronta para competir.
          </p>

          <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('register') }}" class="cs-button border border-white/10 bg-slate-900/80 text-slate-200">Criar conta</a>
            <a href="{{ route('login') }}" class="cs-button bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950">Fazer login</a>
          </div>

          <div class="mt-8 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-slate-950/65 p-5">
              <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400">Gestão</p>
              <p class="mt-3 text-sm leading-6 text-slate-400">Organize escalações, alternativas e estratégia antes de cada confronto.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/65 p-5">
              <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-400">Financeiro</p>
              <p class="mt-3 text-sm leading-6 text-slate-400">Monitore o cofre do clube para fortalecer a estrutura competitiva.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/65 p-5">
              <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-400">Progresso</p>
              <p class="mt-3 text-sm leading-6 text-slate-400">Construa identidade, reputação e evolução para o seu time.</p>
            </div>
          </div>
        </section>

        <aside class="cs-panel rounded-[2rem] p-8">
          <p class="cs-kicker">Conectar ao cliente</p>
          <h2 class="mt-3 text-3xl font-black text-white">Acesse o launcher com sua conta.</h2>

          @if ($errors->any())
            <div class="mt-5 rounded-2xl border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-300">
              {{ $errors->first() }}
            </div>
          @endif

          <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <div>
              <label for="email" class="mb-2 block text-sm font-medium text-slate-300">E-mail</label>
              <input id="email" name="email" type="email" required value="{{ old('email') }}" class="cs-input" />
            </div>
            <div>
              <label for="password" class="mb-2 block text-sm font-medium text-slate-300">Senha</label>
              <input id="password" name="password" type="password" required class="cs-input" />
            </div>
            <div class="flex items-center justify-between text-sm text-slate-400">
              <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-950" />
                Lembrar de mim
              </label>
              <a href="{{ route('register') }}" class="text-amber-400 hover:text-amber-300">Criar conta</a>
            </div>
            <button type="submit" class="cs-button w-full bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950">
              Entrar no launcher
            </button>
          </form>

          <div class="mt-6 rounded-2xl border border-white/10 bg-slate-950/70 p-5 text-sm text-slate-400">
            <p>Não possui conta?</p>
            <a href="{{ route('register') }}" class="mt-2 inline-flex font-semibold text-amber-300">Criar nova conta</a>
          </div>
        </aside>
      </main>
    </div>
  </div>
</body>
</html>
