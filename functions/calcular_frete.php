<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uf = strtoupper($_POST["uf"]); // Estado recebido do JavaScript

    // Definição dos valores fixos de frete por estado
    $valoresFrete = [
        "SC" => 10.00, "PR" => 12.00, "RS" => 15.00,
        "SP" => 18.00, "RJ" => 22.00, "MG" => 25.00,
        "ES" => 28.00, "BA" => 30.00, "PE" => 35.00,
        "CE" => 38.00, "GO" => 32.00, "DF" => 33.00,
        "MT" => 40.00, "MS" => 38.00, "PA" => 42.00,
        "AM" => 45.00, "MA" => 39.00, "PI" => 36.00,
        "RO" => 44.00, "AC" => 50.00, "RR" => 55.00,
        "AP" => 48.00, "AL" => 37.00, "SE" => 35.00,
        "PB" => 39.00, "RN" => 40.00, "TO" => 43.00
    ];

    // Se o estado não for encontrado, define um valor padrão
    $valorFrete = isset($valoresFrete[$uf]) ? $valoresFrete[$uf] : 60.00;

    echo json_encode([
        "estado" => $uf,
        "frete" => "R$ " . number_format($valorFrete, 2, ',', '.')
    ]);
}
?>
