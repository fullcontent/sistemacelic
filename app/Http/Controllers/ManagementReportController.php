<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposta;
use App\Models\PropostaServico;
use App\Models\Servico;
use App\Models\Taxa;
use App\Models\Pendencia;
use App\User;
use Carbon\Carbon;
use DB;

class ManagementReportController extends Controller
{
    public function summary()
    {
        $timezone = 'America/Sao_Paulo';
        $now = Carbon::now($timezone);
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Helper for User 1 exclusion (Allow NULLs)
        $notUser1 = function ($q) {
            return $q->where(function ($sq) {
                $sq->where('responsavel_id', '!=', 1)
                  ->orWhereNull('responsavel_id');
            });
        };

        // Proposals Stats (Created this month)
        $proposalsQuery = Proposta::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereNotIn('empresa_id', [16])
            ->where('status', '!=', 'Arquivada');
        $proposalsQuery = $notUser1($proposalsQuery);
        
        $proposals = (clone $proposalsQuery)->get();
        $totalCreated = $proposals->count();

        $proposalsApproved = $proposals->where('status', 'Aprovada');
        $approvedCount = $proposalsApproved->count();
        $approvedIds = $proposalsApproved->pluck('id');
        $approvedValue = PropostaServico::whereIn('proposta_id', $approvedIds)->sum('valor');
        
        $conversionRate = $totalCreated > 0 ? ($approvedCount / $totalCreated) * 100 : 0;

        // Aging Buckets (Current state of all proposals "Em análise")
        $agingQuery = Proposta::where('status', 'Em análise')
            ->whereNotIn('empresa_id', [16]);
        $agingQuery = $notUser1($agingQuery);

        $aging = [
            '0_7_days' => (clone $agingQuery)->where(function($q) use ($now) {
                $q->where(function($sq) use ($now) {
                    $sq->whereNotNull('sent_to_analysis_at')
                       ->where('sent_to_analysis_at', '>=', $now->copy()->subDays(7));
                })->orWhere(function($sq) use ($now) {
                    $sq->whereNull('sent_to_analysis_at')
                       ->where('created_at', '>=', $now->copy()->subDays(7));
                });
            })->count(),
            '8_15_days' => (clone $agingQuery)->where(function($q) use ($now) {
                $q->where(function($sq) use ($now) {
                    $sq->whereNotNull('sent_to_analysis_at')
                       ->where('sent_to_analysis_at', '<', $now->copy()->subDays(7))
                       ->where('sent_to_analysis_at', '>=', $now->copy()->subDays(15));
                })->orWhere(function($sq) use ($now) {
                    $sq->whereNull('sent_to_analysis_at')
                       ->where('created_at', '<', $now->copy()->subDays(7))
                       ->where('created_at', '>=', $now->copy()->subDays(15));
                });
            })->count(),
            '15_plus_days' => (clone $agingQuery)->where(function($q) use ($now) {
                $q->where(function($sq) use ($now) {
                    $sq->whereNotNull('sent_to_analysis_at')
                       ->where('sent_to_analysis_at', '<', $now->copy()->subDays(15));
                })->orWhere(function($sq) use ($now) {
                    $sq->whereNull('sent_to_analysis_at')
                       ->where('created_at', '<', $now->copy()->subDays(15));
                });
            })->count(),
        ];

        // Active Services
        $activeServices = Servico::where('situacao', 'andamento');
        $activeServices = $notUser1($activeServices);
        $activeServicesCount = $activeServices->count();

        // Expiring soon (60 days)
        $expiringLicenses = Servico::where('situacao', 'finalizado')
            ->where('tipo', 'licencaOperacao')
            ->whereBetween('licenca_validade', [$now->toDateString(), $now->copy()->addDays(60)->toDateString()]);
        $expiringLicenses = $notUser1($expiringLicenses);
        $expiringLicensesCount = $expiringLicenses->count();

        // Pending items (overdue)
        $overduePendencias = Pendencia::where('status', 'pendente');
        $overduePendencias = $notUser1($overduePendencias);
        $overduePendenciasCount = $overduePendencias->where(function ($query) use ($now) {
                $query->whereDate('dataLimite', '<', $now->toDateString())
                    ->orWhereDate('vencimento', '<', $now->toDateString());
            })
            ->count();

        return response()->json([
            'month' => $now->translatedFormat('F Y'),
            'proposals' => [
                'total_created' => $totalCreated,
                'approved_this_month_count' => $approvedCount,
                'approved_this_month_value' => (float)$approvedValue,
                'conversion_rate' => round($conversionRate, 2),
                'aging' => $aging
            ],
            'operational' => [
                'active_services' => $activeServicesCount,
                'expiring_licenses_60d' => $expiringLicensesCount,
                'overdue_pendencias' => $overduePendenciasCount,
            ]
        ]);
    }

