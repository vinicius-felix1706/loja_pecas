<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pedidos Motopecas Central</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    :root { --amber:#E8A020; --amber-lt:#F5C842; --amber-dk:#B87A0A; --rust:#C4520A; --ink:#1A1410; --paper:#FAF3E0; --paper-dk:#F0E6C8; --muted:#7A6A50; --line:rgba(232,160,32,.25); }
    body { background:var(--paper); color:var(--ink); font-family:'Barlow',sans-serif; min-height:100vh; display:flex; flex-direction:column; line-height:1.6; }
    body::before { content:''; position:fixed; inset:0; z-index:0; background-image:repeating-linear-gradient(0deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px),repeating-linear-gradient(90deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px); pointer-events:none; }
    header { position:relative; z-index:2; border-bottom:2px solid var(--amber); background:var(--ink); }
    .header-inner { max-width:1100px; margin:0 auto; padding:18px 48px; display:flex; align-items:center; justify-content:space-between; gap:18px; }
    .logo { font-family:'Bebas Neue',sans-serif; font-size:2rem; letter-spacing:.08em; color:var(--amber); text-decoration:none; }
    .logo span { color:#fff; }
    .nav { display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
    .top-link { font-family:'Barlow Condensed',sans-serif; font-size:.78rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:#9A8A72; text-decoration:none; border:1px solid rgba(255,255,255,.1); padding:7px 18px; }
    .top-link:hover { color:var(--amber); border-color:var(--amber); }
    .page-hero { position:relative; z-index:1; background:var(--ink); border-bottom:3px solid var(--amber); padding:40px 48px 36px; overflow:hidden; }
    .page-hero::after { content:'PEDIDOS'; position:absolute; right:-10px; top:-20px; font-family:'Bebas Neue',sans-serif; font-size:11rem; color:var(--amber); opacity:.05; line-height:1; }
    .page-hero-inner { position:relative; z-index:1; max-width:1100px; margin:0 auto; }
    .eyebrow { font-family:'Barlow Condensed',sans-serif; font-size:.78rem; font-weight:700; letter-spacing:.22em; text-transform:uppercase; color:var(--rust); margin-bottom:10px; display:flex; align-items:center; gap:10px; }
    .eyebrow::before { content:''; width:24px; height:2px; background:var(--rust); }
    h1 { font-family:'Bebas Neue',sans-serif; font-size:3.2rem; line-height:.95; letter-spacing:.03em; color:var(--paper); }
    h1 em { font-style:normal; color:var(--amber); -webkit-text-stroke:1px var(--amber-dk); }
    main { position:relative; z-index:1; flex:1; max-width:1100px; width:100%; margin:0 auto; padding:48px; }
    .flash { padding:14px 18px; margin-bottom:28px; font-size:.88rem; font-family:'Barlow Condensed',sans-serif; font-weight:600; letter-spacing:.06em; border-left:4px solid; }
    .flash-success { background:#edf7f0; border-color:#2a8a4e; color:#2a8a4e; }
    .flash-danger { background:#fdf0ed; border-color:var(--rust); color:var(--rust); }
    .orders { display:grid; gap:2px; }
    .order-card { background:var(--paper-dk); border-left:4px solid var(--amber); padding:24px 28px; display:grid; grid-template-columns:1fr auto; gap:18px; align-items:center; }
    .order-title { font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
    .order-meta { color:var(--muted); font-size:.9rem; margin-top:4px; }
    .order-actions { display:flex; flex-direction:column; align-items:flex-end; gap:14px; }
    .order-price { font-family:'Bebas Neue',sans-serif; font-size:2rem; letter-spacing:.04em; white-space:nowrap; }
    .status { display:inline-flex; margin-top:10px; font-family:'Barlow Condensed',sans-serif; font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:#2a8a4e; }
    .remove-btn { background:transparent; border:1px solid rgba(196,82,10,.35); color:var(--rust); cursor:pointer; font-family:'Barlow Condensed',sans-serif; font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; padding:8px 16px; transition:background .2s,border-color .2s,transform .15s; }
    .remove-btn:hover { background:#fdf0ed; border-color:var(--rust); transform:translateY(-1px); }
    .empty { background:var(--paper-dk); padding:48px; border-left:4px solid var(--amber); color:var(--muted); }
    .empty strong { display:block; font-family:'Barlow Condensed',sans-serif; font-size:1.1rem; text-transform:uppercase; letter-spacing:.08em; color:var(--ink); margin-bottom:8px; }
    footer { position:relative; z-index:1; background:#0E0B07; padding:20px 48px; display:flex; align-items:center; justify-content:space-between; }
    footer .logo { font-size:1.1rem; }
    footer p { font-size:.72rem; color:#4A3E2E; letter-spacing:.08em; }
    @media (max-width:700px) { .header-inner, .page-hero, main { padding-left:20px; padding-right:20px; } .header-inner { align-items:flex-start; flex-direction:column; } .order-card { grid-template-columns:1fr; } .order-actions { align-items:flex-start; } h1 { font-size:2.6rem; } footer { padding:16px 20px; } }
  </style>
</head>
<body>
  <header>
    <div class="header-inner">
      <a href="?url=dashboard" class="logo"><span>Moto</span>Pecas <span>Central</span></a>
      <div class="nav">
        <a href="?url=dashboard" class="top-link">Catalogo</a>
        <a href="?url=auth/logout" class="top-link">Sair</a>
      </div>
    </div>
  </header>

  <div class="page-hero">
    <div class="page-hero-inner">
      <div class="eyebrow">Area do cliente</div>
      <h1>Meus<br><em>pedidos.</em></h1>
    </div>
  </div>

  <main>
    <?php if (!empty($flash)): ?>
      <div class="flash flash-<?= htmlspecialchars($flash['tipo']) ?>"><?= htmlspecialchars($flash['mensagem']) ?></div>
    <?php endif; ?>

    <?php if (empty($pedidos)): ?>
      <div class="empty">
        <strong>Nenhum pedido ainda</strong>
        Escolha uma peca no catalogo para simular seu primeiro pedido.
      </div>
    <?php else: ?>
      <div class="orders">
        <?php foreach ($pedidos as $pedido): ?>
          <div class="order-card">
            <div>
              <div class="order-title">Pedido #<?= (int)$pedido['id'] ?> - <?= htmlspecialchars($pedido['produto_nome'] ?? 'Produto') ?></div>
              <div class="order-meta">
                Data: <?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?> · Quantidade: <?= (int)($pedido['quantidade'] ?? 1) ?>
              </div>
              <div class="status"><?= htmlspecialchars($pedido['status']) ?></div>
            </div>
            <div class="order-actions">
              <div class="order-price">R$ <?= number_format((float)$pedido['total'], 2, ',', '.') ?></div>
              <form action="?url=pedido/remover" method="POST" onsubmit="return confirm('Remover este pedido?');">
                <input type="hidden" name="id_pedido" value="<?= (int)$pedido['id'] ?>">
                <button type="submit" class="remove-btn">Remover</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    <div class="logo"><span>Moto</span>Pecas</div>
    <p>© 2024 Motopecas Central · Montes Claros, MG</p>
  </footer>
</body>
</html>
