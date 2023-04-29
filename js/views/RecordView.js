import { Table } from "../models/DOMLib.js"
import { LocalRequest } from "../models/LocalRequest.js"
import { LocalResponse } from "../models/LocalResponse.js"
import { RequestView } from "./RequestView.js"
import { ResponseView } from "./ResponseView.js"

export class RecordView {
    constructor(record, pager){
        this.record = record
        this.pager = pager
        this.id = this.record.date
        this.el = document.createElement("div")
        this.title = document.createElement("h2")
    }
    render(){
        this.el.innerHTML = ""
        this.title.innerText = "Detalles Registro ID "+ this.id
        this.title.classList.add("mb-4")
        this.el.appendChild(this.title)
        // Request Data
        let requestSubtitle = document.createElement("h4")
        requestSubtitle.classList.add("mt-4")
        requestSubtitle.innerText = "Peticion a SIC"
        let request = new LocalRequest(this.record.request)
        let keys = request != null ? request.keys() : []
        let requestTable = new Table(
            "request_table_"+this.id,
            keys.map(key => key.toUpperCase()),
            {},
            ["mb-4"]
        )
        let requestPath = "req_"+this.record.date
        let values = this.record.request != null ? Object.values(this.record.request) : []
        let nAlumnos = this.record.request.listaAlumnos.length
        this.pager.addPage(requestPath, new RequestView(this.record.request))
        values[values.length - 1] = nAlumnos + (nAlumnos > 1 ? " alumnos" : " alumno")
        requestTable.addRow(
            values,
            (e) => {
                this.pager.show(requestPath)
            }
        )
        this.el.appendChild(requestSubtitle)
        this.el.appendChild(requestTable.render())
        // SIC Response
        let responseSubtitle = document.createElement("h4")
        responseSubtitle.classList.add("mt-4")
        responseSubtitle.innerText = "Respuesta de SIC"
        let responsePath = "res_"+this.record.date
        let response = new LocalResponse(this.record.response)
        keys = response != null ? Object.keys(response) : []
        let responseTable = new Table(
            "response_table_"+this.id,
            keys.length > 0 ? keys.map(key => key.toUpperCase()) : keys,
            {},
            ["mb-4"]
        )
        this.pager.addPage(responsePath, new ResponseView(this.record.response))
        values = response.values()
        responseTable.addRow(
            values,
            (e) => {
                this.pager.show(responsePath)
            }
        )
        this.el.appendChild(responseSubtitle)
        this.el.appendChild(responseTable.render())
        return this.el
    }
}