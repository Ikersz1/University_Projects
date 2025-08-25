import { Component, OnInit } from '@angular/core';
import { ToastController } from '@ionic/angular';
import { MenuController } from '@ionic/angular';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  standalone: false
})
export class AppComponent implements OnInit {

  readonly menuFile: string = './assets/data/menu.json';
  menuOptions: any[] = [];

  constructor(
    private toastController: ToastController,
    private menu: MenuController
  ) {}

  ngOnInit() {
    this.presentWelcomeToast();
    this.getMenu(); // 👈 Cargamos el menú al iniciar
  }

  async presentWelcomeToast() {
    const toast = await this.toastController.create({
      message: 'Welcome to the Star Wars Wiki App!',
      duration: 3000,
      position: 'top',
      color: 'primary'
    });
    toast.present();
  }

  getMenu() {
    fetch(this.menuFile)
      .then(res => res.json())
      .then(json => {
        this.menuOptions = json;
        console.log('Opciones de menú cargadas:', this.menuOptions);
      });
  }
}
