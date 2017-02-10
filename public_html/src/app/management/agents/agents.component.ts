import {Component, OnInit} from '@angular/core';
import {Observable} from "rxjs/Rx";
import {Agent} from "./agent";
import {AgentRepository} from "./agent.repository";

@Component({
  selector: 'app-agents',
  providers: [AgentRepository],
  templateUrl: './agents.component.html',
  styleUrls: ['./agents.component.css']
})
export class AgentsComponent implements OnInit {

  public agents: Observable<Agent>;
  public errors: Array<string>;
  public action: string = 'Add';
  public agent: Agent = new Agent();
  public test: string = '';

  constructor(private repo: AgentRepository) {
    this.agents = this.repo.get();
  }

  ngOnInit() {
  }

}
