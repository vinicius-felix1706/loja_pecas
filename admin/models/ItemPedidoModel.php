<?php
// admin/models/ItemPedidoModel.php

class ItemPedidoModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO item_pedido (id_pedido, id_produto, quantidade)
             VALUES (:id_pedido, :id_produto, :quantidade)'
        );
        return $stmt->execute([
            ':id_pedido' => $data['id_pedido'],
            ':id_produto' => $data['id_produto'],
            ':quantidade' => $data['quantidade'],
        ]);
    }

    public function updateQuantidade(int $idItem, int $quantidade): bool {
        $stmt = $this->db->prepare('UPDATE item_pedido SET quantidade = ? WHERE id = ?');
        return $stmt->execute([$quantidade, $idItem]);
    }

    public function deleteByPedido(int $idPedido): bool {
        $stmt = $this->db->prepare('DELETE FROM item_pedido WHERE id_pedido = ?');
        return $stmt->execute([$idPedido]);
    }
}
