import { Record } from './Record.js'

export const RecordBuilder = {
    build: (list) => {
        let matrix = []
        if(Array.isArray(list)){
            for(let record of list){
                // console.log("ADDED:", record)
                matrix.push(new Record(record))
            }
        }
        return matrix
    }
}