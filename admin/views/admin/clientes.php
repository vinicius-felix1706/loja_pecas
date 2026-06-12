<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clientes Cadastrados</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{--amber:#E8A020;--rust:#C4520A;--ink:#1A1410;--paper:#FAF3E0;--paper-dk:#F0E6C8;--muted:#7A6A50;--line:rgba(232,160,32,.25)}
    body{background:var(--paper);color:var(--ink);font-family:'Barlow',sans-serif;min-height:100vh}
    body::before{content:'';position:fixed;inset:0;background-image:repeating-linear-gradient(0deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px),repeating-linear-gradient(90deg,transparent,transparent 39px,var(--line) 39px,var(--line) 40px);pointer-events:none}
    header{position:relative;background:var(--ink);border-bottom:2px solid var(--amber)}
    .header-inner,main{position:relative;max-width:1100px;margin:0 auto;padding-left:48px;padding-right:48px}
    .header-inner{padding-top:18px;padding-bottom:18px;display:flex;align-items:center;justify-content:space-between;gap:18px}
    .logo,.back{font-family:'Barlow Condensed',sans-serif;font-weight:700;letter-spacing:.14em;text-transform:uppercase;text-decoration:none}.logo{font-family:'Bebas Neue',sans-serif;font-size:2rem;letter-spacing:.08em;color:var(--amber)}.logo span{color:#fff}.back{color:var(--amber);font-size:.78rem}
    main{padding-top:48px;padding-bottom:72px}
    .eyebrow{font-family:'Barlow Condensed',sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--rust);margin-bottom:10px}
    h1{font-family:'Bebas Neue',sans-serif;font-size:3.2rem;line-height:.95;margin-bottom:30px}
    table{width:100%;border-collapse:collapse;background:var(--paper-dk)}
    th,td{text-align:left;padding:15px 18px;border-bottom:1px solid rgba(26,20,16,.08)}
    th{font-family:'Barlow Condensed',sans-serif;font-size:.78rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted)}
    td{font-size:.95rem}.empty{background:var(--paper-dk);padding:28px;color:var(--muted)}
    .flash{padding:14px 18px;margin-bottom:24px;font-size:.88rem;font-family:'Barlow Condensed',sans-serif;font-weight:600;letter-spacing:.06em;border-left:4px solid}
    .flash-success{background:#edf7f0;border-color:#2a8a4e;color:#2a8a4e}
    .flash-danger{background:#fdf0ed;border-color:var(--rust);color:var(--rust)}
    .actions{width:1%;white-space:nowrap}
    .delete-form{margin:0}
    .delete-client{background:transparent;color:var(--rust);border:1px solid rgba(196,82,10,.35);padding:7px 14px;font-family:'Barlow Condensed',sans-serif;font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;cursor:pointer;transition:background .2s,border-color .2s}
    .delete-client:hover{background:rgba(196,82,10,.1);border-color:var(--rust)}
    @media(max-width:700px){.header-inner,main{padding-left:20px;padding-right:20px}table,thead,tbody,tr,th,td{display:block}thead{display:none}td{border-bottom:0}tr{border-bottom:1px solid rgba(26,20,16,.12);padding:10px 0}}
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
    <div class="eyebrow">Clientes</div>
    <h1>Cadastrados</h1>

    <?php if (!empty($flash)): ?>
      <div class="flash flash-<?= htmlspecialchars($flash['tipo']) ?>"><?= htmlspecialchars($flash['mensagem']) ?></div>
    <?php endif; ?>

    <?php if (empty($clientes)): ?>
      <div class="empty">Nenhum cliente cadastrado.</div>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>Nome</th><th>E-mail</th><th>Telefone</th><th class="actions">Acoes</th></tr>
        </thead>
        <tbody>
          <?php foreach ($clientes as $cliente): ?>
            <tr>
              <td><?= htmlspecialchars($cliente['nome'] ?? '') ?></td>
              <td><?= htmlspecialchars($cliente['email'] ?? '') ?></td>
              <td><?= htmlspecialchars($cliente['telefone'] ?? '') ?></td>
              <td class="actions">
                <form class="delete-form" action="?url=admin/clientes/delete" method="POST" onsubmit="return confirm('Excluir este cliente? Todos os pedidos e dados relacionados serao removidos.');">
                  <input type="hidden" name="id_cliente" value="<?= (int)$cliente['id'] ?>">
                  <button type="submit" class="delete-client">Excluir cliente</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>
</body>
</html>