    public function proposalsAging()
    {
        $timezone = 'America/Sao_Paulo';
        $now = Carbon::now($timezone);

        $query = Proposta::where('status', 'Em análise')
            ->whereNotIn('empresa_id', [16]);

        // Fix owner filter for aging endpoint too
        $query->where(function ($q) {
            $q->where('responsavel_id', '!=', 1)
              ->orWhereNull('responsavel_id');
        });

        $aging = [
            '0_to_7_days' => [
                'count' => (clone $query)->where(function($q) use ($now) {
                    $q->where(function($sq) use ($now) {
                        $sq->whereNotNull('sent_to_analysis_at')
                           ->where('sent_to_analysis_at', '>=', $now->copy()->subDays(7));
                    })->orWhere(function($sq) use ($now) {
                        $sq->whereNull('sent_to_analysis_at')
                           ->where('created_at', '>=', $now->copy()->subDays(7));
                    });
                })->count(),
                'recommendation' => 'Acompanhamento Inicial'
            ],
            '8_to_15_days' => [
                'count' => (clone $query)->where(function($q) use ($now) {
                    $q->where(function($sq) use ($now) {
                        $sq->whereNotNull('sent_to_analysis_at')
                           ->where('sent_to_analysis_at', '<', $now->copy()->subDays(7))
                           ->where('sent_to_analysis_at', '>=', $now->copy()->subDays(15));
                    })->orWhere(function($sq) use ($now) {
                        $sq->whereNull('sent_to_analysis_at')
                           ->where('created_at', '<', $now->copy()->subDays(7))
                           ->where('created_at', '>=', $now->copy()->subDays(15));
                    });
                })->count(),
                'recommendation' => 'Follow-up Intensivo'
            ],
            '15_plus_days' => [
                'count' => (clone $query)->where(function($q) use ($now) {
                    $q->where(function($sq) use ($now) {
                        $sq->whereNotNull('sent_to_analysis_at')
                           ->where('sent_to_analysis_at', '<', $now->copy()->subDays(15));
                    })->orWhere(function($sq) use ($now) {
                        $sq->whereNull('sent_to_analysis_at')
                           ->where('created_at', '<', $now->copy()->subDays(15));
                    });
                })->count(),
                'recommendation' => 'Revisão de Estratégia / Escalonação'
            ],
        ];

        return response()->json($aging);
    }

    public function operationalIndicators()
    {
        $timezone = 'America/Sao_Paulo';
        $now = Carbon::now($timezone);

        $notUser1 = function ($q) {
            return $q->where(function ($sq) {
                $sq->where('responsavel_id', '!=', 1)
                  ->orWhereNull('responsavel_id');
            });
        };

        // Licenças 60d
        $licenses60d = Servico::where('situacao', 'finalizado')
            ->where('tipo', 'licencaOperacao')
            ->whereBetween('licenca_validade', [$now->toDateString(), $now->copy()->addDays(60)->toDateString()]);
        $licenses60d = $notUser1($licenses60d)->count();

        // Pendências em Atraso
        $overduePendencias = Pendencia::where('status', 'pendente');
        $overduePendencias = $notUser1($overduePendencias);
        $overduePendencias = $overduePendencias->where(function ($query) use ($now) {
                $query->whereDate('dataLimite', '<', $now->toDateString())
                    ->orWhereDate('vencimento', '<', $now->toDateString());
            })->count();

        // Aging > 15 dias
        $aging15plus = Proposta::where('status', 'Em análise')
            ->whereNotIn('empresa_id', [16]);
        $aging15plus = $notUser1($aging15plus);
        $aging15plus = $aging15plus->where(function($q) use ($now) {
                $q->where(function($sq) use ($now) {
                    $sq->whereNotNull('sent_to_analysis_at')
                       ->where('sent_to_analysis_at', '<', $now->copy()->subDays(15));
                })->orWhere(function($sq) use ($now) {
                    $sq->whereNull('sent_to_analysis_at')
                       ->where('created_at', '<', $now->copy()->subDays(15));
                });
            })->count();

        return response()->json([
            [
                'indicator' => 'Licenças a vencer (60 dias)',
                'volume' => $licenses60d,
                'status' => 'Monitorando'
            ],
            [
                'indicator' => 'Pendências em Atraso',
                'volume' => $overduePendencias,
                'status' => $overduePendencias > 50 ? 'Crítico' : 'Atenção'
            ],
            [
                'indicator' => 'Envelhecimento de Propostas (Aging > 15 dias)',
                'volume' => $aging15plus,
                'status' => $aging15plus > 10 ? 'Alto' : 'Normal'
            ]
        ]);
    }

