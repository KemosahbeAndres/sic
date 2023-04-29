export class LocalResponse {
    constructor(data = {id_proceso, envio, errores, respuesta_SIC}){
        if(data == null) return new LocalResponse({
            id_proceso: null,
            envio: null,
            errores: null,
            respuesta_SIC: null
        })
        this.id = data.id_proceso
        this.envio = data.envio || data.datosEnvio
        this.errores = data.errores || data.datosError
        this.respuesta = data.respuesta_SIC || ""
    }
    keys(){
        return ["id", "envio", "errores", "respuesta"]
    }
    values(){
        if(this.id == null) return []
        return [
            this.id,
            this.envio.length,
            this.errores.length,
            this.respuesta == "" ? "Se encontraron errores" : this.respuesta
        ]
    }
}