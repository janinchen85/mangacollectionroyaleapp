import { Component } from '@angular/core';
import {
  BarcodeScanner,
  BarcodeScannerOptions,
  BarcodeScanResult
} from '@ionic-native/barcode-scanner/ngx';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';
import { Router } from '@angular/router';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss']
})
export class HomePage {
  url: string;
  username = 'Janina';
  user: string;
  stats: string;
  series: string;
  /*isbn: string;
  result: BarcodeScanResult;*/
  apiKey = 'cbaf0b05-d399-4a35-b4d3-d1dfb98a6c22';
  constructor(
    private barcodeScanner: BarcodeScanner,
    public http: Http,
    private router: Router
  ) {}
  // tslint:disable-next-line:use-life-cycle-interface
  ngOnInit() {
    this.url =
      'http://Janina-HP:3000/manga/public/userInfo/user/' +
      this.apiKey +
      '/' +
      this.username;
    this.http
      .get(this.url)
      .map(res => res.json())
      .subscribe(
        data => {
          this.user = data.userinfo;
          this.stats = data.userstats;
          this.series = data.userseries;
        },
        err => {
          console.log(err);
        }
      );
  }

  details(serieID) {
    /*this.router.navigate(['/series-details/']);
    this.router.navigateByUrl(`series-details/${serieID}`);*/
    this.router.navigate(['/series-details', serieID]);
  }
  /*
  async scanBarcode() {
    try {
      const options: BarcodeScannerOptions = {
        prompt: 'Point your camera to object',
        torchOn: false
      };
      this.result = await this.barcodeScanner.scan(options);
      this.loadData(this.result.text);
    } catch (error) {
      console.error(error);
    }
  }
  loadData(isbn) {
    this.isbn = isbn;
    // userid
    // apikey für sicherheit
    this.url = 'http://192.168.1.128:3000/manga/public/manga/isbn/';
    this.http
      .get(this.url + this.isbn)
      .map(res => res.json())
      .subscribe(
        data => {
          this.data = data.volume;
          console.log(this.url);
        },
        err => {
          console.log(err);
        }
      );
  }*/
}
