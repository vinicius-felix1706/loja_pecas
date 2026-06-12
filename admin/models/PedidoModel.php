<?php
// admin/models/PedidoModel.php

class PedidoModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function allByCliente(int $idCliente): array {
        $stmt = $this->db->prepare(
            'SELECT pe.*, ip.quantidade, pr.nome AS produto_nome, pr.preco AS produto_preco
             FROM pedido pe
             LEFT JOIN item_pedido ip ON ip.id_pedido = pe.id
             LEFT JOIN produto pr ON pr.id = ip.id_produto
             WHERE pe.id_cliente = ?
             ORDER BY pe.id DESC'
        );
        $stmt->execute([$idCliente]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int|false {
        $stmt = $this->db->prepare(
            'INSERT INTO pedido (data_pedido, status, total, id_cliente)
             VALUES (:data_pedido, :status, :total, :id_cliente)'
        );
        $ok = $stmt->execute([
            ':data_pedido' => $data['data_pedido'] ?? date('Y-m-d'),
            ':status' => $data['status'] ?? 'Pedido feito',
            ':total' => $data['total'] ?? 0,
            ':id_cliente' => $data['id_cliente'],
        ]);

        return $ok ? (int)$this->db->lastInsertId() : false;
    }

    public function findByClienteProduto(int $idCliente, int $idProduto): ?array {
        $stmt = $this->db->prepare(
            'SELECT pe.*, ip.id AS id_item, ip.quantidade, ip.id_produto
             FROM pedido pe
             JOIN item_pedido ip ON ip.id_pedido = pe.id
             WHERE pe.id_cliente = ? AND ip.id_produto = ?
             ORDER BY pe.id DESC
             LIMIT 1'
        );
        $stmt->execute([$idCliente, $idProduto]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateTotal(int $idPedido, int $idCliente, float $total): bool {
        $stmt = $this->db->prepare('UPDATE pedido SET total = ? WHERE id = ? AND id_cliente = ?');
        return $stmt->execute([$total, $idPedido, $idCliente]);
    }

    public function pertenceAoCliente(int $idPedido, int $idCliente): bool {
        $stmt = $this->db->prepare('SELECT id FROM pedido WHERE id = ? AND id_cliente = ? LIMIT 1');
        $stmt->execute([$idPedido, $idCliente]);
        return (bool)$stmt->fetch();
    }

    public function delete(int $idPedido, int $idCliente): bool {
        $stmt = $this->db->prepare('DELETE FROM pedido WHERE id = ? AND id_cliente = ?');
        return $stmt->execute([$idPedido, $idCliente]);
    }
}
