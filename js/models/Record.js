export class Record {
    constructor(record = {date, request, response}){
        this.date = parseInt(record.date)
        this.request = record.request
        this.response = record.response
    }
}