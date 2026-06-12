<?php

function catalogoPecas(): array {
    $itens = [
        1 => 'Bloco do Motor',
        2 => 'Cabecote',
        3 => 'Pistao',
        4 => 'Anel de Pistao',
        5 => 'Biela',
        6 => 'Virabrequim',
        7 => 'Comando de Valvulas',
        8 => 'Valvula de Admissao',
        9 => 'Valvula de Escape',
        10 => 'Tucho',
        15 => 'Junta do cabecote',
        16 => 'Pino do pistao',
        17 => 'Bronzina de biela',
        18 => 'Bronzina de mancal',
        19 => 'Volante do motor',
        20 => 'Varetas',
        21 => 'Balancins',
        22 => 'Molas de valvula',
        23 => 'Guias de valvula',
        24 => 'Retentores de valvula',
        25 => 'Correia dentada',
        26 => 'Corrente de comando',
        27 => 'Tensor da correia',
        28 => 'Polia do virabrequim',
        29 => 'Polia do comando',
        30 => 'Tampa de valvulas',
        31 => 'Carter',
        32 => 'Junta do carter',
        33 => 'Bomba de oleo',
        34 => 'Pescador de oleo',
        35 => 'Filtro de oleo',
        36 => "Bomba d'agua",
        37 => 'Termostato',
        38 => 'Radiador',
        39 => 'Ventoinha',
        40 => 'Coletor de admissao',
        41 => 'Coletor de escape',
        42 => 'Corpo de borboleta',
        43 => 'Injetores de combustivel',
        44 => 'Bomba de combustivel',
        45 => 'Filtro de combustivel',
        46 => 'Velas de ignicao',
        47 => 'Cabos de vela',
        48 => 'Bobina de ignicao',
        49 => 'Distribuidor',
        50 => 'Sensor de rotacao',
        51 => 'Sensor de fase',
        52 => 'Sensor MAP',
        53 => 'Sensor MAF',
        54 => 'Sensor de temperatura',
        55 => 'Sensor de oxigenio',
        56 => 'Unidade de controle do motor (ECU)',
        57 => 'Alternador',
        58 => 'Motor de partida',
        59 => 'Correia de acessorios',
        60 => 'Tensor da correia de acessorios',
        61 => 'Turbocompressor',
        62 => 'Intercooler',
        63 => 'Valvula wastegate',
        64 => 'Valvula EGR',
        65 => 'Retentor do virabrequim',
        66 => 'Retentor do comando',
        67 => 'Coxim do motor',
        68 => 'Tampa frontal do motor',
        69 => 'Tampa traseira do motor',
        70 => 'Galeria de oleo',
        71 => 'Galeria de agua',
        72 => 'Camara de combustao',
        73 => 'Camisas dos cilindros',
        74 => 'Prisioneiros do cabecote',
        75 => 'Parafusos do cabecote',
        76 => 'Respiro do carter',
        77 => 'Separador de oleo',
        78 => 'Sensor de detonacao',
        79 => 'Sensor de pressao do oleo',
        80 => 'Sensor de nivel de oleo',
        81 => 'Bujao do carter',
        82 => 'Tampa do oleo',
        83 => 'Vareta de oleo',
        84 => 'Junta da tampa de valvulas',
    ];

    $pecas = [];
    foreach ($itens as $id => $nome) {
        $pecas[$id] = [
            'id' => $id,
            'nome' => $nome,
            'descricao' => descricaoPecaPadrao($nome),
            'preco' => precoPecaPadrao($id),
            'sigla' => siglaPeca($nome),
            'shape' => inferirShapePeca($nome),
        ];
    }

    return $pecas;
}

function buscarPecaCatalogo(int $id): ?array {
    $pecas = catalogoPecas();
    return $pecas[$id] ?? null;
}

function catalogoComProdutos(array $produtos): array {
    $pecas = array_map('produtoParaPeca', $produtos);
    $nomesExistentes = [];

    foreach ($pecas as $peca) {
        $nomesExistentes[normalizarNomePeca($peca['nome'] ?? '')] = true;
    }

    foreach (catalogoPecas() as $pecaCatalogo) {
        $nomeNormalizado = normalizarNomePeca($pecaCatalogo['nome']);
        if (!isset($nomesExistentes[$nomeNormalizado])) {
            $pecas[] = $pecaCatalogo;
        }
    }

    return $pecas;
}

function produtoParaPeca(array $produto): array {
    $nome = $produto['nome'] ?? 'Peca';
    $sigla = siglaPeca($nome);

    return [
        'id' => (int)($produto['id'] ?? 0),
        'nome' => $nome,
        'descricao' => $produto['descricao'] ?? descricaoPecaPadrao($nome),
        'preco' => (float)($produto['preco'] ?? 0),
        'estoque' => (int)($produto['estoque'] ?? 0),
        'sigla' => $sigla,
        'shape' => inferirShapePeca($nome),
        'imagem' => $produto['imagem'] ?? null,
        'categoria' => $produto['nome_categoria'] ?? 'Motor',
    ];
}

