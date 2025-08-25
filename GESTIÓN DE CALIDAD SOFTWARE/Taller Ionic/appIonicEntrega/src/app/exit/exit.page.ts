import { Component } from '@angular/core';
import { NavController } from '@ionic/angular';
import { Platform } from '@ionic/angular';

@Component({
  selector: 'app-exit',
  templateUrl: './exit.page.html',
  styleUrls: ['./exit.page.scss'],
  standalone:false
})
export class ExitPage {
  constructor(private navCtrl: NavController, private platform: Platform) {}

  closeApp() {
    if (this.platform.is('capacitor')) {
      (navigator as any).app.exitApp();  // Solo funciona en móviles con Capacitor/Cordova
    } else {
      alert("La app será cerrada.");
    }
  }

  cancel() {
    this.navCtrl.navigateRoot('/tabs/wiki');
  }
}
