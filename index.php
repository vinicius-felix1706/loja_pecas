<?php
// ── Roteador ────────────────────────────────────────────────────
define('ROOT', __DIR__ . '/admin');

require_once ROOT . '/config/conexao.php';
require_once ROOT . '/controllers/BaseController.php';
require_once ROOT . '/models/ClienteModel.php';
require_once ROOT . '/models/ProdutoModel.php';
require_once ROOT . '/models/PedidoModel.php';
require_once ROOT . '/models/ItemPedidoModel.php';
require_once ROOT . '/controllers/AuthController.php';
require_once ROOT . '/helpers/catalogo_pecas.php';

$url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

function exigirLoginComRole(string $role): void {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (empty($_SESSION['usuario_id'])) {
        header('Location: ?url=auth/login');
        exit;
    }

    if (($_SESSION['usuario_role'] ?? 'cliente') !== $role) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Acesso nao autorizado.'];
        header('Location: ' . ($role === 'admin' ? '?url=auth/login' : '?url=admin/dashboard'));
        exit;
    }
}

function usuarioSessao(): array {
    return [
        'id'    => $_SESSION['usuario_id'],
        'nome'  => $_SESSION['usuario_nome']  ?? '',
        'email' => $_SESSION['usuario_email'] ?? '',
        'role'  => $_SESSION['usuario_role']  ?? 'cliente',
    ];
}

function flashSessao(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    return null;
}

// Rotas que passam pelo AuthController
$rotasController = [
    'auth/login'      => ['AuthController', 'login'],
    'auth/cadastro'   => ['AuthController', 'cadastroForm'],
    'auth/autenticar' => ['AuthController', 'autenticar'],
    'auth/registrar'  => ['AuthController', 'registrar'],
    'auth/logout'     => ['AuthController', 'logout'],
];

if (isset($rotasController[$url])) {
    [$classe, $metodo] = $rotasController[$url];
    $controller = new $classe();
    $controller->$metodo();
    exit;
}

if ($url === 'admin/dashboard') {
    exigirLoginComRole('admin');
    $usuario = usuarioSessao();
    $flash = flashSessao();
    try {
        $produtoModel = new ProdutoModel();
        $produtos = $produtoModel->all();
    } catch (Throwable $e) {
        $produtos = [];
        $flash = $flash ?: ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel carregar as pecas.'];
    }
    require ROOT . '/views/admin/dashboard.php';
    exit;
}

if ($url === 'admin/clientes') {
    exigirLoginComRole('admin');
    $usuario = usuarioSessao();
    $flash = flashSessao();
    $clienteModel = new ClienteModel();
    $clientes = $clienteModel->allClientes();
    require ROOT . '/views/admin/clientes.php';
    exit;
}

if ($url === 'admin/clientes/delete') {
    exigirLoginComRole('admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?url=admin/clientes');
        exit;
    }

    $idCliente = (int)($_POST['id_cliente'] ?? 0);
    if ($idCliente <= 0) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Cliente invalido.'];
        header('Location: ?url=admin/clientes');
        exit;
    }

    $clienteModel = new ClienteModel();
    if ($clienteModel->deleteComRelacionados($idCliente)) {
        $_SESSION['flash'] = ['tipo' => 'success', 'mensagem' => 'Cliente e dados relacionados foram excluidos.'];
    } else {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel excluir o cliente.'];
    }

    header('Location: ?url=admin/clientes');
    exit;
}

if ($url === 'admin/pecas/create') {
    exigirLoginComRole('admin');
    $usuario = usuarioSessao();
    $flash = flashSessao();
    $produto = null;
    require ROOT . '/views/admin/peca_form.php';
    exit;
}

if (preg_match('#^admin/pecas/edit/(\d+)$#', $url, $m)) {
    exigirLoginComRole('admin');
    $usuario = usuarioSessao();
    $flash = flashSessao();

    try {
        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->find((int)$m[1]);
    } catch (Throwable $e) {
        $produto = null;
    }

    if (!$produto) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Peca nao encontrada.'];
        header('Location: ?url=admin/dashboard');
        exit;
    }

    require ROOT . '/views/admin/peca_form.php';
    exit;
}

