import { Injectable, signal, computed, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, tap, switchMap } from 'rxjs';
import { environment } from '../../../environments/environment';
import { LoginResponse, User } from '../../models/user.model';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private readonly TOKEN_KEY = 'jwt_token';
  private readonly apiUrl = environment.apiUrl;

  private http = inject(HttpClient);
  private router = inject(Router);

  private currentUserSignal = signal<User | null>(null);

  currentUser = this.currentUserSignal.asReadonly();
  isLoggedIn = computed(() => this.currentUserSignal() !== null);
  isAdmin = computed(
    () => this.currentUserSignal()?.roles.includes('ROLE_ADMIN') ?? false,
  );

  constructor() {
    this.loadUserFromToken();
  }

  login(email: string, password: string): Observable<User> {
    return this.http
      .post<LoginResponse>(`${this.apiUrl}/auth/login`, { email, password })
      .pipe(
        tap((response) => {
          this.setToken(response.token);
        }),
        switchMap(() => this.http.get<User>(`${this.apiUrl}/auth/me`)),
        tap((user) => {
          this.currentUserSignal.set(user);
        }),
      );
  }

  logout(): void {
    localStorage.removeItem(this.TOKEN_KEY);
    this.currentUserSignal.set(null);
    this.router.navigate(['/login']);
  }

  getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY);
  }

  private setToken(token: string): void {
    localStorage.setItem(this.TOKEN_KEY, token);
  }

  private loadUserFromToken(): void {
    const token = this.getToken();
    if (!token) {
      return;
    }
    this.http.get<User>(`${this.apiUrl}/auth/me`).subscribe({
      next: (user) => this.currentUserSignal.set(user),
      error: () => this.logout(),
    });
  }
}
