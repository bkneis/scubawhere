"use strict";

import AddonRepo from 'Repositories/AddonRepo.ts';
import AgentRepo from 'Repositories/AgentRepo.ts';
import IRepository from 'Contracts/IRepository';
import AccommodationRepo from 'Repositories/AccommodationRepo.ts';

export default class RepositoryFactory {
    
    static make(resource : string) : IRepository
    {
        switch (resource) {
            case ('accommodations'):
                return new AccommodationRepo;
            case ('addons'):
                return new AddonRepo;
            case ('agents'):
                return new AgentRepo;
            default:
                console.error('Unexpected behaviour : Unknown Repository type');
                return;
        }
    }
    
}
