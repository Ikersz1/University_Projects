import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { UserService } from '../services/user.service';
import { MenuController } from '@ionic/angular';


@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  standalone : false
})
export class LoginPage implements OnInit {

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private userSrv: UserService,
    private menuCtrl: MenuController
  ) { }

  formLogin: any;
  error: string = '';
  
  ngOnInit() {
    this.formLogin = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  ionViewWillEnter() {
    this.menuCtrl.enable(false, 'principal');
  }

  doLogin() {
    let email = this.formLogin.get('email').value;
    let password = this.formLogin.get('password').value;

    let user = this.userSrv.login(email, password);

    if (user != null) {
      this.router.navigate(['/tabs']);
    } else {
      this.error = 'Error, credenciales incorrectas';
    }
  }

}
