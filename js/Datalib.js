class IDataService {
    constructor(){}
    get getData(){}
    set putData(){}
}

class JsonDataService extends IDataService {
    constructor(){}
    get getData(){}
    set putData(){}
}

const DataManager = (dataServiceImpl)=>{
    this.dataService = null;
    if(dataServiceImpl instanceof IDataService){
        this.dataService = dataServiceImpl
    }else{
        return {
            error: true
        }
    }
    
    const getDataService = () => {
        return this.dataService
    }
    return {
        getDataService
    }
}

module.exports = DataManager