function siglaPeca(string $nome): string {
    $limpo = preg_replace('/[^A-Za-z0-9 ]/', '', textoBuscaPeca($nome));
    $partes = preg_split('/\s+/', trim($limpo));
    $ignorar = ['de', 'da', 'do', 'das', 'dos', 'e', 'ou'];
    $sigla = '';

    foreach ($partes as $parte) {
        if ($parte !== '' && !in_array($parte, $ignorar, true)) {
            $sigla .= strtoupper($parte[0]);
        }
        if (strlen($sigla) >= 2) {
            break;
        }
    }

    return $sigla ?: 'PC';
}

function descricaoPecaPadrao(string $nome): string {
    $descricoes = [
        'blocomotor' => 'Base principal do motor.',
        'cabecote' => 'Fecha os cilindros.',
        'pistao' => 'Comprime a mistura.',
        'anelpistao' => 'Veda o pistao.',
        'biela' => 'Liga pistao ao virabrequim.',
        'virabrequim' => 'Transforma forca em giro.',
        'comandovalvulas' => 'Controla as valvulas.',
        'valvulaadmissao' => 'Entrada de ar e combustivel.',
        'valvulaescape' => 'Saida dos gases.',
        'tucho' => 'Transmite movimento as valvulas.',
        'juntacabecote' => 'Veda o cabecote.',
        'pinopistao' => 'Prende pistao a biela.',
        'bronzinabiela' => 'Apoio da biela.',
        'bronzinamancal' => 'Apoio do virabrequim.',
        'volantemotor' => 'Estabiliza o giro.',
        'varetas' => 'Acionam balancins.',
        'balancins' => 'Movem as valvulas.',
        'molasvalvula' => 'Fecham as valvulas.',
        'guiasvalvula' => 'Guiam as valvulas.',
        'retentoresvalvula' => 'Vedam oleo nas valvulas.',
        'correiadentada' => 'Sincroniza o motor.',
        'correntecomando' => 'Sincroniza o comando.',
        'tensorcorreia' => 'Mantem a correia firme.',
        'poliavirabrequim' => 'Gira acessorios.',
        'poliacomando' => 'Move o comando.',
        'tampavalvulas' => 'Cobre as valvulas.',
        'carter' => 'Reserva o oleo.',
        'juntacarter' => 'Veda o carter.',
        'bombaoleo' => 'Circula o oleo.',
        'pescadoroleo' => 'Capta oleo do carter.',
        'filtrooleo' => 'Filtra o oleo.',
        'bombaagua' => 'Circula agua do motor.',
        'termostato' => 'Controla a temperatura.',
        'radiador' => 'Resfria o motor.',
        'ventoinha' => 'Forca a refrigeracao.',
        'coletoradmissao' => 'Distribui ar ao motor.',
        'coletorescape' => 'Conduz gases de escape.',
        'corpoborboleta' => 'Controla entrada de ar.',
        'injetorescombustivel' => 'Pulverizam combustivel.',
        'bombacombustivel' => 'Envia combustivel.',
        'filtrocombustivel' => 'Filtra combustivel.',
        'velasignicao' => 'Geram a faisca.',
        'cabosvela' => 'Levam energia as velas.',
        'bobinaignicao' => 'Eleva a tensao.',
        'distribuidor' => 'Distribui a faisca.',
        'sensorrotacao' => 'Le a rotacao.',
        'sensorfase' => 'Le fase do comando.',
        'sensormap' => 'Mede pressao do ar.',
        'sensormaf' => 'Mede fluxo de ar.',
        'sensortemperatura' => 'Le temperatura.',
        'sensoroxigenio' => 'Mede oxigenio no escape.',
        'unidadecontrolemotorecu' => 'Gerencia o motor.',
        'alternador' => 'Recarrega a bateria.',
        'motorpartida' => 'Aciona o motor.',
        'correiaacessorios' => 'Move acessorios.',
        'tensorcorreiaacessorios' => 'Tensiona a correia.',
        'turbocompressor' => 'Aumenta a pressao do ar.',
        'intercooler' => 'Resfria ar pressurizado.',
        'valvulawastegate' => 'Controla pressao do turbo.',
        'valvulaegr' => 'Recircula gases.',
        'retentorvirabrequim' => 'Veda o virabrequim.',
        'retentorcomando' => 'Veda o comando.',
        'coximmotor' => 'Absorve vibracoes.',
        'tampafrontalmotor' => 'Fecha a frente do motor.',
        'tampatraseiramotor' => 'Fecha a traseira do motor.',
        'galeriaoleo' => 'Canaliza oleo.',
        'galeriaagua' => 'Canaliza agua.',
        'camaracombustao' => 'Onde ocorre a queima.',
        'camisascilindros' => 'Revestem os cilindros.',
        'prisioneiroscabecote' => 'Fixam o cabecote.',
        'parafusoscabecote' => 'Apertam o cabecote.',
        'respirocarter' => 'Alivia vapores.',
        'separadoroleo' => 'Separa oleo dos vapores.',
        'sensordetonacao' => 'Detecta batida de pino.',
        'sensorpressaooleo' => 'Le pressao do oleo.',
        'sensorniveloleo' => 'Le nivel do oleo.',
        'bujaocarter' => 'Fecha o dreno.',
        'tampaoleo' => 'Fecha a entrada de oleo.',
        'varetaoleo' => 'Mede nivel do oleo.',
        'juntatampavalvulas' => 'Veda a tampa.',
    ];

    return $descricoes[normalizarNomePeca($nome)] ?? 'Peca do motor.';
}

