class BaseDOM {
    constructor(attrs = {}, className = []){
        this.attributes = attrs
        this.className = className.join(" ")
    }
    render(){}
}

export class Table extends BaseDOM {
    constructor(id, headers = [], attrs = {}, className = []){
        super(attrs, className)
        this.table = document.createElement("table")
        this.body = document.createElement("tbody")
        this.rows = []
        this.table.id = id
        this.table.classList.add("table")
        this.table.classList.add("table-striped")
        this.table.classList.add("my-2")
        for(let clase of className){
            this.table.classList.add(clase)
        }
        if(headers.length > 0) this.addHeader(headers)
        // this.table.onclick = (e) => {
        //     this.addRow(["key", "value"])
        // }
        // this.table.oncontextmenu = (e) => {
        //     e.preventDefault()
        //     this.popRow()
        // }
        for(let attr in this.attributes){
            this.table.setAttribute(attr, this.attributes[attr])
        }
    }
    render(){
        this.table.appendChild(this.body)
        return this.table
    }
    addHeader(headers){
        let tr = document.createElement("tr")
        // tr.classList.add("table-dark")
        for(let head of headers){
            let th = document.createElement("th")
            th.classList.add("text-nowrap")
            th.innerText = head
            tr.appendChild(th)
        }
        if(this.body.hasChildNodes()){
            let nodes = this.body.children
            this.body.remove(nodes)
            this.body.appendChild(tr)
            for(let node of nodes){
                this.body.appendChild(node)
            }
        }else{
            this.body.appendChild(tr)
        }
        this.rows.push(tr)
    }
    addRow (data = [], action){
        let tr = document.createElement("tr")
        if(typeof action == "function"){
            tr.onclick = action
            tr.style = "cursor:pointer;"
        }
        // tr.classList.add("table-dark")
        if(Array.isArray(data)){
            for(let col of data){
                let td = document.createElement("td")
                // td.classList.add("table-dark")
                td.innerText = col
                tr.appendChild(td)
            }
        }
        this.body.appendChild(tr)
        this.rows.push(tr)
    }
    popRow(){
        if(this.rows.length > 1){
            let lastrow = this.rows.pop()
            this.body.removeChild(lastrow)
        }else{
            console.info("Can´t remove headers.")
        }
    }
}

export class Link extends BaseDOM {
    constructor(text, action, attrs, className = []){
        super(attrs, className)
    }
    render(){}
}

export class Button extends BaseDOM {
    constructor(value, action, attrs = {}, className = []){
        super(attrs, className)
        this.text = value
        this.action = action
        this.button = document.createElement("button")
        this.button.className = "btn btn-white"
        for(let clase of className){
            this.button.classList.add(clase)
        }
        // this.button.type = "button"
        for(let attr in this.attributes){
            this.button.setAttribute(attr, this.attributes[attr])
        }
        this.childs = []
    }
    addChild(child){
        this.childs.push(child)
    }
    render(){
        for(let child of this.childs){
            this.button.appendChild(child.render() || child)
        }
        this.button.onclick = this.action
        this.button.innerHTML += " "+this.text
        return this.button
    }
}
export class Icon extends BaseDOM {
    constructor(name, attrs = {}, className = []){
        super(attrs, className)
        this.icon = document.createElement("i")
        this.icon.className = "bi bi-"+name
        for(let clase of className){
            this.icon.classList.add(clase)
        }
        for(let attr in this.attributes){
            this.icon.setAttribute(attr, this.attributes[attr])
        }
        this.render = () => {
            return this.icon
        }
    }
    render(){
        return this.icon
    }
}

export class Div extends BaseDOM{
    constructor(childs = [], attrs = {}, className = []){
        super(attrs, className)
        this.div = document.createElement("div")
        for(let attr in this.attributes){
            this.div.setAttribute(attr, this.attributes[attr])
        }
        this.childs = childs
    }
    addChild(child){
        this.childs.push(child)
    }
    render(){
        if(this.childs.length > 0){
            for(let child of this.childs){
                this.div.appendChild(child.render() || child)
            }
        }
        return this.div
    }
}
