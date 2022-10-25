<?php
class PokemonController {
    public function list() {
        $sql = "SELECT * FROM pokemon";
        $conn = Connection::createConnection();
        $listPokemon = $conn->query($sql);
    
        $mapa = array();
    
        foreach ($listPokemon as $pokemon) {
            $mapa[] = array(
                "pok_id" => $pokemon['pok_id'],
                "pok_name" => $pokemon['pok_name'],
                "pok_height" => $pokemon['pok_height'],
                "pok_weight" => $pokemon['pok_weight'],
                "pok_base_experience" => $pokemon['pok_base_experience']
            );
        }
    
        $str = JSON::stringify($mapa);
        $d = JSON::parse($str, true);
    
        //header("Content-Type: application/json");
    
        echo JSON::stringify($d);
    }
}