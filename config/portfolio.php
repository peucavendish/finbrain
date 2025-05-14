<?php

return [
    'analysis' => [
        'portfolio_analysis' => [
            'min_diversification_score' => 40,
            'ideal_diversification_score' => 70,
            'risk_profiles' => [
                'conservador' => [
                    'max_volatility' => 5,
                    'target_return' => 8,
                    'suggested_allocations' => [
                        'renda_fixa' => [60, 80],
                        'renda_variavel' => [0, 20],
                        'alternativos' => [0, 10]
                    ]
                ],
                'moderado' => [
                    'max_volatility' => 15,
                    'target_return' => 12,
                    'suggested_allocations' => [
                        'renda_fixa' => [40, 60],
                        'renda_variavel' => [20, 40],
                        'alternativos' => [10, 20]
                    ]
                ],
                'arrojado' => [
                    'max_volatility' => 25,
                    'target_return' => 16,
                    'suggested_allocations' => [
                        'renda_fixa' => [20, 40],
                        'renda_variavel' => [40, 60],
                        'alternativos' => [20, 30]
                    ]
                ]
            ],
            'asset_classes' => [
                'renda_fixa' => [
                    'tesouro_direto',
                    'cdb',
                    'lci_lca',
                    'debentures'
                ],
                'renda_variavel' => [
                    'acoes',
                    'fiis',
                    'etfs'
                ],
                'alternativos' => [
                    'criptomoedas',
                    'commodities',
                    'fundos_multimercado'
                ]
            ],
            'market_indicators' => [
                'selic',
                'ipca',
                'ibovespa',
                'dolar',
                'euro'
            ]
        ]
    ]
]; 