    public function analystWorkload()
    {
        $currentYear = date('Y');
        
        $pendencias = Pendencia::with(['responsavel'])
            ->where('status', 'pendente')
            ->whereYear('created_at', $currentYear)
            ->where(function ($q) {
                $q->where('responsavel_id', '!=', 1)
                  ->orWhereNull('responsavel_id');
            })
            ->get();

        $workload = $pendencias->groupBy(function ($p) {
            return $p->responsavel->name ?? 'Sem Responsável';
        })->map(function ($items, $name) {
            $volume = $items->count();
            
            $status = 'Estável';
            if ($volume > 100) $status = 'Crítico';
            elseif ($volume > 60) $status = 'Sobrecarregada';
            elseif ($volume > 30) $status = 'Atenção';

            return [
                'analyst' => $name,
                'volume' => $volume,
                'status' => $status
            ];
        })->values();

        return response()->json($workload);
    }

    public function taxConference()
    {
        $now = Carbon::now();
        
        $taxas = Taxa::with(['servico.unidade', 'servico.empresa', 'servico.responsavel'])
            ->where('situacao', 'aberto')
            ->whereHas('servico', function($q) {
                $q->where(function($sq) {
                    $sq->where('responsavel_id', '!=', 1)
                      ->orWhereNull('responsavel_id');
                });
            })
            ->get()
            ->map(function ($t) use ($now) {
                $vencimento = Carbon::parse($t->vencimento);
                $overdueDays = $now->diffInDays($vencimento, false);
                
                // Only return inconsistent or highly overdue ones for "conference"
                if ($overdueDays >= -180) return null;

                return [
                    'id' => $t->id,
                    'nome' => $t->nome,
                    'valor' => $t->valor,
                    'vencimento' => $t->vencimento,
                    'unidade' => $t->servico->unidade->nomeFantasia ?? 'N/A',
                    'empresa' => $t->servico->empresa->nomeFantasia ?? 'N/A',
                    'responsavel' => $t->servico->responsavel->name ?? 'N/A',
                    'status' => 'Inconsistente',
                    'overdue_days' => abs($overdueDays)
                ];
            })->filter()->values();

        return response()->json($taxas);
    }

    public function systemInconsistencies()
    {
        $oneYearAgo = Carbon::now()->subYear();

        $notUser1 = function ($q) {
            return $q->where(function ($sq) {
                $sq->where('responsavel_id', '!=', 1)
                  ->orWhereNull('responsavel_id');
            });
        };

        // Pendências > 1 ano
        $pendencias = Pendencia::with(['responsavel', 'servico.unidade'])
            ->where('status', 'pendente')
            ->where('created_at', '<', $oneYearAgo);
        $pendencias = $notUser1($pendencias)->get()->map(function($p) {
            return [
                'tipo' => 'Pendência',
                'id' => $p->id,
                'descricao' => $p->pendencia,
                'data' => $p->created_at->toDateString(),
                'responsavel' => $p->responsavel->name ?? 'N/A',
                'unidade' => $p->servico->unidade->nomeFantasia ?? 'N/A'
            ];
        });

        // Taxas > 1 ano
        $taxas = Taxa::with(['servico.unidade', 'servico.responsavel'])
            ->where('situacao', 'aberto')
            ->where('vencimento', '<', $oneYearAgo->toDateString());
        $taxas = $taxas->whereHas('servico', function($q) use ($notUser1) {
            $notUser1($q);
        })->get()->map(function($t) {
            return [
                'tipo' => 'Taxa',
                'id' => $t->id,
                'descricao' => $t->nome,
                'data' => $t->vencimento,
                'responsavel' => $t->servico->responsavel->name ?? 'N/A',
                'unidade' => $t->servico->unidade->nomeFantasia ?? 'N/A'
            ];
        });

        // Serviços > 1 ano
        $servicos = Servico::with(['responsavel', 'unidade'])
            ->where('situacao', 'andamento')
            ->where('created_at', '<', $oneYearAgo);
        $servicos = $notUser1($servicos)->get()->map(function($s) {
            return [
                'tipo' => 'Serviço',
                'id' => $s->id,
                'descricao' => $s->nome,
                'data' => $s->created_at->toDateString(),
                'responsavel' => $s->responsavel->name ?? 'N/A',
                'unidade' => $s->unidade->nomeFantasia ?? 'N/A'
            ];
        });

        return response()->json($pendencias->concat($taxas)->concat($servicos));
    }

