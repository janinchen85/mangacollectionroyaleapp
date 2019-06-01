import { Component, OnInit, ViewChild } from '@angular/core';
import {
  CameraPreview,
  CameraPreviewOptions
} from '@ionic-native/camera-preview/ngx';
import {
  BarcodeScanner,
  BarcodeScannerOptions,
  BarcodeScanResult
} from '@ionic-native/barcode-scanner/ngx';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';
import {
  ToastController,
  LoadingController,
  NavController
} from '@ionic/angular';
import { GlobalDataService } from '../global-data.service';
import { Storage } from '@ionic/storage';

@Component({
  selector: 'app-scan-book',
  templateUrl: './scan-book.page.html',
  styleUrls: ['./scan-book.page.scss']
})
export class ScanBookPage implements OnInit {
  public unhidden: boolean;
  public selection = [];
  public result: BarcodeScanResult;
  public message: string;
  public userID: any;
  public userName: string;
  public userApi: string;
  public userIsLoggedIn: any;
  public url: string;
  public url2: string;
  public myHost: string;
  public data: any = {};
  public isbn: string;
  public Status: string;
  public Volume: string;
  public Volumes: string;
  public Series: string;
  public Besitz: string;

  constructor(
    private barcodeScanner: BarcodeScanner,
    public http: Http,
    public camera: CameraPreview,
    public toastController: ToastController,
    public globalData: GlobalDataService,
    public loadingController: LoadingController,
    public navCtrl: NavController,
    private storage: Storage
  ) {
    this.preview();
    this.unhidden = true;
  }
  ngOnInit() {}

  toggleClass(item, ISBN) {
    item.active = !item.active;
    if (!this.selection.includes(ISBN)) {
      this.selection.push(ISBN);
    } else {
      this.selection.splice(this.selection.indexOf(ISBN), 1);
    }
    console.log('SerieVolume' + this.selection);
  }

