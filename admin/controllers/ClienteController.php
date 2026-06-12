<?php
// Controller antigo de clientes, mantido como apoio ao painel.

require_once ROOT . '/app/controllers/BaseController.php';
require_once ROOT . '/app/models/ClienteModel.php';

class ClienteController extends BaseController {

    private ClienteModel $model;

    public function __construct() {
        $this->requireAuth();
        $this->model = new ClienteModel();
    }

    // GET  — lista
    public function index(): void {
        $clientes = $this->model->all();
        $this->render('cliente/index', ['clientes' => $clientes, 'flash' => $this->getFlash()]);
    }

    // GET  — formulário de criação
    public function create(): void {
        $this->render('cliente/form', ['cliente' => null, 'flash' => $this->getFlash()]);
    }

    // POST — salvar novo
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('?url=cliente'); }

        $data = [
            'nome'     => trim($_POST['nome']     ?? ''),
            'email'    => trim($_POST['email']    ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
        ];

        if (empty($data['nome']) || empty($data['email'])) {
            $this->setFlash('danger', 'Nome e e-mail são obrigatórios.');
            $this->redirect('?url=cliente/create');
        }

        if ($this->model->create($data)) {
            $this->setFlash('success', 'Cliente cadastrado com sucesso!');
        } else {
            $this->setFlash('danger', 'Erro ao cadastrar cliente.');
        }
        $this->redirect('?url=cliente');
    }

    // GET  — formulário de edição
    public function edit(?string $id): void {
        $cliente = $this->model->find((int)$id);
        if (!$cliente) { $this->setFlash('danger', 'Cliente não encontrado.'); $this->redirect('?url=cliente'); }
        $this->render('cliente/form', ['cliente' => $cliente, 'flash' => $this->getFlash()]);
    }

    // POST — atualizar
    public function update(?string $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('?url=cliente'); }

        $data = [
            'nome'     => trim($_POST['nome']     ?? ''),
            'email'    => trim($_POST['email']    ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
        ];

        if ($this->model->update((int)$id, $data)) {
            $this->setFlash('success', 'Cliente atualizado!');
        } else {
            $this->setFlash('danger', 'Erro ao atualizar cliente.');
        }
        $this->redirect('?url=cliente');
    }

    // GET  — excluir
    public function delete(?string $id): void {
        if ($this->model->delete((int)$id)) {
            $this->setFlash('success', 'Cliente removido!');
        } else {
            $this->setFlash('danger', 'Erro ao remover cliente.');
        }
        $this->redirect('?url=cliente');
    }
}
