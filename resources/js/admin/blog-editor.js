import tinymce from 'tinymce/tinymce';

import 'tinymce/icons/default';
import 'tinymce/models/dom';
import 'tinymce/themes/silver';

import 'tinymce/plugins/autolink';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/image';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/media';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/table';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/wordcount';


const form = document.querySelector('[data-blog-post-form]');
const contentField = document.querySelector('[data-blog-content-editor]');

if (form && contentField) {
    const fields = {
        title: form.querySelector('#title'),
        slug: form.querySelector('#slug'),
        excerpt: form.querySelector('#excerpt'),
        content: contentField,
        metaTitle: form.querySelector('#meta_title'),
        metaDescription: form.querySelector('#meta_description'),
        status: form.querySelector('#status'),
        publishedAt: form.querySelector('#published_at'),
    };

    const draftKey = `prazzu:admin-blog:draft:${form.dataset.blogDraftKey || 'new'}`;
    const baseUrl = form.dataset.blogBaseUrl || '/blog';
    const initialSlug = fields.slug?.value.trim() || '';
    let slugWasEdited = initialSlug !== '';
    let autosaveTimer = null;
    let submitted = false;

    const slugify = (value) => value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');

    const stripHtml = (value) => {
        const documentFragment = new DOMParser().parseFromString(value || '', 'text/html');
        return documentFragment.body.textContent || '';
    };

    const getContent = () => tinymce.get(contentField.id)?.getContent() ?? contentField.value;

    const updateBadge = (element, length, recommended, minimum) => {
        if (!element) return;

        element.classList.remove('text-bg-success', 'text-bg-warning', 'text-bg-danger');

        if (length === 0) {
            element.textContent = 'Não informado';
            element.classList.add('text-bg-warning');
            return;
        }

        if (length >= minimum && length <= recommended) {
            element.textContent = 'Bom tamanho';
            element.classList.add('text-bg-success');
            return;
        }

        element.textContent = length > recommended ? 'Muito longo' : 'Muito curto';
        element.classList.add(length > recommended ? 'text-bg-danger' : 'text-bg-warning');
    };

    const refreshIndicators = () => {
        const title = fields.title?.value.trim() || '';
        const slug = fields.slug?.value.trim() || slugify(title) || 'slug-da-postagem';
        const excerpt = fields.excerpt?.value.trim() || '';
        const metaTitle = fields.metaTitle?.value.trim() || '';
        const metaDescription = fields.metaDescription?.value.trim() || '';
        const contentText = stripHtml(getContent()).trim();
        const words = contentText === '' ? 0 : contentText.split(/\s+/).filter(Boolean).length;
        const readingTime = words === 0 ? 0 : Math.max(1, Math.ceil(words / 200));

        form.querySelector('[data-blog-excerpt-count]')?.replaceChildren(String(excerpt.length));
        form.querySelector('[data-blog-word-count]')?.replaceChildren(String(words));
        form.querySelector('[data-blog-reading-time]')?.replaceChildren(String(readingTime));
        form.querySelector('#meta-title-count')?.replaceChildren(String(metaTitle.length));
        form.querySelector('#meta-description-count')?.replaceChildren(String(metaDescription.length));
        form.querySelector('#seo-preview-title')?.replaceChildren(metaTitle || title || 'Título da postagem');
        form.querySelector('#seo-preview-url')?.replaceChildren(`${baseUrl}/${slug}`);
        form.querySelector('#seo-preview-description')?.replaceChildren(metaDescription || excerpt || 'A descrição da postagem aparecerá aqui.');

        updateBadge(form.querySelector('[data-blog-meta-title-status]'), metaTitle.length, 60, 30);
        updateBadge(form.querySelector('[data-blog-meta-description-status]'), metaDescription.length, 160, 70);
    };

    const refreshPublication = () => {
        const help = form.querySelector('[data-blog-publication-help]');
        if (!help || !fields.status || !fields.publishedAt) return;

        const scheduled = fields.status.value === 'scheduled';
        fields.publishedAt.required = scheduled;

        if (fields.status.value === 'draft') {
            help.textContent = 'Rascunhos não possuem data de publicação.';
        } else if (scheduled) {
            help.textContent = 'Obrigatória para postagens agendadas.';
        } else {
            help.textContent = 'Se ficar vazia, a publicação usará a data e hora do salvamento.';
        }
    };

    const serializableFields = () => {
        const data = {};

        form.querySelectorAll('input[name], textarea[name], select[name]').forEach((field) => {
            if (field.type === 'file' || field.name === '_token' || field.name === '_method') return;

            if (field.type === 'checkbox') {
                if (!Array.isArray(data[field.name])) data[field.name] = [];
                if (field.checked) data[field.name].push(field.value);
                return;
            }

            data[field.name] = field.value;
        });

        data.content = getContent();

        return data;
    };

    const saveLocalDraft = () => {
        if (submitted) return;

        const payload = {
            savedAt: new Date().toISOString(),
            fields: serializableFields(),
        };

        localStorage.setItem(draftKey, JSON.stringify(payload));
        const status = form.querySelector('[data-blog-autosave-status]');
        if (status) {
            status.textContent = `Rascunho local salvo às ${new Date(payload.savedAt).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}.`;
        }
    };

    const scheduleAutosave = () => {
        window.clearTimeout(autosaveTimer);
        autosaveTimer = window.setTimeout(saveLocalDraft, 800);
    };

    const restoreDraft = (payload) => {
        Object.entries(payload.fields || {}).forEach(([name, value]) => {
            const matchingFields = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
            if (!matchingFields.length) return;

            matchingFields.forEach((field) => {
                if (field.type === 'checkbox') {
                    field.checked = Array.isArray(value) && value.includes(field.value);
                } else if (field.type !== 'file' && !(field.type === 'hidden' && matchingFields.length > 1)) {
                    field.value = value ?? '';
                }
            });
        });

        if (typeof payload.fields?.content === 'string') {
            contentField.value = payload.fields.content;
            tinymce.get(contentField.id)?.setContent(payload.fields.content);
        }

        slugWasEdited = (fields.slug?.value.trim() || '') !== '';
        refreshIndicators();
        refreshPublication();
    };

    const configureDraftRecovery = () => {
        const recovery = form.querySelector('[data-blog-draft-recovery]');
        const stored = localStorage.getItem(draftKey);
        if (!recovery || !stored) return;

        try {
            const payload = JSON.parse(stored);
            const savedAt = new Date(payload.savedAt);
            recovery.classList.remove('d-none');
            recovery.classList.add('d-flex');
            recovery.querySelector('[data-blog-draft-time]').textContent = `Salvo em ${savedAt.toLocaleString('pt-BR')}. Arquivos de imagem precisam ser selecionados novamente.`;
            recovery.querySelector('[data-blog-draft-restore]')?.addEventListener('click', () => {
                restoreDraft(payload);
                recovery.classList.add('d-none');
                recovery.classList.remove('d-flex');
            });
            recovery.querySelector('[data-blog-draft-discard]')?.addEventListener('click', () => {
                localStorage.removeItem(draftKey);
                recovery.classList.add('d-none');
                recovery.classList.remove('d-flex');
            });
        } catch {
            localStorage.removeItem(draftKey);
        }
    };

    const configureImagePreview = (fieldId) => {
        const input = form.querySelector(`#${fieldId}`);
        const wrapper = form.querySelector(`[data-blog-image-preview-wrapper="${fieldId}"]`);
        const image = form.querySelector(`[data-blog-image-preview="${fieldId}"]`);
        if (!input || !wrapper || !image) return;

        input.addEventListener('change', () => {
            const [file] = input.files || [];
            if (!file) return;

            image.src = URL.createObjectURL(file);
            wrapper.classList.remove('d-none');
        });
    };

    const isDarkTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark';

    tinymce.init({
        target: contentField,
        license_key: 'gpl',
        skin: false,
        content_css: false,
        height: 640,
        min_height: 420,
        menubar: 'edit view insert format tools table help',
        plugins: [
            'autolink', 'charmap', 'code', 'fullscreen', 'image', 'link', 'lists',
            'media', 'preview', 'searchreplace', 'table', 'visualblocks', 'wordcount',
        ],
        toolbar: [
            'undo redo | blocks | bold italic underline | forecolor backcolor',
            'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
            'link image media table | blockquote hr | removeformat | code preview fullscreen',
        ].join(' '),
        toolbar_mode: 'sliding',
        statusbar: true,
        branding: false,
        promotion: false,
        resize: true,
        browser_spellcheck: true,
        contextmenu: false,
        convert_urls: false,
        relative_urls: false,
        remove_script_host: false,
        image_advtab: true,
        image_caption: true,
        object_resizing: 'img,table',
        table_default_attributes: { class: 'table table-bordered' },
        table_default_styles: { width: '100%' },
        content_style: `
            body { background: ${isDarkTheme ? '#212529' : '#ffffff'}; color: ${isDarkTheme ? '#f8f9fa' : '#212529'}; font-family: system-ui, -apple-system, "Segoe UI", sans-serif; font-size: 16px; line-height: 1.65; margin: 1.5rem; }
            a { color: #0d6efd; }
            img { height: auto; max-width: 100%; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #adb5bd; padding: .5rem; vertical-align: top; }
            blockquote { border-left: .25rem solid #adb5bd; margin-left: 0; padding-left: 1rem; }
        `,
        setup(editor) {
            editor.on('init', () => {
                refreshIndicators();
                configureDraftRecovery();
            });
            editor.on('change input undo redo', () => {
                editor.save();
                refreshIndicators();
                scheduleAutosave();
            });
        },
    });

    fields.title?.addEventListener('input', () => {
        if (!slugWasEdited && fields.slug) fields.slug.value = slugify(fields.title.value);
        refreshIndicators();
        scheduleAutosave();
    });

    fields.slug?.addEventListener('input', () => {
        slugWasEdited = true;
        fields.slug.value = slugify(fields.slug.value);
        refreshIndicators();
        scheduleAutosave();
    });

    [fields.excerpt, fields.metaTitle, fields.metaDescription].forEach((field) => {
        field?.addEventListener('input', () => {
            refreshIndicators();
            scheduleAutosave();
        });
    });

    fields.status?.addEventListener('change', () => {
        refreshPublication();
        scheduleAutosave();
    });

    form.querySelectorAll('input, select, textarea').forEach((field) => {
        if (!['title', 'slug', 'excerpt', 'meta_title', 'meta_description', 'content', 'status'].includes(field.id)) {
            field.addEventListener('change', scheduleAutosave);
            field.addEventListener('input', scheduleAutosave);
        }
    });

    configureImagePreview('cover_image');
    configureImagePreview('social_image');
    refreshIndicators();
    refreshPublication();

    form.addEventListener('submit', () => {
        submitted = true;
        tinymce.triggerSave();
        localStorage.removeItem(draftKey);
    });
}
