export class LocalRequest {
    constructor(data = {rutOtec, idSistema, token, codigoOferta, codigoGrupo, listAlumnos}){
        if(data == null) return null
        this.rutOtec = data.rutOtec
        this.idSistema = data.idSistema
        this.token = data.token
        this.codigoOferta = data.codigoOferta
        this.codigoGrupo = data.codigoGrupo
        this.listAlumnos = data.listAlumnos
    }
    keys(){
        return ["Rut Otec", "ID Sistema", "Token", "Codigo Oferta", "Codigo Grupo", "N° alumnos"]
    }
}