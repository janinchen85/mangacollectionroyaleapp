import { Router } from '@angular/router';
import { Component, OnInit, ViewChild } from '@angular/core';
import { AlertController, LoadingController } from '@ionic/angular';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';
import { Md5 } from 'ts-md5/dist/md5';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss']
})
export class LoginPage implements OnInit {
  url: string;
  user = { username: '', password: '' };
  md5PreSalt = '$K$p-14$t:';
  md5EndSalt = ':$z7T-$a&t';
  apiKey = 'cbaf0b05-d399-4a35-b4d3-d1dfb98a6c22';
  // hashedPW;
  /* @ViewChild('username') username;
  @ViewChild('password') password;*/

  constructor(
    private router: Router,
    public alertCtrl: AlertController,
    private http: Http,
    public loading: LoadingController
  ) {}

  async alertUsernameEmpty() {
    const alert = await this.alertCtrl.create({
      header: 'Eingabefehler',
      subHeader: 'Benutzername',
      message: 'Bitte Benutzername angeben.',
      buttons: ['OK']
    });

    await alert.present();
  }

  async alertPasswordEmpty() {
    const alert = await this.alertCtrl.create({
      header: 'Eingabefehler',
      subHeader: 'Passwort',
      message: 'Bitte Passwort angeben.',
      buttons: ['OK']
    });

    await alert.present();
  }

  async success() {
    const alert = await this.alertCtrl.create({
      header: 'Login erfolgreich',
      buttons: ['OK']
    });

    await alert.present();
  }

  async failed() {
    const alert = await this.alertCtrl.create({
      header: 'Login fehlgeschlagen',
      message: 'Versuche es erneut.',
      buttons: ['OK']
    });

    await alert.present();
  }

  ngOnInit() {}

  async login() {
    /*
    if (this.user.username === '') {
      this.alertUsernameEmpty();
    } else if (this.user.password === '') {
      this.alertPasswordEmpty();
    } else {
      const loader = await this.loading.create({ message: 'Please wait...' });
      const username =
        this.md5PreSalt + Md5.hashStr(this.user.username) + this.md5EndSalt;
      console.log(username);
      const password = this.user.password;
      this.url =
        'http://192.168.44.1:3000/manga/public/userInfo/user/' +
        this.apiKey +
        '/' +
        this.user.username;
      console.log(this.url);
      loader.present().then(() => {
        this.http
          .get(this.url)
          .map(res => res.json())
          .subscribe(res => {
            loader.dismiss();
            if (res === 'access') {
              console.log('true');
            }
          });
      });
    }*/
    this.router.navigateByUrl('/home');
  }

  register() {
    this.router.navigateByUrl('/register');
  }
}
