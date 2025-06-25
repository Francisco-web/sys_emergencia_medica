<?php

class Database {
    private static $conexao;

    public static function Conexao() {
        if (!self::$conexao) {
            self::$conexao = new PDO("mysql:host=localhost;dbname=sys_emergencia_medica", "root", "");
            self::$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$conexao;
    }
}
