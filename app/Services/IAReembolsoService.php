<?php

namespace App\Services;

use App\Models\Boleto;
use App\Models\Comprovante;
use App\Models\Faturamento;
use Illuminate\Support\Facades\Log;

class IAReembolsoService
{
    /**
     * Cruza os dados do Boleto e Comprovante e aplica a regra:
     * SE O COMPROVANTE É DIFERENTE DE CASTRO, O REEMBOLSO NAO PODE ESTAR COMO SIM.
     */
    public function validarComprovante(Boleto $boleto, Comprovante $comprovante)
    {
        $divergencia = false;
        $motivo = [];
        $reembolsoBloqueado = false;

        // 1. Checagem de Valores
        if ($boleto->valor !== null && $comprovante->valor_pago !== null) {
            // Permitir pequena variação de centavos devido a juros, ou exigir exato
            if (abs($boleto->valor - $comprovante->valor_pago) > 0.05) {
                $divergencia = true;
                $motivo[] = "Valor do boleto (R$ {$boleto->valor}) difere do valor pago (R$ {$comprovante->valor_pago}).";
            }
        }

        // 2. Regra de Negócio Crítica (Reembolso Castro)
        $favorecido = strtoupper($comprovante->favorecido_pago ?? '');
        if (!empty($favorecido)) {
            if (strpos($favorecido, 'CASTRO') === false) {
                $divergencia = true;
                $reembolsoBloqueado = true;
                $motivo[] = "Bloqueio de Reembolso: O favorecido do comprovante ('{$favorecido}') NÃO é a Castro Empresarial.";
            }
        }

        // 3. Atualizar Models
        $comprovante->divergencia = $divergencia;
        $comprovante->motivo_divergencia = implode(" | ", $motivo);
        $comprovante->reembolso_bloqueado = $reembolsoBloqueado;
        $comprovante->status_auditoria = 'extraido';
        $comprovante->save();

        $boleto->status_auditoria = 'extraido';
        $boleto->save();

        if ($reembolsoBloqueado && $boleto->faturamento_id) {
            $faturamento = Faturamento::find($boleto->faturamento_id);
            if ($faturamento) {
                // Remove o status de reembolso do faturamento/taxa se bloqueado pela IA
                // $faturamento->reembolso = 'nao';
                // $faturamento->save();
                Log::warning("Reembolso bloqueado pela IA no Faturamento ID: " . $faturamento->id);
            }
        }

        return [
            'sucesso' => true,
            'divergencia' => $divergencia,
            'reembolso_bloqueado' => $reembolsoBloqueado,
            'mensagens' => $motivo
        ];
    }
}
