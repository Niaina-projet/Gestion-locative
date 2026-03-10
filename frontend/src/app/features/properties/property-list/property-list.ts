import { Component, inject, signal, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatDialogModule, MatDialog } from '@angular/material/dialog';
import { PropertyService } from '../../../core/services/property';
import { StatusBadge } from '../../../shared/components/status-badge/status-badge';
import { Property } from '../../../models/property.model';

@Component({
  selector: 'app-property-list',
  imports: [
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatTooltipModule,
    MatProgressSpinnerModule,
    MatDialogModule,
    StatusBadge,
  ],
  templateUrl: './property-list.html',
  styleUrl: './property-list.scss',
})
export class PropertyList implements OnInit {
  private propertyService = inject(PropertyService);
  private router = inject(Router);
  private dialog = inject(MatDialog);

  properties = signal<Property[]>([]);
  isLoading = signal(true);
  displayedColumns = [
    'title',
    'type',
    'city',
    'rentAmount',
    'status',
    'actions',
  ];

  ngOnInit(): void {
    this.loadProperties();
  }

  loadProperties(): void {
    this.isLoading.set(true);
    this.propertyService.getProperties().subscribe({
      next: (properties) => {
        this.properties.set(properties);
        this.isLoading.set(false);
      },
      error: () => this.isLoading.set(false),
    });
  }

  navigateToCreate(): void {
    this.router.navigate(['/properties/create']);
  }

  navigateToDetail(id: string): void {
    this.router.navigate(['/properties', id]);
  }

  navigateToEdit(id: string): void {
    this.router.navigate(['/properties', id, 'edit']);
  }

  deleteProperty(property: Property): void {
    if (!confirm(`Supprimer "${property.title}" ?`)) {
      return;
    }

    this.propertyService.deleteProperty(property.id).subscribe({
      next: () => this.loadProperties(),
    });
  }

  formatRent(amount: number): string {
    return this.propertyService.formatRentAmount(amount);
  }

  getTypeLabel(type: string): string {
    const labels: Record<string, string> = {
      appartement: 'Appartement',
      maison: 'Maison',
      bureau: 'Bureau',
      commerce: 'Commerce',
    };
    return labels[type] ?? type;
  }
}
