import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import {
  Property,
  CreatePropertyPayload,
  UpdatePropertyPayload,
} from '../../models/property.model';

@Injectable({
  providedIn: 'root',
})
export class PropertyService {
  private http = inject(HttpClient);
  private apiUrl = environment.apiUrl;

  getProperties(): Observable<Property[]> {
    return this.http.get<Property[]>(`${this.apiUrl}/v1/properties`);
  }

  getProperty(id: string): Observable<Property> {
    return this.http.get<Property>(`${this.apiUrl}/v1/properties/${id}`);
  }

  createProperty(payload: CreatePropertyPayload): Observable<Property> {
    return this.http.post<Property>(`${this.apiUrl}/v1/properties`, payload);
  }

  updateProperty(
    id: string,
    payload: UpdatePropertyPayload,
  ): Observable<Property> {
    return this.http.put<Property>(
      `${this.apiUrl}/v1/properties/${id}`,
      payload,
    );
  }

  deleteProperty(id: string): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/v1/properties/${id}`);
  }

  uploadPhoto(id: string, file: File): Observable<Property> {
    const formData = new FormData();
    formData.append('photo', file);

    return this.http.post<Property>(
      `${this.apiUrl}/v1/properties/${id}/photos`,
      formData,
    );
  }

  formatRentAmount(amount: number): string {
    return new Intl.NumberFormat('fr-MG').format(amount) + ' Ar';
  }
}
