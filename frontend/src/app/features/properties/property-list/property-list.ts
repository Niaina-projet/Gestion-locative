import { Component, inject, signal, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormBuilder, ReactiveFormsModule } from '@angular/forms';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { PropertyService } from '../../../core/services/property';
import { AuthService } from '../../../core/services/auth';
import { StatusBadge } from '../../../shared/components/status-badge/status-badge';
import {
  Property,
  PROPERTY_TYPES,
  PROPERTY_STATUSES,
} from '../../../models/property.model';

@Component({
  selector: 'app-property-list',
  imports: [
    ReactiveFormsModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatTooltipModule,
    MatProgressSpinnerModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatPaginatorModule,
    StatusBadge,
  ],
  templateUrl: './property-list.html',
  styleUrl: './property-list.scss',
})
export class PropertyList implements OnInit {
  private propertyService = inject(PropertyService);
  private router = inject(Router);
  private fb = inject(FormBuilder);
  authService = inject(AuthService);

  properties = signal<Property[]>([]);
  isLoading = signal(true);
  totalProperties = signal(0);
  currentPage = signal(1);
  pageSize = signal(10);

  displayedColumns = [
    'title',
    'type',
    'city',
    'rentAmount',
    'status',
    'actions',
  ];
  propertyTypes = PROPERTY_TYPES;
  propertyStatuses = PROPERTY_STATUSES;

  filterForm = this.fb.group({
    search: [''],
    type: [''],
    status: [''],
    city: [''],
  });

  ngOnInit(): void {
    this.loadProperties();

    // Écoute les changements des filtres avec un délai de 400ms
    this.filterForm.valueChanges
      .pipe(debounceTime(400), distinctUntilChanged())
      .subscribe(() => {
        this.currentPage.set(1);
        this.loadProperties();
      });
  }

  loadProperties(): void {
    this.isLoading.set(true);
    const filters = this.filterForm.value;

    this.propertyService
      .getProperties({
        search: filters.search || undefined,
        type: filters.type || undefined,
        status: filters.status || undefined,
        city: filters.city || undefined,
        page: this.currentPage(),
        limit: this.pageSize(),
      })
      .subscribe({
        next: (result) => {
          this.properties.set(result.data);
          this.totalProperties.set(result.total);
          this.isLoading.set(false);
        },
        error: () => this.isLoading.set(false),
      });
  }

  onPageChange(event: PageEvent): void {
    this.currentPage.set(event.pageIndex + 1);
    this.pageSize.set(event.pageSize);
    this.loadProperties();
  }

  resetFilters(): void {
    this.filterForm.reset();
    this.currentPage.set(1);
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