function precoPecaPadrao(int $id): float {
    $precos = [
        1 => 1890.00, 2 => 1240.00, 3 => 186.90, 4 => 72.50, 5 => 298.00,
        6 => 980.00, 7 => 455.00, 8 => 64.90, 9 => 69.90, 10 => 42.00,
        15 => 89.90, 16 => 48.90, 17 => 58.00, 18 => 64.00, 19 => 520.00,
        20 => 55.00, 21 => 86.00, 22 => 38.00, 23 => 46.00, 24 => 34.00,
        25 => 115.00, 26 => 135.00, 27 => 92.00, 28 => 128.00, 29 => 118.00,
        30 => 155.00, 31 => 310.00, 32 => 42.00, 33 => 265.00, 34 => 76.00,
        35 => 39.90, 36 => 280.00, 37 => 74.00, 38 => 390.00, 39 => 180.00,
        40 => 340.00, 41 => 360.00, 42 => 420.00, 43 => 210.00, 44 => 310.00,
        45 => 49.90, 46 => 36.00, 47 => 95.00, 48 => 160.00, 49 => 240.00,
        50 => 145.00, 51 => 135.00, 52 => 190.00, 53 => 220.00, 54 => 84.00,
        55 => 180.00, 56 => 890.00, 57 => 620.00, 58 => 480.00, 59 => 120.00,
        60 => 98.00, 61 => 2450.00, 62 => 730.00, 63 => 410.00, 64 => 360.00,
        65 => 78.00, 66 => 82.00, 67 => 190.00, 68 => 230.00, 69 => 240.00,
        70 => 110.00, 71 => 115.00, 72 => 95.00, 73 => 210.00, 74 => 65.00,
        75 => 58.00, 76 => 48.00, 77 => 72.00, 78 => 135.00, 79 => 125.00,
        80 => 118.00, 81 => 22.00, 82 => 36.00, 83 => 28.00, 84 => 54.00,
    ];

    return (float)($precos[$id] ?? 99.9);
}

function textoBuscaPeca(string $texto): string {
    $texto = strtolower($texto);
    return strtr($texto, [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a',
        'é' => 'e', 'ê' => 'e',
        'í' => 'i',
        'ó' => 'o', 'õ' => 'o', 'ô' => 'o',
        'ú' => 'u',
        'ç' => 'c',
    ]);
}

function normalizarNomePeca(string $nome): string {
    $partes = preg_split('/[^a-z0-9]+/', textoBuscaPeca($nome));
    $ignorar = ['de', 'da', 'do', 'das', 'dos', 'd', 'e'];
    $nomeNormalizado = '';

    foreach ($partes as $parte) {
        if ($parte !== '' && !in_array($parte, $ignorar, true)) {
            $nomeNormalizado .= $parte;
        }
    }

    return $nomeNormalizado;
}

