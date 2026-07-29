const CONFIG_SELECTOR = 'script[data-tool-analytics-config]';
const STORAGE_PREFIX = 'prazzu:tool-analytics:';

const validationErrorCode = (field) => {
    const validity = field.validity;

    if (!validity) return 'invalid';
    if (validity.valueMissing) return 'value_missing';
    if (validity.typeMismatch) return 'type_mismatch';
    if (validity.patternMismatch) return 'pattern_mismatch';
    if (validity.tooLong) return 'too_long';
    if (validity.tooShort) return 'too_short';
    if (validity.rangeUnderflow) return 'range_underflow';
    if (validity.rangeOverflow) return 'range_overflow';
    if (validity.stepMismatch) return 'step_mismatch';
    if (validity.badInput) return 'bad_input';
    if (validity.customError) return 'custom_error';

    return 'invalid';
};

const fieldHasValue = (field) => {
    if (field.disabled) return false;

    if (field instanceof HTMLInputElement && ['checkbox', 'radio'].includes(field.type)) {
        return field.checked;
    }

    if (field instanceof HTMLInputElement && field.type === 'file') {
        return (field.files?.length ?? 0) > 0;
    }

    return String(field.value ?? '').trim() !== '';
};

const completionSnapshot = (fields) => {
    const completed = fields.filter(({element}) => fieldHasValue(element) && element.checkValidity());
    const total = fields.length;

    return {
        filled_fields: completed.length,
        total_fields: total,
        completion_percentage: total === 0 ? 0 : Math.round((completed.length / total) * 100),
    };
};

const safeUuid = () => {
    if (globalThis.crypto?.randomUUID) return globalThis.crypto.randomUUID();

    return `fallback-${Date.now()}-${Math.random().toString(16).slice(2)}`;
};

const parseConfig = (node) => {
    try {
        const config = JSON.parse(node.textContent || '{}');

        if (!config.endpoint || !config.tool || !Array.isArray(config.forms)) return null;

        return config;
    } catch {
        return null;
    }
};

const resolveFields = (form, definition) => definition.fields.flatMap((field) => {
    const selector = field.selector || `[data-analytics-field="${CSS.escape(field.key)}"]`;
    const element = form.querySelector(selector);

    return element ? [{...field, element}] : [];
});

const createTransport = (config) => {
    const send = (event, metadata = {}, beacon = false) => {
        const payload = JSON.stringify({
            tool: config.tool,
            event,
            schema_version: config.schema_version || 1,
            metadata,
            _token: config.csrf,
        });

        if (beacon && navigator.sendBeacon) {
            const queued = navigator.sendBeacon(
                config.endpoint,
                new Blob([payload], {type: 'application/json'}),
            );

            if (queued) return;
        }

        fetch(config.endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            keepalive: true,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrf,
                'Accept': 'application/json',
            },
            body: payload,
        }).catch(() => {});
    };

    return {send};
};

