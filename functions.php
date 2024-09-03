<?php

// Função para calcular IMC
function calculateIMC($weight, $height) {
    $height_in_meters = $height / 100;  // Converte altura para metros
    return $weight / ($height_in_meters * $height_in_meters);
}

// Função para calcular TMB usando a fórmula de Harris-Benedict modificada
function calculateTMB($weight, $height, $age, $sex, $activity_level) {
    if ($sex == 'Masculino') {
        $tmb = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
    } else {
        $tmb = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
    }

    // Ajusta a TMB para o nível de atividade
    switch ($activity_level) {
        case "Sedentário":
            $tmb *= 1.2;
            break;
        case "Levemente Ativo":
            $tmb *= 1.375;
            break;
        case "Moderadamente Ativo":
            $tmb *= 1.55;
            break;
        case "Muito Ativo":
            $tmb *= 1.725;
            break;
        case "Extremamente Ativo":
            $tmb *= 1.9;
            break;
    }

    return $tmb;
}

// Função para calcular Déficit Calórico (exemplo: 500 calorias a menos para perda de peso)
function calculateDeficitCalories($tmb) {
    return $tmb - 500;
}

// Função para calcular Superávit Calórico (exemplo: 500 calorias a mais para ganho de massa)
function calculateSurplusCalories($tmb) {
    return $tmb + 500;
}