    public function expirations()
    {
        $timezone = 'America/Sao_Paulo';
        $now = Carbon::now($timezone);
        $today = $now->toDateString();
        
        $expirations = [
            'licenses' => [
                '60_days' => $this->getLicensesExpirationsInDays(60),
                '90_days' => $this->getLicensesExpirationsInDays(90),
                '120_days' => $this->getLicensesExpirationsInDays(120),
            ],
            'taxas' => Taxa::with(['servico.unidade', 'servico.empresa', 'servico.responsavel'])
                ->where('situacao', 'aberto')
                ->whereHas('servico', function($q) {
                    $q->where(function($sq) {
                        $sq->where('responsavel_id', '!=', 1)
                          ->orWhereNull('responsavel_id');
                    });
                })
                ->get()
                ->map(function ($t) use ($now) {
                    $vencimento = Carbon::parse($t->vencimento);
                    $overdueDays = $now->diffInDays($vencimento, false); // Fix: diff from now to vencimento
                    
                    return [
                        'id' => $t->id,
                        'nome' => $t->nome,
                        'valor' => $t->valor,
                        'vencimento' => $t->vencimento,
                        'unidade' => $t->servico->unidade->nomeFantasia ?? 'N/A',
                        'empresa' => $t->servico->empresa->nomeFantasia ?? 'N/A',
                        'responsavel' => $t->servico->responsavel->name ?? 'N/A',
                        'status' => $t->vencimento < date('Y-m-d') ? 'Atrasada' : 'No Prazo',
                        'inconsistencia' => $overdueDays < -180, // More than 180 days overdue
                    ];
                }),
        ];

        return response()->json($expirations);
    }

    public function collaboratorsPendencias()
    {
        $pendencias = Pendencia::with(['responsavel', 'servico.unidade', 'servico.empresa'])
            ->where('status', 'pendente')
            ->where(function ($q) {
                $q->where('responsavel_id', '!=', 1)
                  ->orWhereNull('responsavel_id');
            })
            ->get();

        $grouped = $pendencias->groupBy(function ($p) {
            return $p->responsavel->name ?? 'Sem Responsável';
        })->map(function ($userPendencias) {
            return $userPendencias->map(function ($p) {
                return [
                    'id' => $p->id,
                    'pendencia' => $p->pendencia,
                    'vencimento' => $p->vencimento ?? $p->dataLimite,
                    'unidade' => $p->servico->unidade->nomeFantasia ?? 'N/A',
                    'empresa' => $p->servico->empresa->nomeFantasia ?? 'N/A',
                    'os' => $p->servico->os ?? 'N/A',
                ];
            });
        });

        return response()->json($grouped);
    }

    private function getLicensesExpirationsInDays($days)
    {
        $timezone = 'America/Sao_Paulo';
        $now = Carbon::now($timezone);
        $today = $now->toDateString();
        $targetDate = $now->copy()->addDays($days)->toDateString();

        return Servico::with(['unidade', 'empresa', 'responsavel'])
            ->where('situacao', 'finalizado')
            ->where('tipo', 'licencaOperacao')
            ->where(function($q) {
                $q->where('responsavel_id', '!=', 1)
                  ->orWhereNull('responsavel_id');
            })
            ->whereBetween('licenca_validade', [$today, $targetDate])
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'nome' => $s->nome,
                    'unidade' => $s->unidade->nomeFantasia ?? 'N/A',
                    'empresa' => $s->empresa->nomeFantasia ?? 'N/A',
                    'vencimento' => $s->licenca_validade,
                    'responsavel' => $s->responsavel->name ?? 'N/A',
                ];
            });
    }
}
