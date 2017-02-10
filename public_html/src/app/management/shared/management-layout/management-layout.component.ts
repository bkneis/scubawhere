import {Component, OnInit, Input} from '@angular/core';

@Component({
  selector: 'management-layout',
  templateUrl: './management-layout.component.html',
  styleUrls: ['./management-layout.component.css']
})
export class ManagementLayoutComponent implements OnInit {

  @Input()
  public action: string;
  @Input()
  public model: string;
  
  constructor() { }

  ngOnInit() {}

}