if ($url === 'admin/pecas/store') {
    exigirLoginComRole('admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?url=admin/pecas/create');
        exit;
    }

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? '0'));
    $estoque = (int)($_POST['estoque'] ?? 0);
    $idCategoria = (int)($_POST['id_categoria'] ?? 1);

    if ($nome === '' || !is_numeric($preco)) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Informe nome e preco validos.'];
        header('Location: ?url=admin/pecas/create');
        exit;
    }

    $imagem = null;
    if (!empty($_FILES['imagem']['name']) && is_uploaded_file($_FILES['imagem']['tmp_name'])) {
        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $mime = mime_content_type($_FILES['imagem']['tmp_name']);

        if (!isset($permitidos[$mime])) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Envie uma imagem JPG, PNG, WEBP ou GIF.'];
            header('Location: ?url=admin/pecas/create');
            exit;
        }

        $dirUpload = ROOT . '/uploads/produtos';
        if (!is_dir($dirUpload)) {
            mkdir($dirUpload, 0775, true);
        }

        $arquivo = uniqid('peca_', true) . '.' . $permitidos[$mime];
        $destino = $dirUpload . '/' . $arquivo;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
            $imagem = 'admin/uploads/produtos/' . $arquivo;
        }
    }

    try {
        $produtoModel = new ProdutoModel();
        $produtoModel->create([
            'nome' => $nome,
            'descricao' => $descricao,
            'preco' => (float)$preco,
            'estoque' => $estoque,
            'id_categoria' => $idCategoria > 0 ? $idCategoria : 1,
            'imagem' => $imagem,
        ]);
        $_SESSION['flash'] = ['tipo' => 'success', 'mensagem' => 'Peca cadastrada com sucesso.'];
        header('Location: ?url=admin/dashboard');
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao cadastrar peca. Confira as colunas e a categoria no banco.'];
        header('Location: ?url=admin/pecas/create');
    }
    exit;
}

if ($url === 'admin/pecas/update') {
    exigirLoginComRole('admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?url=admin/dashboard');
        exit;
    }

    $idProduto = (int)($_POST['id_produto'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? '0'));
    $estoque = (int)($_POST['estoque'] ?? 0);
    $idCategoria = (int)($_POST['id_categoria'] ?? 1);

    if ($idProduto <= 0 || $nome === '' || !is_numeric($preco)) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Informe nome e preco validos.'];
        header('Location: ' . ($idProduto > 0 ? '?url=admin/pecas/edit/' . $idProduto : '?url=admin/dashboard'));
        exit;
    }

    try {
        $produtoModel = new ProdutoModel();
        $produtoAtual = $produtoModel->find($idProduto);

        if (!$produtoAtual) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Peca nao encontrada.'];
            header('Location: ?url=admin/dashboard');
            exit;
        }

        $imagem = $produtoAtual['imagem'] ?? null;
        $novaImagem = null;

        if (!empty($_FILES['imagem']['name']) && is_uploaded_file($_FILES['imagem']['tmp_name'])) {
            $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
            $mime = mime_content_type($_FILES['imagem']['tmp_name']);

            if (!isset($permitidos[$mime])) {
                $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Envie uma imagem JPG, PNG, WEBP ou GIF.'];
                header('Location: ?url=admin/pecas/edit/' . $idProduto);
                exit;
            }

            $dirUpload = ROOT . '/uploads/produtos';
            if (!is_dir($dirUpload)) {
                mkdir($dirUpload, 0775, true);
            }

            $arquivo = uniqid('peca_', true) . '.' . $permitidos[$mime];
            $destino = $dirUpload . '/' . $arquivo;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $novaImagem = 'admin/uploads/produtos/' . $arquivo;
                $imagem = $novaImagem;
            }
        }

        $produtoModel->update($idProduto, [
            'nome' => $nome,
            'descricao' => $descricao,
            'preco' => (float)$preco,
            'estoque' => $estoque,
            'id_categoria' => $idCategoria > 0 ? $idCategoria : 1,
            'imagem' => $imagem,
        ]);

        if ($novaImagem && !empty($produtoAtual['imagem']) && strpos($produtoAtual['imagem'], 'admin/uploads/produtos/') === 0) {
            $caminhoImagem = __DIR__ . '/' . $produtoAtual['imagem'];
            $dirUploads = realpath(ROOT . '/uploads/produtos');
            $imagemReal = realpath($caminhoImagem);

            if ($dirUploads && $imagemReal && strpos($imagemReal, $dirUploads) === 0 && is_file($imagemReal)) {
                unlink($imagemReal);
            }
        }

        $_SESSION['flash'] = ['tipo' => 'success', 'mensagem' => 'Peca atualizada com sucesso.'];
        header('Location: ?url=admin/dashboard');
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao atualizar peca. Confira os dados e tente novamente.'];
        header('Location: ?url=admin/pecas/edit/' . $idProduto);
    }
    exit;
}

