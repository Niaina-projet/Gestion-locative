import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';
import { AuthService } from '../services/auth';

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);
  const authService = inject(AuthService);

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      switch (error.status) {
        case 401:
          // Token expiré ou invalide — déconnexion automatique
          authService.logout();
          break;

        case 403:
          // Accès refusé — redirection vers page d'accueil
          router.navigate(['/dashboard']);
          break;

        case 404:
          // Ressource non trouvée
          console.error('Resource not found:', error.url);
          break;

        case 500:
          // Erreur serveur
          console.error('Server error:', error.message);
          break;
      }

      return throwError(() => error);
    }),
  );
};
