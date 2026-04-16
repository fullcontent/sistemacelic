<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Servico;
use App\Models\Prestador;
use App\User;

$csvFile = __DIR__.'/Ordem de Serviços (1) - Base O.S.csv';
$sqlFile = __DIR__.'/insert_ordem_servicos.sql';

if (!file_exists($csvFile)) {
    die("Arquivo CSV não encontrado.\n");
}

$handle = fopen($csvFile, 'r');
// Ignora o cabeçalho
$header = fgetcsv($handle, 0, ",");

$sql = "-- Importação Reversa do CSV para Ordem de Serviços\n\n";

$missingServicos = [];

$idCounter = 10000; // Começa de um ID seguro ou usar auto_increment

while (($row = fgetcsv($handle, 0, ",")) !== false) {
    if (count($row) < 25) continue;
    
    // Mapeamento dos campos do CSV
    $csv_id = trim($row[0]);
    $csv_num_os = trim($row[1]);
    $csv_status = trim($row[2]);
    $csv_nome_servico = trim($row[3]);
    $csv_os_str = trim($row[7]); // EP1727
    $csv_prestador_nome = trim($row[10]);
    $csv_prestador_telefone = trim($row[12]);
    $csv_prestador_email = trim($row[13]);
    $csv_dados_bancarios = trim($row[14]);
    $csv_valor_servico_raw = trim($row[15]);
    $csv_valor_pago_raw = trim($row[16]);
    $csv_data_pagamento = trim($row[17]); // "18-jun.-21" ou "09/12/2021"
    $csv_valor_aberto_raw = trim($row[18]);
    $csv_valor_reembolso_raw = trim($row[19]);
    $csv_modelo_proposta = trim($row[20]); // '1X', '2X', etc
    $csv_escopo = trim($row[21]);
    $csv_responsavel = trim($row[22]);
    $csv_forma_pagamento = trim($row[23]);
    $csv_conta_bancaria = trim($row[24]);

    // Limpar valores
    $valor_servico = preg_replace('/[^0-9,]/', '', $csv_valor_servico_raw);
    $valor_servico = str_replace(',', '.', $valor_servico);
    $valor_servico = (float)$valor_servico ?: 0;

    $valor_pago = preg_replace('/[^0-9,]/', '', $csv_valor_pago_raw);
    $valor_pago = str_replace(',', '.', $valor_pago);
    $valor_pago = (float)$valor_pago ?: 0;

    // Buscar Servico
    $servico = Servico::where('os', $csv_os_str)->first();
    $servico_id = $servico ? $servico->id : 'NULL';
    
    if (!$servico) {
        $missingServicos[] = $csv_os_str;
    }

    // Prestador
    $prestador = Prestador::where('nome', $csv_prestador_nome)->first();
    if (!$prestador && $csv_prestador_nome != '') {
        $prestador = new Prestador();
        $prestador->nome = $csv_prestador_nome;
        $prestador->telefone = $csv_prestador_telefone;
        $prestador->email = $csv_prestador_email;
        $prestador->chavePix = $csv_dados_bancarios;
        $prestador->formaPagamento = strtolower($csv_forma_pagamento) == 'pix' ? 'pix' : 'transferencia';
        $prestador->save();
    }
    $prestador_id = $prestador ? $prestador->id : 'NULL';

    // Usuario
    $user = User::where('name', 'like', "%{$csv_responsavel}%")->first();
    $user_id = $user ? $user->id : 1;

    // Campos adicionais
    $situacao = !empty($csv_data_pagamento) && $csv_data_pagamento != '-' ? 'finalizado' : 'andamento';
    $formaPagamentoNum = 1; // Forçado para 1 conforme solicitado (retroativo)
    $escopo = addslashes($csv_escopo);
    $now = date('Y-m-d H:i:s');
    $escopo = addslashes($csv_escopo);

    // Converter data do CSV para Y-m-d
    // Formato comum no CSV: "18-jun.-21" ou "09/12/2021"
    $data_pagamento_sql = 'NULL';
    if (!empty($csv_data_pagamento) && $csv_data_pagamento != '-') {
        // Tenta extrair dd/mm/yyyy
        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $csv_data_pagamento, $matches)) {
            $data_pagamento_sql = "'" . $matches[3] . "-" . $matches[2] . "-" . $matches[1] . "'";
        } 
        // Tenta extrair dd-mmm.-yy
        elseif (preg_match('/(\d+)-([a-z]+)\.-(\d+)/i', $csv_data_pagamento, $matches)) {
            $meses = ['jan'=>1,'fev'=>2,'mar'=>3,'abr'=>4,'mai'=>5,'jun'=>6,'jul'=>7,'ago'=>8,'set'=>9,'out'=>10,'nov'=>11,'dez'=>12];
            $m = strtolower($matches[2]);
            if (isset($meses[$m])) {
                $y = '20' . $matches[3]; // Assume anos 2000+
                $data_pagamento_sql = sprintf("'%04d-%02d-%02d'", $y, $meses[$m], $matches[1]);
            }
        }
    }

    // Calcular data de criação baseada no pagamento (ou usar data atual se não houver)
    $created_at = $now;
    if ($data_pagamento_sql != 'NULL') {
        $created_at = trim($data_pagamento_sql, "'") . ' 00:00:00';
    }

    $id_os = (int) $csv_id;
    if ($id_os <= 0) $id_os = $idCounter++;

    $sql .= "INSERT INTO `ordem_servicos` (`id`, `servico_id`, `prestador_id`, `valorServico`, `formaPagamento`, `escopo`, `user_id`, `situacao`, `created_at`, `updated_at`) VALUES ";
    $sql .= "($id_os, $servico_id, $prestador_id, $valor_servico, $formaPagamentoNum, '$escopo', $user_id, '$situacao', '$created_at', '$created_at') ";
    $sql .= "ON DUPLICATE KEY UPDATE `valorServico` = VALUES(`valorServico`), `situacao` = VALUES(`situacao`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);\n";
    
    // Reembolso
    $valor_reembolso_raw = preg_replace('/[^0-9,]/', '', $csv_valor_reembolso_raw);
    $valor_reembolso_raw = str_replace(',', '.', $valor_reembolso_raw);
    $valor_reembolso = (float)$valor_reembolso_raw ?: 0;
    $reembolso_status = $valor_reembolso > 0 ? 'sim' : 'nao';

    // Vínculo
    if ($servico_id != 'NULL') {
        $sql .= "INSERT INTO `ordem_servico_vinculos` (`ordemServico_id`, `servico_id`, `valor`, `reembolso`, `created_at`, `updated_at`) VALUES ";
        $sql .= "($id_os, $servico_id, $valor_servico, '$reembolso_status', '$created_at', '$created_at');\n";
    }
    
    // Calcula o valor por parcela (sempre 1 agora)
    $valor_por_parcela = $valor_servico;
    
    // Verifica se esta parcela está paga
    $situacao_pagamento = !empty($csv_data_pagamento) && $csv_data_pagamento != '-' ? 'pago' : 'aberto';
    $data_pagamento_linha = $data_pagamento_sql;

    $forma_pagamento_slug = strtolower(trim($csv_forma_pagamento)) ?: 'pix';
    
    $sql .= "INSERT INTO `ordem_servico_pagamentos` (`ordemServico_id`, `parcela`, `valor`, `formaPagamento`, `situacao`, `dataPagamento`, `created_at`, `updated_at`) VALUES ";
    $sql .= "($id_os, 1, $valor_por_parcela, '$forma_pagamento_slug', '$situacao_pagamento', $data_pagamento_linha, '$created_at', '$created_at');\n";

}
fclose($handle);
file_put_contents($sqlFile, $sql);

echo "Arquivo SQL gerado em: " . $sqlFile . "\n";
echo "Serviços não encontrados no banco: " . count(array_unique($missingServicos)) . "\n";
