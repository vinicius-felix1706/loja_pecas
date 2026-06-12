<?php
// Controller base com rotinas comuns de sessao e resposta.

class BaseController {

    /**
     * Garante que existe um usuario logado antes de abrir uma area protegida.
     */
    protected function requireAuth(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            $this->setFlash('warning', 'Faca login para acessar esta area.');
            $this->redirect('?url=auth/login');
        }
    }

    /**
     * Retorna os dados basicos do usuario guardados na sessao.
     */
    protected function usuarioLogado(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            return null;
        }

        return [
            'id'    => $_SESSION['usuario_id'],
            'nome'  => $_SESSION['usuario_nome']  ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
            'role'  => $_SESSION['usuario_role']  ?? 'cliente',
        ];
    }

    /**
     * Guarda uma mensagem rapida para ser exibida na proxima tela.
     */
    protected function setFlash(string $tipo, string $mensagem): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
    }

    /**
     * Le a mensagem flash e remove da sessao para ela nao aparecer repetida.
     */
    protected function getFlash(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }

        return null;
    }

    /**
     * Carrega uma view e transforma os dados recebidos em variaveis locais.
     */
    protected function render(string $view, array $dados = []): void {
        extract($dados);
        $viewPath = ROOT . "/app/views/{$view}.php";

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "View nao encontrada: {$viewPath}";
            exit;
        }

        require $viewPath;
    }

    /**
     * Envia o navegador para outra rota e encerra a execucao atual.
     */
    protected function redirect(string $url): never {
        header("Location: {$url}");
        exit;
    }
}
