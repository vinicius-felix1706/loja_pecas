<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmar compra Motopecas Central</title>
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
    main { position:relative; z-index:1; flex:1; max-width:1100px; width:100%; margin:0 auto; padding:64px 48px; }
    .eyebrow { font-family:'Barlow Condensed',sans-serif; font-size:.8rem; font-weight:700; letter-spacing:.22em; text-transform:uppercase; color:var(--rust); margin-bottom:12px; display:flex; align-items:center; gap:10px; }
    .eyebrow::before { content:''; width:28px; height:2px; background:var(--rust); }
    h1 { font-family:'Bebas Neue',sans-serif; font-size:3.6rem; line-height:.95; letter-spacing:.03em; margin-bottom:32px; }
    h1 em { font-style:normal; color:var(--amber); -webkit-text-stroke:1px var(--amber-dk); }
    .checkout { display:grid; grid-template-columns:1fr 1fr; gap:2px; align-items:stretch; }
    .image-box { background:var(--paper-dk); border-left:4px solid var(--amber); min-height:360px; }
    .image-box img { width:100%; height:100%; object-fit:cover; display:block; }
    .summary { background:var(--paper-dk); padding:40px; display:flex; flex-direction:column; justify-content:space-between; gap:28px; }
    .part-name { font-family:'Barlow Condensed',sans-serif; font-size:1.35rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
    .desc { color:var(--muted); margin-top:10px; }
    .price { font-family:'Bebas Neue',sans-serif; font-size:3rem; letter-spacing:.04em; line-height:1; }
    .price span { font-family:'Barlow Condensed',sans-serif; font-size:1rem; color:var(--muted); vertical-align:super; margin-right:4px; }
    .actions { display:flex; gap:14px; flex-wrap:wrap; }
    .btn { display:inline-flex; align-items:center; justify-content:center; padding:16px 36px; font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; text-decoration:none; border:none; cursor:pointer; transition:transform .15s,box-shadow .15s,border-color .15s,color .15s; }
    .btn:hover { transform:translateY(-2px); }
    .btn-primary { background:var(--amber); color:var(--ink); box-shadow:0 4px 24px rgba(232,160,32,.35); }
    .btn-primary:hover { background:var(--amber-lt); box-shadow:0 8px 32px rgba(232,160,32,.5); }
    .btn-outline { background:transparent; color:var(--ink); border:2px solid rgba(26,20,16,.2); }
    .btn-outline:hover { border-color:var(--amber); color:var(--amber-dk); }
    footer { position:relative; z-index:1; background:#0E0B07; padding:20px 48px; display:flex; align-items:center; justify-content:space-between; }
    footer .logo { font-size:1.1rem; }
    footer p { font-size:.72rem; color:#4A3E2E; letter-spacing:.08em; }
    @media (max-width:760px) { .header-inner, main { padding-left:20px; padding-right:20px; } .checkout { grid-template-columns:1fr; } h1 { font-size:2.8rem; } footer { padding:16px 20px; } }
  </style>
</head>
<body>
  <header>
    <div class="header-inner">
      <a href="?url=dashboard" class="logo"><span>Moto</span>Pecas <span>Central</span></a>
      <div class="nav">
        <a href="?url=pedidos" class="top-link">Pedidos</a>
        <a href="?url=dashboard" class="top-link">Catalogo</a>
        <a href="?url=auth/logout" class="top-link">Sair</a>
      </div>
    </div>
  </header>

  <main>
    <div class="eyebrow">Confirmacao de compra</div>
    <h1>Finalizar<br><em>pedido.</em></h1>

    <div class="checkout">
      <div class="image-box">
        <img src="<?= pecaImagem($peca['nome'], $peca['sigla'], $peca['shape'], $peca['imagem'] ?? null) ?>" alt="Imagem da peca <?= htmlspecialchars($peca['nome']) ?>">
      </div>
      <div class="summary">
        <div>
          <div class="part-name"><?= htmlspecialchars($peca['nome']) ?></div>
          <p class="desc"><?= htmlspecialchars($peca['descricao']) ?></p>
        </div>
        <div class="price"><span>R$</span><?= number_format((float)$peca['preco'], 2, ',', '.') ?></div>
        <form action="?url=compra/finalizar" method="POST" class="actions">
          <input type="hidden" name="id_peca" value="<?= (int)$peca['id'] ?>">
          <button type="submit" class="btn btn-primary">Comprar</button>
          <a href="?url=dashboard" class="btn btn-outline">Voltar</a>
        </form>
      </div>
    </div>
  </main>

  <footer>
    <div class="logo"><span>Moto</span>Pecas</div>
    <p>© 2024 Motopecas Central · Montes Claros, MG</p>
  </footer>
</body>
</html>
