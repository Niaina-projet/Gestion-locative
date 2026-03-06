import { Component, inject, signal } from '@angular/core';
import { RouterOutlet, RouterLink, RouterLinkActive } from '@angular/router';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatListModule } from '@angular/material/list';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatMenuModule } from '@angular/material/menu';
import { AuthService } from '../../../core/services/auth';

interface NavItem {
  label: string;
  icon: string;
  route: string;
  adminOnly: boolean;
}

@Component({
  selector: 'app-layout',
  imports: [
    RouterOutlet,
    RouterLink,
    RouterLinkActive,
    MatSidenavModule,
    MatToolbarModule,
    MatListModule,
    MatIconModule,
    MatButtonModule,
    MatMenuModule,
  ],
  templateUrl: './layout.html',
  styleUrl: './layout.scss',
})
export class Layout {
  authService = inject(AuthService);
  isMobileMenuOpen = signal(false);

  navItems: NavItem[] = [
    {
      label: 'Tableau de bord',
      icon: 'dashboard',
      route: '/dashboard',
      adminOnly: false,
    },
    {
      label: 'Biens immobiliers',
      icon: 'home',
      route: '/properties',
      adminOnly: false,
    },
    {
      label: 'Locataires',
      icon: 'people',
      route: '/tenants',
      adminOnly: false,
    },
    {
      label: 'Contrats',
      icon: 'description',
      route: '/contracts',
      adminOnly: false,
    },
    {
      label: 'Paiements',
      icon: 'payments',
      route: '/payments',
      adminOnly: false,
    },
    {
      label: 'Utilisateurs',
      icon: 'manage_accounts',
      route: '/users',
      adminOnly: true,
    },
    {
      label: 'Mon profil',
      icon: 'person',
      route: '/profile',
      adminOnly: false,
    },
  ];

  get visibleNavItems(): NavItem[] {
    return this.navItems.filter(
      (item) => !item.adminOnly || this.authService.isAdmin(),
    );
  }

  toggleMobileMenu(): void {
    this.isMobileMenuOpen.update((value) => !value);
  }
}
