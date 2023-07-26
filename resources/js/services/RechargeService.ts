import ModelService from "@/services/ModelService";

export default class RechargeService extends ModelService {

    constructor() {
        super();
        this.url = '/recharges';
    }
}
