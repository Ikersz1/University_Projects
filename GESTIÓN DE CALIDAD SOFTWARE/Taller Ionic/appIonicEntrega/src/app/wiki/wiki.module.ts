import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { WikiPageRoutingModule } from './wiki-routing.module';
import { WikiPage } from './wiki.page';
import { CategoryComponent } from '../category/category.component';

import { HttpClientModule } from '@angular/common/http';
import { WikiService } from '../services/wiki.service';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    WikiPageRoutingModule,
    HttpClientModule
  ],
  declarations: [
    WikiPage,
    CategoryComponent
  ],
  providers: [
    WikiService
  ]
})
export class WikiPageModule {}
