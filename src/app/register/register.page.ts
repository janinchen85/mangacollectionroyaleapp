import { Component, OnInit, ViewChild } from '@angular/core';
import { AlertController, LoadingController } from '@ionic/angular';
import { Storage } from '@ionic/storage';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';

@Component({
  selector: 'app-register',
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss']
})
export class RegisterPage implements OnInit {
  public url: string;
  public myHost: string;
  public data: any = {};
  public userID: any;
  public userName: string;
  public userApi: string;
  public userIsLoggedIn: any;
  public header: string;
  public msg: string;
  public user = { username: '', email: '', password: '', password2: '' };
  public registerResponse: string;
  @ViewChild('username') username;
  @ViewChild('email') email;
  @ViewChild('password') password;
  @ViewChild('password2') password2;
  constructor(
    public alertCtrl: AlertController,
    public loadingController: LoadingController,
    private storage: Storage,
    private http: Http
  ) {}

  ngOnInit() {}
  register() {
    if (this.user.username === '') {
      this.header = 'Eingabefehler';
      this.msg = 'Bitte Benutzername angeben.';
      this.alert(this.header, this.msg);
    } else if (this.user.password === '') {
      this.header = 'Eingabefehler';
      this.msg = 'Bitte Passwort angeben.';
      this.alert(this.header, this.msg);
    } else if (this.user.password !== this.user.password2) {
      this.header = 'Eingabefehler';
      this.msg = 'Passwörter stimmen nicht überein.';
      this.alert(this.header, this.msg);
    } else if (this.user.email === '') {
      this.header = 'Eingabefehler';
      this.msg = 'Bitte Email angeben.';
      this.alert(this.header, this.msg);
    } else {
      const bcrypt = require('bcryptjs');
      const salt = bcrypt.genSaltSync(10);
      const hash = bcrypt.hashSync(this.user.password, salt);
      this.storage.get('myHost').then(myHost => {
        this.myHost = myHost;
        this.url = this.myHost + 'manga/public/access/user/register/';
        const myData = JSON.stringify({
          username: this.user.username,
          email: this.user.email,
          password: hash
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
          this.getResponse();
        }, 3000);
      });
    }
  }

  async getResponse() {
    this.storage.get('myHost').then(myHost => {
      this.myHost = myHost;
      this.url =
        this.myHost + 'manga/public/access/user/response/' + this.user.username;
      this.http
        .get(this.url)
        .map(res => res.json())
        .subscribe(
          data => {
            this.registerResponse = data.response[0]['Response'];
            if (this.registerResponse === 'UserError') {
              this.header = 'Fehler';
              this.msg = 'Benutername existiert bereits.';
              this.alert(this.header, this.msg);
            } else if (this.registerResponse === 'UserEmailError') {
              this.header = 'Eingabefehler';
              this.msg = 'Benutername und Email existieren bereits.';
              this.alert(this.header, this.msg);
            } else if (this.registerResponse === 'EmailError') {
              this.header = 'Eingabefehler';
              this.msg = 'Email existiert bereits.';
              this.alert(this.header, this.msg);
            } else if (this.registerResponse === 'Success') {
              this.header = 'Erfolg';
              this.msg =
                'Benutzer wurde erstellt, du kannst dich nun einloggen';
              this.alert(this.header, this.msg);
            }
          },
          err => {
            console.log(err);
          }
        );
    });
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
}
