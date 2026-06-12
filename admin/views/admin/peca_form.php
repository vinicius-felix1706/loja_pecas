<?php
$editando = !empty($produto);
$acao = $editando ? '?url=admin/pecas/update' : '?url=admin/pecas/store';
$titulo = $editando ? 'Editar peca' : 'Cadastrar peca';
$botao = $editando ? 'Salvar alteracoes' : 'Cadastrar peca';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titulo) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{--amber:#E8A020;--amber-lt:#F5C842;--rust:#C4520A;--ink:#1A1410;--paper:#FAF3E0;--paper-dk:#F0E6C8;--muted:#7A6A50;--line:rgba(232,160,32,.25)}
    body{background:var(--paper);color:var(--ink);font-family:'Barlow',sans-serif;min-height:100vh}
    body::before{content:'';position:fixed;inset:0;background-image:repeating-linear-gradient(0deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px),repeating-linear-gradient(90deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px);pointer-events:none}
    header{position:relative;background:var(--ink);border-bottom:2px solid var(--amber)}
    .header-inner,main{position:relative;max-width:760px;margin:0 auto;padding-left:32px;padding-right:32px}
    .header-inner{padding-top:18px;padding-bottom:18px;display:flex;align-items:center;justify-content:space-between;gap:18px}
    .logo{font-family:'Bebas Neue',sans-serif;font-size:2rem;letter-spacing:.08em;color:var(--amber);text-decoration:none}.logo span{color:#fff}
    .back{font-family:'Barlow Condensed',sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--amber);text-decoration:none}
    main{padding-top:48px;padding-bottom:72px}
    .eyebrow{font-family:'Barlow Condensed',sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--rust);margin-bottom:10px}
    h1{font-family:'Bebas Neue',sans-serif;font-size:3.2rem;line-height:.95;margin-bottom:30px}
    .flash{padding:14px 18px;margin-bottom:24px;font-family:'Barlow Condensed',sans-serif;font-weight:600;letter-spacing:.06em;border-left:4px solid}
    .flash-danger{background:#fdf0ed;border-color:var(--rust);color:var(--rust)}
    form{background:var(--paper-dk);border-left:4px solid var(--amber);padding:34px}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .field{margin-bottom:20px}.field.full{grid-column:1/-1}
    label{display:block;font-family:'Barlow Condensed',sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;margin-bottom:8px}
    input,textarea{width:100%;background:var(--paper);border:1px solid rgba(26,20,16,.18);border-bottom:2px solid rgba(26,20,16,.25);padding:13px 16px;font:inherit;color:var(--ink);outline:none}
    textarea{min-height:110px;resize:vertical}input:focus,textarea:focus{border-color:var(--amber);background:#fff}
    .current-image{margin-top:8px;color:var(--muted);font-size:.86rem}
    button{width:100%;border:0;background:var(--amber);color:var(--ink);padding:16px 28px;font-family:'Barlow Condensed',sans-serif;font-size:1rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;cursor:pointer;box-shadow:0 4px 24px rgba(232,160,32,.35)}
    button:hover{background:var(--amber-lt)}
    @media(max-width:640px){.header-inner,main{padding-left:20px;padding-right:20px}.grid{grid-template-columns:1fr}form{padding:26px 20px}h1{font-size:2.7rem}}
  </style>
</head>
<body>
  <header>
    <div class="header-inner">
      <a href="?url=admin/dashboard" class="logo"><span>Moto</span>Pecas</a>
      <a href="?url=admin/dashboard" class="back">Voltar</a>
    </div>
  </header>

  <main>
    <div class="eyebrow">Catalogo</div>
    <h1><?= htmlspecialchars($titulo) ?></h1>

    <?php if (!empty($flash)): ?>
      <div class="flash flash-<?= htmlspecialchars($flash['tipo']) ?>"><?= htmlspecialchars($flash['mensagem']) ?></div>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($acao) ?>" method="POST" enctype="multipart/form-data">
      <?php if ($editando): ?>
        <input type="hidden" name="id_produto" value="<?= (int)$produto['id'] ?>">
      <?php endif; ?>
      <div class="grid">
        <div class="field full">
          <label for="nome">Nome</label>
          <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome'] ?? '') ?>" required>
        </div>
        <div class="field full">
          <label for="descricao">Descricao do card</label>
          <textarea id="descricao" name="descricao"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
        </div>
        <div class="field">
          <label for="preco">Preco</label>
          <input type="text" id="preco" name="preco" placeholder="199.90" value="<?= isset($produto['preco']) ? htmlspecialchars(number_format((float)$produto['preco'], 2, '.', '')) : '' ?>" required>
        </div>
        <div class="field">
          <label for="estoque">Estoque</label>
          <input type="number" id="estoque" name="estoque" min="0" value="<?= (int)($produto['estoque'] ?? 0) ?>" required>
        </div>
        <div class="field">
          <label for="id_categoria">ID categoria</label>
          <input type="number" id="id_categoria" name="id_categoria" min="1" value="<?= (int)($produto['id_categoria'] ?? 1) ?>" required>
        </div>
        <div class="field">
          <label for="imagem">Imagem</label>
          <input type="file" id="imagem" name="imagem" accept="image/png,image/jpeg,image/webp,image/gif">
          <?php if ($editando && !empty($produto['imagem'])): ?>
            <div class="current-image">Imagem atual sera mantida se nenhuma nova for enviada.</div>
          <?php endif; ?>
        </div>
      </div>
      <button type="submit"><?= htmlspecialchars($botao) ?></button>
    </form>
  </main>
</body>
</html>
