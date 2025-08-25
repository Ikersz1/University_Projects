import { Component, OnInit } from '@angular/core';
import { StorageService } from '../services/storage.service';

@Component({
  selector: 'app-favourites',
  templateUrl: './favourites.page.html',
  styleUrls: ['./favourites.page.scss'],
  standalone: false
})
export class FavouritesPage implements OnInit {
  favorites: any[] = [];

  constructor(private storageService: StorageService) {}

  async ngOnInit() {
    this.favorites = await this.storageService.getFavorites();
  }

  async ionViewWillEnter() {
    this.favorites = await this.storageService.getFavorites();
  }

  generateURL(cat: string, id: string): string {
    return `/tabs/wiki/article/${cat}/${id}`;
  }
}
