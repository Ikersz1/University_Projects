import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ArticlePageRoutingModule } from './article-routing.module';

import { ArticlePage } from './article.page';

// 🔽 NUEVOS IMPORTS
import { Storage } from '@ionic/storage-angular';
import { StorageService } from '../services/storage.service';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ArticlePageRoutingModule,
  ],
  declarations: [ArticlePage],
  providers: [Storage, StorageService] // 🔽 AÑADIMOS EL PROVIDER
})
export class ArticlePageModule {}
