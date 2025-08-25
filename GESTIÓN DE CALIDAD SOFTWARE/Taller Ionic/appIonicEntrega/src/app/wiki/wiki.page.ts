import { Component, OnInit } from '@angular/core';
import { Category } from '../models/category';

import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';

import { WikiPageRoutingModule } from './wiki-routing.module';

import { CategoryComponent } from '../category/category.component'; // <-- Importa el componente


@Component({
  selector: 'app-wiki',
  templateUrl: './wiki.page.html',
  styleUrls: ['./wiki.page.scss'],
  standalone: false 
})
export class WikiPage implements OnInit {

  readonly categoriesMock: string = './assets/data/categories.json';

  categories: Category[] = [];

  constructor() { }

  ngOnInit() {
    this.getData();
  }

  selectedCategory: string = '';

  selectCategory(name: string) {
    this.selectedCategory = name;
  }

  getData() {
    fetch(this.categoriesMock)
      .then(res => res.json())
      .then(json => {
        this.categories = json;
      });
  }
}
