import { beforeEach, afterEach, expect, test, vi } from 'vitest';
import { csrfToken, refreshCsrfToken, updateCsrfToken } from '../csrfRefresh.js';

let metaContent;

const makeMeta = () => ({
    getAttribute: () => metaContent,
    setAttribute: (_name, value) => {
        metaContent = value;
    },
});

beforeEach(() => {
    metaContent = 'old-token';
    global.document = { querySelector: vi.fn(() => makeMeta()) };
    global.window = { route: vi.fn(() => '/csrf-token') };
});

afterEach(() => {
    vi.restoreAllMocks();
    delete global.document;
    delete global.window;
    delete global.fetch;
});

test('updateCsrfToken writes a new token into the meta tag and reactive ref', () => {
    updateCsrfToken('fresh-token');

    expect(metaContent).toBe('fresh-token');
    expect(csrfToken.value).toBe('fresh-token');
});

test('updateCsrfToken ignores empty tokens', () => {
    updateCsrfToken('');

    expect(metaContent).toBe('old-token');
});

test('refreshCsrfToken updates the meta tag from the endpoint', async () => {
    global.fetch = vi.fn(() =>
        Promise.resolve({
            ok: true,
            json: () => Promise.resolve({ token: 'server-token' }),
        }),
    );

    await refreshCsrfToken();

    expect(global.window.route).toHaveBeenCalledWith('csrf-token');
    expect(metaContent).toBe('server-token');
});

test('refreshCsrfToken leaves the token untouched on a failed response', async () => {
    global.fetch = vi.fn(() =>
        Promise.resolve({
            ok: false,
            json: () => Promise.resolve({}),
        }),
    );

    await refreshCsrfToken();

    expect(metaContent).toBe('old-token');
});

test('refreshCsrfToken swallows network errors', async () => {
    global.fetch = vi.fn(() => Promise.reject(new Error('offline')));

    await refreshCsrfToken();

    expect(metaContent).toBe('old-token');
});
