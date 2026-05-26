function ensureSiswaFormStyles() {
    if (document.getElementById('siswa-ajax-form-styles')) {
        return;
    }

    var style = document.createElement('style');
    style.id = 'siswa-ajax-form-styles';
    style.textContent = '.siswa-form-alert{margin-bottom:12px;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;border-radius:10px;padding:12px 14px;font-size:13px;}.siswa-field-error{display:none;margin-top:4px;font-size:12px;line-height:1.35;color:#dc3545;}.siswa-form-control.is-invalid{border-color:#dc3545 !important;box-shadow:0 0 0 .2rem rgba(220,53,69,.12) !important;}.siswa-spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:999px;animation:siswa-spin .8s linear infinite;}@keyframes siswa-spin{to{transform:rotate(360deg);}}';

    document.head.appendChild(style);
}

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') || '' : '';
}

function escapeAttributeValue(value) {
    return String(value).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
}

function getFieldElement(form, fieldName) {
    return form.querySelector('[name="' + escapeAttributeValue(fieldName) + '"]');
}

function getErrorElement(form, fieldName, fieldElement) {
    var wrapper = fieldElement.closest('div') || fieldElement.parentElement || fieldElement;
    var selector = '[data-error-for="' + escapeAttributeValue(fieldName) + '"]';
    var errorElement = wrapper.querySelector(selector);

    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'siswa-field-error invalid-feedback';
        errorElement.dataset.errorFor = fieldName;
        wrapper.appendChild(errorElement);
    }

    return errorElement;
}

function resetFormState(form) {
    form.querySelectorAll('input, select, textarea').forEach(function(field) {
        field.classList.remove('is-invalid', 'siswa-form-control');
        field.removeAttribute('aria-invalid');
    });

    form.querySelectorAll('[data-error-for]').forEach(function(errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    });

    var alertElement = form.querySelector('[data-siswa-form-alert]');
    if (alertElement) {
        alertElement.remove();
    }
}

function showFormError(form, message) {
    var alertElement = form.querySelector('[data-siswa-form-alert]');

    if (!alertElement) {
        alertElement = document.createElement('div');
        alertElement.dataset.siswaFormAlert = 'true';
        alertElement.className = 'siswa-form-alert';
        form.prepend(alertElement);
    }

    alertElement.textContent = message;
}

function showFieldErrors(form, errors) {
    Object.keys(errors || {}).forEach(function(fieldName) {
        var fieldElement = getFieldElement(form, fieldName);

        if (!fieldElement) {
            return;
        }

        var errorElement = getErrorElement(form, fieldName, fieldElement);
        var message = Array.isArray(errors[fieldName]) ? errors[fieldName][0] : errors[fieldName];

        fieldElement.classList.add('is-invalid', 'siswa-form-control');
        fieldElement.setAttribute('aria-invalid', 'true');

        errorElement.textContent = message;
        errorElement.style.display = 'block';
    });
}

function setButtonLoading(button, isLoading) {
    if (!button) {
        return;
    }

    if (isLoading) {
        if (!button.dataset.originalHtml) {
            button.dataset.originalHtml = button.innerHTML;
        }

        button.disabled = true;
        button.innerHTML = '<span class="siswa-spinner" aria-hidden="true"></span><span style="margin-left:8px;">Menyimpan...</span>';
        return;
    }

    button.disabled = false;

    if (button.dataset.originalHtml) {
        button.innerHTML = button.dataset.originalHtml;
        delete button.dataset.originalHtml;
    }
}

function buildRequestOptions(form, method) {
    var normalizedMethod = String(method || form.getAttribute('method') || 'POST').toUpperCase();
    var formData = new FormData(form);
    var fetchMethod = normalizedMethod;

    if (normalizedMethod !== 'POST' && normalizedMethod !== 'GET') {
        formData.set('_method', normalizedMethod);
        fetchMethod = 'POST';
    }

    return {
        body: formData,
        method: fetchMethod,
    };
}

async function submitSiswaForm(formId, url, method) {
    ensureSiswaFormStyles();

    var form = document.getElementById(formId);

    if (!form || form.dataset.siswaAjaxBound === 'true') {
        return;
    }

    form.dataset.siswaAjaxBound = 'true';

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        resetFormState(form);

        var submitButton = form.querySelector('button[type="submit"]');
        setButtonLoading(submitButton, true);

        var requestUrl = url || form.getAttribute('action') || window.location.href;
        var requestOptions = buildRequestOptions(form, method);

        try {
            var response = await fetch(requestUrl, {
                method: requestOptions.method,
                body: requestOptions.body,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            var contentType = response.headers.get('content-type') || '';

            if (response.status === 422) {
                var validationPayload = contentType.indexOf('application/json') !== -1
                    ? await response.json()
                    : null;

                if (validationPayload && validationPayload.errors) {
                    showFieldErrors(form, validationPayload.errors);
                } else {
                    showFormError(form, 'Validasi gagal. Periksa kembali isian form.');
                }

                return;
            }

            if (response.ok) {
                var payload = contentType.indexOf('application/json') !== -1
                    ? await response.json()
                    : null;

                if (payload && payload.redirect_url) {
                    window.location.href = payload.redirect_url;
                    return;
                }

                window.location.reload();
                return;
            }

            var errorMessage = 'Terjadi kesalahan saat menyimpan data.';

            if (contentType.indexOf('application/json') !== -1) {
                var errorPayload = await response.json().catch(function() {
                    return null;
                });

                errorMessage = (errorPayload && (errorPayload.message || errorPayload.error)) || errorMessage;
            } else {
                var textResponse = await response.text();
                if (textResponse.trim() !== '') {
                    errorMessage = textResponse;
                }
            }

            showFormError(form, errorMessage);
        } catch (error) {
            showFormError(form, error && error.message ? error.message : 'Gagal mengirim data.');
        } finally {
            setButtonLoading(submitButton, false);
        }
    });
}

window.submitSiswaForm = submitSiswaForm;