if ($url === 'admin/pecas/delete') {
    exigirLoginComRole('admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?url=admin/dashboard');
        exit;
    }

    $idProduto = (int)($_POST['id_produto'] ?? 0);
    if ($idProduto <= 0) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Peca invalida.'];
        header('Location: ?url=admin/dashboard');
        exit;
    }

    try {
        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->find($idProduto);

        if (!$produto) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Peca nao encontrada.'];
            header('Location: ?url=admin/dashboard');
            exit;
        }

        $produtoModel->delete($idProduto);

        if (!empty($produto['imagem']) && strpos($produto['imagem'], 'admin/uploads/produtos/') === 0) {
            $caminhoImagem = __DIR__ . '/' . $produto['imagem'];
            $dirUploads = realpath(ROOT . '/uploads/produtos');
            $imagemReal = realpath($caminhoImagem);

            if ($dirUploads && $imagemReal && strpos($imagemReal, $dirUploads) === 0 && is_file($imagemReal)) {
                unlink($imagemReal);
            }
        }

        $_SESSION['flash'] = ['tipo' => 'success', 'mensagem' => 'Peca removida do catalogo.'];
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel remover a peca. Verifique se ela esta vinculada a pedidos.'];
    }

    header('Location: ?url=admin/dashboard');
    exit;
}

// Rota: confirmacao de compra da peca
if (preg_match('#^peca/(\d+)$#', $url, $m)) {
    exigirLoginComRole('cliente');
    $usuario = usuarioSessao();
    $peca = null;

    try {
        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->find((int)$m[1]);
        if ($produto) {
            $peca = produtoParaPeca($produto);
        }
    } catch (Throwable $e) {
        $peca = null;
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel carregar a peca.'];
        header('Location: ?url=dashboard');
        exit;
    }

    if (!$peca) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Peca nao encontrada.'];
        header('Location: ?url=dashboard');
        exit;
    }

    require ROOT . '/views/compra/confirmar.php';
    exit;
}

// Rota: finalizar compra simulada
if ($url === 'compra/finalizar') {
    exigirLoginComRole('cliente');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?url=dashboard');
        exit;
    }

    $idPeca = (int)($_POST['id_peca'] ?? 0);
    $peca = null;
    try {
        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->find($idPeca);
        if ($produto) {
            $peca = produtoParaPeca($produto);
        }
    } catch (Throwable $e) {
        $peca = null;
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel carregar a peca.'];
        header('Location: ?url=dashboard');
        exit;
    }
    if (!$peca) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Peca nao encontrada.'];
        header('Location: ?url=dashboard');
        exit;
    }

    try {
        $pedidoModel = new PedidoModel();
        $itemModel = new ItemPedidoModel();
        $idCliente = (int)$_SESSION['usuario_id'];
        $pedidoExistente = $pedidoModel->findByClienteProduto($idCliente, (int)$peca['id']);

        if ($pedidoExistente) {
            $novaQuantidade = (int)$pedidoExistente['quantidade'] + 1;
            $novoTotal = $novaQuantidade * (float)$peca['preco'];

            $itemModel->updateQuantidade((int)$pedidoExistente['id_item'], $novaQuantidade);
            $pedidoModel->updateTotal((int)$pedidoExistente['id'], $idCliente, $novoTotal);

            $_SESSION['flash'] = ['tipo' => 'success', 'mensagem' => 'Pedido feito. Quantidade atualizada.'];
        } else {
            $idPedido = $pedidoModel->create([
                'id_cliente' => $idCliente,
                'status' => 'Pedido feito',
                'total' => $peca['preco'],
            ]);

            if ($idPedido) {
                $itemModel->create([
                    'id_pedido' => $idPedido,
                    'id_produto' => $peca['id'],
                    'quantidade' => 1,
                ]);
                $_SESSION['flash'] = ['tipo' => 'success', 'mensagem' => 'Pedido feito'];
            } else {
                $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel criar o pedido.'];
            }
        }
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao registrar pedido. Confira se a migracao do banco foi executada.'];
    }

    header('Location: ?url=dashboard');
    exit;
}

