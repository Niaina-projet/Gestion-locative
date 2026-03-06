import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { User } from '../../models/user.model';

export interface UpdateProfilePayload {
  firstName: string;
  lastName: string;
  password?: string;
}

export interface ToggleUserResponse {
  message: string;
  isActive: boolean;
}

@Injectable({
  providedIn: 'root',
})
export class UserService {
  private http = inject(HttpClient);
  private apiUrl = environment.apiUrl;

  getUsers(): Observable<User[]> {
    return this.http.get<User[]>(`${this.apiUrl}/v1/users`);
  }

  toggleUser(userId: string): Observable<ToggleUserResponse> {
    return this.http.patch<ToggleUserResponse>(
      `${this.apiUrl}/v1/users/${userId}/toggle`,
      {},
    );
  }

  updateProfile(
    payload: UpdateProfilePayload,
  ): Observable<{ message: string; user: User }> {
    return this.http.put<{ message: string; user: User }>(
      `${this.apiUrl}/auth/profile`,
      payload,
    );
  }

  getRoleLabel(roles: string[]): string {
    if (roles.includes('ROLE_ADMIN')) {
      return 'Administrateur';
    }

    return 'Gestionnaire';
  }
}
