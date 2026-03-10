export type PropertyType = 'appartement' | 'maison' | 'bureau' | 'commerce';
export type PropertyStatus = 'available' | 'occupied' | 'maintenance';

export interface Property {
  id: string;
  title: string;
  type: PropertyType;
  address: string;
  city: string;
  surface: string | null;
  rooms: number | null;
  rentAmount: number;
  deposit: number | null;
  status: PropertyStatus;
  description: string | null;
  photos: string[];
  createdAt: string;
  owner: {
    id: string;
    firstName: string;
    lastName: string;
  };
}

export interface CreatePropertyPayload {
  title: string;
  type: PropertyType;
  address: string;
  city: string;
  surface?: number;
  rooms?: number;
  rentAmount: number;
  deposit?: number;
  description?: string;
}

export interface UpdatePropertyPayload {
  title?: string;
  type?: PropertyType;
  address?: string;
  city?: string;
  surface?: number;
  rooms?: number;
  rentAmount?: number;
  deposit?: number;
  status?: PropertyStatus;
  description?: string;
}

export const PROPERTY_TYPES: { value: PropertyType; label: string }[] = [
  { value: 'appartement', label: 'Appartement' },
  { value: 'maison', label: 'Maison' },
  { value: 'bureau', label: 'Bureau' },
  { value: 'commerce', label: 'Commerce' },
];

export const PROPERTY_STATUSES: { value: PropertyStatus; label: string }[] = [
  { value: 'available', label: 'Disponible' },
  { value: 'occupied', label: 'Occupé' },
  { value: 'maintenance', label: 'Maintenance' },
];