function inferirShapePeca(string $nome): string {
    $nome = textoBuscaPeca($nome);
    $mapa = [
        'head-gasket' => ['junta do cabecote'],
        'pan-gasket' => ['junta do carter', 'junta da tampa'],
        'bearing' => ['bronzina'],
        'piston-pin' => ['pino do pistao'],
        'piston' => ['pistao', 'piston'],
        'rings' => ['anel', 'aneis', 'jogo de aneis'],
        'rod' => ['biela'],
        'flywheel' => ['volante do motor'],
        'pulley' => ['polia'],
        'seal' => ['retentor'],
        'crank' => ['virabrequim'],
        'chain' => ['corrente de comando', 'corrente', 'relacao', 'coroa', 'pinhao'],
        'camshaft' => ['comando', 'came', 'arvore'],
        'dipstick' => ['vareta de oleo'],
        'pushrod' => ['vareta'],
        'rocker' => ['balancim', 'balancins'],
        'spring' => ['mola'],
        'guide' => ['guia'],
        'tensioner' => ['tensor'],
        'belt' => ['correia dentada', 'correia de acessorios'],
        'cover' => ['tampa de valvulas', 'tampa frontal', 'tampa traseira'],
        'oil-pan' => ['carter'],
        'oil-pump' => ['bomba de oleo'],
        'pickup' => ['pescador'],
        'water-pump' => ["bomba d'agua", 'bomba dagua'],
        'thermostat' => ['termostato'],
        'radiator' => ['radiador'],
        'fan' => ['ventoinha'],
        'manifold-in' => ['coletor de admissao'],
        'manifold-out' => ['coletor de escape'],
        'throttle' => ['corpo de borboleta'],
        'injector' => ['injetor'],
        'fuel-pump' => ['bomba de combustivel'],
        'plug-wire' => ['cabos de vela'],
        'coil' => ['bobina'],
        'distributor' => ['distribuidor'],
        'sensor' => ['sensor'],
        'ecu' => ['ecu', 'unidade de controle'],
        'alternator' => ['alternador'],
        'starter' => ['motor de partida'],
        'turbo' => ['turbocompressor'],
        'intercooler' => ['intercooler'],
        'wastegate' => ['wastegate'],
        'egr' => ['egr'],
        'mount' => ['coxim'],
        'gallery' => ['galeria'],
        'chamber' => ['camara'],
        'sleeve' => ['camisa'],
        'stud' => ['prisioneiro'],
        'bolt' => ['parafuso'],
        'breather' => ['respiro'],
        'separator' => ['separador'],
        'drain-plug' => ['bujao'],
        'oil-cap' => ['tampa do oleo'],
        'valve-in' => ['valvula de admissao'],
        'valve-out' => ['valvula de escape', 'valvula de escapamento'],
        'valve' => ['valvula'],
        'head' => ['cabecote', 'cabecote'],
        'filter' => ['filtro'],
        'battery' => ['bateria'],
        'spark' => ['vela', 'ignicao'],
        'brake' => ['freio', 'pastilha', 'disco'],
        'tire' => ['pneu', 'roda'],
        'gear' => ['engrenagem', 'engrenagens'],
        'shock' => ['amortecedor', 'suspensao'],
        'lamp' => ['lampada', 'farol', 'lanterna'],
        'mirror' => ['retrovisor', 'espelho'],
        'tank' => ['tanque', 'combustivel'],
        'lifter' => ['tucho'],
        'block' => ['bloco do motor', 'bloco'],
    ];

    foreach ($mapa as $shape => $palavras) {
        foreach ($palavras as $palavra) {
            if (strpos($nome, $palavra) !== false) {
                return $shape;
            }
        }
    }

    return 'generic';
}

