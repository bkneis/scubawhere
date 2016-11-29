
class BaseRepo {

    let apiBaseUrl = '/api/v1';

    joinUrl(...args) {
        let url = '';
        _.each(args, function (arg) {
            url += '/' + arg;
        });
        return url;
    }

}

class ResourceRepo implements BaseRepo {

    let resource;

    construct(resource) {
        this.resource = resource;
    }

    req(id, data, successFn, errorFn, type = 'GET') {
        if(typeof id === 'number') {
            url = joinUrl(this.apiBaseUrl, this.resource, id.toString());
        } else {
            url = this.joinUrl(this.apiBaseUrl, this.resource);
        }
        $.ajax({
            type    : 'GET',
            url     : joinUrl(this.apiBaseUrl, this.resource, id.toString()),
            success : successFn,
            error   : errorFn
        });
    }

    get (id, successFn, errorFn) {
       req(id, {}, successFn, errorFn);
    }

    all (successFn, errorFn) {
        req(null, {}, successFn, errorFn);
    }

    add (data, successFn, errorFn) {
        req(null, data, successFn, errorFn, 'POST');
    }

    update (id, data, successFn, errorFn) {
        req(id, data, successFn, errorFn, 'PUT');
    }

    delete (id, data, successFn, errorFn) {
        req(id, data, successFn, errorFn, 'DELETE');
    }
}

class CacheRepo {

    let repo;
    constructor(repo) {
        this.repo = repo;
    }

    

}

class AccommodationRepo implements CacheRepo {


}

}

export let repo = new AccommodationRepo();