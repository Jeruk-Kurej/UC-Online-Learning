// --- JSON requests to the app: the page's CSRF token and JSON headers on every call ---
// sendJson(url, method, body?, headers?) returns the fetch promise; readJsonOrThrow(response) parses the reply and
// throws the parsed body when the status is not 2xx.

export function sendJson(url, method, body = undefined, headers = {}) {
    return fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            Accept: 'application/json',
            ...headers,
        },
        body: body === undefined ? undefined : JSON.stringify(body),
    });
}

export function readJsonOrThrow(response) {
    if (!response.ok) {
        return response.json().then((err) => {
            throw err;
        });
    }
    return response.json();
}
