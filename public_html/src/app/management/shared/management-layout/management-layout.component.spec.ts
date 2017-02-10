/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { ManagementLayoutComponent } from './management-layout.component';

describe('LayoutComponent', () => {
  let component: ManagementLayoutComponent;
  let fixture: ComponentFixture<ManagementLayoutComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ManagementLayoutComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    //noinspection TypeScriptValidateTypes
    fixture = TestBed.createComponent(ManagementLayoutComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
