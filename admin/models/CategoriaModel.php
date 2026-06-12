<?php
// Model simples para manter categorias no painel administrativo antigo.

class CategoriaModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function all(): array {
        return $this->db->query('SELECT * FROM categorias ORDER BY nome_categoria')->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM categorias WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO categorias (nome_categoria, descricao) VALUES (:nome_categoria, :descricao)'
        );
        return $stmt->execute([
            ':nome_categoria' => $data['nome_categoria'],
            ':descricao'      => $data['descricao'] ?? null,
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE categorias SET nome_categoria = :nome_categoria, descricao = :descricao WHERE id = :id'
        );
        return $stmt->execute([
            ':nome_categoria' => $data['nome_categoria'],
            ':descricao'      => $data['descricao'] ?? null,
            ':id'             => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM categorias WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
