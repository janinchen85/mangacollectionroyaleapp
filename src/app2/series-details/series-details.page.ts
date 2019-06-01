import { Component, OnInit } from '@angular/core';
import { Http } from '@angular/http';
import { ActivatedRoute } from '@angular/router';
import { getHeapStatistics, getHeapSpaceStatistics } from 'v8';

@Component({
  selector: 'app-series-details',
  templateUrl: './series-details.page.html',
  styleUrls: ['./series-details.page.scss']
})
export class SeriesDetailsPage implements OnInit {
  url: string;
  serieurl: string;
  username = 'Janina';
  user: string;
  stats: string;
  series: string;
  seriesInfos: string;
  seriesVolumes: string;
  /*isbn: string;
  result: BarcodeScanResult;*/
  apiKey = 'cbaf0b05-d399-4a35-b4d3-d1dfb98a6c22';
  serieID: any;
  constructor(public http: Http, private route: ActivatedRoute) {}

  ngOnInit() {
    this.route.params.subscribe(data => {
      this.serieID = data.id;
    });
    this.getStats();
    this.getSerieInfo(this.serieID);
    console.log(this.serieID);
  }

  getStats() {
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

  getSerieInfo(serieID) {
    this.serieurl =
      'http://Janina-HP:3000/manga/public/userInfo/user/' +
      this.apiKey +
      '/' +
      this.username +
      '/series/' +
      serieID;
    this.http
      .get(this.serieurl)
      .map(res => res.json())
      .subscribe(
        data => {
          this.seriesInfos = data.seriesinfo;
          this.seriesVolumes = data.volumes;
        },
        err => {
          console.log(err);
        }
      );
  }
}
