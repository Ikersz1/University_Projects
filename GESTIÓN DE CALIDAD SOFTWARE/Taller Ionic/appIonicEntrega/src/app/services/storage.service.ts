import { Injectable } from '@angular/core';
import { Storage } from '@ionic/storage-angular';

@Injectable({
  providedIn: 'root'
})
export class StorageService {
  private _storage: Storage | null = null;

  constructor(private storage: Storage) {
    this.init();
  }

  async init() {
    const storage = await this.storage.create();
    this._storage = storage;
  }

  async getFavorites(): Promise<any[]> {
    return (await this._storage?.get('favorites')) || [];
  }

  async addFavorite(item: { id: string, category: string, name: string }) {
    const favorites = await this.getFavorites();
    // Evita duplicados
    const exists = favorites.find((f: any) => f.id === item.id && f.category === item.category);
    if (!exists) {
      favorites.push(item);
      await this._storage?.set('favorites', favorites);
    }
  }

  async removeFavorite(id: string, category: string) {
    let favorites = await this.getFavorites();
    favorites = favorites.filter((f: any) => f.id !== id || f.category !== category);
    await this._storage?.set('favorites', favorites);
  }

  async isFavorite(id: string, category: string): Promise<boolean> {
    const favorites = await this.getFavorites();
    return favorites.some((f: any) => f.id === id && f.category === category);
  }

  async set(key: string, value: any): Promise<void> {
    await this._storage?.set(key, value);
  }

  async get(key: string): Promise<any> {
    return await this._storage?.get(key);
  }
}
