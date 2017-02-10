/* tslint:disable:no-unused-variable */

import { TestBed, async, inject } from '@angular/core/testing';
import { CompanyRepository } from './company.repository';
import {Http, BaseRequestOptions} from "@angular/http";
import {MockBackend} from "@angular/http/testing/mock_backend";

describe('CompanyRepository', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [
        CompanyRepository,
        {
          provide: Http,
          useFactory: (mockBackend, options) => {
            return new Http(mockBackend, options);
          },
          deps: [MockBackend, BaseRequestOptions]
        },
        MockBackend,
        BaseRequestOptions
      ]
    });
  });

  it('should ...', inject([CompanyRepository], (service: CompanyRepository) => {
    expect(service).toBeTruthy();
  }));
});
