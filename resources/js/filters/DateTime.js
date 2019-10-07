import moment from 'moment';

export default class DateTime {

    static secondsSince (carbonDate, fromTimeStamp = null) {
        var now = fromTimeStamp == null ? (new Date).getTime() : fromTimeStamp;
        return Math.round((now - (new Date(carbonDate.date+' GMT')).getTime()) / 1000);
    }

    static durationForHumans (secs) {
        var sec_num = parseInt(secs, 10)
        var hours   = Math.floor(sec_num / 3600)
        var minutes = Math.floor(sec_num / 60) % 60
        var seconds = sec_num % 60
    
        return [hours,minutes,seconds]
            .map(v => v < 10 ? "0" + v : v)
            .filter((v,i) => v !== "00" || i > 0)
            .join(":")
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
}
