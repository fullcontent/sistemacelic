<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline - {{$servico->os}}</title>
    <!-- React & Babel CDNs (Direct UMD links for stability) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.production.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.production.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/7.23.5/babel.min.js"></script>
    <!-- Lucide Core (Much more stable via CDN than the React wrapper) -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --color-castro: #3c8dbc;
            --color-cliente: #00a65a;
            --color-orgao: #f39c12;
            --bg-glass: rgba(255, 255, 255, 0.8);
        }
        body {
            background-color: #f4f6f9;
            margin: 0;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .header {
            background: #fff;
            padding: 15px 30px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header small {
            color: #777;
            font-weight: 400;
        }
        .timeline-container {
            position: relative;
            padding: 40px 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        .timeline-line {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #ddd;
            transform: translateX(-50%);
            z-index: 1;
            border-radius: 4px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 80px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2;
        }
        .timeline-dot {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
            z-index: 3;
        }
        .timeline-dot:hover {
            transform: scale(1.1);
        }
        .timeline-content {
            position: absolute;
            width: 40%;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        .timeline-item:nth-child(odd) .timeline-content {
            right: calc(50% + 40px);
            text-align: right;
        }
        .timeline-item:nth-child(even) .timeline-content {
            left: calc(50% + 40px);
            text-align: left;
        }
        .timeline-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }
        .timeline-meta {
            font-size: 12px;
            color: #777;
        }
        .badge-stakeholder {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            color: #fff;
            margin-bottom: 5px;
        }
        .attention-indicator {
            color: #dd4b39;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            margin-top: 5px;
            justify-content: inherit;
        }
        .skeleton {
            background: #eee;
            background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
            background-size: 200% 100%;
            animation: 1.5s shine linear infinite;
        }
        @keyframes shine {
            to { background-position-x: -200%; }
        }
        .side-panel {
            position: fixed;
            right: -400px;
            top: 0;
            bottom: 0;
            width: 400px;
            background: #fff;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            z-index: 9999;
            transition: right 0.3s ease;
            padding: 30px;
            overflow-y: auto;
        }
        .side-panel.open {
            right: 0;
        }
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            z-index: 9998;
            display: none;
        }
        .overlay.open {
            display: block;
        }
        .btn-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Timeline <small>{{$servico->os}} - {{$servico->nome}}</small></h1>
        <button onclick="window.close()" class="btn btn-default">Fechar</button>
    </div>

    <div id="timeline-root"></div>

    <script>
        window.SERVICE_ID = "{{$servico->id}}";
    </script>

    @verbatim
    <script type="text/babel">
        const { useState, useEffect } = React;

        // Custom Icon component that uses Lucide Core (very stable)
        const Icon = ({ name, color, size = 16, className = "" }) => {
            useEffect(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }, [name]); // Re-run if icon name changes

            return (
                <i 
                    data-lucide={name} 
                    className={className}
                    style={ { 
                        color: color, 
                        width: size, 
                        height: size, 
                        display: 'inline-block',
                        verticalAlign: 'middle'
                    } }
                ></i>
            );
        };

        const Timeline = () => {
            const [data, setData] = useState([]);
            const [loading, setLoading] = useState(true);
            const [selectedItem, setSelectedItem] = useState(null);
            const serviceId = window.SERVICE_ID;

            useEffect(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }, [data, loading]);

            useEffect(() => {
                // Prefixed with /admin because the route is inside the admin group
                fetch(`/admin/api/servico/${serviceId}/timeline`)
                    .then(res => res.json())
                    .then(json => {
                        setData(json);
                        setLoading(false);
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        setLoading(false);
                    });
            }, []);

            const getIconName = (frent) => {
                const mapping = {
                    'Orgao': 'gavel',
                    'Cliente': 'building',
                    'Castro': 'user'
                };
                return mapping[frent] || 'info';
            };

            const getColorCode = (cor) => {
                const colors = {
                    blue: "#3c8dbc",
                    green: "#00a65a",
                    orange: "#f39c12"
                };
                return colors[cor] || "#ddd";
            };

            if (loading) {
                return (
                    <div className="timeline-container">
                        {[1, 2, 3].map(i => (
                            <div key={i} className="timeline-item">
                                <div className="timeline-dot skeleton" style={ { border: 'none' } }></div>
                                <div className="timeline-content skeleton" style={ { height: '80px', border: 'none' } }></div>
                            </div>
                        ))}
                    </div>
                );
            }

            return (
                <div className="timeline-container">
                    <div className="timeline-line"></div>
                    
                    {data.map((item, index) => {
                        const colorCode = getColorCode(item.cor);
                        return (
                            <div key={index} className="timeline-item">
                                <div 
                                    className="timeline-dot" 
                                    style={ { borderColor: colorCode } }
                                    onClick={() => setSelectedItem(item)}
                                >
                                    <Icon name={getIconName(item.frente)} color={colorCode} size={24} />
                                </div>
                                
                                <div className="timeline-content">
                                    <span className="badge-stakeholder" style={ { backgroundColor: colorCode } }>
                                        {item.stakeholder}
                                    </span>
                                    <div className="timeline-title">{item.etapa}</div>
                                    <div className="timeline-meta" style={ { display: 'flex', alignItems: 'center', gap: '4px', justifyContent: index % 2 === 0 ? 'flex-end' : 'flex-start' } }>
                                        <Icon name="clock" color="#777" size={12} />
                                        Duração: {item.dias} {item.dias === 1 ? 'dia' : 'dias'}
                                    </div>
                                    {item.dias > 7 && (
                                        <div className="attention-indicator">
                                            <Icon name="alert-triangle" color="#dd4b39" size={14} /> Gargalo Identificado
                                        </div>
                                    )}
                                    {item.status === "Concluído" && (
                                        <div style={ { color: '#00a65a', fontSize: '11px', marginTop: '5px', display: 'flex', alignItems: 'center', gap: '4px', justifyContent: index % 2 === 0 ? 'flex-end' : 'flex-start' } }>
                                            <Icon name="check-circle" color="#00a65a" size={12} /> {item.data_conclusao}
                                        </div>
                                    )}
                                </div>
                            </div>
                        );
                    })}

                    {/* Side Panel */}
                    <div className={`overlay ${selectedItem ? 'open' : ''}`} onClick={() => setSelectedItem(null)}></div>
                    <div className={`side-panel ${selectedItem ? 'open' : ''}`}>
                        <div style={ { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' } }>
                            <h3 style={ { margin: 0 } }>Detalhes da Etapa</h3>
                            <button onClick={() => setSelectedItem(null)} className="btn-close">
                                <Icon name="x" color="#333" size={24} />
                            </button>
                        </div>
                        
                        {selectedItem && (
                            <div>
                                <div style={ { marginBottom: '15px' } }>
                                    <label style={ { display: 'block', fontSize: '10px', fontWeight: 'bold', color: '#999', textTransform: 'uppercase' } }>Etapa</label>
                                    <div style={ { fontSize: '18px', fontWeight: 'bold' } }>{selectedItem.etapa}</div>
                                </div>
                                
                                <div style={ { marginBottom: '15px' } }>
                                    <label style={ { display: 'block', fontSize: '10px', fontWeight: 'bold', color: '#999', textTransform: 'uppercase' } }>Responsável</label>
                                    <span className="badge-stakeholder" style={ { backgroundColor: getColorCode(selectedItem.cor), fontSize: '12px', padding: '4px 12px' } }>
                                        {selectedItem.stakeholder}
                                    </span>
                                </div>

                                <div style={ { marginBottom: '15px' } }>
                                    <label style={ { display: 'block', fontSize: '10px', fontWeight: 'bold', color: '#999', textTransform: 'uppercase' } }>Tempo Gasto</label>
                                    <div style={ { fontSize: '16px' } }>{selectedItem.dias} dias</div>
                                </div>

                                <div style={ { marginBottom: '20px' } }>
                                    <label style={ { display: 'block', fontSize: '10px', fontWeight: 'bold', color: '#999', textTransform: 'uppercase' } }>Observações</label>
                                    <div 
                                        style={ { background: '#f9f9f9', padding: '15px', borderRadius: '4px', border: '1px solid #eee', lineHeight: '1.6' } }
                                        dangerouslySetInnerHTML={ { __html: selectedItem.observacoes || 'Nenhuma observação registrada.' } }
                                    ></div>
                                </div>

                                {selectedItem.observacoes && selectedItem.observacoes.includes('http') && (
                                    <div>
                                        <label style={ { display: 'block', fontSize: '10px', fontWeight: 'bold', color: '#999', textTransform: 'uppercase' } }>Links Úteis</label>
                                        <div style={ { marginTop: '10px' } }>
                                            <a href="#" style={ { textDecoration: 'none', color: '#3c8dbc', fontSize: '13px', display: 'flex', alignItems: 'center', gap: '5px' } }>
                                                <Icon name="arrow-right" color="#3c8dbc" size={14} /> Acessar Documento Linkado
                                            </a>
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            );
        };

        const root = ReactDOM.createRoot(document.getElementById('timeline-root'));
        root.render(<Timeline />);
    </script>
    @endverbatim
</body>
</html>