import { BaseComponent } from "./models/BaseComponent.js"
import { Nav } from './views/Navigator.js'
import { Container } from './views/Container.js'
import { LocalView } from './views/LocalView.js'
import { RemoteView } from './views/RemoteView.js'
import { LocalController } from './controllers/LocalController.js'

export class App{
    constructor(parent){
        this.components = []
        this.actions = []
        this.html = document.querySelector(parent || "#root")
    }
    start(){
        let localdata = new LocalController()
        let content = new Container()
        content.createSection("Resultados", true, new LocalView(localdata, "Registros"))
        content.createSection("Historial", false, new RemoteView())
        this.addComponent(new Nav(content.getSections()))
        this.addComponent(content)
        return this.render()
    }
    render(){
        this.html.classList.add("card")
        this.html.classList.add("p-2")
        this.html.style = "border: none"
        if(this.components.length > 0){
            for(let component of this.components){
                this.mount(this.html, component.render())
            }
        }
    }
    unmount(node){
        let parent = node.parentNode
        parent.removeChild(node)
    }
    mount(parent, node){
        parent.appendChild(node)
    }
    addComponent(component){
        if(component instanceof BaseComponent){
            this.components.push(component)
        }
    }
}
