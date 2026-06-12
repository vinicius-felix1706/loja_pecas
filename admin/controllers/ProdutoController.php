<?php
// Controller antigo de produtos, mantido como apoio ao painel.

require_once ROOT . '/app/controllers/BaseController.php';
require_once ROOT . '/app/models/ProdutoModel.php';
require_once ROOT . '/app/models/CategoriaModel.php';

class ProdutoController extends BaseController {

    private ProdutoModel   $model;
    private CategoriaModel $catModel;

    public function __construct() {
        $this->requireAuth();
        $this->model    = new ProdutoModel();
        $this->catModel = new CategoriaModel();
    }

    public function index(): void {
        $produtos = $this->model->all();
        $this->render('produto/index', ['produtos' => $produtos, 'flash' => $this->getFlash()]);
    }

    public function create(): void {
        $categorias = $this->catModel->all();
        $this->render('produto/form', ['produto' => null, 'categorias' => $categorias, 'flash' => $this->getFlash()]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('?url=produto'); }

        $data = [
            'nome'         => trim($_POST['nome']         ?? ''),
            'preco'        => $_POST['preco']              ?? 0,
            'estoque'      => (int)($_POST['estoque']     ?? 0),
            'id_categoria' => $_POST['id_categoria']       ?? null,
        ];

        if (empty($data['nome'])) {
            $this->setFlash('danger', 'Nome do produto é obrigatório.');
            $this->redirect('?url=produto/create');
        }

        if ($this->model->create($data)) {
            $this->setFlash('success', 'Produto cadastrado com sucesso!');
        } else {
            $this->setFlash('danger', 'Erro ao cadastrar produto.');
        }
        $this->redirect('?url=produto');
    }

    public function edit(?string $id): void {
        $produto    = $this->model->find((int)$id);
        if (!$produto) { $this->setFlash('danger', 'Produto não encontrado.'); $this->redirect('?url=produto'); }
        $categorias = $this->catModel->all();
        $this->render('produto/form', ['produto' => $produto, 'categorias' => $categorias, 'flash' => $this->getFlash()]);
    }

    public function update(?string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('?url=produto'); }

        $data = [
            'nome'         => trim($_POST['nome']         ?? ''),
            'preco'        => $_POST['preco']              ?? 0,
            'estoque'      => (int)($_POST['estoque']     ?? 0),
            'id_categoria' => $_POST['id_categoria']       ?? null,
        ];

        if ($this->model->update((int)$id, $data)) {
            $this->setFlash('success', 'Produto atualizado!');
        } else {
            $this->setFlash('danger', 'Erro ao atualizar produto.');
        }
        $this->redirect('?url=produto');
    }

    public function delete(?string $id): void {
        if ($this->model->delete((int)$id)) {
            $this->setFlash('success', 'Produto removido!');
        } else {
            $this->setFlash('danger', 'Erro ao remover produto.');
        }
        $this->redirect('?url=produto');
    }
}
