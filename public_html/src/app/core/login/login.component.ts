import { Component, OnInit } from '@angular/core';
import {AuthService} from "../../shared/auth.service";
import {Router} from "@angular/router";

@Component({
  selector: 'app-login',
  providers: [AuthService],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  username: string = '';
  password: string = '';
  error: string = '';

  constructor(private auth: AuthService, private router: Router) {}

  ngOnInit() {
  }

  login() {
    this.auth
        .login(this.username, this.password)
        .subscribe(
            data => {
              console.log(data);
                this.router.navigateByUrl('main');
            }, errors => {
              this.error = errors;
            }
        );
  }

}
