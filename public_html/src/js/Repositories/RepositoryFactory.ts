"use strict";

import IRepository from "Contracts/IRepository";
import AccommodationRepo from 'Repositories/AccommodationRepo.ts';

export default class RepositoryFactory {
    
    static make(resource : string) : IRepository
    {
        switch (resource) {
            case ('accommodations'):
                return new AccommodationRepo;
            default:
                console.error('Unexpected behaviour : Unknown Repository type');
                return;
        }
    }
    
}
