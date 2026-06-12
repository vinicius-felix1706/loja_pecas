<?php
// Model responsavel pelos dados dos clientes e pelo login.

class ClienteModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function all(): array {
        return $this->db->query('SELECT * FROM cliente ORDER BY nome')->fetchAll();
    }

    public function allClientes(): array {
        return $this->db->query("SELECT * FROM cliente WHERE COALESCE(role, 'cliente') = 'cliente' ORDER BY nome")->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM cliente WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Busca um cliente pelo e-mail, incluindo o hash da senha.
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT * FROM cliente WHERE email = ? LIMIT 1');
        $stmt->execute([trim($email)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Evita cadastrar duas contas com o mesmo e-mail.
     */
    public function emailExiste(string $email): bool {
        $stmt = $this->db->prepare('SELECT id FROM cliente WHERE email = ? LIMIT 1');
        $stmt->execute([trim($email)]);
        return (bool) $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO cliente (nome, email, telefone, senha, role) VALUES (:nome, :email, :telefone, :senha, :role)'
        );

        return $stmt->execute([
            ':nome'     => $data['nome'],
            ':email'    => $data['email'],
            ':telefone' => $data['telefone'] ?? null,
            ':senha'    => $data['senha']    ?? null,
            ':role'     => $data['role']     ?? 'cliente',
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE cliente SET nome = :nome, email = :email, telefone = :telefone WHERE id = :id'
        );

        return $stmt->execute([
            ':nome'     => $data['nome'],
            ':email'    => $data['email'],
            ':telefone' => $data['telefone'] ?? null,
            ':id'       => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM cliente WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Remove o cliente e tambem apaga pedidos/itens ligados a ele.
     */
    public function deleteComRelacionados(int $id): bool {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                'DELETE ip
                 FROM item_pedido ip
                 INNER JOIN pedido p ON p.id = ip.id_pedido
                 WHERE p.id_cliente = ?'
            );
            $stmt->execute([$id]);

            $stmt = $this->db->prepare('DELETE FROM pedido WHERE id_cliente = ?');
            $stmt->execute([$id]);

            $stmt = $this->db->prepare("DELETE FROM cliente WHERE id = ? AND COALESCE(role, 'cliente') = 'cliente'");
            $stmt->execute([$id]);

            $ok = $stmt->rowCount() > 0;

            if ($ok) {
                $this->db->commit();
                return true;
            }

            $this->db->rollBack();
            return false;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function lastInsertId(): string {
        return $this->db->lastInsertId();
    }
}
