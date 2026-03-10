import { Component, input } from '@angular/core';
import { MatChipsModule } from '@angular/material/chips';

export type StatusType =
  | 'available'
  | 'occupied'
  | 'maintenance'
  | 'active'
  | 'inactive'
  | 'paid'
  | 'pending'
  | 'late';

interface StatusConfig {
  label: string;
  color: 'primary' | 'accent' | 'warn';
}

const STATUS_CONFIG: Record<StatusType, StatusConfig> = {
  available: { label: 'Disponible', color: 'primary' },
  occupied: { label: 'Occupé', color: 'accent' },
  maintenance: { label: 'Maintenance', color: 'warn' },
  active: { label: 'Actif', color: 'primary' },
  inactive: { label: 'Inactif', color: 'warn' },
  paid: { label: 'Payé', color: 'primary' },
  pending: { label: 'En attente', color: 'accent' },
  late: { label: 'En retard', color: 'warn' },
};

@Component({
  selector: 'app-status-badge',
  imports: [MatChipsModule],
  templateUrl: './status-badge.html',
  styleUrl: './status-badge.scss',
})
export class StatusBadge {
  status = input.required<StatusType>();

  get config(): StatusConfig {
    return (
      STATUS_CONFIG[this.status()] ?? { label: this.status(), color: 'primary' }
    );
  }
}
