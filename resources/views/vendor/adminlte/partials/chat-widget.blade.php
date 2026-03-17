<!-- n8n Chat Styles -->
<link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />

<style>
    :root {
        --n8n-chat-primary-color: #3c8dbc;
        --n8n-chat-button-background: #3c8dbc;
    }

    /* Custom styling to ensure the button matches the requested aesthetic */
    .n8n-chat-widget {
        overflow: hidden;
    }
</style>

<!-- n8n Chat Initialization -->
<script type="module">
    import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

    createChat({
        webhookUrl: 'https://n8n.srv1477025.hstgr.cloud/webhook/9691229a-448d-490f-ad54-94ff0d5a6b59/chat',
        mode: 'window',
        showWelcomeScreen: true,
        title: 'CELIC IA', // Nome mais focado no seu conceito
        subtitle: 'Inteligência de Licenciamentos',

        initialMessages: [
            'Informe o **ID do Serviço** ou o **Tipo de Serviço e Localidade** para auditoria.'
        ],
        i18n: {
            en: {
                title: 'CELIC (IA)',
                subtitle: 'A Inteligência de Licenciamentos',
                getStarted: 'Iniciar Auditoria',
                inputPlaceholder: 'ID ou Serviço/Localidade...',
            }
        },
        style: {
            branding: {
                fontSize: '16px',
            },
            button: {
                backgroundColor: '#1A1A1B', // Um tom mais sóbrio e profissional
                color: '#FFFFFF',
            }
        }
    });
</script>