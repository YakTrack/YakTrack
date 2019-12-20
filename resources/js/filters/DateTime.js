import moment from 'moment';

export default class DateTime {

    static secondsSince (carbonDate, fromTimeStamp = null) {
        var now = fromTimeStamp == null ? (new Date).getTime() : fromTimeStamp;
        return Math.round((now - (new Date(carbonDate.date+' GMT')).getTime()) / 1000);
    }

    static durationForHumans (numberOfSeconds) {
        if (typeof numberOfSeconds === 'undefined') {
            debugger
        }

        var date = new Date(null);

        date.setSeconds(numberOfSeconds);

        return date.toISOString().substr(11, 8);
    }

    static dateForHumans (date) {
        return moment(date).format('ddd Do MMM YYYY');
    }

    static fromNow (date) {
        return moment(date).fromNow();
    }

    static toDateTimeString(date) {
        return moment(date).format('YYYY-MM-DD HH:mm:ss');
    }

    static fromDateTimeString(date) {
        return moment(date, 'YYYY-MM-DD HH:mm:ss').toISOString();
    }

    static toDateTimeForHumans(date) {
        return moment(date).format('h:mm a Mo MMM YYYY');
    }

    static startOfWeek(date) {
        return moment(date).startOf('isoWeek').toISOString();
    }

    static startOfLastWeek(date) {
        return moment(date).startOf('isoWeek').subtract(1, 'week').toISOString();
    }

    static endOfWeek(date) {
        return moment(date).endOf('isoWeek').toISOString();
    }

    static endOfLastWeek(date) {
        return moment(date).endOf('isoWeek').subtract(1, 'week').toISOString();
    }

    static startOfMonth(date) {
        return moment(date).startOf('month').toISOString();
    }

    static startOfLastMonth(date) {
        return moment(date).startOf('month').subtract(1, 'month').toISOString();
    }

    static endOfMonth(date) {
        return moment(date).endOf('month').toISOString();
    }

    static endOfLastMonth(date) {
        return moment(date).endOf('month').subtract(1, 'month').toISOString();
    }

    static moment(date) {
        return moment(date);
    }

    static totalDuration(sessionsCollection) {
        if (sessionsCollection == null) {
            return 0;
        }

        return sessionsCollection.reduce((tally, session) => {
            return session.durationInSeconds + tally
        }, 0);
    }
}
