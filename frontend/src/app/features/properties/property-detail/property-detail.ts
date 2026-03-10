import { Component, inject, signal, OnInit } from '@angular/core';
import { DatePipe } from '@angular/common';
import { Router, ActivatedRoute } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatDividerModule } from '@angular/material/divider';
import { PropertyService } from '../../../core/services/property';
import { StatusBadge } from '../../../shared/components/status-badge/status-badge';
import { Property } from '../../../models/property.model';

@Component({
  selector: 'app-property-detail',
  imports: [
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatProgressSpinnerModule,
    MatDividerModule,
    StatusBadge,
    DatePipe,
  ],
  templateUrl: './property-detail.html',
  styleUrl: './property-detail.scss',
})
export class PropertyDetail implements OnInit {
  propertyService = inject(PropertyService);
  private router = inject(Router);
  private route = inject(ActivatedRoute);

  property = signal<Property | null>(null);
  isLoading = signal(true);

  zoomedPhotoIndex = signal<number | null>(null);

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadProperty(id);
    }
  }

  loadProperty(id: string): void {
    this.propertyService.getProperty(id).subscribe({
      next: (property) => {
        this.property.set(property);
        this.isLoading.set(false);
      },
      error: () => {
        this.isLoading.set(false);
        this.router.navigate(['/properties']);
      },
    });
  }

  navigateToEdit(): void {
    this.router.navigate(['/properties', this.property()?.id, 'edit']);
  }

  navigateBack(): void {
    this.router.navigate(['/properties']);
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

  get zoomedPhoto(): string | null {
    const index = this.zoomedPhotoIndex();
    if (index === null) return null;
    return this.propertyService.getPhotoUrl(this.property()!.photos[index]);
  }

  openZoom(index: number): void {
    this.zoomedPhotoIndex.set(index);
  }

  closeZoom(): void {
    this.zoomedPhotoIndex.set(null);
  }

  prevPhoto(): void {
    const index = this.zoomedPhotoIndex();
    if (index === null) return;
    const total = this.property()!.photos.length;
    this.zoomedPhotoIndex.set((index - 1 + total) % total);
  }

  nextPhoto(): void {
    const index = this.zoomedPhotoIndex();
    if (index === null) return;
    const total = this.property()!.photos.length;
    this.zoomedPhotoIndex.set((index + 1) % total);
  }
}
