<?php

return [
    // Configurações para cálculo de complexidade
    'complexity_factors' => [
        'company_weight' => 20,
        'multiple_heirs_weight' => 10,
        'high_value_weight' => 15,
        'multiple_assets_weight' => 10,
        'no_will_weight' => 15,
        'special_conditions_weight' => 15,
    ],

    // Configurações para cálculo de risco
    'risk_factors' => [
        'no_will_weight' => 25,
        'no_succession_plan_weight' => 20,
        'multiple_heirs_weight' => 15,
        'high_value_weight' => 10,
    ],

    // Limites para classificação de patrimônio
    'asset_thresholds' => [
        'high_value' => 1000000, // R$ 1 milhão
        'very_high_value' => 5000000, // R$ 5 milhões
    ],

    // Alíquotas de ITCMD por tipo de bem
    'tax_rates' => [
        'imoveis' => 0.04,
        'investimentos' => 0.03,
        'empresa' => 0.05,
        'default' => 0.04,
    ],

    // Tipos de bens aceitos
    'asset_types' => [
        'imoveis' => 'Imóveis',
        'investimentos' => 'Investimentos Financeiros',
        'empresa' => 'Participações Societárias',
        'veiculos' => 'Veículos',
        'joias' => 'Joias e Obras de Arte',
        'outros' => 'Outros Bens',
    ],

    // Estados civis aceitos
    'marital_status' => [
        'solteiro' => 'Solteiro(a)',
        'casado' => 'Casado(a)',
        'divorciado' => 'Divorciado(a)',
        'viuvo' => 'Viúvo(a)',
        'uniao_estavel' => 'União Estável',
    ],

    // Tipos de relacionamento com herdeiros
    'heir_relationships' => [
        'filho' => 'Filho(a)',
        'conjuge' => 'Cônjuge',
        'neto' => 'Neto(a)',
        'pai' => 'Pai',
        'mae' => 'Mãe',
        'irmao' => 'Irmão/Irmã',
        'outro' => 'Outro',
    ],

    // Condições especiais que podem afetar o planejamento
    'special_conditions' => [
        'herdeiro_incapaz' => 'Herdeiro Incapaz',
        'herdeiro_menor' => 'Herdeiro Menor de Idade',
        'bem_exterior' => 'Bens no Exterior',
        'processo_judicial' => 'Processos Judiciais em Andamento',
        'holding_familiar' => 'Holding Familiar',
    ],
]; 