<?php
require_once ROOT . '/helpers/catalogo_pecas.php';
$pecas = array_map('produtoParaPeca', $produtos ?? []);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Catalogo Motopecas Central</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root { --amber:#E8A020; --amber-lt:#F5C842; --amber-dk:#B87A0A; --rust:#C4520A; --ink:#1A1410; --paper:#FAF3E0; --paper-dk:#F0E6C8; --muted:#7A6A50; --line:rgba(232,160,32,.25); }
    body { background:var(--paper); color:var(--ink); font-family:'Barlow',sans-serif; font-size:16px; line-height:1.6; min-height:100vh; display:flex; flex-direction:column; }
    body::before { content:''; position:fixed; inset:0; z-index:0; background-image:repeating-linear-gradient(0deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px),repeating-linear-gradient(90deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px); pointer-events:none; }
    header { position:relative; z-index:2; border-bottom:2px solid var(--amber); background:var(--ink); }
    .header-inner { display:flex; align-items:center; justify-content:space-between; gap:24px; padding:18px 48px; max-width:1200px; margin:0 auto; }
    .header-left, .header-right { display:flex; align-items:center; gap:18px; }
    .account-actions { display:flex; flex-direction:column; align-items:stretch; gap:8px; }
    .logo { font-family:'Bebas Neue',sans-serif; font-size:2rem; letter-spacing:.08em; color:var(--amber); text-decoration:none; white-space:nowrap; }
    .logo span { color:#fff; }
    .top-link, .btn-logout, .btn-delete-account { font-family:'Barlow Condensed',sans-serif; font-size:.78rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; text-decoration:none; border:1px solid rgba(255,255,255,.1); padding:7px 18px; transition:border-color .2s,color .2s,background .2s; white-space:nowrap; text-align:center; }
    .top-link { color:var(--amber); border-color:rgba(232,160,32,.35); }
    .top-link:hover { background:rgba(232,160,32,.12); border-color:var(--amber); }
    .btn-logout { color:#9A8A72; }
    .btn-logout:hover { border-color:var(--rust); color:var(--rust); }
    .delete-account-form { margin:0; }
    .btn-delete-account { width:100%; background:transparent; color:var(--rust); cursor:pointer; }
    .btn-delete-account:hover { background:rgba(196,82,10,.12); border-color:var(--rust); }
    .header-user { font-family:'Barlow Condensed',sans-serif; font-size:.82rem; letter-spacing:.08em; color:#9A8A72; }
    .header-user strong { color:var(--amber-lt); font-weight:600; }
    .page-hero { position:relative; z-index:1; background:var(--ink); border-bottom:3px solid var(--amber); padding:40px 48px 36px; overflow:hidden; }
    .page-hero::after { content:'PECAS'; position:absolute; right:-10px; top:-20px; font-family:'Bebas Neue',sans-serif; font-size:11rem; line-height:1; color:var(--amber); opacity:.05; pointer-events:none; }
    .page-hero-inner { position:relative; z-index:1; max-width:1200px; margin:0 auto; display:flex; align-items:flex-end; justify-content:space-between; gap:24px; flex-wrap:wrap; }
    .page-eyebrow { font-family:'Barlow Condensed',sans-serif; font-size:.78rem; font-weight:700; letter-spacing:.22em; text-transform:uppercase; color:var(--rust); margin-bottom:10px; display:flex; align-items:center; gap:10px; }
    .page-eyebrow::before { content:''; width:24px; height:2px; background:var(--rust); }
    h1 { font-family:'Bebas Neue',sans-serif; font-size:3.2rem; line-height:.95; letter-spacing:.03em; color:var(--paper); }
    h1 em { font-style:normal; color:var(--amber); -webkit-text-stroke:1px var(--amber-dk); }
    .hero-count { font-family:'Barlow Condensed',sans-serif; font-size:.82rem; font-weight:600; letter-spacing:.1em; text-transform:uppercase; color:#9A8A72; white-space:nowrap; }
    .hero-count strong { color:var(--amber); font-size:1.5rem; display:block; font-family:'Bebas Neue',sans-serif; letter-spacing:.05em; }
    .search-bar { position:relative; z-index:1; background:var(--paper-dk); border-bottom:1px solid rgba(26,20,16,.1); padding:18px 48px; }
    .search-bar-inner { max-width:1200px; margin:0 auto; display:flex; align-items:center; gap:16px; }
    .filter-label { font-family:'Barlow Condensed',sans-serif; font-size:.75rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--muted); }
    .search-input-wrap { position:relative; flex:1; max-width:420px; }
    .search-input-wrap::before { content:''; position:absolute; left:14px; top:50%; width:12px; height:12px; border:2px solid var(--muted); border-radius:50%; transform:translateY(-60%); pointer-events:none; }
    .search-input-wrap::after { content:''; position:absolute; left:25px; top:50%; width:8px; height:2px; background:var(--muted); transform:translateY(4px) rotate(45deg); pointer-events:none; }
    #busca { width:100%; background:var(--paper); border:1px solid rgba(26,20,16,.15); border-bottom:2px solid rgba(26,20,16,.2); padding:10px 14px 10px 42px; font-family:'Barlow',sans-serif; font-size:.92rem; color:var(--ink); outline:none; transition:border-color .2s; }
    #busca:focus { border-color:var(--amber); border-bottom-color:var(--amber); background:#fff; }
    #resultado-busca { font-family:'Barlow Condensed',sans-serif; font-size:.8rem; font-weight:600; letter-spacing:.08em; color:var(--amber-dk); margin-left:auto; white-space:nowrap; }
    .catalog-wrap { position:relative; z-index:1; flex:1; max-width:1200px; margin:0 auto; width:100%; padding:48px 48px 64px; }
    .products-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:2px; }
    .product-card { background:var(--paper-dk); display:flex; flex-direction:column; border-left:4px solid transparent; transition:border-color .2s,background .2s,transform .2s; text-decoration:none; color:inherit; min-height:100%; }
    .product-card:hover { border-color:var(--amber); background:#ede0c0; transform:translateY(-2px); }
    .card-image { width:100%; aspect-ratio:4/3; background:var(--paper); border-bottom:1px solid rgba(26,20,16,.08); overflow:hidden; position:relative; }
    .card-image img { width:100%; height:100%; object-fit:cover; display:block; }
    .card-badge { position:absolute; top:10px; right:10px; background:var(--ink); color:var(--amber); font-family:'Barlow Condensed',sans-serif; font-size:.65rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; padding:4px 10px; }
    .card-body { padding:22px 24px 20px; flex:1; display:flex; flex-direction:column; gap:8px; }
    .card-name { font-family:'Barlow Condensed',sans-serif; font-size:1.05rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; color:var(--ink); line-height:1.2; }
    .card-desc { font-size:.85rem; color:var(--muted); line-height:1.55; flex:1; }
    .card-footer { padding:14px 24px 20px; display:flex; align-items:center; justify-content:space-between; gap:14px; border-top:1px solid rgba(26,20,16,.07); }
    .card-price { font-family:'Bebas Neue',sans-serif; font-size:1.8rem; letter-spacing:.04em; color:var(--ink); line-height:1; white-space:nowrap; }
    .card-price span { font-family:'Barlow Condensed',sans-serif; font-size:.8rem; font-weight:700; letter-spacing:.06em; color:var(--muted); vertical-align:super; margin-right:3px; }
    .card-stock { font-family:'Barlow Condensed',sans-serif; font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:#2a8a4e; white-space:nowrap; }
    .flash { padding:14px 18px; margin-bottom:28px; font-size:.88rem; font-family:'Barlow Condensed',sans-serif; font-weight:600; letter-spacing:.06em; border-left:4px solid; }
    .flash-success { background:#edf7f0; border-color:#2a8a4e; color:#2a8a4e; }
    .flash-danger { background:#fdf0ed; border-color:var(--rust); color:var(--rust); }
    .reveal { opacity:0; transform:translateY(20px); transition:opacity .5s ease,transform .5s ease; }
    .reveal.visible { opacity:1; transform:none; }
    footer { position:relative; z-index:1; background:#0E0B07; padding:20px 48px; display:flex; align-items:center; justify-content:space-between; }
    footer .logo { font-size:1.1rem; }
    footer p { font-size:.72rem; color:#4A3E2E; letter-spacing:.08em; }
    @media (max-width:768px) { .header-inner,.search-bar,.catalog-wrap{padding-left:20px;padding-right:20px}.header-inner,.search-bar-inner{align-items:flex-start;flex-direction:column}.header-left,.header-right{flex-wrap:wrap}.page-hero{padding:28px 20px 24px}.page-hero h1{font-size:2.4rem}.page-hero::after{font-size:5rem}.products-grid{grid-template-columns:repeat(auto-fill,minmax(200px,1fr))}#resultado-busca{margin-left:0}footer{padding:16px 20px} }
  </style>
</head>
<body>
  <header>
    <div class="header-inner">
      <div class="header-left">
        <a href="?url=pedidos" class="top-link">Pedidos</a>
        <a href="index.php" class="logo"><span>Moto</span>Pecas <span>Central</span></a>
      </div>
      <div class="header-right">
        <div class="header-user">Ola, <strong><?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?></strong></div>
        <div class="account-actions">
          <a href="?url=auth/logout" class="btn-logout">Sair</a>
          <form class="delete-account-form" action="?url=conta/excluir" method="POST" onsubmit="return confirm('Excluir sua conta? Todos os seus pedidos e dados relacionados serao removidos.');">
            <button type="submit" class="btn-delete-account">Excluir conta</button>
          </form>
        </div>
      </div>
    </div>
  </header>

  <div class="page-hero">
    <div class="page-hero-inner">
      <div>
        <div class="page-eyebrow">Area do cliente</div>
        <h1>Pecas de<br><em>motor.</em></h1>
      </div>
      <div class="hero-count"><strong id="total-cards"><?= count($pecas) ?></strong> pecas disponiveis</div>
    </div>
  </div>

  <div class="search-bar">
    <div class="search-bar-inner">
      <span class="filter-label">Filtrar</span>
      <div class="search-input-wrap"><input type="text" id="busca" placeholder="Buscar por nome ou descricao..." autocomplete="off"></div>
      <span id="resultado-busca"></span>
    </div>
  </div>

  <div class="catalog-wrap">
    <?php if (!empty($flash)): ?>
      <div class="flash flash-<?= htmlspecialchars($flash['tipo']) ?>"><?= htmlspecialchars($flash['mensagem']) ?></div>
    <?php endif; ?>

    <div class="products-grid" id="grid">
      <?php foreach ($pecas as $p): ?>
        <a class="product-card reveal" href="?url=peca/<?= (int)$p['id'] ?>" data-nome="<?= htmlspecialchars(strtolower($p['nome'])) ?>" data-desc="<?= htmlspecialchars(strtolower($p['descricao'])) ?>">
          <div class="card-image">
            <img src="<?= pecaImagem($p['nome'], $p['sigla'], $p['shape'], $p['imagem'] ?? null) ?>" alt="Imagem da peca <?= htmlspecialchars($p['nome']) ?>">
            <div class="card-badge"><?= htmlspecialchars($p['categoria'] ?? 'Motor') ?></div>
          </div>
          <div class="card-body">
            <div class="card-name"><?= htmlspecialchars($p['nome']) ?></div>
            <div class="card-desc"><?= htmlspecialchars($p['descricao']) ?></div>
          </div>
          <div class="card-footer">
            <div class="card-price"><span>R$</span><?= number_format((float)$p['preco'], 2, ',', '.') ?></div>
            <div class="card-stock">Comprar</div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <footer>
    <div class="logo"><span>Moto</span>Pecas</div>
    <p>© 2024 Motopecas Central · Montes Claros, MG</p>
  </footer>

  <script>
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.08 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

    const input = document.getElementById('busca');
    const cards = document.querySelectorAll('.product-card');
    const info = document.getElementById('resultado-busca');
    const total = document.getElementById('total-cards');

    input.addEventListener('input', () => {
      const q = input.value.trim().toLowerCase();
      let visivel = 0;
      cards.forEach(card => {
        const match = !q || (card.dataset.nome || '').includes(q) || (card.dataset.desc || '').includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visivel++;
      });
      total.textContent = visivel;
      info.textContent = q ? `${visivel} resultado${visivel !== 1 ? 's' : ''}` : '';
    });
  </script>
</body>
</html>
