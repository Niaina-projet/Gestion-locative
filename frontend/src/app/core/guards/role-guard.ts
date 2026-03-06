import { inject } from '@angular/core';
import { CanActivateFn, Router, ActivatedRouteSnapshot } from '@angular/router';
import { AuthService } from '../services/auth';

export const roleGuard: CanActivateFn = (route: ActivatedRouteSnapshot) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  const requiredRole = route.data['role'] as string;

  if (
    authService.isLoggedIn() &&
    authService.getCurrentUser()?.roles.includes(requiredRole)
  ) {
    return true;
  }

  router.navigate(['/dashboard']);
  return false;
};
