import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { WikiService } from '../services/wiki.service';
import { People } from '../models/people';
import { Planet } from '../models/planet';
import { Species } from '../models/species';
import { Starship } from '../models/starship';
import { StorageService } from '../services/storage.service';
import { ToastController } from '@ionic/angular';

@Component({
  selector: 'app-article',
  templateUrl: './article.page.html',
  styleUrls: ['./article.page.scss'],
  standalone: false
})
export class ArticlePage implements OnInit {

  category: string = "";
  id: string = "";
  title: string = "";

  people: People = new People();
  planet: Planet = new Planet();
  species: Species = new Species();
  starship: Starship = new Starship();

  public isFavorite: boolean = false;
  private favorites: any[] = [];

  constructor(
    private route: ActivatedRoute,
    private srv: WikiService,
    private storageService: StorageService,
    private toastController: ToastController // ✅ Nuevo
  ) {}

  async ngOnInit() {
    this.category = this.route.snapshot.paramMap.get("cat") ?? "";
    this.id = this.route.snapshot.paramMap.get("id") ?? "";
  
    this.srv.getArticle(this.category, this.id).subscribe(async (result: any) => {
      switch (this.category) {
        case 'People':
          this.people = result.result.properties;
          this.title = this.people.name;
          break;
        case 'Planets':
          this.planet = result.result.properties;
          this.title = this.planet.name;
          break;
        case 'Species':
          this.species = result.result.properties;
          this.title = this.species.name;
          break;
        case 'Starships':
          this.starship = result.result.properties;
          this.title = this.starship.name;
          break;
      }
  
      // Una vez tenemos el título, comprobamos si es favorito
      this.isFavorite = await this.storageService.isFavorite(this.id, this.category);
    });
  }
  
  async toggleFavorite() {
    let name = "";
  
    switch (this.category) {
      case 'People':
        name = this.people.name;
        break;
      case 'Planets':
        name = this.planet.name;
        break;
      case 'Species':
        name = this.species.name;
        break;
      case 'Starships':
        name = this.starship.name;
        break;
    }
  
    if (this.isFavorite) {
      await this.storageService.removeFavorite(this.id, this.category);
      this.isFavorite = false;
      this.presentToast('Artículo eliminado de favoritos');
    } else {
      await this.storageService.addFavorite({ id: this.id, category: this.category, name });
      this.isFavorite = true;
      this.presentToast('Artículo añadido a favoritos');
    }
  }
  

  // ✅ Método actualizado usando ToastController
  async presentToast(msg: string) {
    const toast = await this.toastController.create({
      message: msg,
      duration: 2000,
      position: 'top',
      color: 'success'
    });
    await toast.present();
  }

}
