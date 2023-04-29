import { LocalResponse } from "./LocalResponse.js"

export const ResponseBuilder = {
    build(data){
        if(data.hasOwnProperty("envio")){
            return new LocalResponse(data)
        }else{
            return new LocalResponse({
                id_proceso: data.id_proceso,
                envio: data.datosEnviados,
                errores: data.datosError,
                respúesta_SIC: data.respuesta_SIC,
            })
        }
    }
} 