// Rota: remover pedido do usuario logado
if ($url === 'pedido/remover') {
    exigirLoginComRole('cliente');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?url=pedidos');
        exit;
    }

    $idPedido = (int)($_POST['id_pedido'] ?? 0);
    $idCliente = (int)$_SESSION['usuario_id'];

    if ($idPedido <= 0) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Pedido invalido.'];
        header('Location: ?url=pedidos');
        exit;
    }

    try {
        $pedidoModel = new PedidoModel();
        $itemModel = new ItemPedidoModel();

        if (!$pedidoModel->pertenceAoCliente($idPedido, $idCliente)) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Pedido nao encontrado.'];
            header('Location: ?url=pedidos');
            exit;
        }

        $itemModel->deleteByPedido($idPedido);
        $pedidoModel->delete($idPedido, $idCliente);
        $_SESSION['flash'] = ['tipo' => 'success', 'mensagem' => 'Pedido removido.'];
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel remover o pedido.'];
    }

    header('Location: ?url=pedidos');
    exit;
}

// Rota: cliente excluir a propria conta
if ($url === 'conta/excluir') {
    exigirLoginComRole('cliente');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?url=dashboard');
        exit;
    }

    $idCliente = (int)$_SESSION['usuario_id'];

    try {
        $clienteModel = new ClienteModel();

        if ($clienteModel->deleteComRelacionados($idCliente)) {
            unset(
                $_SESSION['usuario_id'],
                $_SESSION['usuario_nome'],
                $_SESSION['usuario_email'],
                $_SESSION['usuario_role']
            );
            $_SESSION['flash'] = ['tipo' => 'success', 'mensagem' => 'Conta excluida com sucesso. Faca um novo cadastro quando quiser voltar.'];
            header('Location: ?url=auth/cadastro');
            exit;
        }

        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel excluir sua conta.'];
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao excluir sua conta. Tente novamente.'];
    }

    header('Location: ?url=dashboard');
    exit;
}

// Rota: meus pedidos
if ($url === 'pedidos') {
    exigirLoginComRole('cliente');
    $usuario = usuarioSessao();
    $flash = flashSessao();

    try {
        $pedidoModel = new PedidoModel();
        $pedidos = $pedidoModel->allByCliente((int)$usuario['id']);
    } catch (Throwable $e) {
        $pedidos = [];
        $flash = ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel carregar seus pedidos. Confira se a migracao do banco foi executada.'];
    }

    require ROOT . '/views/pedidos/index.php';
    exit;
}

