<?php
// Controla login, cadastro e logout dos usuarios.

class AuthController extends BaseController {

    private ClienteModel $model;

    public function __construct() {
        $this->model = new ClienteModel();
    }

    public function login(): void {
        $usuario = $this->usuarioLogado();
        if ($usuario) {
            $this->redirect(($usuario['role'] ?? 'cliente') === 'admin' ? '?url=admin/dashboard' : '?url=dashboard');
        }

        $flash = $this->getFlash();
        require ROOT . '/views/auth/login.php';
    }

    public function cadastroForm(): void {
        if ($this->usuarioLogado()) {
            $this->redirect('?url=dashboard');
        }

        $flash = $this->getFlash();
        require ROOT . '/views/auth/cadastro.php';
    }

    public function autenticar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?url=auth/login');
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipoAcesso = $_POST['tipo_acesso'] ?? 'cliente';
        $tipoAcesso = $tipoAcesso === 'admin' ? 'admin' : 'cliente';

        if (empty($email) || empty($senha)) {
            $this->setFlash('danger', 'Preencha e-mail e senha.');
            $this->redirect('?url=auth/login');
        }

        $cliente = $this->model->findByEmail($email);

        if (!$cliente || empty($cliente['senha']) || !password_verify($senha, $cliente['senha'])) {
            $this->setFlash('danger', 'E-mail ou senha invalidos.');
            $this->redirect('?url=auth/login');
        }

        $role = $cliente['role'] ?? 'cliente';
        if ($role !== $tipoAcesso) {
            $mensagem = $tipoAcesso === 'admin'
                ? 'Este login nao pertence a um administrador.'
                : 'Este login nao pertence a um cliente.';
            $this->setFlash('danger', $mensagem);
            $this->redirect('?url=auth/login');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['usuario_id']    = $cliente['id'];
        $_SESSION['usuario_nome']  = $cliente['nome'];
        $_SESSION['usuario_email'] = $cliente['email'];
        $_SESSION['usuario_role']  = $role;

        $this->setFlash('success', 'Bem-vindo, ' . htmlspecialchars($cliente['nome']) . '!');
        $this->redirect($role === 'admin' ? '?url=admin/dashboard' : '?url=dashboard');
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?url=auth/cadastro');
        }

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $senhaConfirm = $_POST['senha_confirm'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            $this->setFlash('danger', 'Preencha todos os campos obrigatorios.');
            $this->redirect('?url=auth/cadastro');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('danger', 'E-mail invalido.');
            $this->redirect('?url=auth/cadastro');
        }

        if ($senha !== $senhaConfirm) {
            $this->setFlash('danger', 'As senhas nao coincidem.');
            $this->redirect('?url=auth/cadastro');
        }

        if (strlen($senha) < 6) {
            $this->setFlash('danger', 'A senha deve ter pelo menos 6 caracteres.');
            $this->redirect('?url=auth/cadastro');
        }

        if ($this->model->emailExiste($email)) {
            $this->setFlash('danger', 'Este e-mail ja esta cadastrado.');
            $this->redirect('?url=auth/cadastro');
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $this->model->create([
            'nome'     => $nome,
            'email'    => $email,
            'telefone' => $telefone,
            'senha'    => $senhaHash,
            'role'     => 'cliente',
        ]);

        $id = (int) $this->model->lastInsertId();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['usuario_id']    = $id;
        $_SESSION['usuario_nome']  = $nome;
        $_SESSION['usuario_email'] = $email;
        $_SESSION['usuario_role']  = 'cliente';

        $this->setFlash('success', 'Cadastro realizado! Bem-vindo, ' . htmlspecialchars($nome) . '!');
        $this->redirect('?url=dashboard');
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        $this->setFlash('success', 'Voce saiu do sistema.');
        $this->redirect('?url=auth/login');
    }
}
