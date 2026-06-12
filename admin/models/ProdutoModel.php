<?php
// admin/models/ProdutoModel.php

class ProdutoModel {

    private PDO $db;
    private array $colunas = [];

    public function __construct() {
        $this->db = getDB();
    }

    private function hasColumn(string $column): bool {
        if ($this->colunas === []) {
            $stmt = $this->db->query('SHOW COLUMNS FROM produto');
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $coluna) {
                $this->colunas[$coluna['Field']] = true;
            }
        }

        return isset($this->colunas[$column]);
    }

    public function all(): array {
        return $this->db->query(
            'SELECT p.*, c.nome_categoria
             FROM produto p
             LEFT JOIN categoria c ON c.id = p.id_categoria
             ORDER BY p.id'
        )->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.nome_categoria
             FROM produto p
             LEFT JOIN categoria c ON c.id = p.id_categoria
             WHERE p.id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool {
        $campos = ['nome', 'preco', 'estoque', 'id_categoria'];
        $params = [
            ':nome' => $data['nome'],
            ':preco' => $data['preco'],
            ':estoque' => $data['estoque'],
            ':id_categoria' => $data['id_categoria'],
        ];

        if ($this->hasColumn('descricao')) {
            $campos[] = 'descricao';
            $params[':descricao'] = $data['descricao'] ?? null;
        }

        if ($this->hasColumn('imagem')) {
            $campos[] = 'imagem';
            $params[':imagem'] = $data['imagem'] ?? null;
        }

        $placeholders = array_map(fn($campo) => ':' . $campo, $campos);
        $stmt = $this->db->prepare(
            'INSERT INTO produto (' . implode(', ', $campos) . ') VALUES (' . implode(', ', $placeholders) . ')'
        );

        return $stmt->execute($params);
    }

    public function update(int $id, array $data): bool {
        $sets = [
            'nome = :nome',
            'preco = :preco',
            'estoque = :estoque',
            'id_categoria = :id_categoria',
        ];
        $params = [
            ':nome' => $data['nome'],
            ':preco' => $data['preco'],
            ':estoque' => $data['estoque'],
            ':id_categoria' => $data['id_categoria'],
            ':id' => $id,
        ];

        if ($this->hasColumn('descricao')) {
            $sets[] = 'descricao = :descricao';
            $params[':descricao'] = $data['descricao'] ?? null;
        }

        if ($this->hasColumn('imagem')) {
            $sets[] = 'imagem = :imagem';
            $params[':imagem'] = $data['imagem'] ?? null;
        }

        $stmt = $this->db->prepare(
            'UPDATE produto
             SET ' . implode(', ', $sets) . '
             WHERE id = :id'
        );

        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM produto WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
