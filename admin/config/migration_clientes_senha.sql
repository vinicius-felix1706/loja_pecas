-- Migracao: adiciona a coluna de senha na tabela de clientes.
-- Execute uma unica vez no banco de dados.

ALTER TABLE cliente
    ADD COLUMN senha VARCHAR(255) NULL AFTER telefone;

-- A coluna aceita NULL para que clientes antigos, cadastrados sem senha,
-- continuem no banco. Novos cadastros recebem o hash da senha.
