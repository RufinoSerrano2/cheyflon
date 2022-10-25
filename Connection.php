<?php
    class Connection {
        public static function createConnection(): mysqli {
            $conn = new mysqli("localhost", "root", "", "pokemon");

            if ($conn->connect_errno) {
                die("Error al conectar con MySQL: " . $conn->error);
            }
            
            return $conn;
        }
    }