  async scanBook() {
    const isbn = await this.scanISBN();
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
            '/scanBook/' +
            isbn;
          console.log(this.url);
          this.http
            .get(this.url)
            .map(res => res.json())
            .subscribe(
              data => {
                this.Status = data.bookInfo[0]['Status'];
                this.Volume = data.bookInfo[0]['Title'];
                this.Besitz = data.bookInfo[0]['Besitz'];
              },
              err => {
                console.log(err);
              }
            );
          this.unhidden = false;
        });
      });
    });
  }

  async bookRead() {
    this.unhidden = true;
    const postmode = 'setReadStatus';
    this.scanning(postmode);
  }

  async addBook() {
    this.unhidden = true;
    const postmode = 'addVolume';
    this.scanning(postmode);
  }

  async delBook() {
    this.unhidden = true;
    const postmode = 'delVolume';
    this.scanning(postmode);
  }

  async Serie() {
    this.unhidden = true;
    const isbn = await this.scanISBN();
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
            '/showSeries/' +
            isbn;
          this.http
            .get(this.url)
            .map(res => res.json())
            .subscribe(
              data => {
                this.Volumes = data.volumes;
                this.Series = data.series;
              },
              err => {
                console.log(err);
              }
            );
        });
      });
    });
  }

  async addSeries() {
    for (let index = 0; index < this.selection.length; ++index) {
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
              '/scanBook/' +
              this.selection[index];
            this.http
              .get(this.url)
              .map(res => res.json())
              .subscribe(
                data => {
                  this.Volume = data.bookInfo[0]['Title'];
                  this.Besitz = data.bookInfo[0]['Besitz'];
                  this.url2 =
                    this.myHost +
                    'manga/public/userinfo/user/' +
                    this.userName +
                    '/' +
                    this.userApi +
                    '/addVolume/';
                  const myData = JSON.stringify({
                    ISBN: this.selection[index],
                    username: this.userName
                  });
                  this.http.post(this.url2, myData).subscribe(
                    data2 => {
                      this.data.response = data2['_body'];
                      this.message = 'Bücher wurden erfolgreich hinzugefügt.';
                      this.presentLoading();
                    },
                    err => {
                      console.log(err);
                    }
                  );
                },
                err => {
                  console.log(err);
                }
              );
          });
        });
      });
    }
  }

  delSeries() {
    for (let index = 0; index < this.selection.length; ++index) {
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
              '/scanBook/' +
              this.selection[index];
            this.http
              .get(this.url)
              .map(res => res.json())
              .subscribe(
                data => {
                  this.Volume = data.bookInfo[0]['Title'];
                  this.Besitz = data.bookInfo[0]['Besitz'];
                  this.url2 =
                    this.myHost +
                    'manga/public/userinfo/user/' +
                    this.userName +
                    '/' +
                    this.userApi +
                    '/delVolume/';
                  const myData = JSON.stringify({
                    ISBN: this.selection[index],
                    username: this.userName
                  });
                  this.http.post(this.url2, myData).subscribe(
                    data2 => {
                      this.data.response = data2['_body'];
                      this.message = 'Bücher wurden erfolgreich entfernt.';
                      this.presentLoading();
                    },
                    err => {
                      console.log(err);
                    }
                  );
                },
                err => {
                  console.log(err);
                }
              );
          });
        });
      });
    }
  }

  async scanning(postmode) {
    const isbn = await this.scanISBN();
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
            '/scanBook/' +
            isbn;
          this.http
            .get(this.url)
            .map(res => res.json())
            .subscribe(
              data => {
                this.Status = data.bookInfo[0]['Status'];
                this.Volume = data.bookInfo[0]['Title'];
                this.Besitz = data.bookInfo[0]['Besitz'];
                if (this.Besitz === 'Nein' && postmode !== 'addVolume') {
                  this.message = this.Volume + ' nicht vorhanden.';
                  this.presentToast(this.message);
                } else {
                  if (postmode === 'delVolume') {
                    this.message = this.Volume + ' wurde erfolgreich entfernt.';
                  } else if (postmode === 'addVolume') {
                    if (this.Besitz === 'Nein') {
                      this.message =
                        this.Volume + ' wurde erfolgreich hinzugefügt';
                    } else {
                      this.message = this.Volume + ' bereits vorhanden.';
                      this.presentToast(this.message);
                    }
                  } else if (postmode === 'setReadStatus') {
                    if (this.Status === 'gelesen') {
                      this.message = this.Volume + ' bereits gelesen.';
                      this.presentToast(this.message);
                    } else {
                      this.message =
                        this.Volume +
                        ' wurde erfolgreich als gelesen gespeichert';
                    }
                  }
                  this.url2 =
                    this.myHost +
                    'manga/public/userinfo/user/' +
                    this.userName +
                    '/' +
                    this.userApi +
                    '/' +
                    postmode +
                    '/';
                  const myData = JSON.stringify({
                    ISBN: isbn,
                    username: this.userName
                  });
                  this.http.post(this.url2, myData).subscribe(
                    data2 => {
                      this.data.response = data2['_body'];
                      this.presentToast(this.message);
                    },
                    err => {
                      console.log(err);
                    }
                  );
                }
              },
              err => {
                console.log(err);
              }
            );
          this.unhidden = true;
        });
      });
    });
  }

  async select(ISBN) {
    if (!this.selection.includes(ISBN)) {
      this.selection.push(ISBN);
    } else {
      this.selection.splice(this.selection.indexOf(ISBN), 1);
    }
    console.log('SerieVolume' + this.selection);
  }

  async preview() {
    const cameraPreviewOpts: CameraPreviewOptions = {
      x: 0,
      y: 0,
      width: window.screen.width,
      height: window.screen.height,
      camera: 'rear',
      tapPhoto: false,
      toBack: true,
      alpha: 1
    };

    this.camera.startCamera(cameraPreviewOpts).then(
      res => {
        console.log(res);
      },
      err => {
        console.log(err);
      }
    );
  }

  async scanISBN() {
    try {
      const options: BarcodeScannerOptions = {
        prompt: 'Point your camera to object',
        torchOn: true
      };
      this.result = await this.barcodeScanner.scan(options);
      return this.result.text;
    } catch (error) {
      console.error(error);
    }
  }

  /*
  async scanISBN() {
    this.isbn = '9783866071148';
    return this.isbn;
  }
*/
  close() {
    location.reload();
  }

  async presentToast(toastMessage) {
    const toast = await this.toastController.create({
      message: toastMessage,
      duration: 3000,
      animated: true,
      color: 'dark'
    });
    toast.present();
    setTimeout(() => {}, 3000);
  }
  async presentLoading() {
    const loading = await this.loadingController.create({
      duration: 2000
    });
    setTimeout(() => {
      this.presentToast(this.message);
    }, 3000);
    setTimeout(() => {
      location.reload();
    }, 5000);
    return await loading.present();
  }
}
