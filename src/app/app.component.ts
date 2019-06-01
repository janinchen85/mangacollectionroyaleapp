import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { Platform, LoadingController } from '@ionic/angular';
import { SplashScreen } from '@ionic-native/splash-screen/ngx';
import { StatusBar } from '@ionic-native/status-bar/ngx';
import { Storage } from '@ionic/storage';
import { Http } from '@angular/http';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html'
})
export class AppComponent {
  public url: string;
  public data: any = {};
  public myHost: string;
  public userID: any;
  public userName: string;
  public userApi: string;
  public userIsLoggedIn: any;
  constructor(
    private platform: Platform,
    private splashScreen: SplashScreen,
    private statusBar: StatusBar,
    private router: Router,
    private storage: Storage,
    private http: Http,
    public loadingController: LoadingController
  ) {
    this.initializeApp();
    this.storage.set('myHost', 'http://needle-sorcery.com/');
  }
  public appPages = [
    {
      title: 'Home',
      url: '/home',
      icon: 'home'
    }
  ];
  hidden: boolean;
  initializeApp() {
    this.platform.ready().then(() => {
      this.statusBar.styleDefault();
      this.splashScreen.hide();
    });
    this.storage.get('userIsLoggedIn').then(userIsLoggedIn => {
      this.userIsLoggedIn = userIsLoggedIn;
      if (!this.userIsLoggedIn) {
        this.router.navigateByUrl('/login');
      } else if (this.userIsLoggedIn) {
        this.router.navigateByUrl('/home');
      }
    });
  }

  logout() {
    this.storage.get('myHost').then(myHost => {
      this.myHost = myHost;
      this.storage.get('userName').then(userName => {
        this.userName = userName;
        this.url =
          this.myHost + 'manga/public/access/user/logout/' + this.userName;
        const myData = JSON.stringify({
          username: this.userName
        });
        this.http.post(this.url, myData).subscribe(
          data => {
            this.data.response = data['_body'];
          },
          err => {
            console.log(err);
          }
        );
        this.presentLoading();
        setTimeout(() => {
          this.storage.set('userIsLoggedIn', false);
          this.storage.set('userApi', '');
          this.router.navigateByUrl('/login');
        }, 2000);
      });
    });
  }

  async presentLoading() {
    const loading = await this.loadingController.create({
      duration: 2000
    });
    return await loading.present();
  }
}
