/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { AgentsComponent } from './agents.component';
import {AgentRepository, MockAgentRepository} from "./agent.repository";

describe('AgentsComponent', () => {
  let component: AgentsComponent;
  let fixture: ComponentFixture<AgentsComponent>;
  let el;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AgentsComponent ],
      providers: [
        { provide: AgentRepository, useClass: MockAgentRepository }
      ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    //noinspection TypeScriptValidateTypes
    fixture = TestBed.createComponent(AgentsComponent);
    component = fixture.componentInstance;
    el = component.nativeElement;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should list the agents', () => {
    expect(el.textContent).toContain('Darth Vader');
  });
});
