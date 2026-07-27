<!DOCTYPE html>
<html lang="pt-BR" class="bg-slate-950 text-slate-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $team->team_name }} - Idle-Strike Ultimate Team</title>
  
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex flex-col selection:bg-amber-500 selection:text-black">
<div class="bg-red-900 text-white p-4 font-mono text-xs">
  DEBUG LARAVEL -> Total Titulares: {{ count($roster) }} | Total Reservas: {{ count($bench) }}
</div>
  <!-- BARRA DE NAVEGAÇÃO SUPERIOR -->
  <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
      
      <!-- Logo e Nome do Time -->
      <div class="flex items-center gap-4">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center font-black text-black text-lg shadow-lg shadow-amber-500/20">
          IS
        </div>
        <div class="flex flex-col">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Organização Oficial • Laravel ORM</span>
          <div class="flex items-center gap-2 group">
            <input type="text" id="team-name-input" value="{{ $team->team_name }}" onchange="window.game.updateTeamName(this.value)" class="bg-transparent font-black text-lg text-white border-b border-dashed border-slate-700 focus:border-amber-500 focus:outline-none w-52 transition py-0">
            <span class="text-xs text-slate-500 group-hover:text-amber-400 transition">✏️</span>
          </div>
        </div>
      </div>

      <!-- Caixa de Saldo e Rating -->
      <div class="flex items-center gap-6 bg-slate-950 px-4 py-1.5 rounded-lg border border-slate-800 font-mono text-sm">
        <div>
          <span class="text-[10px] text-slate-500 block uppercase font-sans font-bold">Cofre do Clube</span>
          <span class="text-emerald-400 font-bold text-base" id="money-display">$ {{ number_format($team->money, 2, ',', '.') }}</span>
        </div>
        <div class="border-l border-slate-800 pl-4">
          <span class="text-[10px] text-slate-500 block uppercase font-sans font-bold">Rating Competitivo</span>
          <span class="text-amber-400 font-bold" id="elo-display">{{ $team->elo }} pts</span>
        </div>
      </div>

    </div>
  </header>

  <!-- CORPO PRINCIPAL -->
  <main class="max-w-7xl mx-auto px-4 py-6 flex-1 w-full grid grid-cols-1 lg:grid-cols-4 gap-6">
    
    <!-- COLUNA CENTRAL: LOBBY, ELENCO & CAIXAS (3 Colunas) -->
    <div class="lg:col-span-3 flex flex-col gap-6">
      
      <!-- TELA 1: LOBBY PRINCIPAL -->
      <div id="lobby-screen" class="flex flex-col gap-6">
        
        <!-- Banner de Busca de Partida -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 border border-slate-800 rounded-xl p-8 relative overflow-hidden flex flex-col md:flex-row items-center justify-between shadow-2xl gap-6">
          <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

          <div class="z-10 text-center md:text-left">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
              Servidor Autoritativo Conectado • MR12 Async
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Buscar Confronto Oficial</h2>
            <p class="text-sm text-slate-400 mt-1 max-w-lg">
              Sua escalação disputará rodadas utilizando a economia de partida (Pistol, Eco e Full Buy) com base nos status reais do MySQL.
            </p>
          </div>

          <button onclick="window.game.startMatchmaking()" class="z-10 w-full md:w-auto px-8 py-5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-black text-lg tracking-wider uppercase rounded-xl shadow-xl shadow-amber-500/10 transition transform active:scale-95 flex items-center justify-center gap-3 cursor-pointer border border-amber-400">
            <span>🔍 PROCURAR PARTIDA</span>
          </button>
        </div>

        <!-- SEÇÃO 1: ESQUADRÃO TITULAR -->
        <div>
          <div class="flex items-center justify-between mb-3">
            <div>
              <h3 class="text-xs font-bold tracking-wider text-slate-400 uppercase flex items-center gap-2">
                <span>🛡️ Escalação Titular (Em Campo)</span>
              </h3>
            </div>
            <span class="text-xs font-mono font-bold bg-slate-900 px-3 py-1 rounded border border-slate-800 text-amber-400" id="team-power-display">
              Calculando OVR...
            </span>
          </div>

          <!-- Grid dos 5 Titulares -->
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3" id="team-grid">
            <!-- Gerado via JavaScript -->
          </div>
        </div>

        <!-- SEÇÃO 2: BANCO DE RESERVAS -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-5">
          <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
            <div>
              <h3 class="text-xs font-bold tracking-wider text-amber-400 uppercase flex items-center gap-2">
                <span> Banco de Reservas & Elenco</span>
              </h3>
              <p class="text-xs text-slate-400 mt-0.5">Atletas aguardando oportunidade. Para substituir, clique em <strong class="text-slate-200 font-mono">"🔄 SUB"</strong> na carta de um titular acima.</p>
            </div>
            <span class="text-xs font-mono text-slate-400 font-bold bg-slate-950 px-3 py-1 rounded border border-slate-800" id="bench-count-display">
              0 atletas
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3" id="bench-grid">
            <!-- Gerado via JavaScript -->
          </div>
        </div>

        <!-- SEÇÃO 3: CENTRAL DE OLHEIROS (GACHA DE ATLETAS) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 relative overflow-hidden">
          <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
            <div>
              <h3 class="text-sm font-bold tracking-wider text-white uppercase flex items-center gap-2">
                <span> Central de Olheiros (Contratação de Atletas)</span>
              </h3>
              <p class="text-xs text-slate-400 mt-0.5">Invista o saldo em prospecção de mercado para contratar atletas com Overall (OVR) superior.</p>
            </div>
            <span class="text-xs font-mono text-emerald-400 font-bold bg-slate-950 px-3 py-1.5 rounded border border-slate-800">
              RNG Autoritativo no MySQL
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 font-mono">
            <div class="bg-slate-950 border border-slate-800 hover:border-slate-700 p-4 rounded-xl flex flex-col justify-between transition group">
              <div>
                <div class="text-xs font-bold text-slate-300 uppercase flex justify-between">
                  <span>Peneira Local</span>
                  <span class="text-amber-500 font-bold">$ 250</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-2 space-y-1 font-sans">
                  <div>• 85% chance de Tier 4 (Amador)</div>
                  <div>• 14% chance de Tier 3 (Challenger)</div>
                  <div class="text-slate-400 font-semibold">• 1% chance de Tier 2 (Pro League)</div>
                </div>
              </div>
              <button onclick="window.game.buyScout('local')" class="mt-4 w-full py-2 bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 group-hover:border-slate-500 rounded font-bold text-xs transition uppercase cursor-pointer">
                Contratar ($ 250)
              </button>
            </div>

            <div class="bg-slate-950 border border-blue-500/30 hover:border-blue-500/60 p-4 rounded-xl flex flex-col justify-between transition group relative overflow-hidden">
              <div class="absolute top-0 right-0 w-16 h-16 bg-blue-500/5 rounded-full blur-xl pointer-events-none"></div>
              <div>
                <div class="text-xs font-bold text-blue-400 uppercase flex justify-between">
                  <span>Internacional</span>
                  <span class="text-blue-400 font-bold">$ 2.500</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-2 space-y-1 font-sans">
                  <div>• 75% chance de Tier 3 (Challenger)</div>
                  <div>• 23% chance de Tier 2 (Pro League)</div>
                  <div class="text-amber-400 font-bold">• 2% chance de Tier 1 (Lenda Global)</div>
                </div>
              </div>
              <button onclick="window.game.buyScout('international')" class="mt-4 w-full py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 group-hover:border-blue-400 rounded font-bold text-xs transition uppercase cursor-pointer">
                Contratar ($ 2.500)
              </button>
            </div>

            <div class="bg-gradient-to-b from-slate-950 to-slate-900 border border-amber-500/40 hover:border-amber-500 p-4 rounded-xl flex flex-col justify-between transition group relative overflow-hidden shadow-lg shadow-amber-500/5">
              <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>
              <div>
                <div class="text-xs font-bold text-amber-400 uppercase flex justify-between">
                  <span>Contrato de Major</span>
                  <span class="text-amber-400 font-bold">$ 15.000</span>
                </div>
                <div class="text-[11px] text-slate-300 mt-2 space-y-1 font-sans">
                  <div>• 85% chance de Tier 2 (Pro League)</div>
                  <div class="text-amber-400 font-bold animate-pulse">• 15% chance de Tier 1 (Lenda)</div>
                  <div class="text-[10px] text-slate-500 mt-1 italic">Alta chance de dropar FalleN ou s1mple!</div>
                </div>
              </div>
              <button onclick="window.game.buyScout('major')" class="mt-4 w-full py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black border border-amber-400 rounded font-black text-xs transition uppercase cursor-pointer shadow">
                Contratar ($ 15.000)
              </button>
            </div>
          </div>
        </div>

        <!-- SEÇÃO 4: ABERTURA DE CAIXAS DE ARMAS (SKINS GACHA) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 relative overflow-hidden">
          <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
            <div>
              <h3 class="text-sm font-bold tracking-wider text-white uppercase flex items-center gap-2">
                <span>📦 Suprimentos & Abertura de Caixas de Armas</span>
              </h3>
              <p class="text-xs text-slate-400 mt-0.5">Abra caixas para dropar Skins de armas com Float de desgaste e StatTrak™. Elas multiplicam o OVR dos atletas!</p>
            </div>
            <span class="text-xs font-mono text-purple-400 font-bold bg-slate-950 px-3 py-1.5 rounded border border-slate-800">
              Multiplicadores de DPS
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 font-mono">
            
            <!-- 1. Caixa Mil-Spec Básica -->
            <div class="bg-slate-950 border border-blue-500/30 hover:border-blue-500 p-4 rounded-xl flex flex-col justify-between transition group relative">
              <div>
                <div class="text-xs font-bold text-blue-400 uppercase flex justify-between">
                  <span>Caixa Mil-Spec Básica</span>
                  <span class="text-amber-500 font-bold">$ 500</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-2 space-y-1 font-sans">
                  <div class="text-blue-400 font-semibold">• 79% Azul (Mil-Spec)</div>
                  <div class="text-purple-400 font-semibold">• 17% Roxo (Restricted)</div>
                  <div class="text-pink-400 font-semibold">• 3% Rosa | 1% Vermelho</div>
                </div>
              </div>
              <button onclick="window.game.buyCase('basic')" class="mt-4 w-full py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 rounded font-bold text-xs transition uppercase cursor-pointer">
                Abrir Caixa ($ 500)
              </button>
            </div>

            <!-- 2. Caixa Operação Elite -->
            <div class="bg-slate-950 border border-purple-500/30 hover:border-purple-500 p-4 rounded-xl flex flex-col justify-between transition group relative overflow-hidden">
              <div class="absolute top-0 right-0 w-16 h-16 bg-purple-500/5 rounded-full blur-xl pointer-events-none"></div>
              <div>
                <div class="text-xs font-bold text-purple-400 uppercase flex justify-between">
                  <span>Caixa Operação Elite</span>
                  <span class="text-purple-400 font-bold">$ 3.500</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-2 space-y-1 font-sans">
                  <div class="text-purple-400 font-semibold">• 50% Roxo (Restricted)</div>
                  <div class="text-pink-400 font-semibold">• 19% Rosa (Classified)</div>
                  <div class="text-red-400 font-bold">• 5% Vermelho (Covert)</div>
                </div>
              </div>
              <button onclick="window.game.buyCase('operation')" class="mt-4 w-full py-2 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/40 rounded font-bold text-xs transition uppercase cursor-pointer">
                Abrir Caixa ($ 3.500)
              </button>
            </div>

            <!-- 3. Caixa Contrabando & Facas -->
            <div class="bg-gradient-to-b from-slate-950 to-slate-900 border border-red-500/40 hover:border-red-500 p-4 rounded-xl flex flex-col justify-between transition group relative overflow-hidden shadow-lg shadow-red-500/5">
              <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-500/10 rounded-full blur-2xl pointer-events-none"></div>
              <div>
                <div class="text-xs font-bold text-red-400 uppercase flex justify-between">
                  <span>Contrabando & Facas</span>
                  <span class="text-amber-400 font-bold">$ 25.000</span>
                </div>
                <div class="text-[11px] text-slate-300 mt-2 space-y-1 font-sans">
                  <div class="text-pink-400 font-semibold">• 60% Rosa | 20% Vermelho</div>
                  <div class="text-amber-400 font-bold animate-pulse">• 5% Dourado (Faca / D-Lore!)</div>
                  <div class="text-[10px] text-slate-500 mt-1 italic">+300% a +500% Bônus de OVR!</div>
                </div>
              </div>
              <button onclick="window.game.buyCase('covert')" class="mt-4 w-full py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white border border-red-400 rounded font-black text-xs transition uppercase cursor-pointer shadow">
                Abrir Contrabando ($ 25k)
              </button>
            </div>

          </div>
        </div>

      </div>

      <!-- TELA 2: SIMULADOR DE PARTIDA CS2 -->
      <div id="match-screen" class="hidden bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-2xl flex flex-col gap-6">
        
        <div class="flex flex-col md:flex-row items-center justify-between border-b border-slate-800 pb-6 gap-4">
          <div class="flex items-center gap-3 text-center md:text-left">
            <div class="w-12 h-12 rounded-lg bg-blue-600/20 border border-blue-500/40 flex items-center justify-center font-black text-blue-400 text-xl">CT</div>
            <div>
              <div class="text-xs font-bold text-blue-400 uppercase tracking-widest">Seu Clube</div>
              <div class="font-black text-xl text-white" id="match-my-name">{{ $team->team_name }}</div>
              <div class="text-xs text-emerald-400 font-mono mt-0.5" id="match-my-econ">Economia: $ 800 (Pistol)</div>
            </div>
          </div>

          <div class="flex items-center gap-6 bg-slate-950 px-8 py-4 rounded-xl border border-slate-800 shadow-inner">
            <span class="text-4xl font-black text-blue-400 font-mono" id="score-my">0</span>
            <div class="text-center">
              <span class="text-[10px] font-bold text-slate-500 uppercase block">Rodada</span>
              <span class="text-lg font-black text-amber-500 font-mono" id="match-round">1 / 15</span>
            </div>
            <span class="text-4xl font-black text-amber-500 font-mono" id="score-enemy">0</span>
          </div>

          <div class="flex items-center gap-3 text-center md:text-right flex-row-reverse md:flex-row">
            <div>
              <div class="text-xs font-bold text-amber-500 uppercase tracking-widest">Adversário (Async)</div>
              <div class="font-black text-xl text-white" id="enemy-team-name">Challenger Team</div>
              <div class="text-xs text-emerald-400 font-mono mt-0.5" id="match-enemy-econ">Economia: $ 800 (Pistol)</div>
            </div>
            <div class="w-12 h-12 rounded-lg bg-amber-600/20 border border-amber-500/40 flex items-center justify-center font-black text-amber-500 text-xl">TR</div>
          </div>
        </div>

        <div class="bg-slate-950 rounded-lg p-4 border border-slate-800 font-mono text-xs h-72 overflow-y-auto flex flex-col gap-2.5" id="match-log">
          <div class="text-slate-500 text-center py-8">Conectando ao servidor...</div>
        </div>

        <div class="flex justify-between items-center pt-2">
          <span class="text-xs text-slate-400 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> Simulando armamentos, economia e embates táticos...
          </span>
          <button id="btn-return-lobby" onclick="window.game.returnToLobby()" class="hidden px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg text-xs uppercase tracking-wider transition cursor-pointer border border-slate-600">
            🚪 Voltar ao Lobby e Coletar Pagamento
          </button>
        </div>

      </div>

    </div>

    <!-- COLUNA DA DIREITA: ESTATÍSTICAS E RANKING -->
    <div class="flex flex-col gap-6">
      
      <!-- Card do Clube -->
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-800">
          <div class="w-12 h-12 rounded-lg bg-gradient-to-tr from-slate-800 to-slate-700 border border-slate-600 flex items-center justify-center text-2xl shadow">🥉</div>
          <div>
            <div class="font-bold text-white leading-tight" id="sidebar-team-name">{{ $team->team_name }}</div>
            <div class="text-xs text-amber-500 font-semibold mt-0.5">Tier 4 • Divisão Amadora</div>
          </div>
        </div>

        <div class="space-y-2 text-xs">
          <div class="flex justify-between py-1 border-b border-slate-800/50">
            <span class="text-slate-400">Partidas Jogadas</span>
            <span class="font-mono font-bold text-slate-200" id="stat-matches">{{ $team->matches_played }}</span>
          </div>
          <div class="flex justify-between py-1 border-b border-slate-800/50">
            <span class="text-slate-400">Vitórias / Derrotas</span>
            <span class="font-mono font-bold text-slate-200"><span id="stat-wins" class="text-emerald-400">{{ $team->wins }}</span> / <span id="stat-losses" class="text-red-400">{{ $team->losses }}</span></span>
          </div>
          <div class="flex justify-between py-1 border-b border-slate-800/50">
            <span class="text-slate-400">Taxa de Vitória</span>
            @php $winrate = $team->matches_played > 0 ? round(($team->wins / $team->matches_played) * 100) : 0; @endphp
            <span class="font-mono font-bold text-amber-400" id="stat-winrate">{{ $winrate }}%</span>
          </div>
          <div class="flex justify-between py-1">
            <span class="text-slate-400">Skins no Inventário</span>
            <span class="font-mono font-bold text-purple-400" id="stat-skins">{{ count($inventory) }} itens</span>
          </div>
        </div>
      </div>

      <!-- Ranking Global -->
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex-1 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800">
            <h3 class="font-bold text-sm tracking-wider uppercase text-slate-300">🏆 Top Clubes (Async)</h3>
            <span class="text-[10px] font-mono text-slate-500">GLOBAL</span>
          </div>

          <div class="space-y-2.5 font-mono text-xs">
            <div class="flex justify-between p-2 rounded bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold"><span>1. FURIA_Parody</span><span>2.450 pts</span></div>
            <div class="flex justify-between p-2 rounded bg-slate-950/50 border border-slate-800/80 text-slate-300"><span>2. NaVi_Idle</span><span>2.310 pts</span></div>
            <div class="flex justify-between p-2 rounded bg-slate-950/50 border border-slate-800/80 text-slate-300"><span>3. G2_Clickers</span><span>2.100 pts</span></div>
            <div class="flex justify-between p-2 rounded bg-slate-950/50 border border-slate-800/80 text-slate-400"><span>4. MIBR_Base</span><span>1.850 pts</span></div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800 text-center">
          <span class="text-[11px] text-slate-500">Caixas Conectadas! Abra suprimentos para buffar o esquadrão.</span>
        </div>
      </div>

    </div>

    <!-- MODAL 1: CONFIGURAÇÃO DE SETUPS TÁTICOS -->
    <div id="tactical-modal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-5">
          <div>
            <span class="text-[10px] font-mono text-amber-500 uppercase tracking-widest font-bold">Armazenamento do Arsenal</span>
            <h3 class="text-xl font-black text-white" id="modal-player-name">Configurar Operador</h3>
            <p class="text-xs text-slate-400">Defina quais armas base este atleta utilizará em cada momento do jogo.</p>
          </div>
          <button onclick="window.game.closeTacticalModal()" class="text-slate-400 hover:text-white text-xl font-bold p-1 transition cursor-pointer">✕</button>
        </div>

        <form id="setup-form" onsubmit="window.game.saveTacticalSetup(event)" class="space-y-4 font-mono text-xs">
          <input type="hidden" id="modal-player-id">
          <input type="hidden" id="modal-player-index">

          <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800/80">
            <label class="block font-bold text-amber-400 mb-1.5 uppercase tracking-wider">🔫 Pistol Round ($800):</label>
            <select id="select-pistol" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-slate-200 focus:border-amber-500 focus:outline-none transition cursor-pointer"></select>
          </div>

          <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800/80">
            <label class="block font-bold text-blue-400 mb-1.5 uppercase tracking-wider">💵 Eco / Force Buy ($1.500+):</label>
            <select id="select-eco" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-slate-200 focus:border-blue-500 focus:outline-none transition cursor-pointer"></select>
          </div>

          <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800/80">
            <label class="block font-bold text-emerald-400 mb-1.5 uppercase tracking-wider">💎 Full Buy / Armado ($3.500+):</label>
            <select id="select-full" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-slate-200 focus:border-emerald-500 focus:outline-none transition cursor-pointer"></select>
          </div>

          <div class="pt-4 border-t border-slate-800 flex justify-end gap-3 font-sans">
            <button type="button" onclick="window.game.closeTacticalModal()" class="px-5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition cursor-pointer">Cancelar</button>
            <button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-black text-xs uppercase tracking-wider shadow-lg shadow-amber-500/10 transition cursor-pointer">Salvar Setup</button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL 2: SUBSTITUIÇÃO DE JOGADORES (TITULAR <-> RESERVA) -->
    <div id="swap-modal" class="hidden fixed inset-0 z-50 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-amber-500/50 rounded-2xl max-w-md w-full p-6 shadow-2xl relative animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
          <div>
            <span class="text-[10px] font-mono text-amber-500 uppercase tracking-widest font-bold">Gestão de Elenco</span>
            <h3 class="text-xl font-black text-white" id="swap-starter-name">Substituir Operador</h3>
            <p class="text-xs text-slate-400">Escolha quem do banco de reservas assumirá esta titularidade.</p>
          </div>
          <button onclick="window.game.closeSwapModal()" class="text-slate-400 hover:text-white text-xl font-bold p-1 transition cursor-pointer">✕</button>
        </div>

        <input type="hidden" id="swap-starter-id">

        <div id="swap-bench-list" class="space-y-2.5 max-h-64 overflow-y-auto pr-1"></div>

        <div class="mt-5 pt-4 border-t border-slate-800 text-right">
          <button onclick="window.game.closeSwapModal()" class="px-5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition cursor-pointer">Fechar</button>
        </div>
      </div>
    </div>

    <!-- MODAL 3: REVELAÇÃO DO GACHA DE ATLETAS -->
    <div id="gacha-modal" class="hidden fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border-2 border-amber-500 rounded-3xl max-w-sm w-full p-8 text-center shadow-[0_0_50px_rgba(245,158,11,0.2)] relative animate-bounce-once">
        <div class="text-[10px] font-mono font-bold text-amber-500 uppercase tracking-widest mb-1">📋 Relatório de Contratação</div>
        <h3 class="text-xl font-black text-white mb-6">Novo Atleta no Clube!</h3>

        <div id="gacha-card-container" class="bg-gradient-to-b from-slate-800 to-slate-950 border-2 border-amber-400 rounded-2xl p-6 shadow-2xl mx-auto max-w-[220px] transform transition hover:scale-105">
          <div class="flex justify-between items-start mb-2">
            <span id="gacha-tier" class="text-[9px] font-mono font-bold px-2 py-0.5 rounded bg-slate-900 text-amber-400 border border-amber-500/30">Tier 1</span>
            <span id="gacha-ovr" class="text-2xl font-black font-mono text-white bg-slate-900 px-2.5 py-0.5 rounded border border-slate-700">94</span>
          </div>
          <div class="my-4">
            <div id="gacha-role" class="text-xs font-bold text-amber-500 uppercase tracking-wider">IGL • Capitão</div>
            <div id="gacha-name" class="font-black text-xl text-white mt-1 leading-tight">FalleN</div>
          </div>
          <div class="text-[10px] text-emerald-400 font-mono font-bold bg-slate-950 p-1.5 rounded border border-slate-800">
            Enviado para o Banco de Reservas
          </div>
        </div>

        <button onclick="window.game.closeGachaModal()" class="mt-8 w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-black text-sm uppercase tracking-wider rounded-xl shadow-lg shadow-amber-500/10 transition cursor-pointer border border-amber-400">
          ✨ Continuar Gerenciando
        </button>
      </div>
    </div>

    <!-- MODAL 4: REVELAÇÃO DO GACHA DE ARMAS (SKINS) -->
    <div id="case-reveal-modal" class="hidden fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border-2 border-purple-500 rounded-3xl max-w-md w-full p-8 text-center shadow-[0_0_50px_rgba(168,85,247,0.2)] relative animate-bounce-once">
        <div class="text-[10px] font-mono font-bold text-purple-400 uppercase tracking-widest mb-1">📦 Relatório de Suprimentos</div>
        <h3 class="text-xl font-black text-white mb-6">Nova Skin Desbloqueada!</h3>

        <!-- CARTA DA SKIN DROPADA -->
        <div id="case-card-container" class="bg-gradient-to-b from-slate-800 to-slate-950 border-2 border-purple-500 rounded-2xl p-6 shadow-2xl mx-auto max-w-[260px] transform transition hover:scale-105">
          <div class="flex justify-between items-center mb-3">
            <span id="case-rarity" class="text-[9px] font-mono font-bold px-2 py-0.5 rounded bg-purple-950 text-purple-300 border border-purple-500/40">Restricted</span>
            <span id="case-stattrak" class="hidden text-[9px] font-mono font-black px-1.5 py-0.5 rounded bg-orange-600 text-white animate-pulse">STATTRAK™</span>
          </div>
          
          <div class="my-5">
            <div id="case-weapon" class="text-sm font-bold text-slate-400 uppercase tracking-wider">AK-47</div>
            <div id="case-skin-name" class="font-black text-2xl text-white mt-0.5 leading-tight">Redline</div>
          </div>

          <div class="space-y-1.5 font-mono text-[11px] bg-slate-950 p-2.5 rounded-xl border border-slate-800 text-left">
            <div class="flex justify-between"><span class="text-slate-500">Desgaste:</span> <span id="case-wear" class="text-slate-300 font-bold">Field-Tested</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Float:</span> <span id="case-float" class="text-slate-400">0.2451</span></div>
            <div class="flex justify-between pt-1 border-t border-slate-850"><span class="text-purple-400 font-bold">Bônus OVR:</span> <span id="case-buff" class="text-emerald-400 font-black text-xs">+75% DPS</span></div>
          </div>
        </div>

        <button onclick="window.game.closeCaseModal()" class="mt-8 w-full py-3.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-white font-black text-sm uppercase tracking-wider rounded-xl shadow-lg shadow-purple-500/10 transition cursor-pointer border border-purple-400">
          ✨ Guardar no Inventário
        </button>
      </div>
    </div>
    
  </main>

  <script>
    class TacticalManagerEngine {
    constructor() {
        const serverTeam = @json($team);
        const serverRoster = @json($roster);
        const serverBench = @json($bench);
        const serverInventory = @json($inventory);

        this.state = {
          teamName: serverTeam.team_name,
          money: parseFloat(serverTeam.money),
          elo: parseInt(serverTeam.elo),
          matches: parseInt(serverTeam.matches_played),
          wins: parseInt(serverTeam.wins),
          losses: parseInt(serverTeam.losses),
          skinsCount: serverInventory ? serverInventory.length : 0,
          
          // Garante que se o serverRoster vier vazio, o array não quebra
          team: serverRoster ? serverRoster.map(item => this.mapPlayerData(item)) : [],
          bench: serverBench ? serverBench.map(item => this.mapPlayerData(item)) : []
        };

        this.init();
      }

      mapPlayerData(item) {
        return {
          id: item.id,
          role: item.catalog ? item.catalog.role : 'Operador',
          name: item.catalog ? item.catalog.name : 'Recruta',
          baseOvr: item.catalog ? parseInt(item.catalog.base_ovr) : 50,
          tier: item.catalog ? item.catalog.tier : 'Tier 4 (Base)',
          setups: item.tactical_setups || {
            pistol: { weapon: 'Glock-18', skin: 'Padrão', buff: 0.0, mult: 1.0 },
            eco:    { weapon: 'MAC-10',   skin: 'Padrão', buff: 0.0, mult: 1.3 },
            full:   { weapon: 'AK-47',    skin: 'Padrão', buff: 0.0, mult: 2.0 }
          }
        };
      }

      init() {
        this.renderTeam();
        this.renderBench();
        this.updateUI();
        this.populateModalSelects();
      }

      populateModalSelects() {
        const createOptions = (list) => list.map(w => `<option value="${w}">${w}</option>`).join('');
        document.getElementById('select-pistol').innerHTML = createOptions(this.weaponCatalog.pistols);
        document.getElementById('select-eco').innerHTML = createOptions(this.weaponCatalog.eco);
        document.getElementById('select-full').innerHTML = createOptions(this.weaponCatalog.rifles);
      }

      // --- ABERTURA DE CAIXAS DE ARMAS (SKINS GACHA) ---

      async buyCase(caseType) {
        const costs = { basic: 500, operation: 3500, covert: 25000 };
        if (this.state.money < costs[caseType]) {
          alert(`Saldo insuficiente! Você precisa de $ ${costs[caseType].toLocaleString('pt-BR')} para abrir esta caixa.`);
          return;
        }

        try {
          const response = await fetch('/game/case/open', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ case_type: caseType })
          });

          const data = await response.json();

          if (data.success) {
            this.state.money = parseFloat(data.new_money);
            this.state.skinsCount += 1;
            this.updateUI();
            
            // Anima o drop da skin na tela
            this.showCaseReveal(data.dropped_skin);
          } else {
            alert(data.error || "Erro ao abrir caixa de armas.");
          }
        } catch (error) {
          console.error("Erro na requisição de abertura de caixa:", error);
          alert("Erro de conexão com o servidor ao abrir caixa.");
        }
      }

      showCaseReveal(skin) {
        document.getElementById('case-weapon').textContent = skin.weapon_name;
        document.getElementById('case-skin-name').textContent = skin.skin_name;
        document.getElementById('case-rarity').textContent = skin.rarity;
        document.getElementById('case-float').textContent = parseFloat(skin.float_value).toFixed(4);
        document.getElementById('case-buff').textContent = `+${Math.round(skin.buff_multiplier * 100)}% DPS`;

        // Indicador StatTrak
        const stBadge = document.getElementById('case-stattrak');
        if (skin.is_stattrak) stBadge.classList.remove('hidden');
        else stBadge.classList.add('hidden');

        // Tradução de Desgaste (Float Wear)
        let wearText = "Battle-Scarred";
        if (skin.float_value <= 0.07) wearText = "Factory New 🌟";
        elseif (skin.float_value <= 0.15) wearText = "Minimal Wear ✨";
        elseif (skin.float_value <= 0.38) wearText = "Field-Tested";
        elseif (skin.float_value <= 0.45) wearText = "Well-Worn";
        document.getElementById('case-wear').textContent = wearText;

        // Customização da cor da borda de raridade
        const container = document.getElementById('case-card-container');
        const rarityBadge = document.getElementById('case-rarity');
        
        if (skin.rarity === 'Rare Special') {
          container.className = "bg-gradient-to-b from-amber-950 to-slate-950 border-2 border-amber-400 rounded-2xl p-6 shadow-[0_0_30px_rgba(245,158,11,0.4)] mx-auto max-w-[260px] transform transition scale-110 animate-pulse";
          rarityBadge.className = "text-[9px] font-mono font-bold px-2 py-0.5 rounded bg-amber-950 text-amber-300 border border-amber-500/40";
        } else if (skin.rarity === 'Covert') {
          container.className = "bg-gradient-to-b from-red-950 to-slate-950 border-2 border-red-500 rounded-2xl p-6 shadow-2xl mx-auto max-w-[260px] transform transition scale-105";
          rarityBadge.className = "text-[9px] font-mono font-bold px-2 py-0.5 rounded bg-red-950 text-red-300 border border-red-500/40";
        } else if (skin.rarity === 'Classified') {
          container.className = "bg-gradient-to-b from-pink-950 to-slate-950 border-2 border-pink-500 rounded-2xl p-6 shadow-2xl mx-auto max-w-[260px]";
          rarityBadge.className = "text-[9px] font-mono font-bold px-2 py-0.5 rounded bg-pink-950 text-pink-300 border border-pink-500/40";
        } else if (skin.rarity === 'Restricted') {
          container.className = "bg-gradient-to-b from-purple-950 to-slate-950 border-2 border-purple-500 rounded-2xl p-6 shadow-2xl mx-auto max-w-[260px]";
          rarityBadge.className = "text-[9px] font-mono font-bold px-2 py-0.5 rounded bg-purple-950 text-purple-300 border border-purple-500/40";
        } else {
          container.className = "bg-gradient-to-b from-blue-950 to-slate-950 border-2 border-blue-500 rounded-2xl p-6 shadow-2xl mx-auto max-w-[260px]";
          rarityBadge.className = "text-[9px] font-mono font-bold px-2 py-0.5 rounded bg-blue-950 text-blue-300 border border-blue-500/40";
        }

        document.getElementById('case-reveal-modal').classList.remove('hidden');
      }

      closeCaseModal() {
        document.getElementById('case-reveal-modal').classList.add('hidden');
      }

      // --- CONTROLE DO MODAL DE SUB E GESTÃO DE ELENCO ---

      openSwapModal(starterIndex) {
        const starter = this.state.team[starterIndex];
        document.getElementById('swap-starter-id').value = starter.id;
        document.getElementById('swap-starter-name').textContent = `Substituir ${starter.name} (${starter.role})`;
        
        const listContainer = document.getElementById('swap-bench-list');
        listContainer.innerHTML = '';

        if (this.state.bench.length === 0) {
          listContainer.innerHTML = `<div class="text-center py-6 text-slate-500 font-mono text-xs">Seu banco de reservas está vazio!<br>Use a Central de Olheiros abaixo para contratar atletas.</div>`;
        } else {
          this.state.bench.forEach((reserve) => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-amber-500/50 transition font-mono text-xs';
            row.innerHTML = `
              <div>
                <div class="flex items-center gap-2">
                  <span class="font-black text-white text-sm">${reserve.name}</span>
                  <span class="bg-slate-800 text-amber-400 px-1.5 py-0.5 rounded text-[10px] font-bold">${reserve.baseOvr} OVR</span>
                </div>
                <div class="text-[10px] text-slate-500 uppercase mt-0.5">${reserve.role} • <span class="text-slate-400">${reserve.tier}</span></div>
              </div>
              <button onclick="window.game.executeSwap(${starter.id}, ${reserve.id})" class="px-3.5 py-1.5 bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-black font-black uppercase tracking-wider rounded-lg border border-amber-500/30 transition cursor-pointer text-[11px]">
                Escalar
              </button>
            `;
            listContainer.appendChild(row);
          });
        }

        document.getElementById('swap-modal').classList.remove('hidden');
      }

      closeSwapModal() {
        document.getElementById('swap-modal').classList.add('hidden');
      }

      async executeSwap(starterId, benchId) {
        try {
          const response = await fetch('/game/player/swap', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ starter_id: starterId, bench_id: benchId })
          });

          const data = await response.json();

          if (data.success) {
            this.state.team = data.roster.map(this.mapPlayerData);
            this.state.bench = data.bench.map(this.mapPlayerData);
            
            this.renderTeam();
            this.renderBench();
            this.closeSwapModal();
          }
        } catch (error) {
          console.error("Erro ao realizar substituição:", error);
          alert("Erro de conexão ao tentar substituir jogador no banco.");
        }
      }

      renderBench() {
        const grid = document.getElementById('bench-grid');
        grid.innerHTML = '';
        document.getElementById('bench-count-display').textContent = `${this.state.bench.length} atletas`;

        if (this.state.bench.length === 0) {
          grid.innerHTML = `<div class="col-span-full py-8 text-center bg-slate-950/40 rounded-xl border border-dashed border-slate-800 text-slate-500 font-mono text-xs">O banco está vazio. Contrate olheiros na central abaixo!</div>`;
          return;
        }

        this.state.bench.forEach((player) => {
          const card = document.createElement('div');
          card.className = 'bg-slate-950 border border-slate-800/80 rounded-xl p-3 flex flex-col justify-between relative hover:border-slate-700 transition font-mono';
          card.innerHTML = `
            <div>
              <div class="flex justify-between items-center text-[10px]">
                <span class="text-slate-500 uppercase font-bold truncate max-w-[100px]">${player.role}</span>
                <span class="bg-slate-900 text-slate-300 font-bold px-1.5 py-0.5 rounded border border-slate-800">${player.baseOvr}</span>
              </div>
              <div class="font-black text-sm text-white mt-1 font-sans truncate">${player.name}</div>
              <div class="text-[9px] text-amber-500/80 mt-0.5">${player.tier}</div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-900 text-[10px] text-slate-500 text-center italic">
              Aguardando vaga
            </div>
          `;
          grid.appendChild(card);
        });
      }

      // --- CONTROLE DO MODAL DE SETUP TÁTICO ---

      openTacticalModal(index) {
        const player = this.state.team[index];
        document.getElementById('modal-player-id').value = player.id;
        document.getElementById('modal-player-index').value = index;
        document.getElementById('modal-player-name').textContent = `${player.name} (${player.role})`;
        
        document.getElementById('select-pistol').value = player.setups.pistol.weapon;
        document.getElementById('select-eco').value = player.setups.eco.weapon;
        document.getElementById('select-full').value = player.setups.full.weapon;

        document.getElementById('tactical-modal').classList.remove('hidden');
      }

      closeTacticalModal() {
        document.getElementById('tactical-modal').classList.add('hidden');
      }

      async saveTacticalSetup(event) {
        event.preventDefault();
        const playerId = document.getElementById('modal-player-id').value;
        const playerIndex = document.getElementById('modal-player-index').value;
        const pistolWep = document.getElementById('select-pistol').value;
        const ecoWep = document.getElementById('select-eco').value;
        const fullWep = document.getElementById('select-full').value;

        try {
          const response = await fetch('/game/player/setup', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              user_player_id: playerId,
              pistol_weapon: pistolWep,
              eco_weapon: ecoWep,
              full_weapon: fullWep
            })
          });

          const data = await response.json();
          if (data.success) {
            this.state.team[playerIndex].setups = data.updated_setups;
            this.renderTeam();
            this.closeTacticalModal();
          }
        } catch (error) {
          console.error("Erro ao salvar setup tático:", error);
          alert("Erro de conexão ao tentar salvar o setup no banco de dados.");
        }
      }

      // --- SISTEMA DE OLHEIROS E GACHA DE ATLETAS ---

      async buyScout(scoutType) {
        const costs = { local: 250, international: 2500, major: 15000 };
        if (this.state.money < costs[scoutType]) {
          alert(`Saldo insuficiente! Você precisa de $ ${costs[scoutType].toLocaleString('pt-BR')} para esta peneira.`);
          return;
        }

        try {
          const response = await fetch('/game/player/scout', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ scout_type: scoutType })
          });

          const data = await response.json();
          if (data.success) {
            this.state.money = parseFloat(data.new_money);
            this.updateUI();
            this.showGachaReveal(data.pulled_player);
            
            setTimeout(() => location.reload(), 1500); 
          } else {
            alert(data.error || "Erro ao processar contratação.");
          }
        } catch (error) {
          console.error("Erro na requisição de olheiro:", error);
          alert("Erro de conexão com o servidor ao contratar olheiro.");
        }
      }

      showGachaReveal(player) {
        document.getElementById('gacha-tier').textContent = player.tier;
        document.getElementById('gacha-ovr').textContent = player.baseOvr;
        document.getElementById('gacha-role').textContent = player.role;
        document.getElementById('gacha-name').textContent = player.name;

        const container = document.getElementById('gacha-card-container');
        if (player.tier.includes('Tier 1')) {
          container.className = "bg-gradient-to-b from-amber-950 to-slate-950 border-2 border-amber-400 rounded-2xl p-6 shadow-[0_0_30px_rgba(245,158,11,0.3)] mx-auto max-w-[220px] transform transition scale-110 animate-pulse";
        } else if (player.tier.includes('Tier 2')) {
          container.className = "bg-gradient-to-b from-purple-950 to-slate-950 border-2 border-purple-500 rounded-2xl p-6 shadow-2xl mx-auto max-w-[220px] transform transition scale-105";
        } else {
          container.className = "bg-gradient-to-b from-slate-800 to-slate-950 border-2 border-slate-600 rounded-2xl p-6 shadow-2xl mx-auto max-w-[220px]";
        }

        document.getElementById('gacha-modal').classList.remove('hidden');
      }

      closeGachaModal() {
        document.getElementById('gacha-modal').classList.add('hidden');
      }

      // --- RENDERIZAÇÃO DO TIME TITULAR ---

      updateTeamName(newName) {
        if (newName && newName.trim() !== "") {
          this.state.teamName = newName.trim();
          document.getElementById('sidebar-team-name').textContent = this.state.teamName;
        }
      }

      getBaseTeamPower() {
        return this.state.team.reduce((total, p) => total + p.baseOvr, 0);
      }

      renderTeam() {
        const grid = document.getElementById('team-grid');
        grid.innerHTML = '';

        this.state.team.forEach((player, idx) => {
          const card = document.createElement('div');
          card.className = 'bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-700/80 rounded-xl p-3.5 flex flex-col justify-between relative group hover:border-amber-500/50 transition shadow-lg';
          
          card.innerHTML = `
            <div>
              <div class="flex justify-between items-start">
                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">${player.tier}</span>
                <span class="text-lg font-black font-mono text-white bg-slate-800/80 px-2 rounded border border-slate-700">${player.baseOvr}</span>
              </div>
              
              <div class="mt-2">
                <div class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">${player.role}</div>
                <div class="font-black text-base text-white mt-0.5 leading-tight">${player.name}</div>
              </div>

              <!-- Setups Táticos -->
              <div class="mt-3 space-y-1.5 font-mono text-[10px]">
                <div class="p-1.5 rounded bg-slate-950/80 border border-slate-800 flex justify-between items-center">
                  <span class="text-amber-400 font-bold">🔫 Pistol:</span>
                  <span class="text-slate-300 truncate max-w-[90px]">${player.setups.pistol.weapon}</span>
                </div>
                <div class="p-1.5 rounded bg-slate-950/80 border border-slate-800 flex justify-between items-center">
                  <span class="text-blue-400 font-bold">💵 Eco:</span>
                  <span class="text-slate-300 truncate max-w-[90px]">${player.setups.eco.weapon}</span>
                </div>
                <div class="p-1.5 rounded bg-slate-950/80 border border-slate-800 flex justify-between items-center">
                  <span class="text-emerald-400 font-bold">💎 Full Buy:</span>
                  <span class="text-slate-300 font-bold truncate max-w-[90px]">${player.setups.full.weapon}</span>
                </div>
              </div>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-1.5">
              <button onclick="window.game.openTacticalModal(${idx})" class="py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-600 hover:border-amber-500 rounded font-bold text-[10px] transition flex justify-center items-center gap-1 cursor-pointer shadow">
                <span>⚙️ SETUP</span>
              </button>
              <button onclick="window.game.openSwapModal(${idx})" class="py-1.5 bg-amber-500/10 hover:bg-amber-500 text-amber-400 hover:text-black border border-amber-500/30 hover:border-amber-400 rounded font-black text-[10px] transition flex justify-center items-center gap-1 cursor-pointer shadow uppercase tracking-wider">
                <span>🔄 SUB</span>
              </button>
            </div>
          `;
          grid.appendChild(card);
        });

        document.getElementById('team-power-display').textContent = `Poder Base: ${this.getBaseTeamPower()} OVR`;
      }

      // --- SIMULAÇÃO DE PARTIDA ---

      startMatchmaking() {
        document.getElementById('lobby-screen').classList.add('hidden');
        document.getElementById('match-screen').classList.remove('hidden');
        document.getElementById('btn-return-lobby').classList.add('hidden');

        const myPower = this.getBaseTeamPower();
        const variance = (Math.random() * 0.20) - 0.10; 
        const enemyPower = Math.floor(myPower * (1 + variance));
        
        const rivalNames = ['Furia_Academy', 'NaVi_Recruits', 'Cloud9_Idle', 'Astralis_Base', 'Loud_Async'];
        const randomRival = rivalNames[Math.floor(Math.random() * rivalNames.length)];

        document.getElementById('match-my-name').textContent = this.state.teamName;
        document.getElementById('enemy-team-name').textContent = randomRival;
        document.getElementById('score-my').textContent = '0';
        document.getElementById('score-enemy').textContent = '0';
        
        const log = document.getElementById('match-log');
        log.innerHTML = `<div class="text-amber-400 font-bold">⚡ Servidor alocado! ${this.state.teamName} vs ${randomRival}. Formato MR12 com Economia Dinâmica.</div>`;

        this.runMatchSimulation(myPower, enemyPower, randomRival);
      }

      runMatchSimulation(myBasePower, enemyBasePower, rivalName) {
        let currentRound = 1;
        let myScore = 0;
        let enemyScore = 0;
        const maxRounds = 15;

        let myMatchMoney = 800;
        let enemyMatchMoney = 800;

        const interval = setInterval(() => {
          if (myScore >= 8 || enemyScore >= 8 || currentRound > maxRounds) {
            clearInterval(interval);
            this.finishMatch(myScore, enemyScore, rivalName);
            return;
          }

          document.getElementById('match-round').textContent = `${currentRound} / ${maxRounds}`;

          let mySetupType = 'pistol';
          let myMult = 1.0;
          let myEconText = `$ ${myMatchMoney} (Pistol)`;
          let myCost = 0;

          if (currentRound > 1) {
            if (myMatchMoney >= 3500) {
              mySetupType = 'full'; myMult = 2.0; myCost = 3500;
              myEconText = `$ ${myMatchMoney} (Full Buy 💎)`;
            } else if (myMatchMoney >= 1500) {
              mySetupType = 'eco'; myMult = 1.3; myCost = 1500;
              myEconText = `$ ${myMatchMoney} (Eco/Force 💵)`;
            }
          }

          let enemySetupType = enemyMatchMoney >= 3500 ? 'Full Buy' : (enemyMatchMoney >= 1500 ? 'Eco' : 'Pistol');
          let enemyMult = enemyMatchMoney >= 3500 ? 2.0 : (enemyMatchMoney >= 1500 ? 1.3 : 1.0);
          let enemyCost = enemyMatchMoney >= 3500 ? 3500 : (enemyMatchMoney >= 1500 ? 1500 : 0);

          document.getElementById('match-my-econ').textContent = `Cofre: ${myEconText}`;
          document.getElementById('match-enemy-econ').textContent = `Cofre: $ ${enemyMatchMoney} (${enemySetupType})`;

          myMatchMoney -= myCost;
          enemyMatchMoney -= enemyCost;

          const myRoundPower = (myBasePower * myMult) + (Math.random() * 50);
          const enemyRoundPower = (enemyBasePower * enemyMult) + (Math.random() * 50);

          const log = document.getElementById('match-log');
          
          if (myRoundPower >= enemyRoundPower) {
            myScore++;
            myMatchMoney += 3250;
            enemyMatchMoney += 1900;
            document.getElementById('score-my').textContent = myScore;
            
            const weaponUsed = this.state.team[Math.floor(Math.random()*5)].setups[mySetupType].weapon;
            log.innerHTML += `<div class="text-blue-400">► Round ${currentRound} [${mySetupType.toUpperCase()}]: Excelente eliminação de ${weaponUsed}! ${this.state.teamName} venceu (+ $3.250).</div>`;
          } else {
            enemyScore++;
            enemyMatchMoney += 3250;
            myMatchMoney += 1900;
            document.getElementById('score-enemy').textContent = enemyScore;
            log.innerHTML += `<div class="text-amber-500">► Round ${currentRound}: ${rivalName} [${enemySetupType}] dominou o bomb e levou o round. (+ $1.900 consolatórios).</div>`;
          }

          log.scrollTop = log.scrollHeight;
          currentRound++;
        }, 600); 
      }

      async finishMatch(myScore, enemyScore, rivalName) {
        const log = document.getElementById('match-log');
        const btn = document.getElementById('btn-return-lobby');
        
        log.innerHTML += `<div class="mt-3 text-center text-slate-400 font-mono animate-pulse">⏳ Enviando resultado para o servidor autoritativo Laravel...</div>`;
        log.scrollTop = log.scrollHeight;

        try {
          const response = await fetch('/game/match-finish', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              my_score: myScore,
              enemy_score: enemyScore
            })
          });

          const data = await response.json();

          if (data.success) {
            this.state.money = parseFloat(data.new_money);
            this.state.elo = parseInt(data.new_elo);
            this.state.matches = parseInt(data.matches_played);
            this.state.wins = parseInt(data.wins);
            this.state.losses = parseInt(data.losses);

            if (data.is_win) {
              log.innerHTML += `<div class="mt-3 p-3 bg-emerald-500/20 border border-emerald-500 text-emerald-400 font-bold text-center rounded-lg">🏆 VITÓRIA CONFIRMADA NO SERVIDOR (${myScore} - ${enemyScore})!<br>${this.state.teamName} faturou $ ${data.prize_earned} e +25 pts de Rating.</div>`;
            } else {
              log.innerHTML += `<div class="mt-3 p-3 bg-red-500/20 border border-red-500 text-red-300 font-bold text-center rounded-lg">💀 DERROTA TÁTICA vs ${rivalName} (${myScore} - ${enemyScore}).<br>Pagamento de Consolação creditado no banco: $ ${data.prize_earned}.</div>`;
            }

            this.updateUI();
          }
        } catch (error) {
          console.error("Erro ao salvar partida no servidor:", error);
          log.innerHTML += `<div class="mt-3 p-2 bg-red-900/50 text-red-400 text-center rounded">❌ Erro de conexão ao salvar partida no banco de dados.</div>`;
        }

        log.scrollTop = log.scrollHeight;
        btn.classList.remove('hidden');
      }

      returnToLobby() {
        document.getElementById('match-screen').classList.add('hidden');
        document.getElementById('lobby-screen').classList.remove('hidden');
        this.renderTeam();
      }

      updateUI() {
        document.getElementById('money-display').textContent = `$ ${this.state.money.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`;
        document.getElementById('elo-display').textContent = `${this.state.elo} pts`;
        document.getElementById('stat-matches').textContent = this.state.matches;
        document.getElementById('stat-wins').textContent = this.state.wins;
        document.getElementById('stat-losses').textContent = this.state.losses;
        document.getElementById('stat-skins').textContent = `${this.state.skinsCount} itens`;
        
        const winrate = this.state.matches > 0 ? Math.round((this.state.wins / this.state.matches) * 100) : 0;
        document.getElementById('stat-winrate').textContent = `${winrate}%`;
      }
    }

    window.addEventListener('DOMContentLoaded', () => {
      window.game = new TacticalManagerEngine();
    });
  </script>
</body>
</html>