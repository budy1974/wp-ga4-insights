# GA4 Insights

Plugin WordPress multisite che aggiunge un assistente conversazionale per interrogare i dati GA4 filtrati per host del sottosito corrente.

## Funzionalità principali

- Attivazione network-wide con configurazione centralizzata nel Network Admin.
- Voce dedicata nella admin bar di wp-admin per amministratori, editor e author.
- Pannello flottante stile chat per domande in linguaggio naturale sui dati GA4 del sottodominio attivo.
- Proxy server-to-server verso un endpoint MCP configurabile (supporto Basic Auth e token personalizzato).
- Integrazione automatica del filtro GA4 `hostName` basato sul dominio corrente del sito.

## Struttura del plugin

```
wp-content/plugins/wp-ga4-insights/
├── wp-ga4-insights.php
├── includes/
│   ├── class-ga4-insights-plugin.php
│   ├── class-ga4-insights-settings.php
│   ├── class-ga4-insights-admin-bar.php
│   └── class-ga4-insights-chat.php
├── admin/
│   └── views/
│       └── settings-page.php
└── assets/
    ├── css/
    │   └── chat-panel.css
    └── js/
        └── chat-panel.js
```

## Configurazione

1. Attiva il plugin tramite la bacheca Network Admin.
2. Apri **Impostazioni → GA4 Insights** nel Network Admin e configura:
   - Endpoint MCP (default `http://127.0.0.1:8080/chat`).
   - Credenziali opzionali di Basic Auth.
   - API token opzionale inviato nell'header `X-API-Token`.
   - Nome del modello AI da utilizzare.
   - Timeout delle richieste.
3. Gli utenti con ruolo `administrator`, `editor` o `author` vedranno la voce "GA4 Insights" nella admin bar di wp-admin e potranno interagire con il pannello chat.

Il plugin effettua le richieste al servizio MCP passando sempre il dominio corrente tramite la dimensione GA4 `hostName`.
