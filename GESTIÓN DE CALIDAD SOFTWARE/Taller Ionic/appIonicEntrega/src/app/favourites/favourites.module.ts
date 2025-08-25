import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { FavouritesPageRoutingModule } from './favourites-routing.module';

import { FavouritesPage } from './favourites.page';

// 🔽 NUEVOS IMPORTS
import { Storage } from '@ionic/storage-angular';
import { StorageService } from '../services/storage.service';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    FavouritesPageRoutingModule
  ],
  declarations: [FavouritesPage],
  providers: [Storage, StorageService] // 🔽 AÑADIMOS EL PROVIDER
})
export class FavouritesPageModule {}
