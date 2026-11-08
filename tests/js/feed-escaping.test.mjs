import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

function contextFor(response) {
    const elements = {
        'posts-list': { innerHTML: '' },
        'profile-header': { innerHTML: '' },
        'profile-details': { innerHTML: '' },
    };
    const context = vm.createContext({
        window: {}, console,
        document: {
            getElementById: id => elements[id] ?? null,
            querySelector: selector => selector.startsWith('meta') ? {content: 'http://localhost/twitter-clone-demo/api/v1'} : null,
        },
        axios: { get: async () => response },
    });
    for (const file of ['Utils.js', 'Users.js', 'Posts.js']) {
        vm.runInContext(readFileSync(new URL(`../../resources/js/${file}`, import.meta.url), 'utf8'), context);
    }
    return {context, elements};
}

test('escapes text that could become executable markup', () => {
    const {context} = contextFor({});
    assert.equal(context.Utils.escapeHtml(`<img onerror="x">&'`), '&lt;img onerror=&quot;x&quot;&gt;&amp;&#39;');
});

test('renders hostile post content and author names as text', async () => {
    const payload = '<img src=x onerror="alert(1)">';
    const {context, elements} = contextFor({data:{data:[{
        id: 7, user_id: 2, content: payload, created_at: '2026-11-08T12:00:00Z',
        user: {name: payload, username: 'synthetic_user'},
    }]}});
    await context.Posts.loadPosts();
    assert.ok(elements['posts-list'].innerHTML.includes('&lt;img'));
    assert.ok(!elements['posts-list'].innerHTML.includes('<img'));
});

test('renders hostile profile fields as text', async () => {
    const payload = '<script>alert(1)</script>';
    const {context, elements} = contextFor({data:{data:{id:2,name:payload,username:payload,email:payload}}});
    await context.Users.loadProfile(2);
    assert.ok(elements['profile-details'].innerHTML.includes('&lt;script&gt;'));
    assert.ok(!elements['profile-details'].innerHTML.includes('<script>'));
});
