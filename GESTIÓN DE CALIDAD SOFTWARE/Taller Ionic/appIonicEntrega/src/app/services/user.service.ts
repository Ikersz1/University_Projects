import { Injectable } from '@angular/core';
import { User } from '../models/user';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  readonly usersMockup: string = "./assets/data/users.json";
  users: User[] = [];

  constructor() {
    fetch(this.usersMockup)
      .then(res => res.json())
      .then(json => {
        this.users = json;
      });
  }

  public login(email: string, password: string): User | null {
    let aux: User | null = null;

    for (let u of this.users) {
      if (u.email === email && u.password === password) {
        aux = u;
        break;
      }
    }

    return aux;
  }
}
