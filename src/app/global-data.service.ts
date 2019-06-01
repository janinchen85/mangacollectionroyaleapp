import { Injectable } from '@angular/core';
import { Observable } from 'rxjs/Observable';

@Injectable({
  providedIn: 'root'
})
export class GlobalDataService {
  public hidden: boolean;
  public userID: any;
  public userName: string;
  public userApi: string;
  public userStatus: any;
  constructor() {
    this.setUser('', '', '', '');
  }

  public setUser(userID, userName, userApi, userStatus) {
    this.userID = userID;
    this.userName = userName;
    this.userApi = userApi;
    this.userStatus = userStatus;
  }

  public getUserID() {
    return Observable.create(observer => {
      observer.next(this.userID);
      observer.complete();
    });
  }

  public getUserName() {
    return Observable.create(observer => {
      observer.next(this.userName);
      observer.complete();
    });
  }

  public getUserApi() {
    return Observable.create(observer => {
      observer.next(this.userApi);
      observer.complete();
    });
  }

  public getUserStatus() {
    return Observable.create(observer => {
      observer.next(this.userStatus);
      observer.complete();
    });
  }
}
