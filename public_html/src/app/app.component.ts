import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import {CompanyRepository} from "./shared/company.repository";
import {HTTP_UNAUTHORIZED} from "./shared/http-status-codes";

@Component({
  selector: 'app-root',
  providers: [CompanyRepository],
  template: `
    <router-outlet></router-outlet>
  `
})
export class AppComponent implements OnInit {
  
  constructor(private companyRepo: CompanyRepository, private router: Router) {}
  
  ngOnInit() {
    this.companyRepo.get().subscribe(
        data => {
            this.router.navigateByUrl('main');
        },
        error => {
            if (error.status === HTTP_UNAUTHORIZED) {
                this.router.navigateByUrl('login');
            } else {
                this.router.navigateByUrl('error');
            }
        }
    )
  }
  
}
