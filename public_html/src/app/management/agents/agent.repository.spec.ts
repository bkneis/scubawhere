/* tslint:disable:no-unused-variable */

import { TestBed, async, inject } from '@angular/core/testing';
import { AgentRepository } from './agent.repository';

describe('CompanyService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            providers: [AgentRepository]
        });
    });

    it('should ...', inject([AgentRepository], (service: AgentRepository) => {
        expect(service).toBeTruthy();
    }));
});
