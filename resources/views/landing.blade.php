<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Celic - Gestão Estratégica de Licenciamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        :root {
            --primary: #004a99;
            --secondary: #1a1a1a;
            --accent: #28a745;
            --light: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--secondary);
            overflow-x: hidden;
        }

        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, #002d5c 100%);
            color: white;
            padding: 100px 0 150px;
            position: relative;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 0;
            width: 100%;
            height: 100px;
            background: white;
            clip-path: polygon(0 50%, 100% 0, 100% 100%, 0 100%);
        }

        .section-title {
            position: relative;
            margin-bottom: 50px;
            font-weight: 800;
            text-align: center;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--primary);
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            text-align: center;
            height: 100%;
            border-bottom: 4px solid var(--primary);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            display: block;
        }

        .stat-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-top: 10px;
        }

        .map-section {
            background: var(--light);
            padding: 80px 0;
        }

        .map-container,
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.08);
            height: 550px;
        }

        #map {
            height: 100%;
            border-radius: 15px;
        }

        .client-logo-box {
            background: white;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #eee;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .client-logo-box:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .client-logo-box img {
            max-width: 140px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .team-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            border-left: 5px solid var(--primary);
        }

        .team-rank {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin-right: 20px;
        }

        .team-name {
            font-weight: 700;
            flex-grow: 1;
        }

        .team-stats {
            text-align: right;
            font-size: 0.85rem;
            color: #666;
        }

        .bi-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 80px 0;
        }

        .bi-card {
            background: white;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .highlight-box {
            background: var(--primary);
            color: white;
            padding: 40px;
            border-radius: 25px;
            margin-top: 50px;
        }

        .logo-fallback {
            width: 60px;
            height: 60px;
            background: var(--light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--primary);
            border: 2px solid var(--primary);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>

    <header class="hero text-center">
        <div class="container">
            <img src="/img/logoCelicNew.png" alt="Celic Logo" class="mb-5"
                style="height: 80px; filter: brightness(0) invert(1);">
            <h1 class="display-3 fw-bold mb-4">A Plataforma Definitiva para <br>Gestão de Licenciamento</h1>
            <p class="lead mb-5 opacity-75 mx-auto" style="max-width: 700px;">
                Centralize processos, monitore prazos e garanta 100% de compliance
                com a tecnologia que as maiores empresas do Brasil já utilizam.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-light cta-button btn-lg">Acessar Sistema</a>
                <a href="#resultados" class="btn btn-outline-light cta-button btn-lg">Ver Impacto</a>
            </div>
        </div>
    </header>

    <section id="resultados" class="py-5">
        <div class="container py-5">
            <h2 class="section-title">Impacto Real no Ecossistema Digital</h2>
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="stat-card">
                        <span class="stat-value">{{ number_format($maturityRate, 1, ',', '.') }}%</span>
                        <span class="stat-label">Maturidade Digital (Docs)</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <span class="stat-value">{{ number_format($cityCount, 0, ',', '.') }}</span>
                        <span class="stat-label">Cidades Atendidas</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <span class="stat-value">{{ number_format($pendenciaCount / 1000, 1) }}k</span>
                        <span class="stat-label">Pendências Controladas</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <span class="stat-value">{{ round($avgDays) }} Dias</span>
                        <span class="stat-label">Tempo Médio Conclusão</span>
                    </div>
                </div>
            </div>

            <div class="highlight-box">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="fw-bold mb-3">Cobertura Nacional em 100% dos Estados</h3>
                        <p class="mb-0 opacity-75">O Sistema Celic é uma solução de escala continental. Já operamos em
                            <strong>{{ $stateCount }} Unidades Federativas</strong> (incluindo o DF), gerenciando um
                            total de <strong>{{ number_format($totalServicos, 0, ',', '.') }} processos</strong> ao
                            longo de nossa trajetória.
                        </p>
                    </div>
                    <div class="col-lg-4 text-center mt-4 mt-lg-0">
                        <div class="bg-white text-primary p-3 rounded-4 shadow">
                            <h2 class="fw-bold mb-0">{{ number_format($totalLicencas, 0, ',', '.') }}</h2>
                            <small class="text-uppercase fw-bold opacity-75">Licenças & Protocolos Ativos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="bi-duration" class="bi-section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="bi-card">
                        <h4 class="fw-bold mb-4"><i class="fas fa-chart-line text-primary me-2"></i> Inteligência de
                            Prazos</h4>
                        <p class="text-muted mb-4">Análise preditiva baseada em dados históricos por categoria de
                            serviço. Saiba exatamente quanto tempo cada etapa custa para sua operação.</p>
                        <canvas id="durationChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ps-lg-5">
                        <h2 class="fw-bold mb-4">BI: Tempo Médio por Especialidade</h2>
                        <p class="lead text-muted mb-5">O sistema Celic utiliza o histórico de milhares de processos
                            para prever o tempo de entrega com precisão cirúrgica.</p>

                        <div class="row g-4">
                            @foreach($avgTimePerType as $bi)
                                <div class="col-6">
                                    <div class="p-4 bg-white rounded-4 shadow-sm border-top border-primary border-4">
                                        <h3 class="fw-bold mb-0 text-primary">{{ round($bi->avg_days) }}</h3>
                                        <small class="text-uppercase fw-bold opacity-75">
                                            @if($bi->tipo == 'licencaOperacao') Licença Operação
                                            @elseif($bi->tipo == 'nRenovaveis') Não Renováveis
                                            @else Projetos & Laudos
                                            @endif
                                        </small>
                                        <div class="mt-2 text-muted" style="font-size: 0.7rem;">Base: {{ $bi->total }}
                                            processos</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="mapa-regional" class="map-section">
        <div class="container">
            <h2 class="section-title">Presença em Quase 10% dos Municípios do Brasil</h2>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="map-container">
                        <div id="map"></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-container">
                        <h5 class="text-center mb-4">Concentração por UF</h5>
                        <canvas id="regionalChart"></canvas>
                        <hr>
                        <div class="mt-4">
                            <h6>Ranking de Cidades:</h6>
                            <ul class="list-unstyled">
                                @foreach($topCidades as $c)
                                    <li class="d-flex justify-content-between mb-2">
                                        <span>{{ $c->cidade }}</span>
                                        <span class="badge bg-primary">{{ $c->total }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="equipe" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <h2 class="section-title text-start mb-4">Equipe de Alta Performance</h2>
                    <p class="text-muted mb-5">O Celic potencializa pessoas. Já registramos
                        <strong>{{ number_format($historicoCount / 1000, 0) }} mil interações</strong> de auditoria,
                        garantindo que nada escape ao controle dos gestores.
                    </p>

                    @foreach($teamRanking as $index => $collaborator)
                        <div class="team-card">
                            <div class="team-rank">#{{ $index + 1 }}</div>
                            <div class="team-name">{{ $collaborator->user->name }}</div>
                            <div class="team-stats">
                                <div class="fw-bold text-primary">{{ number_format($collaborator->total, 0, ',', '.') }}
                                </div>
                                <small>Logs</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-lg-7">
                    <div class="chart-container" style="height: 450px;">
                        <h5 class="text-center mb-4">Evolução Histórica de Processos</h5>
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="ranking-clientes" class="py-5 bg-light">
        <div class="container py-5">
            <h2 class="section-title">Quem Confia na Nossa Tecnologia</h2>
            <div class="row g-4 justify-content-center">
                @foreach($topClientes as $item)
                    <div class="col-md-6 col-lg-2">
                        <div class="client-logo-box">
                            @if(isset($logosClientes[$item->empresa_id]))
                                <img src="{{ $logosClientes[$item->empresa_id] }}" alt="{{ $item->empresa->nomeFantasia }}"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="logo-fallback" style="display:none;">
                                    {{ substr($item->empresa->nomeFantasia, 0, 1) }}
                                </div>
                            @else
                                <div class="logo-fallback">{{ substr($item->empresa->nomeFantasia, 0, 1) }}</div>
                            @endif
                            <span class="client-name fw-bold"
                                style="font-size: 0.8rem; margin-top: 5px;">{{ $item->empresa->nomeFantasia }}</span>
                            <div class="client-meta">{{ $item->total }} processos</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <footer class="text-center bg-secondary text-white py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">A tecnologia que move o licenciamento no Brasil.</h2>
            <p class="lead mb-5 opacity-75">Junte-se à elite nacional que já utiliza o Celic.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary cta-button btn-lg px-5">Acessar Plataforma</a>
            <div class="mt-5 pt-5 opacity-25 border-top border-secondary">
                <p>&copy; {{ date('Y') }} Sistema Celic. Projetado para Escala Nacional.</p>
            </div>
        </div>
    </footer>

    <script>
        // Map Initialization
        const map = L.map('map').setView([-14.235, -51.925], 4);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const units = {!! json_encode($unidadesMapa) !!};
        units.forEach(unit => {
            if (unit.latitude && unit.longitude) {
                L.marker([unit.latitude, unit.longitude])
                    .bindPopup(`<b>${unit.nomeFantasia}</b><br>${unit.cidade} - ${unit.uf}`)
                    .addTo(map);
            }
        });

        // Regional Pie Chart
        const regionalData = {!! json_encode($concentracaoRegional->take(8)) !!};
        new Chart(document.getElementById('regionalChart'), {
            type: 'pie',
            data: {
                labels: regionalData.map(d => d.uf),
                datasets: [{
                    data: regionalData.map(d => d.total),
                    backgroundColor: [
                        '#004a99', '#0066cc', '#0080ff', '#3399ff', '#66b2ff', '#99ccff', '#cce5ff', '#f0f5ff'
                    ]
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // Duration BI Chart
        const durationData = {!! json_encode($avgTimePerType) !!};
        new Chart(document.getElementById('durationChart'), {
            type: 'bar',
            data: {
                labels: durationData.map(d => d.tipo == 'licencaOperacao' ? 'Licença Op.' : (d.tipo == 'nRenovaveis' ? 'Não Renov.' : 'Proj/Laudos')),
                datasets: [{
                    label: 'Média de Dias',
                    data: durationData.map(d => Math.round(d.avg_days)),
                    backgroundColor: ['#004a99', '#28a745', '#ffc107'],
                    borderRadius: 10
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true } }
            }
        });

        // Evolution Chart
        const evolutionData = {!! json_encode($processosPorAno) !!};
        new Chart(document.getElementById('evolutionChart'), {
            type: 'line',
            data: {
                labels: evolutionData.map(d => d.year),
                datasets: [{
                    label: 'Volume de Processos',
                    data: evolutionData.map(d => d.total),
                    borderColor: '#004a99',
                    backgroundColor: 'rgba(0, 74, 153, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#004a99'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</body>

</html>