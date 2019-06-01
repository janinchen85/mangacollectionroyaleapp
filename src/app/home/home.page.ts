import { Component } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';
import { Router } from '@angular/router';
import { Storage } from '@ionic/storage';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss']
})
export class HomePage {
  public userID: any;
  public userName: string;
  public userApi: string;
  public userIsLoggedIn: any;
  public url: string;
  public myHost: string;
  public user: string;
  public stats: string;
  public series: string;
  constructor(
    public http: Http,
    private router: Router,
    private storage: Storage
  ) {}
  // tslint:disable-next-line:use-life-cycle-interface
  ngOnInit() {
    this.storage.get('myHost').then(myHost => {
      this.myHost = myHost;
      this.storage.get('userName').then(userName => {
        this.userName = userName;
        this.storage.get('userApi').then(userApi => {
          this.userApi = userApi;
          this.url =
            this.myHost +
            'manga/public/userinfo/user/' +
            this.userName +
            '/' +
            this.userApi +
            '/home';
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
        });
      });
    });
  }

  details(serieID) {
    this.router.navigate(['/series-details', serieID]);
  }

  home() {
    this.router.navigateByUrl('/home');
  }

  scanBooks() {
    this.router.navigateByUrl('/scan-book');
  }
}
