import { BrowserModule } from '@angular/platform-browser';
import {NgModule, CUSTOM_ELEMENTS_SCHEMA} from '@angular/core';
import { FormsModule } from '@angular/forms';
import {HttpModule, JsonpModule} from '@angular/http';
import {RouterModule, Routes} from "@angular/router";

import { AppComponent } from './app.component';
import { LoginComponent } from './core/login/login.component';
import {LocationStrategy, HashLocationStrategy} from "@angular/common";
import { MainComponent } from './core/main/main.component';
import { NavBarComponent } from './core/main/nav-bar/nav-bar.component';
import { RegisterComponent } from './core/register/register.component';
import { AgentsComponent } from './management/agents/agents.component';
import { BreadcrumbComponent } from './core/main/breadcrumb/breadcrumb.component';
import { ManagementLayoutComponent } from './management/shared/management-layout/management-layout.component';
import { InTextComponent } from './shared/inputs/in-text/in-text.component';

//noinspection TypeScriptValidateTypes
const appRoutes: Routes = [
    { path: '', component: AppComponent },
    { path: 'login', component: LoginComponent },
    { path: 'main', component: MainComponent, children: [
        { path: 'agents', component: AgentsComponent, outlet: 'tabs' }
    ] }
];

@NgModule({
  declarations: [
      AppComponent,
      LoginComponent,
      MainComponent,
      NavBarComponent,
      RegisterComponent,
      AgentsComponent,
      BreadcrumbComponent,
      ManagementLayoutComponent,
      InTextComponent
  ],
  imports: [
      BrowserModule,
      FormsModule,
      HttpModule,
      JsonpModule,
      RouterModule.forRoot(appRoutes)
  ],
  providers: [
      { provide: LocationStrategy, useClass: HashLocationStrategy }
  ],
  bootstrap: [AppComponent], 
    schemas: [ CUSTOM_ELEMENTS_SCHEMA ]
})
export class AppModule { }
