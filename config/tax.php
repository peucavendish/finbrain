<?php

return [
    // Alíquotas de IRPF 2024
    'income_tax_brackets' => [
        [
            'limit' => 24751.74,
            'rate' => 0.00,
            'deduction' => 0.00
        ],
        [
            'limit' => 32919.00,
            'rate' => 0.075,
            'deduction' => 1856.38
        ],
        [
            'limit' => 41566.16,
            'rate' => 0.150,
            'deduction' => 4316.94
        ],
        [
            'limit' => 50526.92,
            'rate' => 0.225,
            'deduction' => 7353.88
        ],
        [
            'limit' => PHP_FLOAT_MAX,
            'rate' => 0.275,
            'deduction' => 9875.16
        ]
    ],

    // Tipos de renda aceitos
    'income_types' => [
        'salario' => 'Salário/Pró-labore',
        'aluguel' => 'Aluguel',
        'dividendos' => 'Dividendos',
        'investimentos' => 'Rendimentos de Investimentos',
        'servicos' => 'Prestação de Serviços',
        'outros' => 'Outras Rendas'
    ],

    // Alíquotas por tipo de renda
    'income_tax_rates' => [
        'aluguel' => 0.275,
        'investimentos' => 0.225,
        'servicos' => 0.275,
        'outros' => 0.275
    ],

    // Tipos de ativos aceitos
    'asset_types' => [
        'imoveis' => 'Imóveis',
        'veiculos' => 'Veículos',
        'investimentos' => 'Investimentos Financeiros',
        'participacoes' => 'Participações Societárias',
        'outros' => 'Outros Bens'
    ],

    // Regimes tributários
    'tax_regimes' => [
        'simples' => 'Simples Nacional',
        'presumido' => 'Lucro Presumido',
        'real' => 'Lucro Real',
        'mei' => 'MEI',
        'pf' => 'Pessoa Física'
    ],

    // Limites e parâmetros
    'thresholds' => [
        'pj_recommendation' => 300000, // Renda anual a partir da qual recomenda-se PJ
        'simples_limit' => 500000, // Limite para recomendação do Simples
        'max_deduction_percentage' => 0.20 // Percentual máximo de deduções sobre a renda
    ],

    // Tipos de deduções aceitas
    'deduction_types' => [
        'previdencia' => 'Previdência',
        'saude' => 'Despesas Médicas',
        'educacao' => 'Educação',
        'dependentes' => 'Dependentes',
        'pensao' => 'Pensão Alimentícia',
        'outros' => 'Outras Deduções'
    ],

    // Tipos de investimentos para análise fiscal
    'investment_types' => [
        'renda_fixa' => 'Renda Fixa',
        'renda_variavel' => 'Renda Variável',
        'fundos' => 'Fundos de Investimento',
        'previdencia' => 'Previdência Privada',
        'imoveis' => 'Investimentos Imobiliários'
    ]
]; 