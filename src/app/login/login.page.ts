import { Router } from '@angular/router';
import { Component, OnInit, ViewChild } from '@angular/core';
import { AlertController, LoadingController } from '@ionic/angular';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';
import { Storage } from '@ionic/storage';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss']
})
export class LoginPage implements OnInit {
  public url: string;
  public myHost: string;
  public data: any = {};
  public userID: any;
  public userName: string;
  public userApi: string;
  public userIsLoggedIn: any;
  public header: string;
  public msg: string;
  public user = { username: '', password: '' };
  public str: string;
  public block: string;
  public strPos: any;
  public apikey: any;
  @ViewChild('username') username;
  @ViewChild('password') password;

  constructor(
    private router: Router,
    public alertCtrl: AlertController,
    private http: Http,
    public loading: LoadingController,
    private storage: Storage,
    public loadingController: LoadingController
  ) {}

  ngOnInit() {}

  async login() {
    if (this.user.username === '') {
      this.header = 'Eingabefehler';
      this.msg = 'Bitte Benutzername angeben.';
      this.alert(this.header, this.msg);
    } else if (this.user.password === '') {
      this.header = 'Eingabefehler';
      this.msg = 'Bitte Passwort angeben.';
      this.alert(this.header, this.msg);
    }
    this.storage.get('myHost').then(myHost => {
      this.myHost = myHost;
      this.url = this.myHost + 'manga/public/access/user/login/';
      this.apikey = this.generateAPI();
      const myData = JSON.stringify({
        username: this.user.username,
        password: this.user.password,
        apikey: this.apikey
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
        this.userInfos();
      }, 3000);
    });
  }

  async userInfos() {
    this.storage.get('myHost').then(myHost => {
      this.myHost = myHost;
      this.url =
        this.myHost + 'manga/public/access/user/info/' + this.user.username;
      this.userApi = this.apikey;
      this.http
        .get(this.url)
        .map(res => res.json())
        .subscribe(
          data => {
            this.userID = data.user[0]['userID'];
            this.userName = data.user[0]['userName'];
            this.userIsLoggedIn = data.user[0]['userIsLoggedIn'];
            this.check(
              this.userID,
              this.userName,
              this.userApi,
              this.userIsLoggedIn
            );
          },
          err => {
            console.log(err);
          }
        );
    });
  }

  async check(userID, userName, userApi, userIsLoggedIn) {
    if (userIsLoggedIn === '1') {
      this.storage.set('userIsLoggedIn', true);
      this.storage.set('userID', userID);
      this.storage.set('userName', userName);
      this.storage.set('userApi', userApi);
      this.router.navigateByUrl('/home');
    } else {
      this.header = 'Login fehlgeschlagen';
      this.msg = 'Bitte überprüfe deine Eingabe.';
      this.alert(this.header, this.msg);
    }
  }
  async register() {
    this.router.navigateByUrl('/register');
  }

  async alert(header, msg) {
    const alert = await this.alertCtrl.create({
      header: header,
      message: msg,
      buttons: ['OK']
    });
    await alert.present();
  }

  async presentLoading() {
    const loading = await this.loadingController.create({
      duration: 2000
    });
    return await loading.present();
  }

  generateAPI() {
    this.str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    this.strPos = 0;
    for (let i = 0; i < 32; i++) {
      this.block =
        Math.random()
          .toString(36)
          .substring(2, 15) +
        Math.random()
          .toString(36)
          .substring(2, 15);
    }
    const chuncks = this.block.match(/.{1,6}/g);
    this.block = chuncks.join('-');
    return this.block;
  }
}
