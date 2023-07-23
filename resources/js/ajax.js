const ajax = (url, method = 'GET', data = null, domElement = null) => {
    method = method.toLowerCase()

    let options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    }

    const csrfMethods = new Set(['post', 'put', 'patch', 'delete'])

    if (csrfMethods.has(method)) {
        if (method !== 'post') {
            options.method = 'post'
            data = {...data, _METHOD: method.toUpperCase()}
        }
        options.body = JSON.stringify({...data, ...getCsrfFields()})
    } else if (method === 'get') {
        url += '?' + (new URLSearchParams(data)).toString();
    }

    return fetch(url, options).then(response => {
        if (domElement) {
            clearValidationErrors(domElement);
        }

        if (! response.ok) {
            if (response.status === 422) {
                response.json().then(errors => {
                    handleValidationErrors(errors, domElement);
                })
            }
        }

        return response;
    });
}

const get = (url, data = null) => ajax(url, 'GET', data)
const post = (url, data = null, domElement) => ajax(url, 'POST', data, domElement)
const del = (url, data = null) => ajax(url, 'DELETE', data)

function handleValidationErrors(errors, domElement) {
    for (const name in errors) {
        const element = domElement.querySelector(`[name = "${name}"]`);
        element.classList.add('is-invalid');

        for (const error of errors[name]) {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.innerText = error;
            element.parentNode.appendChild(errorDiv);
        }
    }
}

function clearValidationErrors(domElement) {
    domElement.querySelectorAll('.is-invalid').forEach(element => {
        element.classList.remove('is-invalid');
    });

    domElement.querySelectorAll('.invalid-feedback').forEach(element => {
        element.remove();
    });
}

function getCsrfFields() {
    const csrfNameField = document.querySelector('#csrfName');
    const csrfValueField = document.querySelector('#csrfValue');
    const csrfNameKey = csrfNameField.getAttribute('name');
    const csrfName = csrfNameField.content;
    const csrfValueKey = csrfValueField.getAttribute('name');
    const csrfValue = csrfValueField.content;

    return {
        [csrfNameKey]: csrfName,
        [csrfValueKey]: csrfValue
    }
}

export {ajax, get, post, del }

