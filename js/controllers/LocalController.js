import { ResponseBuilder } from "../models/ResponseBuilder.js";
import { BaseController } from "./BaseController.js";
import { RecordBuilder } from './../models/RecordBuilder.js'

export class LocalController extends BaseController {
    constructor(){
        super()
    }
    downloadData(){
        let view = document.querySelector("#history_view")
        let data = JSON.parse(view.getAttribute("local"))
        // console.log("LOCAL DATA:", data)
        let datafileds = RecordBuilder.build(data)
        // let request = RequestBuilder.build(data)
        // let response = ResponseBuilder.build(data)
        console.log("DATAFIELDS:", datafileds)
        return datafileds
    }
    loadData(){}
}