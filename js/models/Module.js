export class Module {
    constructor(codigo, tiempo, avance, estado = 1, inicio, fin, actividades = []){
        this.codigoModulo
        this.tiempoConectividad
        this.porcentajeAvance
        this.estado
        this.fechaInicio
        this.fechaFin
        this.listaActividades
    }
    set listaActividades(lista = []){
        this.listaActividades = lista
    }
    get listaActividades(){
        return this.listaActividades
    }
}