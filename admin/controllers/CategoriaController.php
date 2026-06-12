<?php
// Controller antigo para cadastro de categorias.

require_once ROOT . '/app/controllers/BaseController.php';
require_once ROOT . '/app/models/CategoriaModel.php';

class CategoriaController extends BaseController {

    private CategoriaModel $model;

    public function __construct() {
        $this->requireAuth();
        $this->model = new CategoriaModel();
    }

    public function index(): void {
        $categorias = $this->model->all();
        $this->render('categoria/index', ['categorias' => $categorias, 'flash' => $this->getFlash()]);
    }

    public function create(): void {
        $this->render('categoria/form', ['categoria' => null, 'flash' => $this->getFlash()]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('?url=categoria'); }

        $data = [
            'nome_categoria' => trim($_POST['nome_categoria'] ?? ''),
            'descricao'      => trim($_POST['descricao']      ?? ''),
        ];

        if (empty($data['nome_categoria'])) {
            $this->setFlash('danger', 'Nome da categoria é obrigatório.');
            $this->redirect('?url=categoria/create');
        }

        if ($this->model->create($data)) {
            $this->setFlash('success', 'Categoria criada com sucesso!');
        } else {
            $this->setFlash('danger', 'Erro ao criar categoria.');
        }
        $this->redirect('?url=categoria');
    }

    public function edit(?string $id): void {
        $categoria = $this->model->find((int)$id);
        if (!$categoria) { $this->setFlash('danger', 'Categoria não encontrada.'); $this->redirect('?url=categoria'); }
        $this->render('categoria/form', ['categoria' => $categoria, 'flash' => $this->getFlash()]);
    }

    public function update(?string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('?url=categoria'); }

        $data = [
            'nome_categoria' => trim($_POST['nome_categoria'] ?? ''),
            'descricao'      => trim($_POST['descricao']      ?? ''),
        ];

        if ($this->model->update((int)$id, $data)) {
            $this->setFlash('success', 'Categoria atualizada!');
        } else {
            $this->setFlash('danger', 'Erro ao atualizar categoria.');
        }
        $this->redirect('?url=categoria');
    }

    public function delete(?string $id): void {
        if ($this->model->delete((int)$id)) {
            $this->setFlash('success', 'Categoria removida!');
        } else {
            $this->setFlash('danger', 'Erro ao remover categoria (pode haver produtos vinculados).');
        }
        $this->redirect('?url=categoria');
    }
}
