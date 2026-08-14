<?php

return [
    'calculadora-salario-liquido' => ['custo-funcionario-clt', 'calculadora-hora-extra', 'calculadora-ferias', 'calculadora-de-rescisao'],
    'custo-funcionario-clt' => ['calculadora-salario-liquido', 'encargos-trabalhistas', 'inss-patronal', 'simulador-admissao'],
    'calculadora-hora-extra' => ['calculadora-salario-liquido', 'custo-funcionario-clt', 'calculadora-ferias', 'gerador-holerite'],
    'calculadora-ferias' => ['calculadora-salario-liquido', 'calculadora-de-rescisao', 'gerador-holerite', 'custo-funcionario-clt'],
    'calculadora-de-rescisao' => ['calculadora-ferias', 'calculadora-salario-liquido', 'custo-funcionario-clt', 'gerador-holerite'],
    'simulador-admissao' => ['custo-funcionario-clt', 'calculadora-salario-liquido', 'encargos-trabalhistas', 'inss-patronal'],
    'encargos-trabalhistas' => ['custo-funcionario-clt', 'inss-patronal', 'calculadora-salario-liquido', 'simulador-admissao'],
    'inss-patronal' => ['encargos-trabalhistas', 'custo-funcionario-clt', 'simulador-admissao', 'calculadora-salario-liquido'],
    'reajuste-salarial' => ['calculadora-salario-liquido', 'custo-funcionario-clt', 'gerador-holerite', 'simulador-admissao'],
    'gerador-holerite' => ['calculadora-salario-liquido', 'calculadora-hora-extra', 'calculadora-ferias', 'custo-funcionario-clt'],
    'comissao-vendedores' => ['gerador-holerite', 'calculadora-salario-liquido', 'custo-funcionario-clt', 'reajuste-salarial'],
    'comparador-clt-pj-autonomo' => ['custo-funcionario-clt', 'simulador-pro-labore-ideal', 'calculadora-salario-liquido', 'comparador-tributario'],

    'calculadora-simples-nacional' => ['simulador-fator-r', 'das-em-atraso', 'calculadora-das-retroativo-regularizacao-simples', 'simulador-mei-microempresa'],
    'simulador-fator-r' => ['calculadora-simples-nacional', 'simulador-pro-labore-ideal', 'comparador-tributario', 'calculadora-das-retroativo-regularizacao-simples'],
    'das-em-atraso' => ['calculadora-das-retroativo-regularizacao-simples', 'calculadora-simples-nacional', 'calculadora-parcelamento-tributario', 'simulador-mei-microempresa'],
    'calculadora-das-retroativo-regularizacao-simples' => ['das-em-atraso', 'calculadora-simples-nacional', 'calculadora-parcelamento-tributario', 'simulador-mei-microempresa'],
    'simulador-mei-microempresa' => ['calculadora-simples-nacional', 'simulador-fator-r', 'comparador-tributario', 'das-em-atraso'],
    'comparador-tributario' => ['calculadora-simples-nacional', 'calculadora-irpj-csll-lucro-presumido', 'simulador-pro-labore-ideal', 'simulador-mei-microempresa'],
    'calculadora-irpj-csll-lucro-presumido' => ['comparador-tributario', 'calculadora-pis-cofins', 'calculadora-iss', 'simulador-distribuicao-lucros-balanco'],
    'calculadora-pis-cofins' => ['calculadora-irpj-csll-lucro-presumido', 'calculadora-retencoes-nota-fiscal', 'calculadora-iss', 'comparador-tributario'],
    'calculadora-iss' => ['calculadora-retencoes-nota-fiscal', 'calculadora-pis-cofins', 'calculadora-irpj-csll-lucro-presumido', 'comparador-tributario'],
    'calculadora-retencoes-nota-fiscal' => ['calculadora-iss', 'calculadora-pis-cofins', 'calculadora-irpj-csll-lucro-presumido', 'gerador-darf-gps'],
    'calculadora-difal-icms' => ['calculadora-icms-st', 'calculadora-retencoes-nota-fiscal', 'calculadora-pis-cofins', 'comparador-tributario'],
    'calculadora-icms-st' => ['calculadora-difal-icms', 'calculadora-retencoes-nota-fiscal', 'calculadora-pis-cofins', 'comparador-tributario'],
    'analisador-certificado-digital-a1' => ['validador-fiscal-sefaz', 'conversor-fiscal-xml', 'validador-de-cnpj', 'gerador-de-contratos'],
    'consultor-validador-cfop' => ['validador-fiscal-sefaz', 'conversor-fiscal-xml', 'calculadora-icms-proprio', 'calculadora-difal-icms'],
    'validador-fiscal-sefaz' => ['consultor-validador-cfop', 'conversor-fiscal-xml', 'calculadora-icms-proprio', 'calculadora-difal-icms'],
    'calculadora-icms-proprio' => ['calculadora-difal-icms', 'calculadora-icms-st', 'consultor-validador-cfop', 'validador-fiscal-sefaz'],
    'calculadora-lucro-real' => ['comparador-tributario', 'calculadora-pis-cofins', 'calculadora-irpj-csll-lucro-presumido', 'simulador-reforma-tributaria-consumo'],
    'simulador-reforma-tributaria-consumo' => ['calculadora-lucro-real', 'comparador-tributario', 'calculadora-pis-cofins', 'calculadora-icms-proprio'],
    'simulador-ecad-direitos-autorais' => ['calculadora-retencoes-nota-fiscal', 'calculadora-pis-cofins', 'calculadora-iss', 'fluxo-de-caixa'],
    'calculadora-parcelamento-tributario' => ['das-em-atraso', 'calculadora-das-retroativo-regularizacao-simples', 'calculadora-simples-nacional', 'comparador-tributario'],
    'gerador-darf-gps' => ['calculadora-retencoes-nota-fiscal', 'calculadora-irpj-csll-lucro-presumido', 'calculadora-simples-nacional', 'das-em-atraso'],
    'conversor-fiscal-xml' => ['calculadora-retencoes-nota-fiscal', 'calculadora-pis-cofins', 'calculadora-icms-st', 'calculadora-difal-icms'],

    'simulador-pro-labore-ideal' => ['calculadora-pro-labore-distribuicao-lucros', 'distribuicao-de-lucros', 'simulador-fator-r', 'simulador-distribuicao-lucros-balanco'],
    'calculadora-pro-labore-distribuicao-lucros' => ['simulador-pro-labore-ideal', 'distribuicao-de-lucros', 'simulador-distribuicao-lucros-balanco', 'declaracao-rendimentos'],
    'distribuicao-de-lucros' => ['simulador-distribuicao-lucros-balanco', 'simulador-pro-labore-ideal', 'calculadora-pro-labore-distribuicao-lucros', 'declaracao-rendimentos'],
    'simulador-distribuicao-lucros-balanco' => ['distribuicao-de-lucros', 'simulador-pro-labore-ideal', 'calculadora-pro-labore-distribuicao-lucros', 'declaracao-rendimentos'],
    'declaracao-rendimentos' => ['distribuicao-de-lucros', 'simulador-pro-labore-ideal', 'emissor-de-recibos', 'gerador-de-contratos'],
    'declaracao-trabalho-renda' => ['declaracao-rendimentos', 'emissor-de-recibos', 'gerador-de-contratos', 'calculadora-salario-liquido'],

    'capital-de-giro' => ['fluxo-de-caixa', 'ponto-de-equilibrio', 'calculadora-margem-markup', 'calculadora-de-honorarios-contabeis'],
    'fluxo-de-caixa' => ['capital-de-giro', 'ponto-de-equilibrio', 'calculadora-margem-markup', 'calculadora-de-honorarios-contabeis'],
    'ponto-de-equilibrio' => ['calculadora-margem-markup', 'fluxo-de-caixa', 'capital-de-giro', 'calculadora-de-honorarios-contabeis'],
    'calculadora-margem-markup' => ['ponto-de-equilibrio', 'fluxo-de-caixa', 'capital-de-giro', 'calculadora-de-honorarios-contabeis'],
    'calculadora-de-honorarios-contabeis' => ['calculadora-margem-markup', 'ponto-de-equilibrio', 'fluxo-de-caixa', 'capital-de-giro'],
    'calculadora-depreciacao-ativos' => ['fluxo-de-caixa', 'calculadora-irpj-csll-lucro-presumido', 'ponto-de-equilibrio', 'capital-de-giro'],

    'validador-de-cnpj' => ['gerador-de-contratos', 'emissor-de-recibos', 'conversor-fiscal-xml', 'declaracao-rendimentos'],
    'gerador-de-contratos' => ['validador-de-cnpj', 'emissor-de-recibos', 'declaracao-rendimentos', 'declaracao-trabalho-renda'],
    'emissor-de-recibos' => ['gerador-de-contratos', 'declaracao-rendimentos', 'declaracao-trabalho-renda', 'validador-de-cnpj'],

    // A vertical de RH possui hoje uma única ferramenta oficial. O catálogo não
    // cruza verticais automaticamente; quando novos módulos de RH entrarem, este
    // fluxo pode ser enriquecido sem alterar a heurística global.
    'calculadora-turnover' => [],
];