// Rota: dashboard
if ($url === 'dashboard') {
    exigirLoginComRole('cliente');
    $usuario = usuarioSessao();

    // Carrega produtos com categoria para os cards
    $flash = flashSessao();
    try {
        $produtoModel = new ProdutoModel();
        $produtos = $produtoModel->all();
    } catch (Throwable $e) {
        $produtos = [];
        $flash = $flash ?: ['tipo' => 'danger', 'mensagem' => 'Nao foi possivel carregar as pecas.'];
    }

    require ROOT . '/views/dashboard/index.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Motopeças Central</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --amber:     #E8A020;
      --amber-lt:  #F5C842;
      --amber-dk:  #B87A0A;
      --rust:      #C4520A;
      --ink:       #1A1410;
      --ink-soft:  #2E2318;
      --paper:     #FAF3E0;
      --paper-dk:  #F0E6C8;
      --muted:     #7A6A50;
      --line:      rgba(232,160,32,.25);
    }

    html { scroll-behavior: smooth; }

    body {
      background: var(--paper);
      color: var(--ink);
      font-family: 'Barlow', sans-serif;
      font-size: 16px;
      line-height: 1.6;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* ── Textura de fundo ── */
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0;
      background-image:
        repeating-linear-gradient(0deg, transparent, transparent 39px, var(--line) 39px, var(--line) 40px),
        repeating-linear-gradient(90deg, transparent, transparent 39px, var(--line) 39px, var(--line) 40px);
      pointer-events: none;
    }

    /* ── Layout ── */
    .wrap {
      position: relative; z-index: 1;
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 48px;
    }

    /* ── Header ── */
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
    }
    .logo span { color: #fff; }
    .header-tag {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .75rem;
      font-weight: 500;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: var(--muted);
    }

    /* ── Hero ── */
    .hero {
      position: relative;
      padding: 96px 0 72px;
      overflow: hidden;
    }
    /* texto decorativo de fundo */
    .hero::after {
      content: 'certo';
      position: absolute;
      right: -20px; top: 10px;
      font-family: 'Bebas Neue', sans-serif;
      font-size: 32rem;
      line-height: 1;
      color: var(--amber);
      opacity: .04;
      pointer-events: none;
      user-select: none;
    }

    .hero-eyebrow {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .8rem;
      font-weight: 700;
      letter-spacing: .22em;
      text-transform: uppercase;
      color: var(--rust);
      margin-bottom: 18px;
      display: flex; align-items: center; gap: 10px;
    }
    .hero-eyebrow::before {
      content: '';
      display: inline-block;
      width: 32px; height: 2px;
      background: var(--rust);
    }

    h1 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(3.8rem, 8vw, 7rem);
      line-height: .95;
      letter-spacing: .02em;
      color: var(--ink);
      max-width: 680px;
      margin-bottom: 12px;
    }
    h1 em {
      font-style: normal;
      color: var(--amber);
      -webkit-text-stroke: 1px var(--amber-dk);
    }

    .hero-sub {
      font-size: 1.1rem;
      color: var(--muted);
      max-width: 480px;
      margin: 24px 0 0;
    }

    /* ── Faixa de stats ── */
    .stats {
      position: relative; z-index: 1;
      background: var(--ink);
      border-top: 3px solid var(--amber);
      border-bottom: 3px solid var(--amber);
    }
    .stats-inner {
      display: flex;
      max-width: 1100px;
      margin: 0 auto;
    }
    .stat {
      flex: 1;
      padding: 28px 48px;
      border-right: 1px solid rgba(255,255,255,.07);
    }
    .stat:last-child { border-right: none; }
    .stat-num {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2.8rem;
      color: var(--amber);
      line-height: 1;
    }
    .stat-label {
      font-size: .78rem;
      font-weight: 600;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: #9A8A72;
      margin-top: 4px;
    }

    /* ── Seções de conteúdo ── */
    section {
      position: relative; z-index: 1;
      padding: 80px 0;
    }
    section + section {
      border-top: 1px solid var(--paper-dk);
    }

    .section-label {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .72rem;
      font-weight: 700;
      letter-spacing: .22em;
      text-transform: uppercase;
      color: var(--amber-dk);
      margin-bottom: 14px;
    }

    h2 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(2rem, 4vw, 3rem);
      letter-spacing: .03em;
      color: var(--ink);
      margin-bottom: 20px;
    }

    /* ── Diferenciais ── */
    .grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2px;
      margin-top: 48px;
    }
    .card {
      background: var(--paper-dk);
      padding: 36px 32px;
      border-left: 4px solid transparent;
      transition: border-color .2s, background .2s;
    }
    .card:hover {
      border-color: var(--amber);
      background: #ede0c0;
    }
    .card-icon {
      font-size: 2rem;
      margin-bottom: 16px;
      display: block;
    }
    .card h3 {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.15rem;
      font-weight: 700;
      letter-spacing: .05em;
      text-transform: uppercase;
      color: var(--ink);
      margin-bottom: 8px;
    }
    .card p {
      font-size: .9rem;
      color: var(--muted);
      line-height: 1.6;
    }

    /* ── Sobre ── */
    .sobre-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: start;
    }
    .sobre-text p {
      color: var(--muted);
      margin-bottom: 16px;
    }
    .sobre-text p strong {
      color: var(--ink);
      font-weight: 600;
    }
    .sobre-destaque {
      background: var(--ink);
      color: var(--paper);
      padding: 40px;
      position: relative;
    }
    .sobre-destaque::before {
      content: '"';
      font-family: 'Bebas Neue', sans-serif;
      font-size: 8rem;
      color: var(--amber);
      opacity: .3;
      position: absolute;
      top: -10px; left: 24px;
      line-height: 1;
    }
    .sobre-destaque blockquote {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.5rem;
      font-weight: 500;
      line-height: 1.4;
      color: #F5E8C8;
      position: relative;
      z-index: 1;
    }
    .sobre-destaque cite {
      display: block;
      margin-top: 20px;
      font-family: 'Barlow', sans-serif;
      font-size: .78rem;
      font-style: normal;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--amber);
    }

    /* ── CTA final ── */
    .cta {
      position: relative; z-index: 1;
      background: var(--ink);
      padding: 100px 0 80px;
      overflow: hidden;
    }
    .cta::before {
      content: 'PEÇAS';
      position: absolute;
      left: -20px; bottom: -40px;
      font-family: 'Bebas Neue', sans-serif;
      font-size: 18rem;
      color: var(--amber);
      opacity: .04;
      letter-spacing: -.01em;
      pointer-events: none;
      user-select: none;
      white-space: nowrap;
    }
    .cta-inner {
      position: relative; z-index: 1;
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 48px;
    }
    .cta h2 {
      color: var(--paper);
      font-size: clamp(2.2rem, 5vw, 4rem);
      margin-bottom: 14px;
    }
    .cta-sub {
      color: #9A8A72;
      font-size: 1rem;
      max-width: 440px;
      margin-bottom: 52px;
    }
    .cta-buttons {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 16px 40px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      text-decoration: none;
      transition: transform .15s, box-shadow .15s;
      cursor: pointer;
      border: none;
    }
    .btn:hover { transform: translateY(-2px); }
    .btn-primary {
      background: var(--amber);
      color: var(--ink);
      box-shadow: 0 4px 24px rgba(232,160,32,.35);
    }
    .btn-primary:hover {
      background: var(--amber-lt);
      box-shadow: 0 8px 32px rgba(232,160,32,.5);
    }
    .btn-outline {
      background: transparent;
      color: var(--paper);
      border: 2px solid rgba(255,255,255,.25);
    }
    .btn-outline:hover {
      border-color: var(--amber);
      color: var(--amber);
    }
    .btn-arrow { font-size: 1.1rem; transition: transform .15s; }
    .btn:hover .btn-arrow { transform: translateX(4px); }

    /* ── Footer ── */
    footer {
      position: relative; z-index: 1;
      background: #0E0B07;
      padding: 24px 48px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      max-width: 100%;
    }
    footer .logo { font-size: 1.1rem; }
    footer p {
      font-size: .75rem;
      color: #4A3E2E;
      letter-spacing: .08em;
    }

    /* ── Animações de entrada ── */
    .reveal {
      opacity: 0;
      transform: translateY(24px);
      transition: opacity .6s ease, transform .6s ease;
    }
    .reveal.visible {
      opacity: 1;
      transform: none;
    }
    .reveal-delay-1 { transition-delay: .1s; }
    .reveal-delay-2 { transition-delay: .2s; }
    .reveal-delay-3 { transition-delay: .3s; }

    @media (max-width: 768px) {
      .wrap, .header-inner, .cta-inner { padding: 0 24px; }
      .grid-3 { grid-template-columns: 1fr; }
      .sobre-layout { grid-template-columns: 1fr; gap: 40px; }
      .stats-inner { flex-direction: column; }
      .stat { border-right: none; border-bottom: 1px solid rgba(255,255,255,.07); padding: 20px 24px; }
      h1 { font-size: 3.2rem; }
      .hero::after { font-size: 14rem; }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
    <div class="header-inner">
      <div class="logo"><span>Moto</span>Peças <span>Central</span></div>
      <div class="header-tag">Propósito &nbsp;·&nbsp; Minas Gerais</div>
    </div>
  </header>

  <!-- Hero -->
  <div class="wrap">
    <div class="hero">
      <div class="hero-eyebrow">Especialistas em peças automotivas</div>
      <h1>A peça certa,<br>na hora <em>certa.</em></h1>
      <p class="hero-sub">
        Ajudamos você a encontrar a peça certa para manter seu veículo seguro, pronto para rodar e sem perder tempo. Unimos atendimento próximo, orientação técnica e compromisso com cada solução.
      </p>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="stats-inner">
      <div class="stat reveal">
        <div class="stat-num">Certo</div>
        <div class="stat-label">Peça indicada</div>
      </div>
      <div class="stat reveal reveal-delay-1">
        <div class="stat-num">Guia</div>
        <div class="stat-label">Orientação técnica</div>
      </div>
      <div class="stat reveal reveal-delay-2">
        <div class="stat-num">+8K</div>
        <div class="stat-label">Clientes atendidos</div>
      </div>
      <div class="stat reveal reveal-delay-3">
        <div class="stat-num">24h</div>
        <div class="stat-label">Prazo de entrega</div>
      </div>
    </div>
  </div>

  <!-- Diferenciais -->
  <div class="wrap">
    <section>
      <div class="section-label reveal">Por que escolher a gente</div>
      <h2 class="reveal">Do motor ao<br>retrovisor.</h2>
      <div class="grid-3">
        <div class="card reveal">
          <span class="card-icon">⚙️</span>
          <h3>Peças originais</h3>
          <p>Trabalhamos com distribuidores autorizados. Cada peça com procedência garantida e nota fiscal.</p>
        </div>
        <div class="card reveal reveal-delay-1">
          <span class="card-icon">🔍</span>
          <h3>Busca especializada</h3>
          <p>Não achou no estoque? Nossa equipe localiza a peça que você precisa em até 48 horas.</p>
        </div>
        <div class="card reveal reveal-delay-2">
          <span class="card-icon">🚚</span>
          <h3>Entrega na região</h3>
          <p>Atendemos toda a região de Montes Claros e cidades vizinhas com frota própria.</p>
        </div>
        <div class="card reveal">
          <span class="card-icon">🛡️</span>
          <h3>Garantia real</h3>
          <p>Todas as peças com garantia de fábrica. Problema? A gente resolve sem enrolação.</p>
        </div>
        <div class="card reveal reveal-delay-1">
          <span class="card-icon">💳</span>
          <h3>Parcelamento</h3>
          <p>Pagamento facilitado em até 10x no cartão ou à vista com desconto especial.</p>
        </div>
        <div class="card reveal reveal-delay-2">
          <span class="card-icon">🔧</span>
          <h3>Suporte técnico</h3>
          <p>Dúvida na instalação? Nossa equipe orienta você pelo telefone ou pessoalmente.</p>
        </div>
      </div>
    </section>

    <!-- Sobre -->
    <section>
      <div class="sobre-layout">
        <div class="sobre-text reveal">
          <div class="section-label">Quem somos</div>
          <h2>Confiança para você seguir rodando</h2>
          <p>A <strong>Motopeças Central</strong> existe para simplificar a vida de quem depende do veículo todos os dias. Nosso papel é ouvir, entender a necessidade e indicar peças compatíveis com segurança e clareza.</p>
          <p>Trabalhamos com peças originais, paralelas homologadas e acessórios das principais marcas do mercado, sempre buscando a melhor combinação entre qualidade, prazo e custo-benefício.</p>
          <p>Para oficinas e mecânicos, oferecemos <strong>condições especiais de pagamento</strong> e atendimento preferencial com pedidos por WhatsApp.</p>
        </div>
        <div class="sobre-destaque reveal reveal-delay-1">
          <blockquote>
            "Quando o cliente chega aqui precisando de uma peça, ele sai com a solução. Esse é nosso compromisso desde o primeiro dia."
          </blockquote>
          <cite>— Fundadores, Motopeças Central</cite>
        </div>
      </div>
    </section>
  </div>

  <!-- CTA -->
  <div class="cta">
    <div class="cta-inner">
      <div class="section-label" style="color: var(--amber-dk);">Acesso ao sistema</div>
      <h2>Pronto para<br>começar?</h2>
      <p class="cta-sub">Faça seu cadastro para acompanhar pedidos e histórico de compras, ou entre com sua conta existente.</p>
      <div class="cta-buttons">
        <a href="?url=auth/cadastro" class="btn btn-primary">
          Criar conta <span class="btn-arrow">→</span>
        </a>
        <a href="?url=auth/login" class="btn btn-outline">
          Já tenho conta &nbsp; Entrar
        </a>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="logo"><span>Moto</span>Peças</div>
    <p>© 2024 Motopeças Central · Montes Claros, MG</p>
  </footer>

  <script>
    // Reveal on scroll
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
  </script>

</body>
</html>
