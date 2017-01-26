"use strict";
    
export default class PriceService {
    
    static getMin(prices : Array<any>) : string
    {
        return prices[0] ? prices[0].price : '';
    }
    
    static getMax(prices : Array<any>) : string
    {
        return prices[0] ? prices[0].price : '';
    }
    
    static priceRange(prices : Array<any>) : string
    {
        if (prices.length > 1) {
            return prices[0].decimal_price + ' - ' + prices[prices.length - 1].decimal_price;
        }
        return (prices.length > 0 && prices[0].hasOwnProperty('decimal_price')) ? prices[0].decimal_price : '';
    }
    
    static emptyPrice() : any
    {
        return { decimal_price : 0.00, from : '', until : ''};
    }

    /**
     * @todo Use _.map from lodash
     * @param prices
     * @returns {Array}
     */
    static getValues(prices : Array<any>) : Array<any>
    {
        let values = [];
        for (let i in prices) {
            values.push({
                from : prices[i].from,
                until : prices[i].until,
                amount : parseInt(prices[i].decimal_price) * 100
            });
        }
        return values;
    }
    
}