const instrumentForm = ({config, definition, root, transport, journeyId}) => {
    const selector = definition.selector || `[data-analytics-form="${CSS.escape(definition.key)}"]`;
    const form = root.querySelector(selector);

    if (!(form instanceof HTMLFormElement)) return;

    const fields = resolveFields(form, definition);
    const completedFields = new Set();
    const validationErrors = new Set();
    const visitedSteps = new Set();
    const startedAt = Date.now();
    let started = false;
    let submitted = false;
    let resultViewed = false;

    const baseMetadata = () => ({journey_id: journeyId, form: definition.key});
    const snapshotMetadata = () => ({...baseMetadata(), ...completionSnapshot(fields)});

    const start = () => {
        if (started) return;
        started = true;
        transport.send('tool.started', snapshotMetadata());
    };

    const visitStep = (step) => {
        if (!step || visitedSteps.has(step)) return;
        visitedSteps.add(step);
        transport.send('tool.step.changed', {...snapshotMetadata(), step});
    };

    const completeField = (field) => {
        start();
        visitStep(field.step);

        if (!fieldHasValue(field.element) || !field.element.checkValidity() || completedFields.has(field.key)) return;

        completedFields.add(field.key);
        transport.send('tool.field.completed', {...snapshotMetadata(), field: field.key, step: field.step});
    };

    fields.forEach((field) => {
        field.element.addEventListener('focus', () => {
            start();
            visitStep(field.step);
        }, {passive: true});
        field.element.addEventListener('change', () => completeField(field));
        field.element.addEventListener('blur', () => completeField(field));
        field.element.addEventListener('invalid', () => {
            start();
            visitStep(field.step);
            const code = validationErrorCode(field.element);
            const dedupeKey = `${field.key}:${code}`;

            if (validationErrors.has(dedupeKey)) return;
            validationErrors.add(dedupeKey);
            transport.send('tool.validation.error', {
                ...snapshotMetadata(),
                field: field.key,
                step: field.step,
                validation_error: code,
            });
        });
    });

    form.addEventListener('submit', (event) => {
        const submitterAction = event.submitter?.dataset?.analyticsAction;
        if (submitterAction === 'export' || submitterAction === 'share') return;

        start();
        submitted = true;
        const pendingKey = `${STORAGE_PREFIX}${config.tool}:${definition.key}`;

        try {
            sessionStorage.setItem(pendingKey, JSON.stringify({journey_id: journeyId, submitted_at: Date.now()}));
        } catch {
            // Storage may be unavailable in private or restricted browser contexts.
        }

        transport.send('tool.calculation.started', {...snapshotMetadata(), action: 'calculate'});
    });

    const resultSelector = definition.result_selector || `[data-analytics-result="${CSS.escape(definition.key)}"]`;
    const result = root.querySelector(resultSelector);
    const pendingKey = `${STORAGE_PREFIX}${config.tool}:${definition.key}`;
    let pending = null;

    try {
        pending = sessionStorage.getItem(pendingKey);
    } catch {
        // Storage may be unavailable in private or restricted browser contexts.
    }

    if (pending) {
        try {
            const previous = JSON.parse(pending);
            const executionTime = Math.max(0, Math.min(3600000, Date.now() - Number(previous.submitted_at || Date.now())));
            const metadata = {
                journey_id: previous.journey_id || journeyId,
                form: definition.key,
                action: 'calculate',
                calculation_success: Boolean(result),
                execution_time_ms: executionTime,
            };

            if (result) {
                transport.send('tool.calculation.executed', metadata);
                transport.send('tool.result.viewed', metadata);
                resultViewed = true;
            } else if (config.has_validation_errors) {
                transport.send('tool.calculation.executed', metadata);
                transport.send('tool.validation.error', {
                    ...metadata,
                    validation_error: 'server_validation',
                });
            }

            if (result || config.has_validation_errors) {
                try { sessionStorage.removeItem(pendingKey); } catch {}
            }
        } catch {
            try { sessionStorage.removeItem(pendingKey); } catch {}
        }
    }

    root.querySelectorAll(`[data-analytics-action][data-analytics-form="${CSS.escape(definition.key)}"]`).forEach((element) => {
        element.addEventListener('click', () => {
            const action = element.dataset.analyticsAction;
            if (!definition.actions.includes(action)) return;

            const metadata = {...snapshotMetadata(), action};
            if (action === 'export') {
                // Server-backed downloads are recorded only after the response is
                // successfully delivered by CaptureAnalyticsContext. Tracking the
                // click as well would double count exports and count failed ones.
                if (element.dataset.analyticsClientOnly === 'true') {
                    transport.send('tool.result.exported', {...metadata, export_format: element.dataset.analyticsFormat || 'unknown'});
                }
            } else if (action === 'share') {
                transport.send('tool.shared', {...metadata, share_method: element.dataset.analyticsMethod || 'unknown'});
            }
        });
    });

    window.addEventListener('pagehide', () => {
        if (!started || submitted || resultViewed) return;

        const elapsed = Math.min(86400, Math.max(0, Math.round((Date.now() - startedAt) / 1000)));
        transport.send('tool.abandoned', {...snapshotMetadata(), abandoned_after_seconds: elapsed}, true);
    }, {once: true});
};

export const initializeToolJourneyAnalytics = (documentRoot = document) => {
    documentRoot.querySelectorAll(CONFIG_SELECTOR).forEach((node) => {
        if (node.dataset.initialized === '1') return;

        const config = parseConfig(node);
        if (!config) return;

        const root = documentRoot.querySelector(`[data-tool="${CSS.escape(config.tool)}"]`);
        if (!root) return;

        node.dataset.initialized = '1';
        const transport = createTransport(config);
        const journeyId = safeUuid();

        config.forms.forEach((definition) => instrumentForm({
            config,
            definition,
            root,
            transport,
            journeyId,
        }));
    });
};

export {completionSnapshot, validationErrorCode};
