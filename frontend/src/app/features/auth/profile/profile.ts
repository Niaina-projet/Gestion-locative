import { Component, inject, signal } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { AuthService } from '../../../core/services/auth';
import { UserService } from '../../../core/services/user';

@Component({
  selector: 'app-profile',
  imports: [
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    MatProgressSpinnerModule,
    MatSnackBarModule,
  ],
  templateUrl: './profile.html',
  styleUrl: './profile.scss',
})
export class Profile {
  private fb = inject(FormBuilder);
  private userService = inject(UserService);
  private snackBar = inject(MatSnackBar);
  authService = inject(AuthService);

  isLoading = signal(false);
  hidePassword = signal(true);

  profileForm: FormGroup = this.fb.group({
    firstName: [
      this.authService.getCurrentUser()?.firstName ?? '',
      [Validators.required, Validators.minLength(2)],
    ],
    lastName: [
      this.authService.getCurrentUser()?.lastName ?? '',
      [Validators.required, Validators.minLength(2)],
    ],
    password: ['', [Validators.minLength(8)]],
  });

  onSubmit(): void {
    if (this.profileForm.invalid) {
      return;
    }

    this.isLoading.set(true);

    const payload = {
      firstName: this.profileForm.value.firstName,
      lastName: this.profileForm.value.lastName,
      ...(this.profileForm.value.password && {
        password: this.profileForm.value.password,
      }),
    };

    this.userService.updateProfile(payload).subscribe({
      next: () => {
        this.isLoading.set(false);
        this.authService.refreshToken().subscribe({
          next: () => {
            this.snackBar.open('Profil mis à jour avec succès', 'Fermer', {
              duration: 3000,
            });
            window.location.reload();
          },
        });
        this.profileForm.patchValue({ password: '' });
      },
      error: () => {
        this.isLoading.set(false);
        this.snackBar.open('Erreur lors de la mise à jour', 'Fermer', {
          duration: 3000,
          panelClass: 'error-snackbar',
        });
      },
    });
  }

  togglePasswordVisibility(): void {
    this.hidePassword.update((value) => !value);
  }
}
