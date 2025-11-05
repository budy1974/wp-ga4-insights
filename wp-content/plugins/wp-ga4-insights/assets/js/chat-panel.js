(function () {
    var config = window.GA4InsightsConfig || {};
    var panel = document.getElementById('ga4-insights-panel');
    if (!panel) {
        return;
    }

    var toggleNode = document.querySelector('#wp-admin-bar-ga4-insights-toggle');
    var closeBtn = panel.querySelector('.ga4-insights-panel__close');
    var form = panel.querySelector('.ga4-insights-panel__form');
    var textarea = panel.querySelector('#ga4-insights-question');
    var messagesContainer = panel.querySelector('.ga4-insights-panel__messages');
    var statusEl = null;

    var getString = function (key, fallback) {
        if (config.i18n && config.i18n[key]) {
            return config.i18n[key];
        }
        return fallback;
    };

    var createStatusElement = function () {
        if (!statusEl) {
            statusEl = document.createElement('div');
            statusEl.className = 'ga4-insights-panel__status';
            form.appendChild(statusEl);
        }
        return statusEl;
    };

    var setStatus = function (text, isError) {
        if (isError === void 0) {
            isError = false;
        }
        var el = createStatusElement();
        el.textContent = text || '';
        if (text) {
            el.style.display = 'flex';
            if (isError) {
                el.classList.add('is-error');
            } else {
                el.classList.remove('is-error');
            }
        } else {
            el.style.display = 'none';
        }
    };

    var appendMessage = function (message, type) {
        var bubble = document.createElement('div');
        bubble.className = 'ga4-insights-panel__message ga4-insights-panel__message--' + type;
        bubble.textContent = message;
        messagesContainer.appendChild(bubble);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    var togglePanel = function (open) {
        var isOpen = panel.classList.contains('is-open');
        var shouldOpen = typeof open === 'boolean' ? open : !isOpen;
        panel.classList.toggle('is-open', shouldOpen);
        panel.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
        if (shouldOpen) {
            textarea.focus();
        }
    };

    var handleToggle = function (event) {
        event.preventDefault();
        togglePanel();
    };

    var handleClose = function (event) {
        event.preventDefault();
        togglePanel(false);
    };

    var handleSubmit = function (event) {
        event.preventDefault();
        var question = textarea.value.trim();
        if (!question) {
            setStatus(getString('emptyMessage', 'Inserisci una domanda prima di inviare.'), true);
            return;
        }

        appendMessage(question, 'user');
        textarea.value = '';
        setStatus(getString('sending', 'Invio in corso…'), false);
        var submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        fetch(config.restUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': config.nonce
            },
            body: JSON.stringify({ question: question })
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error(response.statusText || 'Request failed');
                }
                return response.json();
            })
            .then(function (data) {
                var payload = data && data.data ? data.data : {};
                var reply = payload.reply || payload.answer || payload.content;
                if (!reply && payload.messages && payload.messages.length) {
                    reply = payload.messages.map(function (message) {
                        return message.content || '';
                    }).join('\n');
                }
                if (!reply) {
                    reply = JSON.stringify(payload);
                }
                appendMessage(reply, 'assistant');
                setStatus('', false);
            })
            .catch(function (error) {
                console.error('GA4 Insights error', error);
                setStatus(getString('error', 'Si è verificato un errore durante il recupero dei dati.'), true);
            })
            .finally(function () {
                submitButton.disabled = false;
            });
    };

    if (toggleNode) {
        toggleNode.addEventListener('click', handleToggle);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', handleClose);
    }

    form.addEventListener('submit', handleSubmit);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && panel.classList.contains('is-open')) {
            togglePanel(false);
        }
    });
})();
