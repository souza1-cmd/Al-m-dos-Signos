<?php  
include('header.php');

// 1. Validação da Data
$data_nascimento_input = $_POST['data_nascimento'] ?? null;
if (!$data_nascimento_input) {
    header("Location: index.php");
    exit;
}

$data_nascimento = DateTime::createFromFormat('Y-m-d', $data_nascimento_input);

// 2. Carregamento do XML com tratamento de erro
// Ajuste o caminho: se o XML está na raiz e este script em layouts/, use '../signos.xml'
$xmlPath = 'signos.xml'; 

if (!file_exists($xmlPath)) {
    die("Erro: Arquivo XML não encontrado no caminho: " . realpath($xmlPath));
}

$signos = simplexml_load_file($xmlPath);

if ($signos === false) {
    echo "Erro ao ler o XML. Verifique se o arquivo está formatado corretamente.";
    exit;
}

function verificar_signo($data, $inicio, $fim) {
    $ano = $data->format('Y');
    $data_inicio = DateTime::createFromFormat('d/m/Y', "$inicio/$ano");
    $data_fim = DateTime::createFromFormat('d/m/Y', "$fim/$ano");
    
    // Tratamento para Capricórnio (vira o ano)
    if ($data_inicio > $data_fim) {
        if ($data->format('m') == '01') {
            $data_inicio->modify('-1 year');
        } else {
            $data_fim->modify('+1 year');
        }
    }
    return ($data >= $data_inicio && $data <= $data_fim);
}

$signo_encontrado = null;

foreach ($signos->signo as $signo) {
    if (verificar_signo($data_nascimento, (string)$signo->dataInicio, (string)$signo->dataFim)) {
        $signo_encontrado = $signo;
        break;
    }
}
?>

<body>
    <div class="container mt-5">
        <div class="content-wrapper text-center p-5" style="background-color: white; border-radius: 15px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); margin: auto; max-width: 600px;">
            <?php if ($signo_encontrado): ?>
                <h1 class="text-primary"><?= $signo_encontrado->signoNome ?></h1>
                <p class="lead mt-3"><?= $signo_encontrado->descricao ?></p>
            <?php else: ?>
                <p>Não foi possível determinar seu signo. Verifique a data informada.</p>
            <?php endif; ?>
            <a href="index.php" class="btn btn-secondary mt-4">Voltar</a>
        </div>
    </div>
</body>
