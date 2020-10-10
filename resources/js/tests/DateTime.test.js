import dateTime from '../filters/DateTime.js';

test('durationForHumans method returns expected results for given inputs', () => {
    let testCases = [
        {
            input: 0,
            output: "00:00:00"
        },
        {
            input: 123,
            output: "00:02:03"
        },
        {
            input: 1234,
            output: "00:20:34"
        },
        {
            input: 12345,
            output: "03:25:45"
        },
        {
            input: -1,
            output: "-00:00:01"
        },
        {
            input: 28800,
            output: "08:00:00"
        },
    ];

    Object.keys(testCases).forEach(key => {
        expect(dateTime.durationForHumans(testCases[key].input))
            .toBe(testCases[key].output);
    });
});