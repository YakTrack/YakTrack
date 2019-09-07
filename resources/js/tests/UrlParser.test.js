import urlParser from '../UrlParser.js';

test('mergeParameters method merges parameter objects', () => {
    expect(
        urlParser.mergeParameters({
            foo: 'bar',
            baz: 'buzz',
        }, {
            baz: 'bat',
        })
    ).toMatchObject({
        foo: 'bar',
        baz: 'bat',
    });
});

test('urlOnly method', () => {
    expect(urlParser.urlOnly('https://some-domain.com/app?foo=bar'))
        .toBe('https://some-domain.com/app');
});

test('queryOnly method', () => {
    expect(urlParser.queryOnly('https://some-domain.com/app?foo=bar'))
        .toBe('foo=bar');
});

test('queryToObject method', () => {
    expect(urlParser.queryToObject('foo=bar&fizz-buzz=bar'))
        .toMatchObject({
            foo: 'bar',
            fizzBuzz: 'bar',
        });
});

test('mergeQueryObjectIntoUrl method', () => {
    expect(urlParser.mergeQueryObjectIntoUrl({
        foo: 'bar',
        fizzBuzz: 'bat'
    }, 'https://some-domain.com/app?hello=there&fizz-buzz=bar'))
        .toBe('https://some-domain.com/app?hello=there&fizz-buzz=bat&foo=bar');
});