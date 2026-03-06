import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, tap } from 'rxjs';
import { environment } from '../../../environments/environment';
import { LoginResponse } from '../../models/user.model';

export interface CurrentUser {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  roles: string[];
}

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private readonly TOKEN_KEY = 'jwt_token';
  private readonly apiUrl = environment.apiUrl;

  private http = inject(HttpClient);
  private router = inject(Router);

  login(email: string, password: string): Observable<LoginResponse> {
    return this.http
      .post<LoginResponse>(`${this.apiUrl}/auth/login`, { email, password })
      .pipe(tap((response) => this.saveToken(response.token)));
  }

  logout(): void {
    localStorage.removeItem(this.TOKEN_KEY);
    this.router.navigate(['/login']);
  }

  getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY);
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }

  isAdmin(): boolean {
    return this.getCurrentUser()?.roles.includes('ROLE_ADMIN') ?? false;
  }

  getCurrentUser(): CurrentUser | null {
    const payload = this.getPayload();
    if (!payload) {
      return null;
    }
    return {
      id: (payload['id'] as string) ?? '',
      email: (payload['username'] as string) ?? '',
      firstName: (payload['firstName'] as string) ?? '',
      lastName: (payload['lastName'] as string) ?? '',
      roles: (payload['roles'] as string[]) ?? [],
    };
  }

  private getPayload(): Record<string, unknown> | null {
    const token = this.getToken();
    if (!token) {
      return null;
    }
    try {
      return JSON.parse(atob(token.split('.')[1]));
    } catch {
      return null;
    }
  }

  private saveToken(token: string): void {
    localStorage.setItem(this.TOKEN_KEY, token);
  }

  refreshToken(): Observable<LoginResponse> {
    return this.http
      .get<LoginResponse>(`${this.apiUrl}/auth/refresh-token`)
      .pipe(tap((response) => this.saveToken(response.token)));
  }
}
