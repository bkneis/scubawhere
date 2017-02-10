/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { InTextComponent } from './in-text.component';

describe('InTextComponent', () => {
  let component: InTextComponent;
  let fixture: ComponentFixture<InTextComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ InTextComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(InTextComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
