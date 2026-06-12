-- Script antigo para criar uma tabela separada de administradores.
-- No fluxo atual do sistema, clientes e administradores ficam na tabela cliente
-- e sao diferenciados pela coluna role.

CREATE TABLE IF NOT EXISTS usuarios (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    senha      VARCHAR(255) NOT NULL,          -- senha armazenada com password_hash()
    criado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exemplo de usuario padrao: admin / admin123.
-- Troque o hash antes de usar em producao.
INSERT INTO usuarios (nome, email, senha) VALUES
    ('Administrador', 'admin@loja.com', '$2y$12$YourHashHere');

-- Para gerar um hash pelo PHP:
-- php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