function pecaImagem(string $nome, string $sigla, string $shape, ?string $imagem = null): string {
    if (!empty($imagem)) {
        return $imagem;
    }

    $desenhos = [
        'block' => '<rect x="58" y="70" width="124" height="72" rx="10"/><circle cx="88" cy="106" r="18"/><circle cx="126" cy="106" r="18"/><circle cx="164" cy="106" r="18"/><rect x="72" y="54" width="96" height="18" rx="5"/><path d="M58 142h124l-14 22H72z"/>',
        'head' => '<rect x="50" y="72" width="140" height="48" rx="9"/><path d="M62 72l20-22h76l20 22"/><circle cx="82" cy="96" r="10"/><circle cx="120" cy="96" r="10"/><circle cx="158" cy="96" r="10"/><path d="M70 132h100"/>',
        'head-gasket' => '<path d="M48 68h144v72H48z"/><circle cx="76" cy="104" r="17"/><circle cx="112" cy="104" r="17"/><circle cx="148" cy="104" r="17"/><path d="M58 78h18M164 78h18M58 130h18M164 130h18"/><circle cx="92" cy="82" r="5"/><circle cx="132" cy="126" r="5"/>',
        'pan-gasket' => '<path d="M54 72h132l-18 72H72z"/><path d="M72 88h96M78 120h84"/><circle cx="74" cy="72" r="6"/><circle cx="166" cy="72" r="6"/><circle cx="88" cy="144" r="6"/><circle cx="152" cy="144" r="6"/>',
        'bearing' => '<path d="M70 124a50 50 0 0 1 100 0"/><path d="M92 124a28 28 0 0 1 56 0"/><path d="M70 124h22M148 124h22"/><path d="M82 96h76"/><path d="M96 76c14-12 34-12 48 0"/>',
        'piston-pin' => '<rect x="54" y="82" width="132" height="44" rx="22"/><ellipse cx="54" cy="104" rx="14" ry="22"/><ellipse cx="186" cy="104" rx="14" ry="22"/><path d="M78 86v36M162 86v36M96 104h48"/>',
        'piston' => '<rect x="82" y="50" width="76" height="70" rx="10"/><path d="M82 70h76M82 88h76"/><circle cx="120" cy="104" r="14"/><path d="M120 118v42M96 160h48"/>',
        'rings' => '<circle cx="120" cy="88" r="44"/><circle cx="120" cy="88" r="28"/><circle cx="120" cy="128" r="44"/><circle cx="120" cy="128" r="28"/><path d="M154 58l14-10M86 158l-14 10"/>',
        'rod' => '<circle cx="84" cy="80" r="26"/><circle cx="156" cy="132" r="34"/><path d="M102 98l30 22M110 88l38 28"/><circle cx="84" cy="80" r="12"/><circle cx="156" cy="132" r="18"/>',
        'flywheel' => '<circle cx="120" cy="104" r="58"/><circle cx="120" cy="104" r="24"/><path d="M120 46v26M120 136v26M62 104h26M152 104h26M78 62l20 20M142 126l20 20M162 62l-20 20M98 126l-20 20"/><path d="M94 104h52"/>',
        'pulley' => '<circle cx="120" cy="104" r="50"/><circle cx="120" cy="104" r="20"/><path d="M82 70c22-16 54-16 76 0M82 138c22 16 54 16 76 0"/><path d="M120 54v24M120 130v24M70 104h24M146 104h24"/>',
        'seal' => '<circle cx="120" cy="104" r="48"/><circle cx="120" cy="104" r="26"/><path d="M88 70c18-12 46-12 64 0M88 138c18 12 46 12 64 0"/><path d="M72 104h20M148 104h20"/>',
        'crank' => '<path d="M46 118h42l24-34h42l20 34h20"/><circle cx="88" cy="118" r="16"/><circle cx="154" cy="84" r="16"/><circle cx="174" cy="118" r="16"/><path d="M46 136h148"/>',
        'camshaft' => '<path d="M46 116h148"/><circle cx="70" cy="116" r="16"/><ellipse cx="104" cy="100" rx="14" ry="28"/><circle cx="134" cy="116" r="16"/><ellipse cx="166" cy="132" rx="14" ry="28"/>',
        'pushrod' => '<path d="M70 146L154 58"/><path d="M86 154L170 66"/><circle cx="70" cy="146" r="10"/><circle cx="170" cy="66" r="10"/><path d="M94 132l20 20M126 98l20 20"/>',
        'rocker' => '<path d="M62 108c28-36 86-48 128-18"/><path d="M80 126c30-16 74-24 112-8"/><circle cx="120" cy="104" r="18"/><path d="M52 118h28M162 88h30"/><path d="M112 104h16"/>',
        'spring' => '<path d="M96 50c48 0 48 18 0 18s-48 18 0 18 48 18 0 18-48 18 0 18 48 18 0 18-48 18 0 18"/><path d="M144 50c-48 0-48 18 0 18s48 18 0 18-48 18 0 18 48 18 0 18-48 18 0 18 48 18 0 18"/>',
        'guide' => '<rect x="88" y="54" width="64" height="108" rx="12"/><path d="M104 54v108M136 54v108"/><ellipse cx="120" cy="54" rx="32" ry="12"/><ellipse cx="120" cy="162" rx="32" ry="12"/><path d="M96 86h48M96 130h48"/>',
        'belt' => '<rect x="62" y="58" width="116" height="92" rx="42"/><rect x="88" y="84" width="64" height="40" rx="20"/><path d="M72 78h28M140 78h28M72 130h28M140 130h28"/><path d="M80 58l-12 92M160 58l12 92"/>',
        'tensioner' => '<circle cx="118" cy="104" r="42"/><circle cx="118" cy="104" r="16"/><path d="M154 74l30-20M154 134l30 20M62 104H36"/><path d="M174 62l14 18M174 146l14-18"/>',
        'cover' => '<path d="M58 78c12-22 112-22 124 0v58c-10 24-114 24-124 0z"/><path d="M80 90h80M72 122h96"/><circle cx="84" cy="78" r="6"/><circle cx="156" cy="78" r="6"/><circle cx="84" cy="146" r="6"/><circle cx="156" cy="146" r="6"/>',
        'oil-pan' => '<path d="M54 76h132l-16 68c-5 16-95 16-100 0z"/><path d="M74 96h92M88 130h64"/><circle cx="120" cy="146" r="8"/>',
        'oil-pump' => '<rect x="68" y="66" width="104" height="84" rx="18"/><circle cx="108" cy="108" r="24"/><circle cx="146" cy="108" r="18"/><path d="M46 108h22M172 94h24M172 122h24"/><path d="M108 84v48M84 108h48"/>',
        'pickup' => '<path d="M60 68h64c22 0 34 18 34 36v38"/><rect x="130" y="130" width="58" height="28" rx="10"/><path d="M142 144h34M70 68v34M92 68v28"/>',
        'water-pump' => '<circle cx="118" cy="106" r="42"/><path d="M118 64v84M76 106h84M88 76l60 60M148 76l-60 60"/><path d="M160 92h30v28h-30M62 94H40v24h22"/>',
        'thermostat' => '<path d="M86 70h68l22 34-22 34H86l-22-34z"/><circle cx="120" cy="104" r="24"/><path d="M120 80v48M96 104h48"/>',
        'radiator' => '<rect x="58" y="54" width="124" height="106" rx="8"/><path d="M78 54v106M98 54v106M118 54v106M138 54v106M158 54v106"/><path d="M58 80h124M58 106h124M58 132h124"/><path d="M78 42h84M78 172h84"/>',
        'fan' => '<circle cx="120" cy="104" r="18"/><path d="M120 86c-6-38 24-44 38-24 10 14-8 30-38 42M138 114c36 14 30 44 6 50-18 4-24-20-24-60M102 114c-30 24-52 4-42-20 8-18 32-10 60 10"/><circle cx="120" cy="104" r="62"/>',
        'manifold-in' => '<path d="M52 86h54c18 0 24 18 24 32v28"/><path d="M84 86v60M112 86v60M140 86v60"/><path d="M130 118h58M188 98v40"/><path d="M52 70h72"/>',
        'manifold-out' => '<path d="M54 72h34c22 0 22 24 42 24h56"/><path d="M54 104h44c18 0 20 22 38 22h50"/><path d="M54 136h56c20 0 22-18 42-18h34"/><path d="M186 88v58"/>',
        'throttle' => '<circle cx="120" cy="104" r="44"/><circle cx="120" cy="104" r="24"/><path d="M76 104H42M164 104h34"/><path d="M92 76l56 56"/><path d="M92 132l56-56"/>',
        'injector' => '<path d="M92 54h56v34l-14 18v32H106v-32L92 88z"/><path d="M100 70h40M104 88h32"/><path d="M112 138l-12 22M128 138l12 22"/><path d="M76 58h18M146 58h18"/>',
        'fuel-pump' => '<rect x="72" y="62" width="96" height="76" rx="14"/><circle cx="104" cy="100" r="20"/><path d="M168 84h24v38h-24M48 100h24M96 138v22h48v-22"/><path d="M104 80v40M84 100h40"/>',
        'plug-wire' => '<path d="M54 130c22-80 100-80 132-12"/><path d="M74 142c16-54 76-58 100-10"/><rect x="50" y="130" width="34" height="22" rx="8"/><rect x="156" y="120" width="34" height="22" rx="8"/><path d="M62 152v14M172 142v14"/>',
        'coil' => '<rect x="74" y="60" width="92" height="90" rx="12"/><path d="M92 60V46h20v14M128 60V46h20v14"/><path d="M90 88h60M90 116h60M100 150v16M140 150v16"/><path d="M106 102h28"/>',
        'distributor' => '<path d="M82 84c10-28 66-28 76 0v58H82z"/><path d="M94 84V58h52v26"/><circle cx="120" cy="112" r="20"/><path d="M120 92v40M100 112h40"/><path d="M76 62H50M164 62h26M120 58V36"/>',
        'sensor' => '<rect x="82" y="62" width="76" height="48" rx="12"/><path d="M120 110v42"/><path d="M102 152h36"/><path d="M88 82H62M152 82h26M62 82v28M178 82v28"/><circle cx="120" cy="86" r="12"/>',
        'ecu' => '<rect x="58" y="58" width="124" height="92" rx="10"/><path d="M82 58v-18M104 58v-18M126 58v-18M148 58v-18"/><path d="M82 150v18M104 150v18M126 150v18M148 150v18"/><rect x="88" y="84" width="64" height="40" rx="6"/><path d="M98 104h44"/>',
        'alternator' => '<circle cx="120" cy="104" r="48"/><circle cx="120" cy="104" r="18"/><path d="M84 70l72 68M156 70l-72 68M72 104h96"/><path d="M72 76l-18-18h34M168 132l18 18h-34"/>',
        'starter' => '<rect x="58" y="78" width="104" height="54" rx="18"/><circle cx="86" cy="105" r="22"/><path d="M162 92h34v26h-34M58 105H36"/><path d="M92 70h48M92 140h48"/>',
        'turbo' => '<path d="M80 62c44-30 100 0 100 46 0 34-28 58-62 54-36-4-54-38-38-70"/><circle cx="120" cy="106" r="28"/><path d="M120 78v56M92 106h56M100 86l40 40M140 86l-40 40"/><path d="M176 104h28v34h-42"/>',
        'intercooler' => '<rect x="48" y="70" width="144" height="68" rx="8"/><path d="M68 70v68M88 70v68M108 70v68M128 70v68M148 70v68M168 70v68"/><path d="M48 90h144M48 118h144"/><path d="M48 104H28M192 104h20"/>',
        'wastegate' => '<circle cx="102" cy="104" r="38"/><path d="M140 104h48M158 82l30 22-30 22"/><path d="M102 66v76M64 104h76"/><path d="M78 78l48 52"/>',
        'egr' => '<rect x="72" y="78" width="96" height="58" rx="18"/><path d="M72 106H44M168 106h28"/><circle cx="120" cy="107" r="22"/><path d="M108 95l24 24M132 95l-24 24"/><path d="M88 78V58h64v20"/>',
        'mount' => '<path d="M68 144l22-72h60l22 72z"/><path d="M92 72l28-28 28 28"/><circle cx="120" cy="94" r="18"/><path d="M70 144h100M96 144v18M144 144v18"/>',
        'gallery' => '<path d="M54 92h132"/><path d="M72 92v46M108 92v46M144 92v46M180 92v46"/><circle cx="72" cy="138" r="12"/><circle cx="108" cy="138" r="12"/><circle cx="144" cy="138" r="12"/><circle cx="180" cy="138" r="12"/><path d="M54 76h132"/>',
        'chamber' => '<path d="M76 76c20-30 68-30 88 0 22 34 2 78-44 78S54 110 76 76z"/><path d="M92 92h56M100 122h40"/><circle cx="104" cy="72" r="10"/><circle cx="136" cy="72" r="10"/>',
        'sleeve' => '<ellipse cx="120" cy="62" rx="44" ry="18"/><path d="M76 62v84M164 62v84"/><ellipse cx="120" cy="146" rx="44" ry="18"/><ellipse cx="120" cy="62" rx="24" ry="10"/><path d="M94 92h52M94 120h52"/>',
        'stud' => '<path d="M86 54v108M120 54v108M154 54v108"/><path d="M78 70h16M112 70h16M146 70h16M78 146h16M112 146h16M146 146h16"/><path d="M82 54h8M116 54h8M150 54h8"/>',
        'bolt' => '<path d="M88 66l20-20h24l20 20-20 20h-24z"/><path d="M120 86v76"/><path d="M100 102h40M100 122h40M100 142h40"/>',
        'breather' => '<rect x="74" y="78" width="92" height="52" rx="18"/><path d="M86 78V58h68v20"/><path d="M92 130v26h56v-26"/><path d="M94 104h52M104 90l32 28M136 90l-32 28"/>',
        'separator' => '<rect x="66" y="60" width="108" height="92" rx="14"/><path d="M88 60v92M120 60v92M152 60v92"/><path d="M66 92h108M66 122h108"/><path d="M50 78h16M174 136h18"/>',
        'drain-plug' => '<path d="M82 78h76l16 24-16 24H82l-16-24z"/><path d="M98 126v30h44v-30"/><path d="M96 96h48M104 112h32"/>',
        'oil-cap' => '<path d="M76 92h88l-10 54H86z"/><path d="M92 72h56v20H92z"/><path d="M98 72V54h44v18"/><path d="M100 118h40"/>',
        'dipstick' => '<path d="M86 50c28 0 28 30 0 30s-28-30 0-30z"/><path d="M86 80l66 82"/><path d="M138 146l28-22M126 130l18-14"/><path d="M70 64h32"/>',
        'valve-in' => '<path d="M120 44v88"/><path d="M88 150c8-20 56-20 64 0"/><path d="M96 132h48"/><path d="M108 44h24"/><path d="M72 80h26M72 80l12-12M72 80l12 12"/>',
        'valve-out' => '<path d="M120 44v88"/><path d="M88 150c8-20 56-20 64 0"/><path d="M96 132h48"/><path d="M108 44h24"/><path d="M142 80h26M168 80l-12-12M168 80l-12 12"/>',
        'valve' => '<path d="M120 44v88"/><path d="M88 150c8-20 56-20 64 0"/><path d="M96 132h48"/><path d="M108 44h24"/>',
        'lifter' => '<rect x="90" y="58" width="60" height="92" rx="12"/><path d="M96 82h48M96 126h48"/><ellipse cx="120" cy="58" rx="30" ry="12"/><ellipse cx="120" cy="150" rx="30" ry="12"/>',
        'filter' => '<rect x="70" y="58" width="100" height="96" rx="12"/><path d="M88 58v96M106 58v96M124 58v96M142 58v96M70 80h100M70 132h100"/><ellipse cx="120" cy="58" rx="50" ry="14"/><ellipse cx="120" cy="154" rx="50" ry="14"/>',
        'battery' => '<rect x="62" y="68" width="116" height="78" rx="10"/><path d="M88 68V54h20v14M132 68V54h20v14M82 106h28M138 106h28M152 92v28"/>',
        'spark' => '<path d="M96 48h48l-8 52H104z"/><path d="M108 100v28l-18 28M132 100v28l18 28M102 68h36M100 84h40"/>',
        'brake' => '<circle cx="120" cy="104" r="52"/><circle cx="120" cy="104" r="18"/><path d="M120 52v26M120 130v26M68 104h26M146 104h26M84 68l18 18M138 122l18 18M156 68l-18 18M102 122l-18 18"/>',
        'tire' => '<circle cx="120" cy="104" r="58"/><circle cx="120" cy="104" r="34"/><circle cx="120" cy="104" r="10"/><path d="M120 46v24M120 138v24M62 104h24M154 104h24M80 64l18 18M142 126l18 18M160 64l-18 18M98 126l-18 18"/>',
        'chain' => '<rect x="52" y="78" width="54" height="32" rx="16"/><rect x="92" y="78" width="54" height="32" rx="16"/><rect x="132" y="78" width="54" height="32" rx="16"/><rect x="52" y="118" width="54" height="32" rx="16"/><rect x="92" y="118" width="54" height="32" rx="16"/><rect x="132" y="118" width="54" height="32" rx="16"/>',
        'gear' => '<circle cx="120" cy="104" r="46"/><circle cx="120" cy="104" r="18"/><path d="M120 42v24M120 142v24M58 104h24M158 104h24M76 60l18 18M146 130l18 18M164 60l-18 18M94 130l-18 18"/>',
        'shock' => '<path d="M82 44l76 102M102 44l76 102"/><path d="M92 58h36M104 76h36M116 94h36M128 112h36M140 130h36"/><circle cx="82" cy="44" r="14"/><circle cx="178" cy="146" r="14"/>',
        'lamp' => '<path d="M72 78c22-28 74-28 96 0v52c-22 28-74 28-96 0z"/><path d="M92 104h56M120 76v56"/><path d="M58 92H36M58 116H36M182 92h22M182 116h22"/>',
        'mirror' => '<path d="M64 76c26-26 86-20 112 8-4 36-44 54-92 36-18-7-28-22-20-44z"/><path d="M118 128l-24 34M94 162h52"/>',
        'tank' => '<path d="M58 94c20-34 82-42 124-12 6 28-12 58-54 68H78c-20-12-28-34-20-56z"/><path d="M142 62h28v18M86 108h42"/>',
        'generic' => '<path d="M64 132l32-72h50l30 72"/><path d="M82 132h76"/><circle cx="96" cy="92" r="12"/><circle cx="144" cy="92" r="12"/><path d="M108 60l24 72"/>',
    ];

    $paletas = [
        ['#FAF3E0', '#E8A020', '#B87A0A'],
        ['#F6EFE2', '#D86F45', '#A23D22'],
        ['#EEF3E7', '#7DA453', '#426B2C'],
        ['#EDF1F2', '#6294A0', '#315C66'],
        ['#F4EDE7', '#B98762', '#704832'],
        ['#F1F0E8', '#A8A13D', '#66631E'],
    ];
    $paleta = $paletas[abs(crc32($shape . $nome)) % count($paletas)];

    $label = htmlspecialchars($nome, ENT_QUOTES);
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 190" role="img" aria-label="' . $label . '"><defs><linearGradient id="bg" x1="0" x2="1" y1="0" y2="1"><stop stop-color="' . $paleta[0] . '"/><stop offset="1" stop-color="' . $paleta[1] . '"/></linearGradient><filter id="shadow" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="8" stdDeviation="8" flood-color="#1A1410" flood-opacity=".18"/></filter></defs><rect width="240" height="190" fill="url(#bg)"/><path d="M0 150H240V190H0Z" fill="#1A1410" opacity=".08"/><g fill="none" stroke="#1A1410" stroke-width="9" stroke-linecap="round" stroke-linejoin="round" filter="url(#shadow)">' . ($desenhos[$shape] ?? $desenhos['block']) . '</g><text x="18" y="34" font-family="Arial, sans-serif" font-size="18" font-weight="700" fill="' . $paleta[2] . '">' . htmlspecialchars($sigla, ENT_QUOTES) . '</text></svg>';

    return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
}
