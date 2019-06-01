import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ScanBookPage } from './scan-book.page';

describe('ScanBookPage', () => {
  let component: ScanBookPage;
  let fixture: ComponentFixture<ScanBookPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ScanBookPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ScanBookPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
