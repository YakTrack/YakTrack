import moment from 'moment';

export default class DateTime {

    static secondsSince (carbonDate, fromTimeStamp = null) {
        var now = fromTimeStamp == null ? (new Date).getTime() : fromTimeStamp;
        return Math.round((now - (new Date(carbonDate.date+' GMT')).getTime()) / 1000);
    }

    static durationForHumans (numberOfSeconds) {
        var date = new Date(null);

        date.setSeconds(numberOfSeconds);

        return date.toISOString().substr(11, 8);
    }

    static toDateTimeString(date) {
        return moment(date).format('YYYY-MM-DD HH:mm:ss');
    }

    static toDateTimeForHumans(date) {
        return moment(date).format('h:mm a Mo MMM YYYY');
    }

    static startOfWeek(date) {
        return moment(date).startOf('isoWeek').toISOString();
    }

    static endOfWeek(date) {
        return moment(date).endOf('isoWeek').toISOString();
    }

    static moment(date) {
        return moment(date);
    }
}