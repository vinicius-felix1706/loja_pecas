<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Painel Administrativo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{--amber:#E8A020;--amber-lt:#F5C842;--rust:#C4520A;--ink:#1A1410;--paper:#FAF3E0;--paper-dk:#F0E6C8;--muted:#7A6A50;--line:rgba(232,160,32,.25)}
    body{background:var(--paper);color:var(--ink);font-family:'Barlow',sans-serif;min-height:100vh;display:flex;flex-direction:column}
    body::before{content:'';position:fixed;inset:0;z-index:0;background-image:repeating-linear-gradient(0deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px),repeating-linear-gradient(90deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px);pointer-events:none}
    header{position:relative;z-index:1;background:var(--ink);border-bottom:2px solid var(--amber)}
    .header-inner{max-width:1100px;margin:0 auto;padding:18px 48px;display:flex;align-items:center;justify-content:space-between;gap:24px}
    .logo{font-family:'Bebas Neue',sans-serif;font-size:2rem;letter-spacing:.08em;color:var(--amber);text-decoration:none}.logo span{color:#fff}
    .logout{font-family:'Barlow Condensed',sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#9A8A72;text-decoration:none;border:1px solid rgba(255,255,255,.1);padding:7px 18px}
    main{position:relative;z-index:1;flex:1;max-width:1100px;width:100%;margin:0 auto;padding:56px 48px 72px}
    .eyebrow{font-family:'Barlow Condensed',sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--rust);margin-bottom:10px}
    h1{font-family:'Bebas Neue',sans-serif;font-size:3.6rem;line-height:.95;letter-spacing:.03em;margin-bottom:32px}
    h1 em{font-style:normal;color:var(--amber)}
    .flash{padding:14px 18px;margin-bottom:28px;font-family:'Barlow Condensed',sans-serif;font-weight:600;letter-spacing:.06em;border-left:4px solid}
    .flash-success{background:#edf7f0;border-color:#2a8a4e;color:#2a8a4e}.flash-danger{background:#fdf0ed;border-color:var(--rust);color:var(--rust)}
    .actions{display:grid;grid-template-columns:repeat(2,minmax(240px,1fr));gap:2px}
    .action{background:var(--paper-dk);border-left:4px solid transparent;padding:34px 32px;text-decoration:none;color:inherit;transition:border-color .2s,background .2s,transform .2s}
    .action:hover{border-color:var(--amber);background:#ede0c0;transform:translateY(-2px)}
    .action strong{display:block;font-family:'Barlow Condensed',sans-serif;font-size:1.35rem;letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px}
    .action span{display:block;color:var(--muted);line-height:1.55}
    .section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin:42px 0 16px}
    .section-title{font-family:'Barlow Condensed',sans-serif;font-size:1.15rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
    .search-panel{background:var(--paper-dk);border-left:4px solid var(--amber);padding:18px 20px;margin-bottom:18px;display:flex;align-items:center;gap:16px}
    .search-label{font-family:'Barlow Condensed',sans-serif;font-size:.75rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);white-space:nowrap}
    .search-wrap{position:relative;flex:1}
    .search-wrap::before{content:'';position:absolute;left:14px;top:50%;width:12px;height:12px;border:2px solid var(--muted);border-radius:50%;transform:translateY(-60%);pointer-events:none}
    .search-wrap::after{content:'';position:absolute;left:25px;top:50%;width:8px;height:2px;background:var(--muted);transform:translateY(4px) rotate(45deg);pointer-events:none}
    #busca-peca{width:100%;background:var(--paper);border:1px solid rgba(26,20,16,.15);border-bottom:2px solid rgba(26,20,16,.2);padding:11px 14px 11px 42px;font-family:'Barlow',sans-serif;font-size:.94rem;color:var(--ink);outline:none;transition:border-color .2s,background .2s}
    #busca-peca:focus{background:#fff;border-color:var(--amber);border-bottom-color:var(--amber)}
    #resultado-busca{font-family:'Barlow Condensed',sans-serif;font-size:.8rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--rust);white-space:nowrap}
    .products{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:2px}
    .product{background:var(--paper-dk);border-left:4px solid transparent;padding:22px 24px;display:flex;flex-direction:column;gap:14px}
    .product:hover{border-color:var(--amber)}
    .product-name{font-family:'Barlow Condensed',sans-serif;font-size:1.08rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase}
    .product-meta{display:flex;align-items:center;justify-content:space-between;gap:12px;color:var(--muted);font-size:.9rem}
    .product-price{font-family:'Bebas Neue',sans-serif;font-size:1.6rem;color:var(--ink);letter-spacing:.04em}
    .product-actions{margin-top:auto;display:flex;flex-direction:column;gap:8px}
    .delete-form{margin:0}
    .edit-btn,.delete-btn{width:100%;display:block;text-align:center;text-decoration:none;padding:10px 14px;font-family:'Barlow Condensed',sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;cursor:pointer;transition:background .2s,color .2s,border-color .2s}
    .edit-btn{border:1px solid rgba(232,160,32,.45);background:var(--amber);color:var(--ink)}
    .edit-btn:hover{background:var(--amber-lt);border-color:var(--amber-lt)}
    .delete-btn{border:1px solid rgba(196,82,10,.35);background:transparent;color:var(--rust)}
    .delete-btn:hover{background:var(--rust);border-color:var(--rust);color:#fff}
    .empty{background:var(--paper-dk);padding:24px;color:var(--muted)}
    .empty[hidden]{display:none}
    footer{position:relative;z-index:1;background:#0E0B07;padding:20px 48px;color:#4A3E2E;font-size:.72rem;letter-spacing:.08em}
    @media(max-width:700px){.header-inner,main{padding-left:20px;padding-right:20px}.actions{grid-template-columns:1fr}.section-head,.search-panel{align-items:stretch;flex-direction:column}#resultado-busca{white-space:normal}h1{font-size:2.8rem}}
  </style>
</head>
<body>
  <header>
    <div class="header-inner">
      <a href="?url=admin/dashboard" class="logo"><span>Moto</span>Pecas <span>Admin</span></a>
      <a href="?url=auth/logout" class="logout">Sair</a>
    </div>
  </header>

  <main>
    <div class="eyebrow">Area administrativa</div>
    <h1>Ola, <em><?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?></em>.</h1>

    <?php if (!empty($flash)): ?>
      <div class="flash flash-<?= htmlspecialchars($flash['tipo']) ?>"><?= htmlspecialchars($flash['mensagem']) ?></div>
    <?php endif; ?>

    <div class="actions">
      <a class="action" href="?url=admin/clientes">
        <strong>Ver clientes cadastrados</strong>
        <span>Lista apenas usuarios com perfil de cliente. Administradores ficam ocultos.</span>
      </a>
      <a class="action" href="?url=admin/pecas/create">
        <strong>Cadastrar peca</strong>
        <span>Adicione nome, descricao, preco, estoque e uma imagem para aparecer no card.</span>
      </a>
    </div>

    <div class="section-head">
      <div class="section-title">Pecas cadastradas</div>
      <div id="resultado-busca"><?= count($produtos ?? []) ?> peca<?= count($produtos ?? []) === 1 ? '' : 's' ?></div>
    </div>
    <?php if (empty($produtos)): ?>
      <div class="empty">Nenhuma peca cadastrada no banco.</div>
    <?php else: ?>
      <div class="search-panel">
        <label class="search-label" for="busca-peca">Pesquisar</label>
        <div class="search-wrap">
          <input type="text" id="busca-peca" placeholder="Digite nome, categoria, estoque ou preco..." autocomplete="off">
        </div>
      </div>
      <div class="empty" id="sem-resultados" hidden>Nenhuma peca encontrada para essa busca.</div>
      <div class="products">
        <?php foreach ($produtos as $produto): ?>
          <div class="product" data-search="<?= htmlspecialchars(strtolower(($produto['nome'] ?? '') . ' ' . ($produto['nome_categoria'] ?? '') . ' estoque ' . ($produto['estoque'] ?? '') . ' preco ' . number_format((float)($produto['preco'] ?? 0), 2, ',', '.'))) ?>">
            <div class="product-name"><?= htmlspecialchars($produto['nome'] ?? '') ?></div>
            <div class="product-meta">
              <span><?= htmlspecialchars($produto['nome_categoria'] ?? 'Sem categoria') ?></span>
              <span>Estoque: <?= (int)($produto['estoque'] ?? 0) ?></span>
            </div>
            <div class="product-price">R$ <?= number_format((float)($produto['preco'] ?? 0), 2, ',', '.') ?></div>
            <div class="product-actions">
              <form class="delete-form" action="?url=admin/pecas/delete" method="POST" onsubmit="return confirm('Remover esta peca do catalogo? Os clientes nao vao mais ve-la.');">
                <input type="hidden" name="id_produto" value="<?= (int)$produto['id'] ?>">
                <button type="submit" class="delete-btn">Remover peca</button>
              </form>
              <a class="edit-btn" href="?url=admin/pecas/edit/<?= (int)$produto['id'] ?>">Editar peca</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer>© 2024 Motopecas Central · Montes Claros, MG</footer>
  <script>
    const buscaPeca = document.getElementById('busca-peca');
    const produtos = document.querySelectorAll('.product');
    const resultadoBusca = document.getElementById('resultado-busca');
    const semResultados = document.getElementById('sem-resultados');

    if (buscaPeca) {
      buscaPeca.addEventListener('input', () => {
        const termo = buscaPeca.value.trim().toLowerCase();
        let visiveis = 0;

        produtos.forEach((produto) => {
          const encontrou = !termo || (produto.dataset.search || '').includes(termo);
          produto.style.display = encontrou ? '' : 'none';
          if (encontrou) visiveis++;
        });

        resultadoBusca.textContent = `${visiveis} peca${visiveis === 1 ? '' : 's'}`;
        semResultados.hidden = visiveis > 0;
      });
    }
  </script>
</body>
</html>
