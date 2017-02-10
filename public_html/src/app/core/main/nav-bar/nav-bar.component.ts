import {Component, OnInit, style, state, animate, transition, trigger} from '@angular/core';
import collapse from '../../../shared/collapse.animation';

@Component({
  selector: 'nav-bar',
  templateUrl: './nav-bar.component.html',
  styleUrls: ['./nav-bar.component.css'],
  animations: [
    trigger('calendarActive', collapse),
    trigger('crmActive', collapse),
    trigger('managementActive', collapse),
    trigger('settingsActive', collapse)
  ]
})
export class NavBarComponent implements OnInit {

  public calendarShow: boolean = false;
  public crmShow: boolean = false;
  public managementShow: boolean = false;
  public settingsShow: boolean = false;

  constructor() { }

  ngOnInit() {
  }

}
