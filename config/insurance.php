<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações do Módulo de Seguro de Vida
    |--------------------------------------------------------------------------
    |
    | Este arquivo contém as configurações específicas para o módulo de
    | análise de seguro de vida, incluindo parâmetros para cálculos
    | de risco, cobertura e prêmios.
    |
    */

    'life_insurance' => [
        // Configurações de análise de risco
        'risk_analysis' => [
            'base_score' => 50,
        ],

        // Configurações da API OpenAI
        'api' => [
            'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ],
    ],
]; 