import dayjs from 'dayjs';

export default {

    secondsSince (carbonDate, fromTimeStamp = null) {
        var now = fromTimeStamp == null ? (new Date).getTime() : fromTimeStamp;
        return Math.round((now - (new Date(carbonDate.date+' GMT')).getTime()) / 1000);
    },

    durationForHumans (secs) {
        var sec_num = parseInt(secs, 10)
        var hours   = Math.floor(sec_num / 3600)
        var minutes = Math.floor(sec_num / 60) % 60
        var seconds = sec_num % 60
    
        return [hours,minutes,seconds]
            .map(v => v < 10 ? "0" + v : v)
            .filter((v,i) => v !== "00" || i > 0)
            .join(":")
    },

    dateForHumans (date) {
        return dayjs(date).format('ddd Do MMM YYYY');
    },

    fromNow (date) {
        return dayjs(date).fromNow();
    },

    toInputFormat(date) {
        return date.format('YYYY-MM-DDTHH:mm:ss');
    },

    toDateTimeString(date) {
        return dayjs(date).format('YYYY-MM-DD HH:mm:ss');
    },

    fromDateTimeString(date) {
        return dayjs(date, 'YYYY-MM-DD HH:mm:ss');
    },

    fromSearchParam(date) {
        return dayjs(date, 'yyyy-MM-ddThh:mm');
    },

    toDateTimeForHumans(date) {
        return dayjs(date).format('h:mm a Mo MMM YYYY');
    },

    startOfWeek(date) {
        return this.toInputFormat(dayjs(date).startOf('week'));
    },

    startOfLastWeek(date) {
        return this.toInputFormat(dayjs(date).startOf('week').subtract(1, 'week'));
    },

    endOfWeek(date) {
        return this.toInputFormat(dayjs(date).endOf('week'));
    },

    endOfLastWeek(date) {
        return this.toInputFormat(dayjs(date).endOf('week').subtract(1, 'week'));
    },

    startOfMonth(date) {
        return this.toInputFormat(dayjs(date).startOf('month'));
    },

    startOfLastMonth(date) {
        return this.toInputFormat(dayjs(date).startOf('month').subtract(1, 'month'));
    },

    endOfMonth(date) {
        return this.toInputFormat(dayjs(date).endOf('month'));
    },

    endOfLastMonth(date) {
        return this.toInputFormat(dayjs(date).endOf('month').subtract(1, 'month'));
    },

    dayjs(date) {
        return dayjs(date);
    },

    totalDuration(sessionsCollection) {
        if (sessionsCollection == null) {
            return 0;
        }

        return sessionsCollection.reduce((tally, session) => {
            return session.durationInSeconds + tally
        }, 0);
    },
}
