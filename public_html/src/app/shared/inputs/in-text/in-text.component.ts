import {Component, OnInit, Input, forwardRef} from '@angular/core';
import {ControlValueAccessor, NG_VALUE_ACCESSOR} from "@angular/forms";

@Component({
  selector: 'in-text',
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => InTextComponent),
      multi: true
    }
  ],
  templateUrl: './in-text.component.html',
  styleUrls: ['./in-text.component.css']
})
export class InTextComponent implements OnInit, ControlValueAccessor {

  @Input()
  text: string;
  @Input()
  placeholder: string = '';
  @Input()
  label: string;
  
  propagateChange = (_: any) => {};

  constructor() { }
  
  writeValue(value: any): void {
    if (value !== undefined) {
      this.text = value;
      this.propagateChange(this.text);
    }
  }

  registerOnChange(fn: any): void {
    this.propagateChange = fn;
  }

  registerOnTouched(fn: any): void {}

  updateValue(event: any): void {
    this.writeValue(event.target.value);
  }

  ngOnInit() {
  }

}
