<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --amber:    #E8A020;
      --amber-lt: #F5C842;
      --amber-dk: #B87A0A;
      --rust:     #C4520A;
      --ink:      #1A1410;
      --paper:    #FAF3E0;
      --paper-dk: #F0E6C8;
      --muted:    #7A6A50;
      --line:     rgba(232,160,32,.25);
    }

    html { scroll-behavior: smooth; }

    body {
      background: var(--paper);
      color: var(--ink);
      font-family: 'Barlow', sans-serif;
      font-size: 16px;
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0;
      background-image:
        repeating-linear-gradient(0deg, transparent, transparent 39px, var(--line) 39px, var(--line) 40px),
        repeating-linear-gradient(90deg, transparent, transparent 39px, var(--line) 39px, var(--line) 40px);
      pointer-events: none;
    }

    /* Header */
    header {
      position: relative; z-index: 2;
      border-bottom: 2px solid var(--amber);
      background: var(--ink);
    }
    .header-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 18px 48px;
      max-width: 1100px;
      margin: 0 auto;
    }
    .logo {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2rem;
      letter-spacing: .08em;
      color: var(--amber);
      text-decoration: none;
    }
    .logo span { color: #fff; }
    .header-tag {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .75rem;
      font-weight: 500;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: #7A6A50;
    }

    /* Main */
    main {
      position: relative; z-index: 1;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 64px 24px;
    }

    /* Watermark */
    main::before {
      content: 'ENTRAR';
      position: absolute;
      left: 50%; bottom: -20px;
      transform: translateX(-50%);
      font-family: 'Bebas Neue', sans-serif;
      font-size: 18rem;
      color: var(--amber);
      opacity: .04;
      letter-spacing: -.01em;
      pointer-events: none;
      user-select: none;
      white-space: nowrap;
    }

    .form-wrapper {
      position: relative; z-index: 1;
      width: 100%;
      max-width: 440px;
    }

    /* Eyebrow */
    .eyebrow {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .8rem;
      font-weight: 700;
      letter-spacing: .22em;
      text-transform: uppercase;
      color: var(--rust);
      margin-bottom: 10px;
      display: flex; align-items: center; gap: 10px;
    }
    .eyebrow::before {
      content: '';
      display: inline-block;
      width: 28px; height: 2px;
      background: var(--rust);
    }

    h1 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 3.6rem;
      line-height: .95;
      letter-spacing: .02em;
      color: var(--ink);
      margin-bottom: 32px;
    }
    h1 em {
      font-style: normal;
      color: var(--amber);
      -webkit-text-stroke: 1px var(--amber-dk);
    }

    /* Flash */
    .flash {
      padding: 14px 18px;
      margin-bottom: 24px;
      font-size: .88rem;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 600;
      letter-spacing: .06em;
      border-left: 4px solid;
    }
    .flash-danger  { background: #fdf0ed; border-color: var(--rust); color: var(--rust); }
    .flash-success { background: #edf7f0; border-color: #2a8a4e; color: #2a8a4e; }

    /* Card do formulário */
    .form-card {
      background: var(--paper-dk);
      padding: 40px;
      border-left: 4px solid var(--amber);
    }

    .access-choice {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 24px;
    }
    .access-choice input { position: absolute; opacity: 0; pointer-events: none; }
    .access-choice label {
      margin: 0;
      padding: 13px 12px;
      text-align: center;
      background: var(--paper);
      border: 1px solid rgba(26,20,16,.16);
      cursor: pointer;
      transition: background .2s, border-color .2s, color .2s;
    }
    .access-choice input:checked + label {
      background: var(--ink);
      border-color: var(--amber);
      color: var(--amber);
    }

    .field { margin-bottom: 24px; }

    label {
      display: block;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .78rem;
      font-weight: 700;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: var(--ink);
      margin-bottom: 8px;
    }

    input[type="email"],
    input[type="password"],
    input[type="text"] {
      width: 100%;
      background: var(--paper);
      border: 1px solid rgba(26,20,16,.18);
      border-bottom: 2px solid rgba(26,20,16,.25);
      padding: 13px 16px;
      font-family: 'Barlow', sans-serif;
      font-size: .95rem;
      color: var(--ink);
      outline: none;
      transition: border-color .2s;
      appearance: none;
    }
    input:focus {
      border-color: var(--amber);
      border-bottom-color: var(--amber);
      background: #fff;
    }
    input::placeholder { color: #bfad95; }

    /* Botões */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 16px 40px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      border: none;
      cursor: pointer;
      transition: transform .15s, box-shadow .15s;
      text-decoration: none;
    }
    .btn:hover { transform: translateY(-2px); }
    .btn-primary {
      background: var(--amber);
      color: var(--ink);
      box-shadow: 0 4px 24px rgba(232,160,32,.35);
      margin-bottom: 14px;
    }
    .btn-primary:hover {
      background: var(--amber-lt);
      box-shadow: 0 8px 32px rgba(232,160,32,.5);
    }
    .btn-arrow { font-size: 1.1rem; transition: transform .15s; }
    .btn:hover .btn-arrow { transform: translateX(4px); }

    /* Link de cadastro */
    .alt-link {
      text-align: center;
      margin-top: 28px;
      font-size: .85rem;
      color: var(--muted);
    }
    .alt-link a {
      color: var(--amber-dk);
      font-weight: 600;
      text-decoration: none;
      letter-spacing: .04em;
    }
    .alt-link a:hover { color: var(--amber); }

    /* Footer */
    footer {
      position: relative; z-index: 1;
      background: #0E0B07;
      padding: 20px 48px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    footer .logo { font-size: 1.1rem; }
    footer p { font-size: .72rem; color: #4A3E2E; letter-spacing: .08em; }

    /* Reveal */
    .reveal {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity .5s ease, transform .5s ease;
    }
    .reveal.visible { opacity: 1; transform: none; }
    .reveal-delay-1 { transition-delay: .1s; }
    .reveal-delay-2 { transition-delay: .2s; }

    @media (max-width: 520px) {
      .header-inner { padding: 16px 20px; }
      .form-card { padding: 28px 22px; }
      h1 { font-size: 2.8rem; }
      main::before { font-size: 8rem; }
    }
  </style>
</head>
<body>

  <header>
    <div class="header-inner">
      <a href="index.php" class="logo"><span>Moto</span>Peças <span>Central</span></a>
      <div class="header-tag">Est. 1994 &nbsp;·&nbsp; Minas Gerais</div>
    </div>
  </header>

  <main>
    <div class="form-wrapper">

      <div class="eyebrow reveal">Acesso ao sistema</div>
      <h1 class="reveal reveal-delay-1">Bem-vindo<br>de <em>volta.</em></h1>

      <?php if (!empty($flash)): ?>
        <div class="flash flash-<?= htmlspecialchars($flash['tipo']) ?> reveal reveal-delay-1">
          <?= htmlspecialchars($flash['mensagem']) ?>
        </div>
      <?php endif; ?>

      <div class="form-card reveal reveal-delay-2">
        <form action="?url=auth/autenticar" method="POST" novalidate>
          <div class="access-choice" aria-label="Tipo de acesso">
            <input type="radio" id="acesso_cliente" name="tipo_acesso" value="cliente" checked>
            <label for="acesso_cliente">Cliente</label>
            <input type="radio" id="acesso_admin" name="tipo_acesso" value="admin">
            <label for="acesso_admin">Administrador</label>
          </div>

          <div class="field">
            <label for="email" id="email-label">E-mail</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="seu@email.com"
              required
              autofocus
              autocomplete="username"
            >
          </div>

          <div class="field">
            <label for="senha" id="senha-label">Senha</label>
            <input
              type="password"
              id="senha"
              name="senha"
              placeholder="••••••••"
              required
              autocomplete="current-password"
            >
          </div>

          <button type="submit" class="btn btn-primary">
            Entrar <span class="btn-arrow">→</span>
          </button>

        </form>
      </div>

      <div class="alt-link reveal reveal-delay-2">
        Ainda não tem conta? <a href="?url=auth/cadastro">Criar conta →</a>
      </div>

    </div>
  </main>

  <footer>
    <div class="logo"><span>Moto</span>Peças</div>
    <p>© 2024 Motopeças Central · Montes Claros, MG</p>
  </footer>

  <script>
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

    const acessoCliente = document.getElementById('acesso_cliente');
    const acessoAdmin = document.getElementById('acesso_admin');
    const emailLabel = document.getElementById('email-label');
    const senhaLabel = document.getElementById('senha-label');
    const emailInput = document.getElementById('email');

    function atualizarTipoAcesso() {
      const admin = acessoAdmin.checked;
      emailLabel.textContent = admin ? 'E-mail do administrador' : 'E-mail';
      senhaLabel.textContent = admin ? 'Senha do administrador' : 'Senha';
      emailInput.placeholder = admin ? 'admin@email.com' : 'seu@email.com';
    }

    acessoCliente.addEventListener('change', atualizarTipoAcesso);
    acessoAdmin.addEventListener('change', atualizarTipoAcesso);
  </script>

</body>
</html>
