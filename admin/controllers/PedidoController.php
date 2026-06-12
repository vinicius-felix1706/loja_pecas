<?php
// Controller antigo para pedidos e seus itens.

require_once ROOT . '/app/controllers/BaseController.php';
require_once ROOT . '/app/models/PedidoModel.php';
require_once ROOT . '/app/models/ItemPedidoModel.php';
require_once ROOT . '/app/models/ClienteModel.php';
require_once ROOT . '/app/models/ProdutoModel.php';

class PedidoController extends BaseController {

    private PedidoModel     $model;
    private ItemPedidoModel $itemModel;
    private ClienteModel    $clienteModel;
    private ProdutoModel    $produtoModel;

    public function __construct() {
        $this->requireAuth();
        $this->model        = new PedidoModel();
        $this->itemModel    = new ItemPedidoModel();
        $this->clienteModel = new ClienteModel();
        $this->produtoModel = new ProdutoModel();
    }

    public function index(): void {
        $pedidos = $this->model->all();
        $this->render('pedido/index', ['pedidos' => $pedidos, 'flash' => $this->getFlash()]);
    }

    public function create(): void {
        $clientes = $this->clienteModel->all();
        $this->render('pedido/form', ['pedido' => null, 'clientes' => $clientes, 'flash' => $this->getFlash()]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('?url=pedido'); }

        $data = [
            'id_cliente' => (int)($_POST['id_cliente'] ?? 0),
            'status'     => $_POST['status'] ?? 'pendente',
            'total'      => 0,
        ];

        if (!$data['id_cliente']) {
            $this->setFlash('danger', 'Selecione um cliente.');
            $this->redirect('?url=pedido/create');
        }

        $idPedido = $this->model->create($data);
        if ($idPedido) {
            $this->setFlash('success', 'Pedido criado! Adicione os itens.');
            $this->redirect("?url=pedido/show/{$idPedido}");
        }

        $this->setFlash('danger', 'Erro ao criar pedido.');
        $this->redirect('?url=pedido');
    }

    // Detalhe do pedido + itens
    public function show(?string $id): void {
        $pedido   = $this->model->find((int)$id);
        if (!$pedido) { $this->setFlash('danger', 'Pedido não encontrado.'); $this->redirect('?url=pedido'); }
        $itens    = $this->itemModel->allByPedido((int)$id);
        $produtos = $this->produtoModel->all();
        $this->render('pedido/show', [
            'pedido'   => $pedido,
            'itens'    => $itens,
            'produtos' => $produtos,
            'flash'    => $this->getFlash(),
        ]);
    }

    public function edit(?string $id): void {
        $pedido   = $this->model->find((int)$id);
        if (!$pedido) { $this->setFlash('danger', 'Pedido não encontrado.'); $this->redirect('?url=pedido'); }
        $clientes = $this->clienteModel->all();
        $this->render('pedido/form', ['pedido' => $pedido, 'clientes' => $clientes, 'flash' => $this->getFlash()]);
    }

    public function update(?string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('?url=pedido'); }

        $data = [
            'id_cliente' => (int)($_POST['id_cliente'] ?? 0),
            'status'     => $_POST['status'] ?? 'pendente',
        ];

        if ($this->model->update((int)$id, $data)) {
            $this->setFlash('success', 'Pedido atualizado!');
        } else {
            $this->setFlash('danger', 'Erro ao atualizar pedido.');
        }
        $this->redirect("?url=pedido/show/{$id}");
    }

    public function delete(?string $id): void {
        if ($this->model->delete((int)$id)) {
            $this->setFlash('success', 'Pedido removido!');
        } else {
            $this->setFlash('danger', 'Erro ao remover pedido.');
        }
        $this->redirect('?url=pedido');
    }

    // ── Itens ──────────────────────────────────────────────

    public function addItem(?string $idPedido): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('?url=pedido'); }

        $idProduto  = (int)($_POST['id_produto']  ?? 0);
        $quantidade = (int)($_POST['quantidade']  ?? 1);
        $produto    = $this->produtoModel->find($idProduto);

        if (!$produto) {
            $this->setFlash('danger', 'Produto não encontrado.');
            $this->redirect("?url=pedido/show/{$idPedido}");
        }

        $data = [
            'id_pedido'      => (int)$idPedido,
            'id_produto'     => $idProduto,
            'quantidade'     => $quantidade,
            'preco_unitario' => $produto['preco'],
        ];

        if ($this->itemModel->create($data)) {
            $this->model->updateTotal((int)$idPedido);
            $this->setFlash('success', 'Item adicionado!');
        } else {
            $this->setFlash('danger', 'Erro ao adicionar item.');
        }
        $this->redirect("?url=pedido/show/{$idPedido}");
    }

    public function removeItem(?string $idItem): void {
        $item = $this->itemModel->find((int)$idItem);
        if ($item) {
            $this->itemModel->delete((int)$idItem);
            $this->model->updateTotal($item['id_pedido']);
            $this->setFlash('success', 'Item removido!');
            $this->redirect("?url=pedido/show/{$item['id_pedido']}");
        }
        $this->setFlash('danger', 'Item não encontrado.');
        $this->redirect('?url=pedido');
    }
}
