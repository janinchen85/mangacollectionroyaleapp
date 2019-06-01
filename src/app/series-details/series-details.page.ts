import { Component, OnInit } from '@angular/core';
import { Http } from '@angular/http';
import { ActivatedRoute, Router } from '@angular/router';
import { Storage } from '@ionic/storage';

@Component({
  selector: 'app-series-details',
  templateUrl: './series-details.page.html',
  styleUrls: ['./series-details.page.scss']
})
export class SeriesDetailsPage implements OnInit {
  public url: string;
  public myHost: string;
  public userID: any;
  public userName: string;
  public userApi: string;
  public userIsLoggedIn: any;
  public serieStats: string;
  public seriesInfos: string;
  public seriesVolumes: string;
  public serieID: any;
  constructor(
    public http: Http,
    private route: ActivatedRoute,
    private router: Router,
    private storage: Storage
  ) {}

  ngOnInit() {
    this.route.params.subscribe(data => {
      this.serieID = data.id;
    });
    this.getSerieStats(this.serieID);
  }

  getSerieStats(serieID) {
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
            '/serieStats/' +
            serieID;
          this.http
            .get(this.url)
            .map(res => res.json())
            .subscribe(
              data => {
                this.serieStats = data.serieStats;
                this.seriesInfos = data.seriesInfos;
                this.seriesVolumes = data.seriesVolumes;
              },
              err => {
                console.log(err);
              }
            );
        });
      });
    });
  }

  home() {
    this.router.navigateByUrl('/home');
  }

  scanBooks() {
    this.router.navigateByUrl('/scan-book');
  }
}
